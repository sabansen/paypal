<?php


namespace PaypalAddons\classes\API\Builder;

use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

class PaymentBuilderEC extends PaymentBuilder
{
    public function build()
    {
        $payment = parent::build();
        $payment->setExperienceProfileId(\Configuration::get('PAYPAL_EC_EXPERIENCE'));

        return $payment;
    }
}