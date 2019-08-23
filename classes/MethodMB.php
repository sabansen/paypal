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
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;

/**
 * Class MethodPPP
 * @see https://paypal.github.io/PayPal-PHP-SDK/ REST API sdk doc
 * @see https://developer.paypal.com/docs/api/payments/v1/ REST API references
 */
class MethodMB extends AbstractMethodPaypal
{
    private $_items = array();

    private $_itemTotalValue = 0;

    private $_taxTotalValue = 0;

    /* @var $_itemList ItemList*/
    private $_itemList;

    /* @var $_amount Amount*/
    private $_amount;

    protected $payment_method = 'PayPal';

    public $errors = array();

    /** payment Object IDl*/
    public $paymentId;

    /** @var $payerId string*/
    protected $payerId;

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
        Configuration::updateValue('PAYPAL_MB_EXPERIENCE', '');
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

    /**
     * Customize payment experience
     * @return bool|\PayPal\Api\CreateProfileResponse
     */
    public function createWebExperience()
    {
        $brand_name = Configuration::get('PAYPAL_PPP_CONFIG_BRAND')?Configuration::get('PAYPAL_PPP_CONFIG_BRAND'):Configuration::get('PS_SHOP_NAME');
        $brand_logo = file_exists(_PS_MODULE_DIR_.'paypal/views/img/ppp_logo'.Context::getContext()->shop->id.'.png')?Context::getContext()->link->getBaseLink(Context::getContext()->shop->id, true).'modules/paypal/views/img/ppp_logo'.Context::getContext()->shop->id.'.png':Context::getContext()->link->getBaseLink().'img/'.Configuration::get('PS_LOGO');
        $flowConfig = new \PayPal\Api\FlowConfig();
        // When set to "commit", the buyer is shown an amount, and the button text will read "Pay Now" on the checkout page.
        $flowConfig->setUserAction("commit");
        // Defines the HTTP method to use to redirect the user to a return URL. A valid value is `GET` or `POST`.
        $flowConfig->setReturnUriHttpMethod("GET");
        // Parameters for style and presentation.
        $presentation = new \PayPal\Api\Presentation();
        // A URL to logo image. Allowed vaues: .gif, .jpg, or .png.
        $presentation->setLogoImage($brand_logo)
            //	A label that overrides the business name in the PayPal account on the PayPal pages.
            ->setBrandName($brand_name)
            //  Locale of pages displayed by PayPal payment experience.
            ->setLocaleCode(Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT')))
            // A label to use as hypertext for the return to merchant link.
            ->setReturnUrlLabel("Return");
        // Parameters for input fields customization.
        $inputFields = new \PayPal\Api\InputFields();
        // Enables the buyer to enter a note to the merchant on the PayPal page during checkout.
        $inputFields->setAllowNote(false)
            // Determines whether or not PayPal displays shipping address fields on the experience pages. Allowed values: 0, 1, or 2. When set to 0, PayPal displays the shipping address on the PayPal pages. When set to 1, PayPal does not display shipping address fields whatsoever. When set to 2, if you do not pass the shipping address, PayPal obtains it from the buyer’s account profile. For digital goods, this field is required, and you must set it to 1.
            ->setNoShipping(1)
            // Determines whether or not the PayPal pages should display the shipping address and not the shipping address on file with PayPal for this buyer. Displaying the PayPal street address on file does not allow the buyer to edit that address. Allowed values: 0 or 1. When set to 0, the PayPal pages should not display the shipping address. When set to 1, the PayPal pages should display the shipping address.
            ->setAddressOverride(1);
        // #### Payment Web experience profile resource
        $webProfile = new \PayPal\Api\WebProfile();
        // Name of the web experience profile. Required. Must be unique
        $webProfile->setName(Tools::substr(Configuration::get('PS_SHOP_NAME'), 0, 30) . uniqid())
            // Parameters for flow configuration.
            ->setFlowConfig($flowConfig)
            // Parameters for style and presentation.
            ->setPresentation($presentation)
            // Parameters for input field customization.
            ->setInputFields($inputFields)
            // Indicates whether the profile persists for three hours or permanently. Set to `false` to persist the profile permanently. Set to `true` to persist the profile for three hours.
            ->setTemporary(false);

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

    /**
     * @see AbstractMethodPaypal::init()
     */
    public function init()
    {
        if ($this->isConfigured() == false) {
            return '';
        }

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
        $payer->setPayerInfo($this->getPayerInfo());
        // ### Itemized information
        // (Optional) Lets you specify item wise information

        $this->_itemList = new ItemList();
        $this->_amount = new Amount();

        $this->_getPaymentDetails();

        // ### Transaction
        // A transaction defines the contract of a
        // payment - what is the payment for and who
        // is fulfilling it.


        $transaction = new Transaction();
        $transaction->setAmount($this->_amount)
            ->setItemList($this->_itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        // ### Redirect urls
        // Set the urls that the buyer must be redirected to after
        // payment approval/ cancellation.

        $redirectUrls = new RedirectUrls();
        $return_url = Context::getContext()->link->getModuleLink($this->name, 'pppValidation', array(), true);
        $redirectUrls->setReturnUrl($return_url)
            ->setCancelUrl(Context::getContext()->link->getPageLink('order', true));

        // ### Payment
        // A Payment Resource; create one using
        // the above types and intent set to 'sale'

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction))
            ->setExperienceProfileId(Configuration::get('PAYPAL_MB_EXPERIENCE'));

        // ### Create Payment
        // Create a payment by calling the 'create' method
        // passing it a valid apiContext.
        // The return object contains the state and the
        // url to which the buyer must be redirected to
        // for payment approval

        try {
            $payment->create($this->_getCredentialsInfo());
        } catch (Exception $e) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];
            $message = 'Error in ' . $backtrace['file'];
            $message .= ' (line ' . $backtrace['line'] . '); ';
            $message .= 'Message: ' . $e->getMessage() . ';';
            $message .= 'File: ' . $e->getFile() . '; ';
            $message .= 'Line: ' . $e->getLine();

            ProcessLoggerHandler::openLogger();
            ProcessLoggerHandler::logError($message);
            ProcessLoggerHandler::closeLogger();

            return '';
        }

        // ### Get redirect url
        // The API response provides the url that you must redirect
        // the buyer to. Retrieve the url from the $payment->getApprovalLink() method
        $this->setPaymentId($payment->id);
        return $payment->getApprovalLink();
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

        $discounts = Context::getContext()->cart->getCartRules();
        if (count($discounts) > 0) {
            throw new Exception('The total of the order do not match amount paid.');
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
        $order_state = Configuration::get('PS_OS_PAYMENT');
        $paypal->validateOrder($cart->id, $order_state, $total, $this->getPaymentMethod(), null, $this->getDetailsTransaction(), (int)$currency->id, false, $customer->secure_key);
    }

    public function setDetailsTransaction($transaction)
    {
        $payment_info = $transaction->transactions[0];

        $this->transaction_detail = array(
            'method' => 'MB',
            'currency' => $payment_info->amount->currency,
            'transaction_id' => pSQL($payment_info->related_resources[0]->sale->id),
            'payment_status' => $transaction->state,
            'payment_method' => $transaction->payer->payment_method,
            'id_payment' => pSQL($transaction->id),
            'capture' => false,
            'payment_tool' => isset($transaction->payment_instruction)?$transaction->payment_instruction->instruction_type:'',
            'date_transaction' => $this->getDateTransaction($transaction)
        );
    }

    public function getDateTransaction($transaction)
    {
        $dateServer = DateTime::createFromFormat(DateTime::ISO8601, $transaction->update_time);
        return $dateServer->format('Y-m-d H:i:s');
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
        return (bool)Configuration::get('PAYPAL_MB_EXPERIENCE');
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
        $experience_web = $this->createWebExperience();
        if ($experience_web) {
            Configuration::updateValue('PAYPAL_MB_EXPERIENCE', $experience_web->id);
        } else {
            Configuration::updateValue('PAYPAL_MB_EXPERIENCE', '');
        }
    }

    /**
     * Assign form data for Paypal Plus payment option
     * @return boolean
     */
    public function assignJSvarsPaypalMB()
    {
        $context = Context::getContext();
        Media::addJsDef(array(
            'ajaxPatch' => $context->link->getModuleLink('paypal', 'mbValidation', array(), true)
        ));
    }

    private function _getPaymentDetails()
    {
        $paypal = Module::getInstanceByName($this->name);
        $currency = $paypal->getPaymentCurrencyIso();
        $this->_getProductsList($currency);
        //$this->_getDiscountsList($items, $total_products);
        $this->_getGiftWrapping($currency);
        $this->_getPaymentValues($currency);
    }

    private function _getProductsList($currency)
    {
        $products = Context::getContext()->cart->getProducts();
        foreach ($products as $product) {
            $product['product_tax'] = $this->formatPrice($product['price_wt']) - $this->formatPrice($product['price']);
            $item = new Item();
            $item->setName(Tools::substr($product['name'], 0, 126))
                ->setCurrency($currency)
                ->setDescription(isset($product['attributes']) ? $product['attributes'] : '')
                ->setQuantity($product['quantity'])
                ->setSku($product['id_product']) // Similar to `item_number` in Classic API
                ->setPrice($this->formatPrice($product['price']));

            $this->_items[] = $item;
            $this->_itemTotalValue += $this->formatPrice($product['price']) * $product['quantity'];
            $this->_taxTotalValue += $product['product_tax'] * $product['quantity'];
        }
    }

    private function _getGiftWrapping($currency)
    {
        $wrapping_price = Context::getContext()->cart->gift ? Context::getContext()->cart->getGiftWrappingPrice() : 0;
        if ($wrapping_price > 0) {
            $wrapping_price = $this->formatPrice($wrapping_price);
            $item = new Item();
            $item->setName('Gift wrapping')
                ->setCurrency($currency)
                ->setQuantity(1)
                ->setSku('wrapping') // Similar to `item_number` in Classic API
                ->setPrice($wrapping_price);
            $this->_items[] = $item;
            $this->_itemTotalValue += $wrapping_price;
        }
    }

    private function _getPaymentValues($currency)
    {
        $this->_itemList->setItems($this->_items);
        $context = Context::getContext();
        $cart = $context->cart;
        $shipping_cost_wt = $cart->getTotalShippingCost();
        $shipping = $this->formatPrice($shipping_cost_wt);
        $total = $this->formatPrice($cart->getOrderTotal(true, Cart::BOTH));
        $summary = $cart->getSummaryDetails();
        $subtotal = $this->formatPrice($summary['total_products']);
        $total_tax = number_format($this->_taxTotalValue, Paypal::getDecimal(), ".", '');
        // total shipping amount
        $shippingTotal = $shipping;

        if ($subtotal != $this->_itemTotalValue) {
            $subtotal = $this->_itemTotalValue;
        }
        //total
        $total_cart = $shippingTotal + $this->_itemTotalValue + $this->_taxTotalValue;

        if ($total != $total_cart) {
            $total = $total_cart;
        }

        // ### Additional payment details
        // Use this optional field to set additional
        // payment information such as tax, shipping
        // charges etc.
        $details = new Details();
        $details->setShipping($shippingTotal)
            ->setTax($total_tax)
            ->setSubtotal($subtotal);
        // ### Amount
        // Lets you specify a payment amount.
        // You can also specify additional details
        // such as shipping, tax.
        $this->_amount->setCurrency($currency)
            ->setTotal($total)
            ->setDetails($details);
    }

    protected function getPayerInfo()
    {
        $customer = Context::getContext()->customer;
        $addressCustomer = new Address(Context::getContext()->cart->id_address_delivery);
        $countryCustomer = new Country($addressCustomer->id_country);
        $payerInfo = new PayerInfo();
        $payerInfo->setEmail($customer->email);
        $payerInfo->setFirstName($customer->firstname);
        $payerInfo->setLastName($customer->lastname);
        $payerInfo->setPayerId(Configuration::get('PAYPAL_MB_EXPERIENCE'));

        if ($countryCustomer->iso_code == 'BR') {
            $payerInfo->setTaxId('');
        } else {
            $payerInfo->setTaxId('');
        }

        return $payerInfo;
    }

    public function getPaymentInfo()
    {
        $context = Context::getContext();

        try {
            $approval_url = $this->init();
            $context->cookie->__set('paypal_plus_mb_payment', $this->paymentId);
        } catch (Exception $e) {
            return false;
        }

        $paymentInfo = array(
            'approvalUrlPPP' => $approval_url,
            'paymentId' => $this->getPaymentId(),
            'paypalMode' => Configuration::get('PAYPAL_SANDBOX')  ? 'sandbox' : 'live',
            'payerInfo' => $this->getPayerInfo()->toArray(),
        );

        return $paymentInfo;
    }

    public function setPaymentId($payemtId)
    {
        $this->paymentId = $payemtId;
    }

    public function getPaymentId()
    {
        return $this->paymentId;
    }

    public function setPayerId($payerId)
    {
        $this->payerId = $payerId;
    }

    public function getPayerId()
    {
        return $this->payerId;
    }
}
