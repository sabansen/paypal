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
require_once _PS_MODULE_DIR_.'paypal/classes/MethodEC.php';

use PHPUnit\Framework\TestCase;
use \PayPal\PayPalAPI\GetExpressCheckoutDetailsResponseType;
use PaypalAddons\classes\PaypalException;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class MethodECTest extends TestCase
{
    /* @var \MethodEC*/
    protected $method;

    public $moduleManagerBuilder;

    public $moduleManager;

    public $moduleNames;

    protected function setUp()
    {
        $this->method = new \MethodEC();
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
     * @dataProvider getDataForGetCredentialsInfo
     */
    public function testGetCredentialsInfo($mode)
    {
        $keys = array(
            'acct1.UserName',
            'acct1.Password',
            'acct1.Signature',
            'acct1.Signature',
            'mode',
            'log.LogEnabled'
        );
        $credentialInfo = $this->method->_getCredentialsInfo($mode);
        $this->assertIsArray($credentialInfo);

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $credentialInfo);
        }
    }

    /**
     * @dataProvider getDataForFormatPrice
     */
    public function testFormatPrice($price)
    {
        $priceFormated = $this->method->formatPrice($price);
        $this->assertIsString($priceFormated);
    }

    public function testGetDateTransaction()
    {
        $this->assertIsString($this->method->getDateTransaction());
    }

    public function testGetInfo()
    {
        try {
            $info = $this->method->getInfo();
            $this->assertInstanceOf(GetExpressCheckoutDetailsResponseType::class, $info);
        } catch (\Exception $e) {
            $this->assertInstanceOf(PaypalException::class, $e, $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @dataProvider getDataForGetLinkToTransaction
     */
    public function testGetLinkToTransaction($id_transaction, $sandbox)
    {
        $this->assertIsString($this->method->getLinkToTransaction($id_transaction, $sandbox));
    }

    public function testInit()
    {
        try {
            $urlAPI = $this->method->init();
            $this->assertIsString($urlAPI);
        } catch (\Exception $e) {
            $this->assertInstanceOf(PaypalException::class, $e, $e->getMessage());
        }
    }

    /**
     * @dataProvider getDataForGetCredentialsInfo
     */
    public function testIsConfigured()
    {
        $this->assertIsBool($this->method->isConfigured());
    }

    /**
     * @dataProvider getDataForRedirectToAPI
     */
    public function testRedirectToAPI($method)
    {
        $this->assertIsString($this->method->redirectToAPI($method));
    }

    /**
     * @dataProvider getDataForRenderExpressCheckoutShortCut
     */
    public function testRenderExpressCheckoutShortCut(&$context, $type, $page_source)
    {
        $this->assertIsString($this->method->renderExpressCheckoutShortCut($context, $type, $page_source));
    }


    public function testUseMobile()
    {
        $this->assertIsBool($this->method->useMobile());
    }

    public function getDataForGetCredentialsInfo()
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

    public function getDataForFormatPrice()
    {
        $data = array(
            array(1),
            array(023),
            array('123'),
            array(00),
            array(null),
        );
        return $data;
    }

    public function getDataForGetLinkToTransaction()
    {
        $data = array(
            array(1, 1),
            array(0, 0),
            array('string', 1),
            array(00, 'string'),
            array(null, null),
        );
        return $data;
    }

    public function getDataForRedirectToAPI()
    {
        $data = array(
            array(1),
            array(023),
            array('123'),
            array(00),
            array(null),
        );
        return $data;
    }

    public function getDataForRenderExpressCheckoutShortCut()
    {
        $context = \Context::getContext();
        $data = array(
            array($context, 'EC', 'product'),
            array($context, 'PPP', 'product'),
            array($context, 'BT', 'order'),
            array($context, 'EC', 'order'),
            array($context, 'PPP', 'order'),
            array($context, 'string', 'string'),
            array($context, null, null)
        );
        return $data;
    }

}
