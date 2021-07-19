<?php
/**
 * 2007-2021 PayPal
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2021 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


use PaypalAddons\classes\Constants\WebhookHandler;
use PaypalAddons\classes\Webhook\RequestValidator;
use PaypalAddons\services\StatusMapping;
use PaypalAddons\services\ServicePaypalOrder;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Configuration;
use Tools;

/**
 * Class PaypalAbstarctModuleFrontController
 */
class PaypalWebhookhandlerModuleFrontController extends PaypalAbstarctModuleFrontController
{
    /** @var ServicePaypalOrder*/
    protected $servicePaypalOrder;

    public function __construct()
    {
        parent::__construct();

        $this->servicePaypalOrder = new ServicePaypalOrder();
    }
    public function run()
    {
        if ($this->isCheckAvailability()) {
            header("HTTP/1.1 " . WebhookHandler::STATUS_AVAILABLE); die;
        }

        try {
            if ($this->requestIsValid()) {
                if ($this->handleIpn(Tools::getAllValues())) {
                    header("HTTP/1.1 200 OK");
                } else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                }
            }
        } catch (\Exception $e) {
            $message = 'Error code: ' . $e->getCode() . '.';
            $message .= 'Short message: ' . $e->getMessage() . '.';

            ProcessLoggerHandler::openLogger();
            ProcessLoggerHandler::logError(
                $message,
                Tools::getValue('txn_id') ? Tools::getValue('txn_id') : null,
                null,
                null,
                null,
                null,
                (int)Configuration::get('PAYPAL_SANDBOX'),
                null
            );
            ProcessLoggerHandler::closeLogger();

            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        }
    }

    protected function isCheckAvailability()
    {
        return (bool)Tools::isSubmit('checkAvailability');
    }

    /**
     * @return bool
     */
    protected function requestIsValid()
    {
        return (new RequestValidator())->validate($_POST);
    }

    /**
     * @param $data array Ipn message data
     * @return bool
     */
    protected function handleIpn($data)
    {
        if ($this->alreadyHandled($data)) {
            return true;
        }

        $logResponse = array(
            'payment_status' => isset($data['payment_status']) ? $data['payment_status'] : null,
            'ipn_track_id' => isset($data['ipn_track_id']) ? $data['ipn_track_id'] : null
        );

        if ($data['payment_status'] == 'Refunded' && isset($data['parent_txn_id'])) {
            $transactionRef = $data['parent_txn_id'];
        } else {
            $transactionRef = $data['txn_id'];
        }

        $paypalOrder = $this->servicePaypalOrder->getPaypalOrderByTransaction($transactionRef);

        if (Validate::isLoadedObject($paypalOrder) == false) {
            return false;
        }

        $orders = $this->servicePaypalOrder->getPsOrders($paypalOrder);

        ProcessLoggerHandler::openLogger();
        foreach ($orders as $order) {
            ProcessLoggerHandler::logInfo(
                'Webhook event : ' . $this->jsonEncode($logResponse),
                $data['txn_id'],
                $order->id,
                $order->id_cart,
                null,
                'PayPal',
                (int)Configuration::get('PAYPAL_SANDBOX')
            );
        }
        ProcessLoggerHandler::closeLogger();

        $psOrderStatus = $this->getPsOrderStatus($data['payment_status']);

        if ($psOrderStatus > 0) {
            $this->servicePaypalOrder->setOrderStatus($paypalOrder, $psOrderStatus, false);
        }

        return true;
    }

    protected function alreadyHandled(array $data)
    {
        //todo: to implement
        return false;
    }

    protected function getPsOrderStatus($transactionStatus)
    {
        return (new StatusMapping())->getPsOrderStatusByTransaction($transactionStatus);
    }
}
