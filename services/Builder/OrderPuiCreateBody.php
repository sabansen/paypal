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
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\services\Builder;

use Address;
use Configuration;
use Context;
use Country;
use Customer;
use Module;
use Paypal;
use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\Constants\PaypalConfigurations;
use PaypalAddons\classes\PUI\DataUserForm;
use PaypalAddons\services\FormatterPaypal;

class OrderPuiCreateBody extends OrderCreateBody
{
    public function build()
    {
        $body = parent::build();
        $dataUser = $this->getDataUser();
        $billingAddress = new Address($this->context->cart->id_address_invoice);
        $country = new Country($billingAddress->id_country);

        $body['payment_source'] = [
            'pay_upon_invoice' => [
                'name' => [
                    'given_name' => $dataUser->getFirstName(),
                    'surname' => $dataUser->getLastName()
                ],
                'birth_date' => $dataUser->getBirth(),
                'email' => $dataUser->getEmail(),
                'billing_address' => $this->getAddress($billingAddress),
                'phone' => [
                    'national_number' => $dataUser->getPhone(),
                    'country_code' => $country->call_prefix
                ],
                'experience_context' => [
                    'locale' => 'en-DE',
                    'customer_service_instructions' => $this->getCustomerServiceInstructions()
                ]
            ]
        ];

        return $body;
    }

    protected function getCustomerServiceInstructions()
    {
        $instructions = Configuration::get(PaypalConfigurations::PUI_CUSTOMER_SERVICE_INSTRUCTIONS);

        if (false == $instructions) {
            $instructions = 'Instructions are not found';
        }

        return [$instructions];
    }

    /** @return DataUserForm*/
    protected function getDataUser()
    {
        return $this->method->getPuiDataUser();
    }
}
