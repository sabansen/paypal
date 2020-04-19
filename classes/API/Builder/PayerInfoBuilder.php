<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\PayerInfo;
use PayPal\Api\ShippingAddress;

class PayerInfoBuilder extends BuilderAbstract
{
    /**
     * @return PayerInfo
     */
    public function build()
    {
        $payerInfo = new PayerInfo();
        $payerInfo->setEmail($this->context->customer->email)
            ->setFirstName($this->context->customer->firstname)
            ->setLastName($this->context->customer->lastname)
            ->setShippingAddress($this->getShippingAddress());

        return $payerInfo;
    }

    /**
     * @return ShippingAddress
     */
    protected function getShippingAddress()
    {
        return $this->paypalApiManager->get($this->method->getBuilderClass('shippingAddress'))->build();
    }
}
