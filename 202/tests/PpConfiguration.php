<?php
/**
 * 2011-2017 202 ecommerce
 *
 *  @author    202 ecommerce <contact@202-ecommerce.com>
 *  @copyright 202 ecommerce
 */

namespace TotTest\tests;

class PpConfiguration
{
    public function __construct() {
        \Configuration::updateValue('PAYPAL_METHOD', 'EC');
        \Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT', 1);
        \Configuration::updateValue('PAYPAL_USERNAME_SANDBOX', 'demo202_api1.demo.com');
        \Configuration::updateValue('PAYPAL_PSWD_SANDBOX', 'A2ZU2KJ9ALDE5X2A');
        \Configuration::updateValue('PAYPAL_SIGNATURE_SANDBOX', 'AS0U8jiKEDx2lJOGj2e8G6FIXni1AOagpEZULaFkDMF3S3rwZg8zH4Tg');
        \Configuration::updateValue('PAYPAL_SANDBOX_ACCESS', 1);
        \Configuration::updateValue('PAYPAL_MERCHANT_ID_SANDBOX', 'X35UTCLXGYU36');
        \Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT_IN_CONTEXT', 1);
        \Configuration::updateValue('PAYPAL_SANDBOX', 1);
    }
}
