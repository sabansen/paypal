<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\Amount;
use PayPal\Api\Details;
use Symfony\Component\VarDumper\VarDumper;

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
        $subTotalExcl = $this->getOrderTotalWithoutShipping(false);
        $subTotalIncl = $this->getOrderTotalWithoutShipping(true);
        $subTotalTax = $subTotalIncl - $subTotalExcl;
        $totalOrder = $this->context->cart->getOrderTotal(true, \Cart::BOTH);

        $details->setShipping($this->method->formatPrice($shippingTotal))
            ->setTax($this->method->formatPrice($subTotalTax))
            ->setSubtotal($this->method->formatPrice($subTotalExcl));

        $amount->setCurrency($currency)
            ->setTotal($this->method->formatPrice($totalOrder))
            ->setDetails($details);

        return $amount;
    }

    /**
     * Use this method because on some version of PS Cart::getOrderTotal($tax, Cart::BOTH_WITHOUT_SHIPPING) doesn't work right
     *
     * @param $tax bool with/without tax
     * @return float
     */
    protected function getOrderTotalWithoutShipping($tax = true)
    {
        return $this->context->cart->getOrderTotal($tax, \Cart::ONLY_PRODUCTS) - $this->context->cart->getOrderTotal($tax, \Cart::ONLY_DISCOUNTS);
    }
}
