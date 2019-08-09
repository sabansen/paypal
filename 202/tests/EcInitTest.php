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
require_once _PS_MODULE_DIR_.'paypal/controllers/front/ecInit.php';
require_once _PS_MODULE_DIR_.'paypal/classes/MethodEC.php';
require_once _PS_MODULE_DIR_.'paypal/paypal.php';

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class EcInitTest extends TestCase
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
     * @dataProvider providerPostProcessFailureRedirect
     */
    public function testPostProcessFailureRedirect($values, $methodMock)
    {
        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values = $values;
        $ecInit->setMethod($methodMock);
        $ecInit->postProcess();
        $this->assertNotEmpty($ecInit->errors);
        $this->assertStringContainsString('controller=error', $ecInit->redirectUrl);
    }

    /**
     * @dataProvider providerPostProcessFailureJson
     */
    public function testPostProcessFailureJson($values, $methodMock)
    {
        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values = $values;
        $ecInit->setMethod($methodMock);
        $ecInit->postProcess();
        $this->assertNotEmpty($ecInit->errors);
        $this->assertFalse($ecInit->jsonValues['success']);
        $this->assertArrayHasKey('redirect_link', $ecInit->jsonValues);
    }

    /**
     * @dataProvider providerPostProcessSuccessRedirect
     */
    public function testPostProcessSuccessRedirect($values, $methodMock)
    {
        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values = $values;
        $ecInit->setMethod($methodMock);
        $ecInit->postProcess();
        $this->assertEmpty($ecInit->errors);
        $this->assertStringStartsWith('https://www.sandbox.paypal.com', $ecInit->redirectUrl);
    }

    /**
     * @dataProvider providerPostProcessSuccessJson
     */
    public function testPostProcessSuccessJson($values, $methodMock)
    {
        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values = $values;
        $ecInit->setMethod($methodMock);
        $ecInit->postProcess();
        $this->assertEmpty($ecInit->errors);
        $this->assertTrue($ecInit->jsonValues['success']);
        $this->assertArrayHasKey('token', $ecInit->jsonValues);
    }

    public function providerPostProcessFailureRedirect()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willThrowException(new \Exception('test exception'));
        $data = array(
            array(
                array('getToken' => false),
                $methodMock
            )
        );

        return $data;
    }

    public function providerPostProcessFailureJson()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willThrowException(new \Exception('test exception'));
        $data = array(
            array(
                array('getToken' => true),
                $methodMock
            )
        );

        return $data;
    }

    public function providerPostProcessSuccessRedirect()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willReturn($methodMock->redirectToAPI('setExpressCheckout'));
        $data = array(
            array(
                array('getToken' => false),
                $methodMock
            )
        );

        return $data;
    }

    public function providerPostProcessSuccessJson()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->token = 'testToken';
        $data = array(
            array(
                array('getToken' => true),
                $methodMock
            )
        );

        return $data;
    }
}
