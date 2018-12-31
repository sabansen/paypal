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

namespace Tests\Unit\modules\paypal;

use Module;
use Employee;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PHPUnit\Framework\TestCase;
use Tests\Unit\ContextMocker;

class PaypalTest extends TestCase
{

    public $moduleManagerBuilder;
    public $moduleManager;

    public $moduleNames;
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

        $this->moduleNames= [
            'paypal',
        ];
    }

    public function testInstall()
    {
        /**
         * Both modules install overrides in the same files.
         * This test only checks that modules are installed properly.
         */
        foreach ($this->moduleNames as $name) {
            $this->assertTrue((bool)$this->moduleManager->install($name), "Could not install $name");
        }
    }


    public function testUninstall()
    {
        /** Then it checks that the overrides are removed once the modules are
         *  uninstalled.
         */
        foreach ($this->moduleNames as $name) {
            $this->assertTrue((bool)$this->moduleManager->uninstall($name), "Could not uninstall $name");
        }
    }

}