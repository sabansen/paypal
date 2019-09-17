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

include_once _PS_MODULE_DIR_.'paypal/classes/AbstractMethodPaypal.php';
include_once _PS_MODULE_DIR_.'paypal/controllers/front/abstract.php';
include_once _PS_MODULE_DIR_.'paypal/classes/PaypalIpn.php';

use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;


class PaypalIpnModuleFrontController extends PaypalAbstarctModuleFrontController
{
    public function run()
    {
        $curl = curl_init($this->module->getIpnPaypalListener() . '?cmd=_notify-validate&' . http_build_query($_POST));
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        $response = curl_exec($curl);

        if (trim($response) == 'VERIFIED') {
            $this->handleIpn(Tools::getAllValues());
            header("HTTP/1.1 200 OK");
        }
    }

    protected function handleIpn($data)
    {

        $paypalIpn = new PaypalIpn();
        $paypalIpn->id_transaction = $data['txn_id'];
        $paypalIpn->status = $data['payment_status'];
        $paypalIpn->response = Tools::jsonEncode($data);
        try {
            $paypalIpn->save();
        } catch (Exception $e) {
            $message = 'Error code: ' . $e->getCode() . '.';
            $message .= 'Short message: ' . $e->getMessage() . '.';

            ProcessLoggerHandler::openLogger();
            ProcessLoggerHandler::logError(
                $message,
                null,
                null,
                null,
                null,
                isset($data['txn_id']) ? $data['txn_id'] : null,
                (int)\Configuration::get('PAYPAL_SANDBOX'),
                null
            );
            ProcessLoggerHandler::closeLogger();
            throw $e;
        }
    }
}
