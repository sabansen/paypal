<?php

namespace PaypalAddons\services\Order;

use Order;
use PayPal;

class RefundAmountCalculator
{
    /**
     * @param mixed $params
     *
     * @return float
     */
    public function calculate($params)
    {
        $amount = 0;

        if (empty($params['productList'])) {
            return $amount;
        }

        foreach ($params['productList'] as $product) {
            $amount += \Tools::ps_round($product['amount'], PayPal::getPrecision());
        }

        if (false == empty($params['partialRefundShippingCost'])) {
            $amount += $params['partialRefundShippingCost'];
        }

        // For prestashop version > 1.7.7
        if (false == empty($params['cancel_product'])) {
            $refundData = $params['cancel_product'];
            $amount += floatval(str_replace(',', '.', $refundData['shipping_amount']));
        }

        $amount -= $this->calculateDiscount($params);

        return $amount;
    }

    /**
     * @param mixed $params
     *
     * @return float
     */
    public function calculateDiscount($params)
    {
        // $params differs according PS version
        $amount = 0;

        if (false == empty($params['refund_voucher_off'])) {
            if (false == empty($params['order_discount_price'])) {
                return floatval($params['order_discount_price']);
            }
        }

        if (false == empty($params['cancel_product']['voucher_refund_type'])) {
            if ($params['cancel_product']['voucher_refund_type'] == 1) {
                if ($params['order'] instanceof Order) {
                    return (float) $params['order']->total_discounts_tax_incl;
                }
            }
        }

        return $amount;
    }
}
