<?php


namespace PaypalAddons\classes\API;


use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Request\PaypalAccessTokenRequest;
use PaypalAddons\classes\API\Request\PaypalAuthorizationVoidRequest;
use PaypalAddons\classes\API\Request\PaypalCaptureAuthorizeRequest;
use PaypalAddons\classes\API\Request\PaypalOrderCaptureRequest;
use PaypalAddons\classes\API\Request\PaypalOrderCreateRequest;
use PaypalAddons\classes\API\Request\PaypalOrderAuthorizeRequest;
use PaypalAddons\classes\API\Request\PaypalOrderPartialRefundRequest;
use PaypalAddons\classes\API\Request\PaypalOrderRefundRequest;

class PaypalApiManager
{
    /** @var AbstractMethodPaypal*/
    protected $method;

    /** @var PaypalClient*/
    protected $client;

    public function __construct(AbstractMethodPaypal $method)
    {
        $this->method = $method;
        $this->client = PaypalClient::get($method);
    }

    public function getAccessTokenRequest()
    {
        return new PaypalAccessTokenRequest($this->client, $this->method);
    }

    public function getOrderRequest()
    {
        return new PaypalOrderCreateRequest($this->client, $this->method);
    }

    public function getOrderCaptureRequest($idPayment)
    {
        return new PaypalOrderCaptureRequest($this->client, $this->method, $idPayment);
    }

    public function getOrderAuthorizeRequest($idPayment)
    {
        return new PaypalOrderAuthorizeRequest($this->client, $this->method, $idPayment);
    }

    public function getOrderRefundRequest(\PaypalOrder $paypalOrder)
    {
        return new PaypalOrderRefundRequest($this->client, $this->method, $paypalOrder);
    }

    public function getOrderPartialRefundRequest(\PaypalOrder $paypalOrder, $amount)
    {
        return new PaypalOrderPartialRefundRequest($this->client, $this->method, $paypalOrder, $amount);
    }

    public function getAuthorizationVoidRequest(\PaypalOrder $orderPayPal)
    {
        return new PaypalAuthorizationVoidRequest($this->client, $this->method, $orderPayPal);
    }

    public function getCaptureAuthorizeRequest(\PaypalOrder $paypalOrder)
    {
        return new PaypalCaptureAuthorizeRequest($this->client, $this->method, $paypalOrder);
    }
}
