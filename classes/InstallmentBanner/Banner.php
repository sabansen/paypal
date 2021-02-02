<?php
/**
 * 2007-2021 PayPal
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
 *  @author 2007-2020 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\classes\InstallmentBanner;

use \Context;
use \Module;
use PaypalAddons\classes\AbstractMethodPaypal;
use \Configuration;
use PaypalAddons\classes\InstallmentBanner\ConfigurationMap;

class Banner
{
    /** @var \PayPal*/
    protected $module;

    /** @var string*/
    protected $placement;

    /** @var string*/
    protected $layout;

    /** @var float*/
    protected $amount;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('paypal');
    }

    public function render()
    {
        return Context::getContext()->smarty
            ->assign('JSvars', $this->getJsVars())
            ->assign($this->getTplVars())
            ->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/installmentBanner/banner.tpl');
    }

    protected function getJsVars()
    {
        $vars = [];
        /** @var \MethodEC $methodEC*/
        $methodEC = AbstractMethodPaypal::load('EC');
        $vars['installmentLib'] = $methodEC->getInstallmentLib();

        return $vars;
    }

    protected function getTplVars()
    {
        $vars = [];
        $vars['color'] = Configuration::get(ConfigurationMap::COLOR);
        $vars['placement'] = $this->getPlacement();
        $vars['layout'] = $this->getLayout();

        if ($this->getAmount()) {
            $vars['amount'] = $this->getAmount();
        }

        return $vars;
    }

    /**
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement ? $this->placement : 'home';
    }

    /**
     * @return self
     */
    public function setPlacement($placement)
    {
        $this->placement = (string)$placement;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return (float)$this->amount;
    }

    /**
     * @param float $amount
     * @return Banner
     */
    public function setAmount($amount)
    {
        $this->amount = (float)$amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout ? $this->layout : 'flex';
    }

    /**
     * @param string $layout
     * @return Banner
     */
    public function setLayout($layout)
    {
        $this->layout = (string)$layout ;
        return $this;
    }
}
