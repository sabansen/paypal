<?php


namespace PaypalAddons\classes\API\Request;


use PaypalAddons\classes\AbstractMethodPaypal;
use PayPalCheckoutSdk\Core\PayPalHttpClient;

class PaypalOrderPartialRefundRequest extends PaypalOrderRefundRequest
{
    protected $amount;

    public function __construct(PayPalHttpClient $client, AbstractMethodPaypal $method, \PaypalOrder $paypalOrder, $amount)
    {
        parent::__construct($client, $method, $paypalOrder);
        $this->amount = $amount;
    }

    /**
     * @return array
     */
    protected function buildRequestBody()
    {
        $body = [
            'amount' => $this->getAmount()
        ];

        return $body;
    }

    /**
     * @return array
     */
    protected function getAmount()
    {
        $amount = [
            'currency_code' => $this->paypalOrder->currency,
            'value' => $this->amount
        ];

        return $amount;
    }
}
