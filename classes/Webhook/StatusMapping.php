<?php
/**
 * 2007-2021 PayPal
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2021 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\classes\Webhook;

use Configuration;

class StatusMapping
{
    /**
     * @return string $transactionStatus
     * @return int
     */
    public function getPsOrderStatusByTransaction($transactionStatus)
    {
        $orderStatus = 0;
        if ((int)Configuration::get('PAYPAL_CUSTOMIZE_ORDER_STATUS')) {
            switch ($transactionStatus) {
                case 'Completed':
                    $orderStatus = (int)Configuration::get('PAYPAL_OS_ACCEPTED_TWO');
                    break;
                case 'Refunded':
                    $orderStatus = (int)Configuration::get('PAYPAL_OS_REFUNDED_PAYPAL');
                    break;
                case 'Failed':
                    $orderStatus = (int)Configuration::get('PAYPAL_OS_VALIDATION_ERROR');
                    break;
                case 'Reversed':
                    $orderStatus = (int)Configuration::get('PAYPAL_OS_VALIDATION_ERROR');
                    break;
                case 'Denied':
                    $orderStatus = (int)Configuration::get('PAYPAL_OS_VALIDATION_ERROR');
                    break;
            }
        } else {
            switch ($transactionStatus) {
                case 'Completed':
                    $orderStatus = (int)Configuration::get('PS_OS_PAYMENT');
                    break;
                case 'Refunded':
                    $orderStatus = (int)Configuration::get('PS_OS_REFUND');
                    break;
                case 'Failed':
                    $orderStatus = (int)Configuration::get('PS_OS_CANCELED');
                    break;
                case 'Reversed':
                    $orderStatus = (int)Configuration::get('PS_OS_CANCELED');
                    break;
                case 'Denied':
                    $orderStatus = (int)Configuration::get('PS_OS_CANCELED');
                    break;
            }
        }

        return $orderStatus;
    }
}
