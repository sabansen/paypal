<?php

namespace PaypalAddons\classes\API\Request;

use PaypalAddons\classes\AbstractMethodPaypal;
use PayPalCheckoutSdk\Core\PayPalHttpClient;

abstract class RequestAbstract
{
    /** PayPalHttpClient*/
    protected $client;

    /** @var \Context*/
    protected $context;

    /** @var AbstractMethodPaypal*/
    protected $method;

    /** @var \Module*/
    protected $module;

    public function __construct(PayPalHttpClient $client, AbstractMethodPaypal $method)
    {
        $this->client = $client;
        $this->method = $method;
        $this->context = \Context::getContext();
        $this->module = \Module::getInstanceByName($method->name);
    }

    /**
     * @return array
     */
    protected function getHeaders()
    {
        $headers = [
            'PayPal-Partner-Attribution-Id' => $this->method->getPaypalPartnerId()
        ];

        return $headers;
    }

    abstract public function execute();
}
