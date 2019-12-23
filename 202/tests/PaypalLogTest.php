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
 * @copyright PayPal
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PayPalTest;

require_once dirname(__FILE__) . '/TotTestCase.php';
require_once _PS_MODULE_DIR_.'paypal/vendor/autoload.php';
require_once _PS_MODULE_DIR_.'paypal/classes/PaypalLog.php';

class PaypalLogTest extends \TotTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @dataProvider getDataForGetLinkToTransaction
     */
    public function testGetLinkToTransaction($idPayPalLog)
    {
        $payPalLog = new \PaypalLog($idPayPalLog);
        $this->assertTrue(is_string($payPalLog->getLinkToTransaction()));
    }

    public function getDataForGetLinkToTransaction()
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
}
