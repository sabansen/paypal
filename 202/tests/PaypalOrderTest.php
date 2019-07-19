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
require_once _PS_MODULE_DIR_.'paypal/classes/PaypalOrder.php';

use PHPUnit\Framework\TestCase;

class PaypalOrderTest extends TestCase
{
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
        $this->assertIsInt($id_order);
    }

    /**
     * @dataProvider getDataForGetOrderById
     */
    public function testGetOrderById($id_order)
    {
        $paypalOrder = \PaypalOrder::getOrderById($id_order);
        $this->assertIsArray($paypalOrder);
    }

    public function testGetPaypalBtOrdersIds()
    {
        $orderBtIds = \PaypalOrder::getPaypalBtOrdersIds();
        $this->assertIsArray($orderBtIds);
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
