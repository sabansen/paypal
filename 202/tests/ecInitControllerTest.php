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
require_once _PS_MODULE_DIR_.'paypal/controllers/front/ecInit.php';
require_once 'PpCustomer.php';
require_once 'PpConfiguration.php';

class ecInitControllerTest extends TestCase
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

    public function testPostProcessFailureRedirect()
    {
        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values['getToken'] = 0;
        $ecInit->values['credit_card'] = 0;
        $ecInit->postProcess();
        $this->assertNotEmpty($ecInit->errors);
        $this->assertContains('controller=error', $ecInit->redirectUrl);
    }

    public function testPostProcessFailureJson()
    {
        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values['getToken'] = 1;
        $ecInit->values['credit_card'] = 0;
        $ecInit->postProcess();
        $this->assertNotEmpty($ecInit->errors);
        $this->assertFalse($ecInit->jsonValues['success']);
        $this->assertArrayHasKey('redirect_link', $ecInit->jsonValues);
    }

    public function testPostProcessSuccessRedirect()
    {
        $this->customer->add2cart(1, 1, 1, false);
        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values['getToken'] = 0;
        $ecInit->values['credit_card'] = 0;
        $ecInit->postProcess();
        $this->assertEmpty($ecInit->errors);
        $this->assertStringStartsWith('https://www.sandbox.paypal.com', $ecInit->redirectUrl);
    }

    public function testPostProcessSuccessJson()
    {
        $this->customer->add2cart(1, 1,1, false);
        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values['getToken'] = 1;
        $ecInit->values['credit_card'] = 0;
        $ecInit->postProcess();
        $this->assertEmpty($ecInit->errors);
        $this->assertTrue($ecInit->jsonValues['success']);
        $this->assertArrayHasKey('token', $ecInit->jsonValues);
    }

    public function testSuccessRedirectWithDiscount()
    {
        $this->customer->add2cart(1, 1, 1, false);
        $this->customer->addCartRule();
        \Context::getContext()->cart->addCartRule(1);

        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values['getToken'] = 0;
        $ecInit->values['credit_card'] = 0;
        $ecInit->postProcess();
        $this->assertEmpty($ecInit->errors);
        $this->assertStringStartsWith('https://www.sandbox.paypal.com', $ecInit->redirectUrl);
    }
}
