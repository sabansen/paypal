<?php
/**
 * 2007-2020 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2019 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PayPalTest;

use \PayPal\PayPalAPI\GetExpressCheckoutDetailsResponseType;
use PaypalAddons\classes\PaypalException;

require_once dirname(__FILE__) . '/TotTestCase.php';
require_once _PS_MODULE_DIR_.'paypal/vendor/autoload.php';
require_once _PS_MODULE_DIR_.'paypal/classes/MethodEC.php';

class MethodECTest extends \TotTestCase
{
    /* @var \MethodEC*/
    protected $method;

    protected function setUp()
    {
        parent::setUp();

        $this->method = new \MethodEC();
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
        $this->assertTrue(is_array($credentialInfo));

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
        $this->assertTrue(is_string($priceFormated));
    }

    public function testGetDateTransaction()
    {
        $this->assertTrue(is_string($this->method->getDateTransaction()));
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
    public function testGetLinkToTransaction($log)
    {
        $this->assertTrue(is_string($this->method->getLinkToTransaction($log)));
    }

    public function testInit()
    {
        try {
            $urlAPI = $this->method->init();
            $this->assertTrue(is_string($urlAPI));
        } catch (\Exception $e) {
            $this->assertInstanceOf(PaypalException::class, $e, $e->getMessage());
        }
    }

    /**
     * @dataProvider getDataForGetCredentialsInfo
     */
    public function testIsConfigured()
    {
        $this->assertTrue(is_bool($this->method->isConfigured()));
    }

    /**
     * @dataProvider getDataForRedirectToAPI
     */
    public function testRedirectToAPI($method)
    {
        $this->assertTrue(is_string($this->method->redirectToAPI($method)));
    }

    /**
     * @dataProvider getDataForRenderExpressCheckoutShortCut
     */
    public function testRenderExpressCheckoutShortCut($context, $type, $page_source)
    {
        $this->assertTrue(is_string($this->method->renderExpressCheckoutShortCut($context, $type, $page_source)));
    }


    public function testUseMobile()
    {
        $this->assertTrue(is_bool($this->method->useMobile()));
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
            array(new \PaypalLog(0)),
            array(new \PaypalLog(1)),
            array(new \PaypalLog(2))
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
            array($context, 'EC', 'order'),
            array($context, 'PPP', 'order'),
            array($context, 'string', 'string'),
            array($context, null, null)
        );
        return $data;
    }

}
