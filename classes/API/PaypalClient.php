<?php


namespace PaypalAddons\classes\API;


use PaypalAddons\classes\AbstractMethodPaypal;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

class PaypalClient
{
    protected $method;

    public function __construct($method)
    {
        $this->method = $method;
    }

    public static function get(AbstractMethodPaypal $method)
    {
        if ($method->isSandbox()) {
            $environment = new SandboxEnvironment($method->getClientId(), $method->getSecret());
        } else {
            $environment = new ProductionEnvironment($method->getClientId(), $method->getSecret());
        }

        $client = new PayPalHttpClient($environment);
        return $client;
    }
}
