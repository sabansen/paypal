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
require_once _PS_MODULE_DIR_.'paypal/classes/MethodPPP.php';

use PHPUnit\Framework\TestCase;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class MethodPPPMock extends TestCase
{
    public function getInstance()
    {
        $methodMock = $this->getMockBuilder(\MethodPPP::class)
            ->setMethods(array('_getCredentialsInfo'))
            ->getMock();

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                'ASY0PPD9m_iTYj3qVRYuUI484zJmxV9KGVHksm2Eqvp4w3J8cpWGXkwfHP0fqTSZI10o147UsgFEMkSd',
                'EMfCLrLpZ6cN8j1vQ4OyFKVkk_anFcSJWxYFZZJvnTwEYNIROLpDpw4f08lj5YAmsn21MVrywRzbhu6n'
            )
        );
        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => false,
                'cache.enabled' => true,
            )
        );
        $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', (getenv('PLATEFORM') == 'PSREAD')?'PrestaShop_Cart_Ready_PPP':'PrestaShop_Cart_PPP');
        $methodMock->method('_getCredentialsInfo')->willReturn($apiContext);

        return $methodMock;
    }

}
