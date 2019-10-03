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
 * @copyright 202-ecommerce
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PayPalTest;

require_once dirname(__FILE__) . '/TotTestCase.php';
require_once _PS_MODULE_DIR_.'paypal/vendor/autoload.php';
require_once _PS_MODULE_DIR_.'paypal/controllers/front/ScInit.php';
require_once _PS_MODULE_DIR_.'paypal/classes/MethodEC.php';

class ScInitTest extends \TotTestCase
{
    protected function setUp()
    {
        parent::setUp();
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
    public function testSuccessProductRedirect($values)
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();
        $methodMock->method('init')->willReturn($methodMock->redirectToAPI('setExpressCheckout'));

        $scInitMock = $this->getMockBuilder(\PaypalScInitModuleFrontController::class)
            ->setMethods(array('prepareProduct'))
            ->getMock();

        $scInitMock->values = $values;
        $scInitMock->setMethod($methodMock);
        $scInitMock->postProcess();
        $this->assertEmpty($scInitMock->errors);
        $this->assertTrue(is_string($scInitMock->redirectUrl));
    }

    /**
     * @dataProvider providerSuccessProductJson
     */
    public function testSuccessProductJson($values)
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();
        $methodMock->token = 'testToken';

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
    public function testFailureCartRedirect($values)
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
        ->setMethods(array('init'))
        ->getMock();
        $methodMock->method('init')->willThrowException(new \Exception('test exception'));

        $scInitMock = $this->getMockBuilder(\PaypalScInitModuleFrontController::class)
            ->setMethods(array('prepareProduct'))
            ->getMock();

        $scInitMock->values = $values;
        $scInitMock->setMethod($methodMock);
        $scInitMock->postProcess();
        $this->assertNotEmpty($scInitMock->errors);
        $this->assertContains('controller=error', $scInitMock->redirectUrl);
    }

    /**
     * @dataProvider providerFailureCartJson
     */
    public function testFailureCartJson($values)
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('init'))
            ->getMock();
        $methodMock->method('init')->willThrowException(new \Exception('test exception'));

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
        $data = array(
            'source_cart' => array(
                array('checkAvailability' => false, 'source_page' => 'cart', 'getToken' => false)
            ),
            'source_product' => array(
                array('checkAvailability' => false, 'source_page' => 'product', 'getToken' => false)
            )
        );

        return $data;
    }

    public function providerSuccessProductJson()
    {
        $data = array(
            'source_cart' => array(
                array('checkAvailability' => false, 'source_page' => 'cart', 'getToken' => true)
            ),
            'source_product' => array(
                array('checkAvailability' => false, 'source_page' => 'product', 'getToken' => true)
            )
        );

        return $data;
    }

    public function providerFailureCartRedirect()
    {
        $data = array(
            'source_cart' => array(
                array('checkAvailability' => false, 'source_page' => 'cart', 'getToken' => false)
            )
        );

        return $data;
    }

    public function providerFailureCartJson()
    {
        $data = array(
            'source_cart' => array(
                array('checkAvailability' => false, 'source_page' => 'cart', 'getToken' => true)
            )
        );

        return $data;
    }
}
