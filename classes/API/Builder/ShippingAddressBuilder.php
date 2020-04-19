<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\ShippingAddress;

class ShippingAddressBuilder extends BuilderAbstract
{
    /**
     * @return ShippingAddress
     */
    public function build()
    {
        $addressCustomer = new \Address($this->context->cart->id_address_delivery);
        $shippingAddress = new ShippingAddress();
        $shippingAddress->setCountryCode(\Country::getIsoById($addressCustomer->id_country));
        $shippingAddress->setCity($addressCustomer->city);
        $shippingAddress->setLine1($addressCustomer->address1);
        $shippingAddress->setPostalCode($addressCustomer->postcode);
        $shippingAddress->setRecipientName(implode(" ", array($addressCustomer->firstname, $addressCustomer->lastname)));

        if ((int)$addressCustomer->id_state) {
            $state = new \State($addressCustomer->id_state);
            $shippingAddress->setState($state->iso_code);
        }

        return $shippingAddress;
    }
}
