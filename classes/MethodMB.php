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

require_once 'AbstractMethodPaypal.php';

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Refund;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;

/**
 * Class MethodPPP
 * @see https://paypal.github.io/PayPal-PHP-SDK/ REST API sdk doc
 * @see https://developer.paypal.com/docs/api/payments/v1/ REST API references
 */
class MethodMB extends AbstractMethodPaypal
{

    protected $payment_method = 'PayPal';

    public $errors = array();

    /**
     * @param $values array replace for tools::getValues()
     */
    public function setParameters($values)
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function logOut($sandbox = null)
    {
        if ($sandbox == null) {
            $mode = Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';
        } else {
            $mode = (int)$sandbox ? 'SANDBOX' : 'LIVE';
        }

        Configuration::updateValue('PAYPAL_MB_' . $mode . '_CLIENTID', '');
        Configuration::updateValue('PAYPAL_MB_' . $mode . '_SECRET', '');
    }

    /**
     * @see AbstractMethodPaypal::setConfig()
     */
    public function setConfig($params)
    {
    }

    public function getConfig(Paypal $paypal)
    {
    }

    /**
     * @return ApiContext
     */
    public function _getCredentialsInfo($mode_order = null)
    {
        if ($mode_order === null) {
            $mode_order = (int) Configuration::get('PAYPAL_SANDBOX');
        }
        switch ($mode_order) {
            case 0:
                $apiContext = new ApiContext(
                    new OAuthTokenCredential(
                        Configuration::get('PAYPAL_MB_LIVE_CLIENTID'),
                        Configuration::get('PAYPAL_MB_LIVE_SECRET')
                    )
                );
                break;
            case 1:
                $apiContext = new ApiContext(
                    new OAuthTokenCredential(
                        Configuration::get('PAYPAL_MB_SANDBOX_CLIENTID'),
                        Configuration::get('PAYPAL_MB_SANDBOX_SECRET')
                    )
                );
                break;
        }

        $apiContext->setConfig(
            array(
                'mode' => $mode_order ? 'sandbox' : 'live',
                'log.LogEnabled' => false,
                'cache.enabled' => true,
            )
        );
        $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', (getenv('PLATEFORM') == 'PSREAD')?'PrestaShop_Cart_Ready_PPP':'PrestaShop_Cart_PPP');
        return $apiContext;
    }

    public function createWebExperience()
    {
    }

    /**
     * @see AbstractMethodPaypal::init()
     */
    public function init()
    {

    }

    public function formatPrice($price)
    {
        $context = Context::getContext();
        $context_currency = $context->currency;
        $paypal = Module::getInstanceByName($this->name);
        if ($id_currency_to = $paypal->needConvert()) {
            $currency_to_convert = new Currency($id_currency_to);
            $price = Tools::convertPriceFull($price, $context_currency, $currency_to_convert);
        }
        $price = number_format($price, Paypal::getDecimal(), ".", '');
        return $price;
    }

    private function _getPaymentDetails()
    {

    }

    /**
     * @see AbstractMethodPaypal::validation()
     */
    public function validation()
    {
    }

    public function setDetailsTransaction($transaction)
    {
    }

    public function getDateTransaction($transaction)
    {
    }

    /**
     * @see AbstractMethodPaypal::confirmCapture()
     */
    public function confirmCapture($orderPayPal)
    {
    }

    /**
     * @see AbstractMethodPaypal::refund()
     */
    public function refund($paypal_order)
    {
    }

    /**
     * @see AbstractMethodPaypal::partialRefund()
     */
    public function partialRefund($params)
    {
    }

    /**
     * @see AbstractMethodPaypal::void()
     */
    public function void($orderPayPal)
    {
    }



    /**
     * @see AbstractMethodPaypal::getLinkToTransaction()
     */
    public function getLinkToTransaction($id_transaction, $sandbox)
    {
        if ($sandbox) {
            $url = 'https://www.sandbox.paypal.com/activity/payment/';
        } else {
            $url = 'https://www.paypal.com/activity/payment/';
        }
        return $url . $id_transaction;
    }

    /**
     * @return bool
     */
    public function isConfigured($mode = null)
    {
    }

    public function getTplVars()
    {
        $tpl_vars = array(
            'paypal_mb_sandbox_clientid' => Configuration::get('PAYPAL_MB_SANDBOX_CLIENTID'),
            'paypal_mb_live_clientid' => Configuration::get('PAYPAL_MB_LIVE_CLIENTID'),
            'paypal_mb_sandbox_secret' => Configuration::get('PAYPAL_MB_SANDBOX_SECRET'),
            'paypal_mb_live_secret' => Configuration::get('PAYPAL_MB_LIVE_SECRET'),
            'accountConfigured' => $this->isConfigured(),
        );

        return $tpl_vars;
    }

    public function checkCredentials()
    {

    }
}
