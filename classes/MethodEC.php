<?php
/**
 * 2007-2020 PayPal
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
 *  @author 2007-2020 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PaypalAddons\classes\API\PaypalApiManager;
use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;


/**
 * Class MethodEC.
 * @see https://developer.paypal.com/docs/classic/api/ NVP SOAP SDK
 * @see https://developer.paypal.com/docs/classic/api/nvpsoap-sdks/
 */
class MethodEC extends AbstractMethodPaypal
{
    /** @var boolean pay with card without pp account */
    public $credit_card;

    /** @var boolean shortcut payment from product or cart page*/
    public $short_cut;

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
        if (is_string($payemtId)) {
            $this->paymentId = $payemtId;
        }

        return $this;
    }

    public function getPaymentId()
    {
        return (string) $this->paymentId;
    }

    public function setShortCut($shortCut)
    {
        $this->short_cut = (bool) $shortCut;
        return $this;
    }

    public function getShortCut()
    {
        return (bool) $this->short_cut;
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
        if ($this->isSandbox()) {
            Configuration::updateValue('PAYPAL_EC_CLIENTID_SANDBOX', $params['clientId']);
            Configuration::updateValue('PAYPAL_EC_SECRET_SANDBOX', $params['secret']);
        } else {
            Configuration::updateValue('PAYPAL_EC_CLIENTID_LIVE', $params['clientId']);
            Configuration::updateValue('PAYPAL_EC_SECRET_LIVE', $params['secret']);
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
    public function confirmCapture($paypalOrder)
    {
        return $this->paypalApiManager->getCaptureAuthorizeRequest($paypalOrder)->execute();
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
                'es_cs_product_attribute' => Tools::getValue('id_product_attribute'),
                'paypalIdProduct' => Tools::getValue('id_product')
            ));
        }
        $context->smarty->assign('source_page', $page_source);
        return $context->smarty->fetch('module:paypal/views/templates/hook/shortcut.tpl');
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return (bool)Configuration::get('PAYPAL_CONNECTION_EC_CONFIGURED');
    }

    public function checkCredentials()
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
        $countryDefault = new Country((int)\Configuration::get('PS_COUNTRY_DEFAULT'), Context::getContext()->language->id);

        $tplVars['accountConfigured'] = $this->isConfigured();
        $tplVars['urlOnboarding'] = $this->getUrlOnboarding();
        $tplVars['country_iso'] = $countryDefault->iso_code;
        $tplVars['idShop'] = Context::getContext()->shop->id;
        $tplVars['mode'] = $this->isSandbox() ? 'SANDBOX' : 'LIVE';
        $tplVars['paypal_ec_clientid'] = $this->getClientId();
        $tplVars['paypal_ec_secret'] = $this->getSecret();

        \Media::addJsDef([
            'paypalOnboardingLib' => $this->isSandbox() ? 'https://www.sandbox.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js' : 'https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js'
        ]);

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

    public function getIntent()
    {
        return Configuration::get('PAYPAL_API_INTENT') == 'sale' ? 'CAPTURE' : 'AUTHORIZE';
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
            return (string) $this->secret;
        }

        if ($this->isSandbox()) {
            $secret = Configuration::get('PAYPAL_EC_SECRET_SANDBOX');
        } else {
            $secret = Configuration::get('PAYPAL_EC_SECRET_LIVE');
        }

        $this->secret = $secret;
        return (string) $this->secret;
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
        return (getenv('PLATEFORM') == 'PSREAD') ? 'PrestaShop_Cart_Ready_EC' : 'PRESTASHOP_Cart_SPB';
    }

    /** @return  string*/
    public function getLandingPage()
    {
        if ((int)$this->credit_card) {
            return 'BILLING';
        }

        return 'LOGIN';
    }
}
