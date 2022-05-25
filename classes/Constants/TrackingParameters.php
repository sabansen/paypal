<?php
/**
 * 2007-2022 PayPal
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
 *  @author 2007-2022 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  @copyright PayPal
 */

namespace PaypalAddons\classes\Constants;

class TrackingParameters
{
    const CARRIER_MAP = 'PAYPAL_TRACKING_CARRIER_MAP';

    const CARRIER_OTHER = 'OTHER';

    public static function getGermanCarriers()
    {
        return [
            [
                'key' => 'ASENDIA_DE',
                'name' => 'Asendia Germany',
            ],
            [
                'key' => 'DELTEC_DE',
                'name' => 'Deltec Germany',
            ],
            [
                'key' => 'DEUTSCHE_DE',
                'name' => 'Deutsche',
            ],
            [
                'key' => 'DHL_DEUTSCHE_POST',
                'name' => 'DHL Deutsche Post',
            ],
            [
                'key' => 'DPD_DE',
                'name' => 'DPD Germany',
            ],
            [
                'key' => 'GLS_DE',
                'name' => 'GLS Germany',
            ],
            [
                'key' => 'HERMES_DE',
                'name' => 'Hermes Germany',
            ],
            [
                'key' => 'TNT_DE',
                'name' => 'TNT Germany',
            ],
            [
                'key' => 'OTHER',
                'name' => 'Other',
            ],
        ];
    }
}
