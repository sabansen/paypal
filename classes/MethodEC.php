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

require_once 'AbstractMethodPaypal.php';

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
use PaypalAddons\classes\API\PaypalOrderRefund;
use PaypalAddons\classes\PaypalException;
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

    /** @var object PaymentDetailsType */
    private $_paymentDetails;

    /** @var float total item amount HT */
    private $_itemTotalValue = 0;

    /** @var float total cart taxes */
    private $_taxTotalValue = 0;

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

    /** @var PaypalApiManager*/
    protected $paypalApiManager;

    public function __construct()
    {
        $this->paypalApiManager = new PaypalApiManager(Context::getContext(), $this);
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
        Configuration::updateValue('PAYPAL_EC_EXPERIENCE', '');
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

        /** @var $payment Payment*/
        $payment = $this->paypalApiManager->get($this->getBuilderClass('payment'))->build();
        $payment->create($this->_getCredentialsInfo());

        // ### Get redirect url
        // The API response provides the url that you must redirect
        // the buyer to. Retrieve the url from the $payment->getApprovalLink() method
        $this->setPaymentId($payment->id);
        return $payment->getApprovalLink();
    }

    /**
     * @param String $key
     * @return String
     */
    public function getBuilderClass($key)
    {
        switch ($key) {
            case 'payment':
                $builder = PaymentBuilder::class;
                break;
            case 'itemList':
                $builder = ItemListBuilder::class;
                break;
            case 'amount':
                $builder = AmountBuilder::class;
                break;
            case 'redirectUrls':
                $builder = RedirectUrlsBuilder::class;
                break;
            case 'payer':
                $builder = PayerBuilder::class;
                break;
            case 'payerInfo':
                $builder = PayerInfoBuilder::class;
                break;
            case 'shippingAddress':
                $builder = ShippingAddressBuilder::class;
                break;
            case 'transaction':
                $builder = TransactionBuilder::class;
                break;
            case 'webProfile':
                $builder = WebProfileBuilder::class;
                break;
        }

        return $builder;
    }

    /**
     * Collect items information
     */
    private function _getPaymentDetails()
    {
        $paypal = Module::getInstanceByName($this->name);
        $currency = $paypal->getPaymentCurrencyIso();
        $this->_getProductsList($currency);
        $this->_getDiscountsList($currency);
        $this->_getGiftWrapping($currency);
        $this->_getPaymentValues($currency);
    }

    /**
     * @param $currency string
     */
    private function _getProductsList($currency)
    {
        $products = Context::getContext()->cart->getProducts();
        foreach ($products as $product) {
            $itemDetails = new PaymentDetailsItemType();
            $product['product_tax'] = $this->formatPrice($product['price_wt']) - $this->formatPrice($product['price']);
            $itemAmount = new BasicAmountType($currency, $this->formatPrice($product['price']));
            if (isset($product['attributes']) && (empty($product['attributes']) === false)) {
                $product['name'] .= ' - '.$product['attributes'];
            }
            $itemDetails->Name = $product['name'];
            $itemDetails->Amount = $itemAmount;
            $itemDetails->Quantity = $product['quantity'];
            $itemDetails->Tax = new BasicAmountType($currency, number_format($product['product_tax'], Paypal::getDecimal(), ".", ''));
            $this->_paymentDetails->PaymentDetailsItem[] = $itemDetails;
            $this->_itemTotalValue += $this->formatPrice($product['price']) * $product['quantity'];
            $this->_taxTotalValue += $product['product_tax'] * $product['quantity'];
        }
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

    private function _getDiscountsList($currency)
    {
        $discounts = Context::getContext()->cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING);
        if (count($discounts)) {
            $totalDiscounts = Context::getContext()->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
            $totalDiscounts = -1 * $this->formatPrice($totalDiscounts);

            $itemDetails = new PaymentDetailsItemType();
            $itemDetails->Name = 'Total discounts';
            $itemDetails->Amount = new BasicAmountType($currency, $totalDiscounts);
            $itemDetails->Quantity = 1;
            $this->_paymentDetails->PaymentDetailsItem[] = $itemDetails;
            $this->_itemTotalValue += $totalDiscounts;
        } else {
            $discounts = Context::getContext()->cart->getCartRules();
            $order_total = Context::getContext()->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
            $order_total_with_reduction = $order_total;
            if (count($discounts) > 0) {
                foreach ($discounts as $discount) {
                    if (isset($discount['description']) && !empty($discount['description'])) {
                        $discount['description'] = Tools::substr(strip_tags($discount['description']), 0, 50).'...';
                    }
                    // It's needed to take a percentage of the order amount, taking into account the others discounts
                    if ((int)$discount['reduction_percent'] > 0) {
                        $discount['value_real'] = $order_total_with_reduction * ($discount['value_real'] / $order_total);
                    }

                    if ((int)$discount['free_shipping'] == false) {
                        $order_total_with_reduction -= $discount['value_real'];
                    }

                    $discount['value_real'] = -1 * $this->formatPrice($discount['value_real']);
                    $itemDetails = new PaymentDetailsItemType();
                    $itemDetails->Name = $discount['name'];
                    $itemDetails->Amount = new BasicAmountType($currency, $discount['value_real']);
                    $itemDetails->Quantity = 1;
                    $this->_paymentDetails->PaymentDetailsItem[] = $itemDetails;
                    $this->_itemTotalValue += $discount['value_real'];
                }
            }
        }
    }

    private function _getGiftWrapping($currency)
    {
        $wrapping_price = Context::getContext()->cart->gift ? Context::getContext()->cart->getGiftWrappingPrice() : 0;
        if ($wrapping_price > 0) {
            $wrapping_price = $this->formatPrice($wrapping_price);
            $itemDetails = new PaymentDetailsItemType();
            $itemDetails->Name = 'Gift wrapping';
            $itemDetails->Amount = new BasicAmountType($currency, $wrapping_price);
            $itemDetails->Quantity = 1;
            $this->_paymentDetails->PaymentDetailsItem[] = $itemDetails;
            $this->_itemTotalValue += $wrapping_price;
        }
    }

    /**
     * Set total payment values
     * @param $currency
     */
    private function _getPaymentValues($currency)
    {
        $context = Context::getContext();
        $cart = $context->cart;
        $shipping_cost_wt = $cart->getTotalShippingCost();
        $shipping = $this->formatPrice($shipping_cost_wt);
        $total = $this->formatPrice($cart->getOrderTotal(true, Cart::BOTH));
        $summary = $cart->getSummaryDetails();
        $subtotal = $this->formatPrice($summary['total_products']);
        $total_tax = number_format($this->_taxTotalValue, Paypal::getDecimal(), ".", '');
        // total shipping amount
        $shippingTotal = new BasicAmountType($currency, $shipping);
        //total handling amount if any
        $handlingTotal = new BasicAmountType($currency, number_format(0, Paypal::getDecimal(), ".", ''));
        //total insurance amount if any
        $insuranceTotal = new BasicAmountType($currency, number_format(0, Paypal::getDecimal(), ".", ''));

        if ($subtotal != $this->_itemTotalValue) {
            $subtotal = $this->_itemTotalValue;
        }
        //total
        $total_cart = $shippingTotal->value + $handlingTotal->value +
            $insuranceTotal->value +
            $this->_itemTotalValue + $this->_taxTotalValue;

        if ($total != $total_cart) {
            $total = $total_cart;
        }

        $this->_paymentDetails->ItemTotal = new BasicAmountType($currency, $subtotal);
        $this->_paymentDetails->TaxTotal = new BasicAmountType($currency, $total_tax);
        $this->_paymentDetails->OrderTotal = new BasicAmountType($currency, $total);

        $this->_paymentDetails->HandlingTotal = $handlingTotal;
        $this->_paymentDetails->InsuranceTotal = $insuranceTotal;
        $this->_paymentDetails->ShippingTotal = $shippingTotal;
    }

    private function _getShippingAddress()
    {
        $context = Context::getContext();
        $cart = $context->cart;
        $customer = $context->customer;
        $id_address = (int) $cart->id_address_delivery;
        if (($id_address == 0) && ($customer)) {
            $id_address = Address::getFirstCustomerAddressId($customer->id);
        }
        $address = new Address($id_address);
        $country = new Country((int) $address->id_country);
        $ship_addr_state = PayPal::getPaypalStateCode($address);

        $address_pp = new AddressType();
        $address_pp->CityName = $address->city;
        $address_pp->Name = $address->firstname.' '.$address->lastname;
        $address_pp->Street1 = $address->address1;
        $address_pp->StateOrProvince = $ship_addr_state;
        $address_pp->PostalCode = $address->postcode;
        $address_pp->Country = $country->iso_code;
        $address_pp->Phone = (empty($address->phone)) ? $address->phone_mobile : $address->phone;
        return $address_pp;
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

        // Get the payment Object by passing paymentId
        // payment id was previously stored in session in
        // CreatePaymentUsingPayPal.php
        $payment = Payment::get($this->getPaymentId(), $this->_getCredentialsInfo());

        // ### Payment Execute
        // PaymentExecution object includes information necessary
        // to execute a PayPal account payment.
        // The payer_id is added to the request query parameters
        // when the user is redirected from paypal back to your site
        $execution = new PaymentExecution();
        $execution->setPayerId($this->getPayerId());

        // ### Optional Changes to Amount
        // If you wish to update the amount that you wish to charge the customer,
        // based on the shipping address or any other reason, you could
        // do that by passing the transaction object with just `amount` field in it.
        $exec_payment = $payment->execute($execution, $this->_getCredentialsInfo());
        $this->setDetailsTransaction($exec_payment);
        $currency = $context->currency;
        $total = (float)$exec_payment->transactions[0]->amount->total;
        $paypal = Module::getInstanceByName($this->name);
        $order_state = $this->getOrderStatus();
        $paypal->validateOrder($cart->id,
            $order_state, $total,
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

    public function setDetailsTransaction($transaction)
    {
        $payment_info = $transaction->transactions[0];
        $transaction_detail = array(
            'method' => 'EC',
            'currency' => $payment_info->amount->currency,
            'payment_status' => $transaction->state,
            'payment_method' => $transaction->payer->payment_method,
            'id_payment' => pSQL($transaction->id),
            'payment_tool' => isset($transaction->payment_instruction)?$transaction->payment_instruction->instruction_type:'',
            'date_transaction' => $this->getDateTransaction($transaction)
        );

        if ($transaction->intent == 'authorize') {
            $transaction_detail['capture'] = true;
            $transaction_detail['transaction_id'] = pSQL($payment_info->related_resources[0]->authorization->id);
        } else {
            $transaction_detail['capture'] = false;
            $transaction_detail['transaction_id'] = pSQL($payment_info->related_resources[0]->sale->id);
        }

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
    public function refund($paypal_order)
    {
        $paypalRefund = new PaypalOrderRefund($paypal_order);
        $response = $paypalRefund->execute();
        return $response;
    }

    /**
     * @see AbstractMethodPaypal::partialRefund()
     */
    public function partialRefund($params)
    {
        $paypal_order = PaypalOrder::loadByOrderId($params['order']->id);
        $id_paypal_order = $paypal_order->id;
        $capture = PaypalCapture::loadByOrderPayPalId($id_paypal_order);
        $id_transaction = Validate::isLoadedObject($capture) ? $capture->id_capture : $paypal_order->id_transaction;
        $currency = $paypal_order->currency;
        $amount = 0;
        foreach ($params['productList'] as $product) {
            $amount += $product['amount'];
        }
        if (Tools::getValue('partialRefundShippingCost')) {
            $amount += Tools::getValue('partialRefundShippingCost');
        }
        $refundTransactionReqType = new RefundTransactionRequestType();
        $refundTransactionReqType->TransactionID = $id_transaction;
        $refundTransactionReqType->RefundType = 'Partial';
        $refundTransactionReqType->Amount =  new BasicAmountType($currency, number_format($amount, Paypal::getDecimal(), ".", ''));
        $refundTransactionReq = new RefundTransactionReq();
        $refundTransactionReq->RefundTransactionRequest = $refundTransactionReqType;

        $paypalService = new PayPalAPIInterfaceServiceService($this->_getCredentialsInfo($paypal_order->sandbox));
        $response = $paypalService->RefundTransaction($refundTransactionReq);

        if ($response instanceof PayPal\PayPalAPI\RefundTransactionResponseType) {
            if (isset($response->Errors)) {
                $result = array(
                    'status' => $response->Ack,
                    'error_code' => $response->Errors[0]->ErrorCode,
                    'error_message' => $response->Errors[0]->LongMessage,
                );
                if (Validate::isLoadedObject($capture) && $response->Errors[0]->ErrorCode == "10009") {
                    $result['already_refunded'] = true;
                }
            } else {
                $result =  array(
                    'success' => true,
                    'refund_id' => $response->RefundTransactionID,
                    'status' => $response->Ack,
                    'total_amount' => $response->TotalRefundedAmount->value,
                    'net_amount' => $response->NetRefundAmount->value,
                    'currency' => $response->TotalRefundedAmount->currencyID,
                );
            }
        }

        return $result;
    }

    /**
     * @see AbstractMethodPaypal::void()
     */
    public function void($orderPayPal)
    {
        $doVoidReqType = new DoVoidRequestType();
        $doVoidReqType->AuthorizationID = array('authorization_id'=>$orderPayPal->id_transaction);
        $doVoidReq = new DoVoidReq();
        $doVoidReq->DoVoidRequest = $doVoidReqType;

        $paypalService = new PayPalAPIInterfaceServiceService($this->_getCredentialsInfo($orderPayPal->sandbox));
        $response = $paypalService->DoVoid($doVoidReq);

        if ($response instanceof PayPal\PayPalAPI\DoVoidResponseType) {
            if (isset($response->Errors)) {
                $response =  array(
                    'error_code' => $response->Errors[0]->ErrorCode,
                    'error_message' => $response->Errors[0]->LongMessage,
                );
            } else {
                $response =  array(
                    'transaction_id' => $response->AuthorizationID,
                    'status' => $response->Ack,
                    'success' => true,
                    'date_transaction' => $this->getDateTransaction()
                );
            }
        }
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
     * @return \PayPal\PayPalAPI\GetExpressCheckoutDetailsResponseType
     * @throws Exception
     */
    public function getInfo()
    {
        $getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($this->payment_token);
        $getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;
        $paypalService = new PayPalAPIInterfaceServiceService($this->_getCredentialsInfo());
        $response = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
        if (isset($response->Errors)) {
            throw new PaypalException($response->Errors[0]->ErrorCode, $response->Errors[0]->ShortMessage, $response->Errors[0]->LongMessage);
        }
        return $response;
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
        return (bool)Configuration::get('PAYPAL_EC_EXPERIENCE');
    }

    public function checkCredentials($mode = null)
    {
        $experience_web = $this->createWebExperience();
        if ($experience_web) {
            Configuration::updateValue('PAYPAL_EC_EXPERIENCE', $experience_web->id);
        } else {
            Configuration::updateValue('PAYPAL_EC_EXPERIENCE', '');
        }
    }

    /**
     * Customize payment experience
     * @return bool|\PayPal\Api\CreateProfileResponse
     */
    public function createWebExperience()
    {
        $mode = Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';

        if (!Configuration::get('PAYPAL_EC_CLIENTID_' . $mode) || !Configuration::get('PAYPAL_EC_SECRET_' . $mode)) {
            return false;
        }

        $webProfile = $this->paypalApiManager->get($this->getBuilderClass('webProfile'))->build();

        // For Sample Purposes Only.
        try {
            // Use this call to create a profile.
            $createProfileResponse = $webProfile->create($this->_getCredentialsInfo());
        } catch (Exception $ex) {
            $module = Module::getInstanceByName('paypal');
            $this->errors[] = $module->l('An error occurred while creating your web experience. Check your credentials.', get_class($this));
            return false;
        }

        return $createProfileResponse;
    }

    public function getTplVars()
    {
        /*$urlParameters = array(
            'paypal_set_config' => 1,
            'method' => 'EC',
            'with_card' => 0,
            'modify' => 1
        );
        $context = Context::getContext();
        $countryDefault = new \Country((int)\Configuration::get('PS_COUNTRY_DEFAULT'), $context->language->id);
        $tpl_vars = array(
            'accountConfigured' => $this->isConfigured(),
            'urlOnboarding' => $context->link->getAdminLink('AdminPayPalSetup', true, null, $urlParameters),
            'country_iso' => $countryDefault->iso_code,
            'idShop' => Context::getContext()->shop->id,
        );

        if ((int)Configuration::get('PAYPAL_SANDBOX')) {
            $tpl_vars['paypal_api_user_name'] = Configuration::get('PAYPAL_USERNAME_SANDBOX');
            $tpl_vars['paypal_pswd'] = Configuration::get('PAYPAL_PSWD_SANDBOX');
            $tpl_vars['paypal_signature'] = Configuration::get('PAYPAL_SIGNATURE_SANDBOX');
            $tpl_vars['paypal_merchant_id'] = Configuration::get('PAYPAL_MERCHANT_ID_SANDBOX');
            $tpl_vars['mode'] = 'SANDBOX';
        } else {
            $tpl_vars['paypal_api_user_name'] = Configuration::get('PAYPAL_USERNAME_LIVE');
            $tpl_vars['paypal_pswd'] = Configuration::get('PAYPAL_PSWD_LIVE');
            $tpl_vars['paypal_signature'] = Configuration::get('PAYPAL_SIGNATURE_LIVE');
            $tpl_vars['paypal_merchant_id'] = Configuration::get('PAYPAL_MERCHANT_ID_LIVE');
            $tpl_vars['mode'] = 'LIVE';
        }

        return $tpl_vars;*/

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

    public function getExperienceProfileId()
    {
        return Configuration::get('PAYPAL_EC_EXPERIENCE');
    }

    public function getIntent()
    {
        return Configuration::get('PAYPAL_API_INTENT');
    }
}
