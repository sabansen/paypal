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
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */


use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Refund;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;
use PaypalAddons\classes\API\Action\PaypalOrderRefund;
use PaypalAddons\classes\API\Builder\AmountBuilder;
use PaypalAddons\classes\API\Builder\ItemListBuilder;
use PaypalAddons\classes\API\Builder\PayerBuilder;
use PaypalAddons\classes\API\Builder\PayerInfoBuilder;
use PaypalAddons\classes\API\Builder\PaymentBuilder;
use PaypalAddons\classes\API\Builder\RedirectUrlsBuilder;
use PaypalAddons\classes\API\Builder\ShippingAddressBuilder;
use PaypalAddons\classes\API\Builder\TransactionBuilder;
use PaypalAddons\classes\API\Builder\WebProfileBuilder;
use PaypalAddons\classes\API\PaypalApiManager;
use PaypalAddons\classes\API\PaypalApiSdkBuilerFactory;
use PaypalAddons\classes\API\PaypalClient;
use PaypalAddons\classes\PaypalException;
use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;


/**
 * Class MethodEC.
 * @see https://developer.paypal.com/docs/classic/api/ NVP SOAP SDK
 * @see https://developer.paypal.com/docs/classic/api/nvpsoap-sdks/
 */
class MethodEC extends AbstractMethodPaypal
{
    /** @var string token. for in-context */
    public $token;

    /** @var boolean pay with card without pp account */
    public $credit_card;

    /** @var boolean shortcut payment from product or cart page*/
    public $short_cut;

    /** @var string payment token returned by paypal*/
    private $payment_token;

    /** @var string payment payer ID returned by paypal*/
    private $payerId;

    protected $payment_method = 'PayPal';

    protected $transaction_detail;

    public $errors = array();

    public $advancedFormParametres = array(
        'paypal_os_accepted_two',
        'paypal_os_waiting_validation'
    );

    /** payment Object IDl*/
    protected $paymentId;

    /** @var bool*/
    protected $isSandbox;

    /** @var string*/
    protected $clientId;

    /** @var string*/
    protected $secret;

    protected $paypalApiManager;

    public function __construct()
    {
        $this->paypalApiManager = new PaypalApiManager($this);
    }

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

    public function setPaymentId($payemtId)
    {
        $this->paymentId = $payemtId;
        return $this;
    }

    public function getPaymentId()
    {
        return $this->paymentId;
    }

    public function setPayerId($payerId)
    {
        $this->payerId = $payerId;
        return $this;
    }

    public function getPayerId()
    {
        return $this->payerId;
    }

    public function setShortCut($shortCut)
    {
        $this->short_cut = $shortCut;
        return $this;
    }

    public function getShortCut()
    {
        return $this->short_cut;
    }

    /**
     * @see AbstractMethodPaypal::getConfig()
     */
    public function getConfig(\PayPal $module)
    {
    }

    public function logOut($sandbox = null)
    {
        if ($sandbox == null) {
            $mode = Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';
        } else {
            $mode = (int)$sandbox ? 'SANDBOX' : 'LIVE';
        }

        Configuration::updateValue('PAYPAL_EC_CLIENTID_' . $mode, '');
        Configuration::updateValue('PAYPAL_EC_SECRET_' . $mode, '');
        Configuration::updateValue('PAYPAL_CONNECTION_EC_CONFIGURED', 0);
    }

    /**
     * @see AbstractMethodPaypal::setConfig()
     */
    public function setConfig($params)
    {
        $mode = Configuration::get('PAYPAL_SANDBOX');

        if (Configuration::get('PAYPAL_SANDBOX')) {
            Configuration::updateValue('PAYPAL_EC_CLIENTID_SANDBOX', Tools::getValue('paypal_ec_clientid'));
            Configuration::updateValue('PAYPAL_EC_SECRET_SANDBOX', Tools::getValue('paypal_ec_secret'));
        } else {
            Configuration::updateValue('PAYPAL_EC_CLIENTID_LIVE', Tools::getValue('paypal_ec_clientid'));
            Configuration::updateValue('PAYPAL_EC_SECRET_LIVE', Tools::getValue('paypal_ec_secret'));
        }

        $this->checkCredentials($mode);
    }

    /**
     * The SetExpressCheckout API operation initiates an Express Checkout transaction
     * @see AbstractMethodPaypal::init()
     */
    public function init()
    {
        if ($this->isConfigured() == false) {
            return '';
        }

        /** @var $payment \PaypalAddons\classes\API\Response\ResponseOrderCreate*/
        $response = $this->paypalApiManager->getOrderRequest()->execute();

        if ($response->isSuccess() == false) {
            throw new Exception($response->getError()->getMessage());
        }

        $this->setPaymentId($response->getPaymentId());

        return $response->getApproveLink();
    }

    /**
     * Convert and format price
     * @param $price
     * @return float|int|string
     */
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

    /**
     * @param string $method
     * @return string Url
     */
    public function redirectToAPI($method)
    {
        if ($this->useMobile()) {
            $url = '/cgi-bin/webscr?cmd=_express-checkout-mobile';
        } else {
            $url = '/websc&cmd=_express-checkout';
        }

        if (($method == 'SetExpressCheckout') && $this->credit_card) {
            $url .= '&useraction=commit';
        }
        $paypal = Module::getInstanceByName($this->name);
        return $paypal->getUrl().$url.'&token='.urldecode($this->token);
    }

    /**
     * @return bool
     */
    public function useMobile()
    {
        if ((method_exists(Context::getContext(), 'getMobileDevice') && Context::getContext()->getMobileDevice())
            || Tools::getValue('ps_mobile_site')) {
            return true;
        }

        return false;
    }

    /**
     * @return array Merchant Credentiales
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
                        Configuration::get('PAYPAL_EC_CLIENTID_LIVE'),
                        Configuration::get('PAYPAL_EC_SECRET_LIVE')
                    )
                );
                break;
            case 1:
                $apiContext = new ApiContext(
                    new OAuthTokenCredential(
                        Configuration::get('PAYPAL_EC_CLIENTID_SANDBOX'),
                        Configuration::get('PAYPAL_EC_SECRET_SANDBOX')
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

        $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', (getenv('PLATEFORM') == 'PSREAD') ? 'PrestaShop_Cart_Ready_EC' : 'PrestaShop_Cart_EC');
        return $apiContext;
    }

    /**
     * @param $cart Cart
     * @return string additional payment information
     */
    public function getCustomFieldInformation(Cart $cart)
    {
        $module = Module::getInstanceByName($this->name);
        $return = $module->l('Cart ID: ',  get_class($this)) . $cart->id . '.';

        if (Shop::isFeatureActive()) {
            $shop = new Shop($cart->id_shop, $cart->id_lang);

            if (Validate::isLoadedObject($shop)) {
                $return .= $module->l('Shop name: ',  get_class($this)) . $shop->name;
            }
        }

        return $return;

    }

    /**
     * @see AbstractMethodPaypal::validation()
     */
    public function validation()
    {
        $context = Context::getContext();
        $cart = $context->cart;
        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            throw new Exception('Customer is not loaded object');
        }

        if ($this->getPayerId() == false) {
            throw new Exception('Payer ID isn\'t setted');
        }

        if ($this->getPaymentId() == false) {
            throw new Exception('Payment ID isn\'t setted');
        }

        if ($this->getIntent() == 'CAPTURE') {
            $response = $this->paypalApiManager->getOrderCaptureRequest($this->getPaymentId())->execute();
        } else {
            $response = $this->paypalApiManager->getOrderAuthorizeRequest($this->getPaymentId())->execute();
        }

        if ($response->isSuccess() == false) {
            throw new Exception($response->getError()->getMessage());
        }


        $this->setDetailsTransaction($response);
        $currency = $context->currency;
        $total = $response->getTotalPaid();
        $paypal = Module::getInstanceByName($this->name);
        $order_state = $this->getOrderStatus();
        $paypal->validateOrder($cart->id,
            $order_state,
            $total,
            $this->getPaymentMethod(),
            null,
            $this->getDetailsTransaction(),
            (int)$currency->id,
            false,
            $customer->secure_key);
    }

    /**
     * @return int id of the order status
     **/
    public function getOrderStatus()
    {
        if ((int)Configuration::get('PAYPAL_CUSTOMIZE_ORDER_STATUS')) {
            if (Configuration::get('PAYPAL_API_INTENT') == "sale") {
                $orderStatus = (int)Configuration::get('PAYPAL_OS_ACCEPTED_TWO');
            } else {
                $orderStatus = (int)Configuration::get('PAYPAL_OS_WAITING_VALIDATION');
            }
        } else {
            if (Configuration::get('PAYPAL_API_INTENT') == "sale") {
                $orderStatus = (int)Configuration::get('PS_OS_PAYMENT');
            } else {
                $orderStatus = (int)Configuration::get('PAYPAL_OS_WAITING');
            }
        }

        return $orderStatus;
    }

    public function setDetailsTransaction($data)
    {
        /** @var $data \PaypalAddons\classes\API\Response\ResponseOrderCapture*/
        $transaction_detail = array(
            'method' => $data->getMethod(),
            'currency' => $data->getCurrency(),
            'payment_status' => $data->getStatus(),
            'payment_method' => $data->getPaymentMethod(),
            'id_payment' => pSQL($data->getPaymentId()),
            'payment_tool' => $data->getPaymentTool(),
            'date_transaction' => $data->getDateTransaction()->format('Y-m-d H:i:s'),
            'transaction_id' => $data->getTransactionId(),
            'capture' => $data->isCapture()
        );

        $this->transaction_detail = $transaction_detail;
    }

    public function getDateTransaction()
    {
        $dateServer = new DateTime();
        $timeZonePayPal = new DateTimeZone('PST');
        $dateServer->setTimezone($timeZonePayPal);
        return $dateServer->format('Y-m-d H:i:s');
    }

    /**
     * @see AbstractMethodPaypal::confirmCapture()
     */
    public function confirmCapture($paypal_order)
    {
        $id_paypal_order = $paypal_order->id;
        $currency = $paypal_order->currency;
        $amount = $paypal_order->total_paid;
        $doCaptureRequestType = new DoCaptureRequestType();
        $doCaptureRequestType->AuthorizationID = $paypal_order->id_transaction;
        $doCaptureRequestType->Amount = new BasicAmountType($currency, number_format($amount, Paypal::getDecimal(), ".", ''));
        $doCaptureRequestType->CompleteType = 'Complete';
        $doCaptureReq = new DoCaptureReq();
        $doCaptureReq->DoCaptureRequest = $doCaptureRequestType;

        $paypalService = new PayPalAPIInterfaceServiceService($this->_getCredentialsInfo($paypal_order->sandbox));
        $response = $paypalService->DoCapture($doCaptureReq);

        if ($response instanceof PayPal\PayPalAPI\DoCaptureResponseType) {
            $authorization_id = $response->DoCaptureResponseDetails->AuthorizationID;
            if (isset($response->Errors)) {
                $result = array(
                    'authorization_id' => $authorization_id,
                    'status' => $response->Ack,
                    'error_code' => $response->Errors[0]->ErrorCode,
                    'error_message' => $response->Errors[0]->LongMessage,
                );
                if ($response->Errors[0]->ErrorCode == "10602") {
                    $result['already_captured'] = true;
                }
            } else {
                $payment_info = $response->DoCaptureResponseDetails->PaymentInfo;
                PaypalCapture::updateCapture($payment_info->TransactionID, $payment_info->GrossAmount->value, $payment_info->PaymentStatus, $id_paypal_order);
                $result =  array(
                    'success' => true,
                    'authorization_id' => $payment_info->TransactionID,
                    'status' => $payment_info->PaymentStatus,
                    'amount' => $payment_info->GrossAmount->value,
                    'currency' => $payment_info->GrossAmount->currencyID,
                    'parent_payment' => $payment_info->ParentTransactionID,
                    'pending_reason' => $payment_info->PendingReason,
                    'date_transaction' => $this->getDateTransaction()
                );
            }
        }

        return $result;
    }

    /**
     * @see AbstractMethodPaypal::refund()
     */
    public function refund($paypalOrder)
    {
        $response = $this->paypalApiManager->getOrderRefundRequest($paypalOrder)->execute();
        return $response;
    }

    /**
     * @see AbstractMethodPaypal::partialRefund()
     */
    public function partialRefund($params)
    {
        $paypalOrder = PaypalOrder::loadByOrderId($params['order']->id);
        $amount = 0;

        foreach ($params['productList'] as $product) {
            $amount += $product['amount'];
        }

        if (Tools::getValue('partialRefundShippingCost')) {
            $amount += Tools::getValue('partialRefundShippingCost');
        }

        return $response = $this->paypalApiManager->getOrderPartialRefundRequest($paypalOrder, $amount)->execute();
    }

    /**
     * @see AbstractMethodPaypal::void()
     */
    public function void($orderPayPal)
    {
        $response = $this->paypalApiManager->getAuthorizationVoidRequest($orderPayPal)->execute();
        return $response;
    }

    /**
     * @param $context
     * @param $type
     * @param $page_source
     * @return mixed
     */
    public function renderExpressCheckoutShortCut(Context &$context, $type, $page_source)
    {
        $lang = $context->language->iso_code;
        $environment = (Configuration::get('PAYPAL_SANDBOX')?'sandbox':'live');
        $img_esc = "modules/paypal/views/img/ECShortcut/".Tools::strtolower($lang)."/buy/buy.png";

        if (!file_exists(_PS_ROOT_DIR_.'/'.$img_esc)) {
            $img_esc = "modules/paypal/views/img/ECShortcut/us/buy/buy.png";
        }
        $shop_url = Context::getContext()->link->getBaseLink(Context::getContext()->shop->id, true);
        $context->smarty->assign(array(
            'shop_url' => $shop_url,
            'PayPal_payment_type' => $type,
            'PayPal_img_esc' => $shop_url.$img_esc,
            'action_url' => $context->link->getModuleLink($this->name, 'ScInit', array(), true),
            'ec_sc_in_context' => Configuration::get('PAYPAL_EXPRESS_CHECKOUT_IN_CONTEXT'),
            'merchant_id' => Configuration::get('PAYPAL_MERCHANT_ID_'.Tools::strtoupper($environment)),
            'environment' => $environment,
        ));
        if ($page_source == 'product') {
            $context->smarty->assign(array(
                'es_cs_product_attribute' => Tools::getValue('id_product_attribute')
            ));
        }
        $context->smarty->assign('source_page', $page_source);
        return $context->smarty->fetch('module:paypal/views/templates/hook/shortcut.tpl');
    }

    /**
     * @see AbstractMethodPaypal::getLinkToTransaction()
     */
    public function getLinkToTransaction($log)
    {
        if ($log->sandbox) {
            $url = 'https://www.sandbox.paypal.com/activity/payment/';
        } else {
            $url = 'https://www.paypal.com/activity/payment/';
        }
        return $url . $log->id_transaction;
    }

    /**
     * @return bool
     */
    public function isConfigured($mode = null)
    {
        return (bool)Configuration::get('PAYPAL_CONNECTION_EC_CONFIGURED');
    }

    public function checkCredentials($mode = null)
    {
        $response = $this->paypalApiManager->getAccessTokenRequest()->execute();

        if ($response->isSuccess()) {
            Configuration::updateValue('PAYPAL_CONNECTION_EC_CONFIGURED', 1);
        } else {
            Configuration::updateValue('PAYPAL_CONNECTION_EC_CONFIGURED', 0);

            if ($response->getError()) {
                $this->errors[] = $response->getError()->getMessage();
            }
        }
    }

    public function getTplVars()
    {
        $tplVars = array();

        if ((int)Configuration::get('PAYPAL_SANDBOX')) {
            $tplVars['paypal_ec_clientid'] = Configuration::get('PAYPAL_EC_CLIENTID_SANDBOX');
            $tplVars['paypal_ec_secret'] = Configuration::get('PAYPAL_EC_SECRET_SANDBOX');
        } else {
            $tplVars['paypal_ec_clientid'] = Configuration::get('PAYPAL_EC_CLIENTID_LIVE');
            $tplVars['paypal_ec_secret'] = Configuration::get('PAYPAL_EC_SECRET_LIVE');
        }

        $tplVars['accountConfigured'] = $this->isConfigured();

        return $tplVars;
    }

    public function getAdvancedFormInputs()
    {
        $inputs = array();
        $module = Module::getInstanceByName($this->name);
        $orderStatuses = $module->getOrderStatuses();

        if (Configuration::get('PAYPAL_API_INTENT') == 'authorization') {
            $inputs[] = array(
                'type' => 'select',
                'label' => $module->l('Payment authorized and waiting for validation by admin', get_class($this)),
                'name' => 'paypal_os_waiting_validation',
                'hint' => $module->l('You are currently using the Authorize mode. It means that you separate the payment authorization from the capture of the authorized payment. By default the orders will be created in the "Waiting for PayPal payment" but you can customize it if needed.', get_class($this)),
                'desc' => $module->l('Default status : Waiting for PayPal payment', get_class($this)),
                'options' => array(
                    'query' => $orderStatuses,
                    'id' => 'id',
                    'name' => 'name'
                )
            );
        } else {
            $inputs[] = array(
                'type' => 'select',
                'label' => $module->l('Payment accepted and transaction completed', get_class($this)),
                'name' => 'paypal_os_accepted_two',
                'hint' => $module->l('You are currently using the Sale mode (the authorization and capture occur at the same time as the sale). So the payement is accepted instantly and the new order is created in the "Payment accepted" status. You can customize the status for orders with completed transactions. Ex : you can create an additional status "Payment accepted via PayPal" and set it as the default status.', get_class($this)),
                'desc' => $module->l('Default status : Payment accepted', get_class($this)),
                'options' => array(
                    'query' => $orderStatuses,
                    'id' => 'id',
                    'name' => 'name'
                )
            );
        }

        return $inputs;
    }

    public function getPaymentReturnUrl()
    {
        if ($this->short_cut) {
            return Context::getContext()->link->getModuleLink($this->name, 'ecScOrder', array(), true);
        } else {
            return Context::getContext()->link->getModuleLink($this->name, 'ecValidation', array(), true);
        }
    }

    public function getIntent()
    {
        return Configuration::get('PAYPAL_API_INTENT') == 'sale' ? 'CAPTURE' : 'AUTHORIZE';
    }

    public function isSandbox()
    {
        if ($this->isSandbox !== null) {
            return $this->isSandbox;
        }

        $this->isSandbox = (bool)Configuration::get('PAYPAL_SANDBOX');
        return $this->isSandbox;
    }

    public function getClientId()
    {
        if ($this->clientId !== null) {
            return $this->clientId;
        }

        if ($this->isSandbox()) {
            $clientId = Configuration::get('PAYPAL_EC_CLIENTID_SANDBOX');
        } else {
            $clientId = Configuration::get('PAYPAL_EC_CLIENTID_LIVE');
        }

        $this->clientId = $clientId;
        return $this->clientId;
    }

    public function getSecret()
    {
        if ($this->secret !== null) {
            return $this->secret;
        }

        if ($this->isSandbox()) {
            $secret = Configuration::get('PAYPAL_EC_SECRET_SANDBOX');
        } else {
            $secret = Configuration::get('PAYPAL_EC_SECRET_LIVE');
        }

        $this->secret = $secret;
        return $this->secret;
    }

    public function getReturnUrl()
    {
        return Context::getContext()->link->getModuleLink($this->name, 'ecValidation', [], true);
    }

    public function getCancelUrl()
    {
        return Context::getContext()->link->getPageLink('order', true);
    }

    public function getPaypalPartnerId()
    {
        return (getenv('PLATEFORM') == 'PSREAD') ? 'PrestaShop_Cart_Ready_EC' : 'PrestaShop_Cart_EC';
    }
}
