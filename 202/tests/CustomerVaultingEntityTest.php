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

use PHPUnit\Framework\TestCase;
use Tests\Unit\ContextMocker;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
require_once _PS_MODULE_DIR_.'paypal/classes/PaypalVaulting.php';
require_once _PS_MODULE_DIR_.'paypal/classes/PaypalCustomer.php';

class CustomerVaultingEntityTest extends TestCase
{

    public $moduleManagerBuilder;
    public $moduleManager;

    public $moduleNames;

    /**
     * @var object Customer create test customer and cart
     */
    public $customer;
    /**
     * @var ContextMocker
     */
    protected $contextMocker;


    protected function setUp()
    {
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();

        \ContextCore::getContext()->employee = new \Employee(1);
        $this->moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $this->moduleManager = $this->moduleManagerBuilder->build();

        $this->moduleNames = 'paypal';
    }

    public function testInstall()
    {
        $this->assertTrue((bool)$this->moduleManager->install($this->moduleNames), "Could not install $this->moduleNames");
    }

    public function testCustomerAdd()
    {
        $customer = new \PaypalCustomer();
        $customer->id_customer = 1;
        $customer->reference = "test";
        $customer->method = "BT";
        $customer->add();
        $this->assertTrue((bool)$customer->id);
    }

    public function testCustomerRequests()
    {
        $customer = \PaypalCustomer::loadCustomerByMethod(1, 'BT');
        $this->assertEquals(1, $customer->id_customer);
    }

    public function testVaultingAdd()
    {
        $vaulting = new \PaypalVaulting();
        $vaulting->id_paypal_customer = 1;
        $vaulting->token = "test";
        $vaulting->name = "test";
        $vaulting->info = '1111*';
        $vaulting->payment_tool = 'paypal';
        $vaulting->add();
        $this->assertTrue((bool)$vaulting->id);
    }

    public function testVaultingRequests()
    {
        $vaulting = \PaypalVaulting::vaultingExist('test', 1);
        $this->assertTrue($vaulting);

        $vaulting = \PaypalVaulting::getCustomerMethods(1, 'paypal');
        $this->assertTrue(is_array($vaulting));

        $vaulting = \PaypalVaulting::getCustomerGroupedMethods(1);
        $this->assertArrayHasKey('paypal', $vaulting);
    }
}
