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
require_once _PS_MODULE_DIR_.'paypal/classes/PaypalCapture.php';
require_once _PS_MODULE_DIR_.'paypal/classes/PaypalOrder.php';

class OrderCaptureEntityTest extends TestCase
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

    public function testOrderAdd()
    {
        $order = new \PaypalOrder();
        $order->id_order = 1;
        $order->id_cart = 1;
        $order->id_transaction = "test";
        $order->id_payment = "test";
        $order->currency = 'eur';
        $order->total_paid = 10;
        $order->total_prestashop = 10;
        $order->method = 'EC';
        $order->payment_status = 'Completed';
        $order->payment_method = "instant";
        $order->add();
        $this->assertTrue((bool)$order->id);
    }

    public function testCaptureAdd()
    {
        $capture = new \PaypalCapture();
        $capture->id_paypal_order = 1;
        $capture->add();
        $this->assertTrue((bool)$capture->id);
    }

    public function testOrderRequests()
    {
        $order = \PaypalOrder::getIdOrderByTransactionId('test');
        $this->assertEquals(1, $order);

        $order = \PaypalOrder::getOrderById(1);
        $this->assertEquals(1, $order['id_order']);

        $order = \PaypalOrder::loadByOrderId(1);
        $this->assertEquals(1, $order->id_order);

        $order = \PaypalOrder::getPaypalBtOrdersIds();
        $this->assertTrue(is_array($order));
    }

    public function testCaptureRequests()
    {
        $capture = \PaypalCapture::getByOrderId(1);
        $this->assertEquals('test', $capture['id_transaction']);

        \PaypalCapture::updateCapture('test', 15, 'Completed', 1);

        $capture = \PaypalCapture::loadByOrderPayPalId(1);
        $this->assertEquals('test', $capture->id_capture);
    }
}
