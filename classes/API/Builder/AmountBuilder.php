<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\Amount;
use PayPal\Api\Details;

class AmountBuilder extends BuilderAbstract
{
    /**
     * @return Amount
     */
    public function build()
    {
        $currency = $this->module->getPaymentCurrencyIso();
        $amount = new Amount();
        $details = new Details();

        $shippingTotal = $this->context->cart->getTotalShippingCost();
        $totalProductExl = $this->context->cart->getOrderTotal(false, \Cart::ONLY_PRODUCTS);
        $totalProductIncl = $this->context->cart->getOrderTotal(true, \Cart::ONLY_PRODUCTS);
        $totalProductTax = $totalProductIncl - $totalProductExl;
        $totalOrder = $this->context->cart->getOrderTotal(true, \Cart::BOTH);

        $details->setShipping($this->method->formatPrice($shippingTotal))
            ->setTax($this->method->formatPrice($totalProductTax))
            ->setSubtotal($this->method->formatPrice($totalProductExl));

        $amount->setCurrency($currency)
            ->setTotal($this->method->formatPrice($totalOrder))
            ->setDetails($details);

        return $amount;
    }
}
