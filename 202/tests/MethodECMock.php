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

class MethodECMock extends TestCase
{
    public function getInstance()
    {
        $methodMock = $this->getMockBuilder(\MethodEC::class)
            ->setMethods(array('_getCredentialsInfo'))
            ->getMock();

        $params = array();
        $params['acct1.UserName'] = \Configuration::get('PAYPAL_USERNAME_SANDBOX');
        $params['acct1.Password'] = \Configuration::get('PAYPAL_PSWD_SANDBOX');
        $params['acct1.Signature'] = \Configuration::get('PAYPAL_SIGNATURE_SANDBOX');
        $params['mode'] = 'sandbox';
        $params['log.LogEnabled'] = false;
        $params['http.headers.PayPal-Partner-Attribution-Id'] = getenv('PLATEFORM') == 'PSREAD' ? 'PrestaShop_Cart_Ready_EC' : 'PrestaShop_Cart_EC';

        $methodMock->method('_getCredentialsInfo')->willReturn($params);

        return $methodMock;
    }

}
