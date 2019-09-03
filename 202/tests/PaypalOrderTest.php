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
require_once _PS_MODULE_DIR_.'paypal/classes/PaypalOrder.php';

class PaypalOrderTest extends TotTest
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @dataProvider getDataForLoadByOrderId
     */
    public function testLoadByOrderId($id_order)
    {
        $paypalOrder = \PaypalOrder::loadByOrderId($id_order);
        $this->assertInstanceOf(\PaypalOrder::class, $paypalOrder);
    }

    /**
     * @dataProvider getDataForGetIdOrderByTransactionId
     */
    public function testGetIdOrderByTransactionId($id_transaction)
    {
        $id_order = \PaypalOrder::getIdOrderByTransactionId($id_transaction);
        $this->assertTrue(is_int($id_order));
    }

    /**
     * @dataProvider getDataForGetOrderById
     */
    public function testGetOrderById($id_order)
    {
        $paypalOrder = \PaypalOrder::getOrderById($id_order);
        $this->assertTrue(is_array($paypalOrder));
    }

    public function testGetPaypalBtOrdersIds()
    {
        $orderBtIds = \PaypalOrder::getPaypalBtOrdersIds();
        $this->assertTrue(is_array($orderBtIds));
    }

    public function getDataForLoadByOrderId()
    {
        $data = array(
            array(1),
            array(0),
            array('string'),
            array(00),
            array(null),
        );
        return $data;
    }

    public function getDataForGetIdOrderByTransactionId()
    {
        $data = $this->getDataForLoadByOrderId();
        return $data;
    }

    public function getDataForGetOrderById()
    {
        $data = $this->getDataForLoadByOrderId();
        return $data;
    }

}
