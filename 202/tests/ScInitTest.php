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

$pathConfig = dirname(__FILE__) . '/../../../../config/config.inc.php';
$pathInit = dirname(__FILE__) . '/../../../../init.php';
if (file_exists($pathConfig)) {
    require_once $pathConfig;
}
if (file_exists($pathInit)) {
    require_once $pathInit;
}
require_once _PS_MODULE_DIR_.'paypal/vendor/autoload.php';
require_once _PS_MODULE_DIR_.'paypal/controllers/front/ScInit.php';
require_once _PS_MODULE_DIR_.'paypal/classes/MethodEC.php';

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class ScInitTest extends TestCase
{
    public $moduleManagerBuilder;

    public $moduleManager;

    public $moduleNames;

    protected function setUp()
    {
        $_GET['module'] = 'paypal';

        $this->moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $this->moduleManager = $this->moduleManagerBuilder->build();
        $this->moduleNames = 'paypal';
    }

    public function testInstall()
    {
        $employees = \Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_);
        $contextTest = \Context::getContext();
        $contextTest->employee = new \Employee((int)$employees[0]['id_employee']);
        $contextTest->cookie->update();
        \Context::setInstanceForTesting($contextTest);
        $this->assertTrue((bool)$this->moduleManager->install($this->moduleNames), "Could not install $this->moduleNames");
    }


    /**
     * @dataProvider providerCheckAvailability
     */
    public function testCheckAvailability($values, $methodsCart, $return)
    {
        $cartMock = $this->createMock(\Cart::class);

        if (empty($methodsCart) == false) {
            foreach ($methodsCart as $methodCart) {
                $cartMock->method($methodCart['methodName'])->willReturn($methodCart['methodReturn']);
            }
        }

        $contextTest = \Context::getContext();
        $contextTest->cart = $cartMock;
        \Context::setInstanceForTesting($contextTest);
        $scInit = new \PaypalScInitModuleFrontController();
        $scInit->values = $values;
        $scInit->checkAvailability();
        $this->assertEquals($return, $scInit->jsonValues['success']);
    }

    /**
     * @dataProvider providerSuccessProductRedirect
     */
    public function testSuccessProductRedirect($values, $methodMock)
    {
        $scInitMock = $this->getMockBuilder(\PaypalScInitModuleFrontController::class)
            ->setMethods(array('prepareProduct'))
            ->getMock();

        $scInitMock->values = $values;
        $scInitMock->setMethod($methodMock);
        $scInitMock->postProcess();
        $this->assertEmpty($scInitMock->errors);
        $this->assertStringStartsWith('https://www.sandbox.paypal.com', $scInitMock->redirectUrl);
    }

    /**
     * @dataProvider providerSuccessProductJson
     */
    public function testSuccessProductJson($values, $methodMock)
    {
        $scInitMock = $this->getMockBuilder(\PaypalScInitModuleFrontController::class)
            ->setMethods(array('prepareProduct'))
            ->getMock();

        $scInitMock->values = $values;
        $scInitMock->setMethod($methodMock);
        $scInitMock->postProcess();
        $this->assertEmpty($scInitMock->errors);
        $this->assertTrue($scInitMock->jsonValues['success']);
        $this->assertArrayHasKey('token', $scInitMock->jsonValues);
    }

    /**
     * @dataProvider providerFailureCartRedirect
     */
    public function testFailureCartRedirect($values, $methodMock)
    {
        $scInitMock = $this->getMockBuilder(\PaypalScInitModuleFrontController::class)
            ->setMethods(array('prepareProduct'))
            ->getMock();

        $scInitMock->values = $values;
        $scInitMock->setMethod($methodMock);
        $scInitMock->postProcess();
        $this->assertNotEmpty($scInitMock->errors);
        $this->assertStringContainsString('controller=error', $scInitMock->redirectUrl);
    }

    /**
     * @dataProvider providerFailureCartJson
     */
    public function testFailureCartJson($values, $methodMock)
    {
        $scInitMock = $this->getMockBuilder(\PaypalScInitModuleFrontController::class)
            ->setMethods(array('prepareProduct'))
            ->getMock();

        $scInitMock->values = $values;
        $scInitMock->setMethod($methodMock);
        $scInitMock->postProcess();
        $this->assertNotEmpty($scInitMock->errors);
        $this->assertFalse($scInitMock->jsonValues['success']);
        $this->assertArrayHasKey('redirect_link', $scInitMock->jsonValues);
    }

    public function providerCheckAvailability()
    {
        $dataProvider = array(
            'cart_1' => array(
                array('source_page' => 'cart'),
                array(
                    array('methodName' => 'checkQuantities', 'methodReturn' => false),
                    array('methodName' => 'hasProducts', 'methodReturn' => false),
                ),
                false
            ),
            'cart_2' => array(
                array('source_page' => 'cart'),
                array(
                    array('methodName' => 'checkQuantities', 'methodReturn' => true),
                    array('methodName' => 'hasProducts', 'methodReturn' => false),
                ),
                false
            ),
            'cart_3' => array(
                array('source_page' => 'cart'),
                array(
                    array('methodName' => 'checkQuantities', 'methodReturn' => false),
                    array('methodName' => 'hasProducts', 'methodReturn' => true),
                ),
                false
            ),
            'cart_4' => array(
                array('source_page' => 'cart'),
                array(
                    array('methodName' => 'checkQuantities', 'methodReturn' => true),
                    array('methodName' => 'hasProducts', 'methodReturn' => true),
                ),
                true
            ),
            'product_1' => array(
                array('source_page' => 'product', 'id_product' => 1, 'id_product_attribute' => 0, 'quantity' => 999999999, 'product_attribute' => 0),
                array(),
                false
            ),
            'product_2' => array(
                array('source_page' => 'product', 'id_product' => 1, 'id_product_attribute' => 0, 'quantity' => 1, 'product_attribute' => 0),
                array(),
                true
            ),
        );

        return $dataProvider;
    }

    public function providerSuccessProductRedirect()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willReturn($methodMock->redirectToAPI('setExpressCheckout'));
        $data = array(
            'source_cart' => array(
                array('checkAvailability' => false, 'source_page' => 'cart', 'getToken' => false),
                $methodMock
            ),
            'source_product' => array(
                array('checkAvailability' => false, 'source_page' => 'product', 'getToken' => false),
                $methodMock
            )
        );

        return $data;
    }

    public function providerSuccessProductJson()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->token = 'testToken';
        $data = array(
            'source_cart' => array(
                array('checkAvailability' => false, 'source_page' => 'cart', 'getToken' => true),
                $methodMock
            ),
            'source_product' => array(
                array('checkAvailability' => false, 'source_page' => 'product', 'getToken' => true),
                $methodMock
            )
        );

        return $data;
    }

    public function providerFailureCartRedirect()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willThrowException(new \Exception('test exception'));
        $data = array(
            'source_cart' => array(
                array('checkAvailability' => false, 'source_page' => 'cart', 'getToken' => false),
                $methodMock
            )
        );

        return $data;
    }

    public function providerFailureCartJson()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willThrowException(new \Exception('test exception'));
        $data = array(
            'source_cart' => array(
                array('checkAvailability' => false, 'source_page' => 'cart', 'getToken' => true),
                $methodMock
            )
        );

        return $data;
    }
}
