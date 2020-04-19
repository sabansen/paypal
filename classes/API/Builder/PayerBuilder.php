<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;

class PayerBuilder extends BuilderAbstract
{
    /**
     * @return Payer
     */
    public function build()
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal")
            ->setPayerInfo($this->getPayerInfo());

        return $payer;
    }

    /**
     * @return PayerInfo
     */
    protected function getPayerInfo()
    {
        return $this->paypalApiManager->get($this->method->getBuilderClass('payerInfo'))->build();
    }
}
