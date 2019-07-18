<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL 202 ecommence
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL 202 ecommence is strictly forbidden.
 * In order to obtain a license, please contact us: tech@202-ecommerce.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe 202 ecommence
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL 202 ecommence est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter 202-ecommerce <tech@202-ecommerce.com>
 * ...........................................................................
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 202-ecommerce
 * @license   Commercial license
 */

namespace TotTest\tests;

require_once dirname(__FILE__) . '/../../../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../../../init.php';
require_once _PS_MODULE_DIR_.'paypal/vendor/autoload.php';
require_once _PS_MODULE_DIR_.'paypal/classes/PaypalCustomer.php';

use PHPUnit\Framework\TestCase;

class PaypalCustomerTest extends TestCase
{

    /**
     * @dataProvider getDataForLoadCustomerByMethod
     */
    public function testLoadCustomerByMethod($id_customer, $method, $sandbox)
    {
        $customer = \PaypalCustomer::loadCustomerByMethod($id_customer, $method, $sandbox);
        $this->assertInstanceOf(\PaypalCustomer::class, $customer);
    }

    public function getDataForLoadCustomerByMethod()
    {
        $data = array(
            array(1, 'BT', 1),
            array(1, 'BT', 0),
            array(1, 'EC', 1),
            array(1, 'EC', 0)
        );

        return $data;
    }
}
