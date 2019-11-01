<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once _PS_MODULE_DIR_.'paypal/classes/PaypalOrder.php';
include_once _PS_MODULE_DIR_.'paypal/controllers/front/abstract.php';
include_once _PS_MODULE_DIR_.'paypal/classes/PaypalIpn.php';

use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;
use PaypalAddons\services\ServicePaypalIpn;


class PaypalIpnModuleFrontController extends PaypalAbstarctModuleFrontController
{
    /** @var ServicePaypalIpn*/
    protected $servicePaypalIpn;

    public function __construct()
    {
        parent::__construct();
        $this->servicePaypalIpn = new ServicePaypalIpn();
    }

    public function run()
    {
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
                null,
                null,
                null,
                null,
                \Tools::getValue('txn_id') ? \Tools::getValue('txn_id') : null,
                (int)\Configuration::get('PAYPAL_SANDBOX'),
                null
            );
            ProcessLoggerHandler::closeLogger();

            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        }

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

        $orders = $this->servicePaypalIpn->getOrdersPsByTransaction($data['txn_id']);

        if (is_array($orders) == false || empty($orders)) {
            return false;
        }

        ProcessLoggerHandler::openLogger();
        foreach ($orders as $order) {
            ProcessLoggerHandler::logInfo(
                'IPN response : ' . $this->jsonEncode($logResponse),
                isset($data['txn_id']) ? $data['txn_id'] : null,
                $order->id,
                $order->id_cart,
                null,
                'PayPal',
                (int)Configuration::get('PAYPAL_SANDBOX')
            );
        }
        ProcessLoggerHandler::closeLogger();

        $paypalIpn = new PaypalIpn();
        $paypalIpn->id_transaction = $data['txn_id'];
        $paypalIpn->status = $data['payment_status'];
        $paypalIpn->response = $this->jsonEncode($logResponse);
        $paypalIpn->save();

        if ($data['payment_status'] == 'Completed') {
            $this->setOrderStatus($orders, (int)\Configuration::get('PS_OS_PAYMENT'));
        }

        if (in_array($data['payment_status'], array('Failed', 'Reversed', 'Denied'))) {
            $this->setOrderStatus($orders, (int)\Configuration::get('PS_OS_CANCELED'));
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function requestIsValid()
    {
        $curl = curl_init($this->module->getIpnPaypalListener() . '?cmd=_notify-validate&' . http_build_query($_POST));
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        $response = curl_exec($curl);

        return trim($response) == 'VERIFIED';
    }

    protected function alreadyHandled($data)
    {
        return $this->servicePaypalIpn->exists($data['txn_id'], $data['payment_status']);
    }

    /**
     * @param $orders array
     * @param $idState int
     * @return bool
     */
    protected function setOrderStatus($orders, $idState)
    {
        /** @var $order \Order*/
        foreach ($orders as $order) {
            $order->setCurrentState((int)$idState);
        }

        return true;
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
        } else if (is_string ($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

}
