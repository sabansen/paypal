<?php
/**
 * 2011-2017 202 ecommerce
 *
 *  @author    202 ecommerce <contact@202-ecommerce.com>
 *  @copyright 202 ecommerce
 */

namespace TotTest\tests;

class PpCustomer
{
    public function __construct($id) {
        $this->id = $id;
        $this->id_lang = 1;
        $this->id_currency = 1;
        $this->id_address = \Address::getFirstCustomerAddressId($this->id);

        $this->context = \Context::getContext();
        $this->context->customer = new \Customer($this->id);
        $this->context->currency = \Currency::getCurrencyInstance($this->id_currency);
        $this->context->cart = new \Cart();
        $this->context->cart->id_customer = $this->id;
        $this->context->cart->id_currency = $this->id_currency;
        $this->context->cart->id_lang = 1;
        $this->context->cart->id_address_delivery = $this->id_address;
        $this->context->cart->id_address_invoice = $this->id_address;
        $this->context->cart->save();  // on force l'id pour la carte


        $this->cart = $this->context->cart;
    }

    public static function load($id) {
        return new Customer($id);
    }

    public function selectPaymentMethod($name) {
        $paymentMethod = \Module::getInstanceByName($name);
        $this->selected_payment_method = $paymentMethod;
    }

    public function selectShippingMethod($id,$options=null) {
        $this->cart->id_carrier = $id;
    }

    /**
     * @param int $id_product
     * @param int $id_attribute
     * @param int $qty
     */
    public function add2cart($id_product, $id_attribute=null, $qty=1) {
        \Context::getContext()->cart->updateQty($qty, $id_product,$id_attribute, false);
    }


    public function buy($id_product,$id_attribute=null,$qty=1) {

        $this->add2cart($id_product,$id_attribute,$qty);

        $summary = $this->cart->getSummaryDetails(1,true);
        $total = (string) $summary['total_price'];
        $paymentMethod = $this->selected_payment_method;
        $valid = $paymentMethod->validateOrder($this->cart->id, _PS_OS_CHEQUE_, $total, $paymentMethod->displayName, NULL, array(), $this->cart->id_currency);
        return $valid ? new \Order($paymentMethod->currentOrder) : null;
    }

    public function addCartRule()
    {
        if (\Validate::isLoadedObject(new \CartRule(1))) {
            return;
        }
        $cart_rule                     = new \CartRule();
        $cart_rule->code               = 'unit_test';
        $cart_rule->id_customer        = (int)1;
        $cart_rule->reduction_currency = (int)1;
        $cart_rule->reduction_amount   = 5;
        $cart_rule->quantity           = 10;
        $cart_rule->highlight          = 1;
        $cart_rule->quantity_per_user  = 10;
        $cart_rule->reduction_tax      = 1;
        $cart_rule->date_from = date('Y-m-d H:i:s');
        $cart_rule->date_to   = date('Y-m-d H:i:s', strtotime($cart_rule->date_from.' +1 year'));
        $cart_rule->active                  = 1;
        $cart_rule->name[(int)1] = "unit test";
        $cart_rule->add();
    }
}
