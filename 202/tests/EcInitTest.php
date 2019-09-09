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

namespace PayPalTest;

use PayPalTest\TotTest;

require_once _PS_MODULE_DIR_.'paypal/vendor/autoload.php';
require_once _PS_MODULE_DIR_.'paypal/controllers/front/ecInit.php';
require_once _PS_MODULE_DIR_.'paypal/classes/MethodEC.php';
require_once _PS_MODULE_DIR_.'paypal/paypal.php';

class EcInitTest extends TotTest
{
    protected $methodMock;

    protected function setUp()
    {
        parent::setUp();
    }


    public function testPostProcessFailureRedirect()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willThrowException(new \Exception('test exception'));

        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values = array('getToken' => false);
        $ecInit->setMethod($methodMock);
        $ecInit->postProcess();
        $this->assertNotEmpty($ecInit->errors);
        $this->assertContains('controller=error', $ecInit->redirectUrl);
    }

    public function testPostProcessFailureJson()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willThrowException(new \Exception('test exception'));

        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values = array('getToken' => true);
        $ecInit->setMethod($methodMock);
        $ecInit->postProcess();
        $this->assertNotEmpty($ecInit->errors);
        $this->assertFalse($ecInit->jsonValues['success']);
        $this->assertArrayHasKey('redirect_link', $ecInit->jsonValues);
    }

    public function testPostProcessSuccessRedirect()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->method('init')->willReturn($methodMock->redirectToAPI('setExpressCheckout'));

        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values = array('getToken' => false);
        $ecInit->setMethod($methodMock);
        $ecInit->postProcess();
        $this->assertEmpty($ecInit->errors);
        $this->assertTrue(is_string($ecInit->redirectUrl));
    }

    public function testPostProcessSuccessJson()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();

        $methodMock->token = 'testToken';

        $ecInit = new \PaypalEcInitModuleFrontController();
        $ecInit->values = array('getToken' => true);
        $ecInit->setMethod($methodMock);
        $ecInit->postProcess();
        $this->assertEmpty($ecInit->errors);
        $this->assertTrue($ecInit->jsonValues['success']);
        $this->assertArrayHasKey('token', $ecInit->jsonValues);
    }
}
