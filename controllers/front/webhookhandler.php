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
use PaypalAddons\classes\Constants\WebHookType;
use PaypalAddons\classes\Webhook\RequestValidator;
use PaypalAddons\services\ContainerService;
use PaypalAddons\services\StatusMapping;
use PaypalAddons\services\ServicePaypalOrder;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;

/**
 * Class PaypalAbstarctModuleFrontController
 */
class PaypalWebhookhandlerModuleFrontController extends PaypalAbstarctModuleFrontController
{
    /** @var ServicePaypalOrder*/
    protected $servicePaypalOrder;

    /** @var array*/
    protected $requestData;

    public function __construct()
    {
        parent::__construct();

        $this->servicePaypalOrder = new ServicePaypalOrder();
        $this->initContainer();
    }
    public function run()
    {
        if ($this->isCheckAvailability()) {
            header("HTTP/1.1 " . WebhookHandler::STATUS_AVAILABLE); die;
        }

        try {
            if ($this->requestIsValid()) {
                if ($this->handleIpn($this->getRequestData())) {
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
                $this->getTransactionRef($this->getRequestData()),
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
        // todo: to implement
        return true;
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

        if (false == isset($data['resource'])) {
            return false;
        }

        $msg = 'Webhook event : ' . $this->jsonEncode([
                'event_type' => $this->eventType($data),
                'webhook_id' => $this->getWebhookId($data),
                'data' => $data
            ]);
        $msg = Tools::substr($msg, 0, 999);

        $transaction = $this->getTransactionRef($data);
        $paypalOrder = $this->servicePaypalOrder->getPaypalOrderByTransaction($transaction);

        if (Validate::isLoadedObject($paypalOrder) == false) {
            return false;
        }

        $orders = $this->servicePaypalOrder->getPsOrders($paypalOrder);

        ProcessLoggerHandler::openLogger();
        foreach ($orders as $order) {
            ProcessLoggerHandler::logInfo(
                $msg,
                $transaction,
                $order->id,
                $order->id_cart,
                null,
                'PayPal',
                (int)Configuration::get('PAYPAL_SANDBOX')
            );
        }
        ProcessLoggerHandler::closeLogger();

        $psOrderStatus = $this->getPsOrderStatus($data);

        if ($psOrderStatus > 0) {
            $this->servicePaypalOrder->setOrderStatus($paypalOrder, $psOrderStatus, false);
        }

        return true;
    }

    protected function alreadyHandled($data)
    {
        //todo: to implement
        return false;
    }

    protected function getPsOrderStatus($data)
    {
        return (new StatusMapping())->getPsOrderStatusByEventType($this->eventType($data));
    }

    protected function getRequestData()
    {
        if (false == empty($this->requestData)) {
            return $this->requestData;
        }

        $this->requestData = json_decode(file_get_contents('php://input'), true);
        return $this->requestData;
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function eventType($data)
    {
        return isset($data['event_type']) ? (string)$data['event_type'] : '';
    }

    /**
     * @param mixed
     * @return string
     */
    protected function getTransactionRef($data)
    {
        if (false == isset($data['resource'])) {
            return '';
        }

        if ($this->eventType($data) == WebHookType::CAPTURE_REFUNDED) {
            foreach ($data['resource']['links'] as $link) {
                if ($link['rel'] == 'up') {
                    return $this->getTransactionFromHref($link['href']);
                }
            }
        }

        return isset($data['resource']['id']) ? (string)$data['resource']['id'] : '';
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function getWebhookId($data)
    {
        return isset($data['id']) ? $data['id'] : '';
    }

    /**
     * @param $value mixed
     * @return string
     */
    public function jsonEncode($value)
    {
        $result = json_encode($value);

        if (json_last_error() == JSON_ERROR_UTF8) {
            $result = json_encode($this->utf8ize($value));
        }

        return $result;
    }

    public function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } else if (is_string($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

    /**
     * @param string $href
     * @return string
     */
    protected function getTransactionFromHref($href)
    {
        $tmp = explode('/', $href);
        return (string)array_pop($tmp);
    }

    protected function initContainer()
    {
        $this->container = ContainerService::init();
    }
}
