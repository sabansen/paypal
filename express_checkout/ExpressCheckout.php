<?php

require_once _PS_MODULE_DIR_ . 'paypal/api/paypal_lib.php';

class ExpressCheckout
{
    const API_USER = 'PAYPAL_API_USER';

    const API_PASSWORD = 'PAYPAL_API_PASSWORD';

    const API_SIGNATURE = 'PAYPAL_API_SIGNATURE';

    const SANDBOX = 'PAYPAL_SANDBOX';

    const CONFIGURED = 'PAYPAL_EC_CONFIGURED';

    const API_MERCHANT_ID = 'PAYPAL_IN_CONTEXT_CHECKOUT_M_ID';

    /** @var PayPal*/
    protected $module;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('paypal');
    }

    /**
     * @param string $apiUser
     * @return self
     */
    public function setApiUser($apiUser)
    {
        Configuration::updateValue(self::API_USER, pSQL($apiUser));
        return $this;
    }

    /**
     * @return string
     */
    public function getApiUser()
    {
        return (string)Configuration::get(self::API_USER);
    }

    /**
     * @param string $apiPassword
     * @return self
     */
    public function setApiPassword($apiPassword)
    {
        Configuration::updateValue(self::API_PASSWORD, pSQL($apiPassword));
        return $this;
    }

    /**
     * @return string
     */
    public function getApiPassword()
    {
        return (string)Configuration::get(self::API_PASSWORD);
    }

    /**
     * @param string $apiSignature
     * @return self
     */
    public function setApiSignature($apiSignature)
    {
        Configuration::updateValue(self::API_SIGNATURE, pSQL($apiSignature));
        return $this;
    }

    /**
     * @return string
     */
    public function getApiSignature()
    {
        return (string)Configuration::get(self::API_SIGNATURE);
    }

    /**
     * @return void
     */
    public function checkCredentials()
    {
        Configuration::updateValue(self::CONFIGURED, (int)$this->isRightCredentials());
    }

    /**
     * @return bool
     */
    public function isRightCredentials()
    {
        $merchantId = $this->getMechantIdFromPaypal();

        if (empty($merchantId)) {
            return false;
        }

        if ($this->getApiMerchantId() && $this->getApiMerchantId() !== $merchantId) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getMechantIdFromPaypal()
    {
        $paypalApi = new PaypalLib();
        $response = $paypalApi->makeCall($this->module->getAPIURL(), $this->module->getAPIScript(), 'GetPalDetails', []);

        if (isset($response['ACK']) && $response['ACK'] == 'Success') {
            return (string)$response['PAL'];
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isSandbox()
    {
        return Configuration::get(self::SANDBOX) === 'sandbox';
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        if (false === Configuration::hasKey(self::CONFIGURED)) {
            $this->checkCredentials();
        }

        return (bool)Configuration::get(self::CONFIGURED);
    }

    /**
     * @param string $merchantId
     * @return self
     */
    public function setApiMerchantId($merchantId)
    {
        Configuration::updateValue(self::API_MERCHANT_ID, pSQL($merchantId));
        return $this;
    }

    /**
     * @return string
     */
    public function getApiMerchantId()
    {
        return (string)Configuration::get(self::API_MERCHANT_ID);
    }
}
