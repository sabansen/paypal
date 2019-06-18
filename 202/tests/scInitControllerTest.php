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
require_once _PS_MODULE_DIR_.'paypal/controllers/front/ScInit.php';
require_once 'PpCustomer.php';
require_once 'PpConfiguration.php';

class scInitControllerTest extends TestCase
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
        $_GET['module'] = 'paypal';
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();

        $this->customer = new PpCustomer(1);
        new PpConfiguration();
        \ContextCore::getContext()->employee = new \Employee(1);
        $this->moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $this->moduleManager = $this->moduleManagerBuilder->build();

        $this->moduleNames = 'paypal';
    }

    public function testInstall()
    {
        $this->assertTrue((bool)$this->moduleManager->install($this->moduleNames), "Could not install $this->moduleNames");
    }

    public function testCheckAvailabilityProduct()
    {
        $scInit = new \PaypalScInitModuleFrontController();
        $scInit->values['source_page'] = 'product';
        $scInit->values['checkAvailability'] = true;
        $scInit->values['id_product'] = 1;
        $scInit->values['product_attribute'] = 1;
        $scInit->values['quantity'] = 1;
        $scInit->postProcess();
        $this->assertTrue($scInit->jsonValues['success']);
        $scInit->values['quantity'] = 100000;
        $scInit->postProcess();
        $this->assertFalse($scInit->jsonValues['success']);
    }

    public function testSuccessProductRedirect()
    {
        $scInit = new \PaypalScInitModuleFrontController();
        $scInit->values['checkAvailability'] = false;
        $scInit->values['short_cut'] = 1;
        $scInit->values['getToken'] = 0;
        $scInit->values['source_page'] = 'product';
        $scInit->values['id_product'] = 1;
        $scInit->values['product_attribute'] = 1;
        $scInit->values['quantity'] = 1;
        $scInit->values['combination'] = null;
        $scInit->postProcess();
        $this->assertEmpty($scInit->errors);
        $this->assertStringStartsWith('https://www.sandbox.paypal.com', $scInit->redirectUrl);
    }

    public function testSuccessProductJson()
    {
        $scInit = new \PaypalScInitModuleFrontController();
        $scInit->values['checkAvailability'] = false;
        $scInit->values['short_cut'] = 1;
        $scInit->values['getToken'] = 1;
        $scInit->values['source_page'] = 'product';
        $scInit->values['id_product'] = 1;
        $scInit->values['product_attribute'] = 1;
        $scInit->values['quantity'] = 1;
        $scInit->values['combination'] = null;
        $scInit->postProcess();
        $this->assertEmpty($scInit->errors);
        $this->assertTrue($scInit->jsonValues['success']);
        $this->assertArrayHasKey('token', $scInit->jsonValues);
    }

    public function testFailureCartRedirect()
    {
        $scInit = new \PaypalScInitModuleFrontController();
        $scInit->values['checkAvailability'] = false;
        $scInit->values['short_cut'] = 1;
        $scInit->values['getToken'] = 0;
        $scInit->values['source_page'] = 'cart';
        $scInit->values['combination'] = null;
        \Context::getContext()->cart->delete();
        $scInit->postProcess();
        $this->assertNotEmpty($scInit->errors);
        $this->assertContains('controller=error', $scInit->redirectUrl);
    }

    public function testFailureCartJson()
    {
        $scInit = new \PaypalScInitModuleFrontController();
        $scInit->values['checkAvailability'] = false;
        $scInit->values['short_cut'] = 1;
        $scInit->values['getToken'] = 1;
        $scInit->values['source_page'] = 'cart';
        $scInit->values['combination'] = null;
        \Context::getContext()->cart->delete();
        $scInit->postProcess();
        $this->assertNotEmpty($scInit->errors);
        $this->assertFalse($scInit->jsonValues['success']);
        $this->assertArrayHasKey('redirect_link', $scInit->jsonValues);
    }

    public function testCheckAvailabilityCart()
    {
        $scInit = new \PaypalScInitModuleFrontController();
        $scInit->values['source_page'] = 'cart';
        $scInit->values['checkAvailability'] = true;
        $this->customer->add2cart(1, 1, 1, false);
        $scInit->checkAvailability();
        $this->assertTrue($scInit->jsonValues['success']);
    }
}
