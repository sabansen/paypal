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

namespace PaypalAddons\classes\Form\Controller\AdminPayPalInstallment;


use PaypalAddons\classes\Form\FormInterface;
use \Module;
use \Configuration;
use \Tools;
use \Context;
use PaypalAddons\classes\Form\Field\Select;
use PaypalAddons\classes\Form\Field\SelectOption;

class FormInstallment implements FormInterface
{
    /** @var \Paypal*/
    protected $module;

    protected $className;

    const ENABLE_INSTALLMENT = 'PAYPAL_ENABLE_INSTALLMENT';

    const ADVANCED_OPTIONS_INSTALLMENT = 'PAYPAL_ADVANCED_OPTIONS_INSTALLMENT';

    const PRODUCT_PAGE = 'PAYPAL_INSTALLMENT_PRODUCT_PAGE';

    const HOME_PAGE = 'PAYPAL_INSTALLMENT_HOME_PAGE';

    const CART_PAGE = 'PAYPAL_INSTALLMENT_CART_PAGE';

    const CATEGORY_PAGE = 'PAYPAL_INSTALLMENT_CATEGORY_PAGE';

    const COLOR = 'PAYPAL_INSTALLMENT_COLOR';

    public function __construct()
    {
        $this->module = Module::getInstanceByName('paypal');

        $reflection = new \ReflectionClass($this);
        $this->className = $reflection->getShortName();
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $fields = array(
            'legend' => array(
                'title' => $this->module->l('Settings', $this->className),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable the display of 4x banners', $this->className),
                    'name' => self::ENABLE_INSTALLMENT,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => self::ENABLE_INSTALLMENT . '_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', $this->className),
                        ),
                        array(
                            'id' => self::ENABLE_INSTALLMENT . '_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', $this->className),
                        )
                    ),
                ),
                array(
                    'type' => 'html',
                    'html_content' => $this->getHtmlBlockPageDisplayingSetting(),
                    'name' => '',
                    'label' => '',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Advanced options', $this->className),
                    'name' => self::ADVANCED_OPTIONS_INSTALLMENT,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => self::ADVANCED_OPTIONS_INSTALLMENT . '_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', $this->className),
                        ),
                        array(
                            'id' => self::ADVANCED_OPTIONS_INSTALLMENT . '_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', $this->className),
                        )
                    ),
                ),
                array(
                    'type' => 'html',
                    'label' => $this->module->l('Widget code', $this->className),
                    'hint' => $this->module->l('By default, PayPal 4x banner is displayed on your web site via PrestaShop native hook.
If you choose to use widgets, you will be able to copy widget code and insert it wherever you want in the web site template.', $this->className),
                    'name' => '',
                    'html_content' => $this->getWidgetField()
                ),
                array(
                    'type' => 'html',
                    'html_content' => $this->getBannerStyleSection(),
                    'name' => '',
                    'label' => $this->module->l('The styles for the home page and category pages', $this->className),
                )
            ),
            'submit' => array(
                'title' => $this->module->l('Save', $this->className),
                'class' => 'btn btn-default pull-right button',
                'name' => 'installmentForm'
            ),
            'id_form' => 'pp_config_installment'
        );

        return $fields;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            self::ENABLE_INSTALLMENT => (int)Configuration::get(self::ENABLE_INSTALLMENT),
            self::ADVANCED_OPTIONS_INSTALLMENT => (int)Configuration::get(self::ADVANCED_OPTIONS_INSTALLMENT)
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        $return = true;

        if (Tools::isSubmit('installmentForm') === false) {
            return $return;
        }

        $return &= Configuration::updateValue(self::ENABLE_INSTALLMENT, (int)Tools::getValue(self::ENABLE_INSTALLMENT));
        $return &= Configuration::updateValue(self::ADVANCED_OPTIONS_INSTALLMENT, (int)Tools::getValue(self::ADVANCED_OPTIONS_INSTALLMENT));
        $return &= Configuration::updateValue(self::PRODUCT_PAGE, (int)Tools::getValue(self::PRODUCT_PAGE));
        $return &= Configuration::updateValue(self::CART_PAGE, (int)Tools::getValue(self::CART_PAGE));
        $return &= Configuration::updateValue(self::HOME_PAGE, (int)Tools::getValue(self::HOME_PAGE));
        $return &= Configuration::updateValue(self::CATEGORY_PAGE, (int)Tools::getValue(self::CATEGORY_PAGE));
    }

    /**
     * @return string
     */
    protected function getHtmlBlockPageDisplayingSetting()
    {
        Context::getContext()->smarty->assign([
            self::PRODUCT_PAGE => Configuration::get(self::PRODUCT_PAGE),
            self::HOME_PAGE => Configuration::get(self::HOME_PAGE),
            self::CATEGORY_PAGE => Configuration::get(self::CATEGORY_PAGE),
            self::CART_PAGE => Configuration::get(self::CART_PAGE)
        ]);
        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/installmentPageDisplayingSetting.tpl');
    }

    /**
     * @return string
     */
    protected function getWidgetField()
    {
        return Context::getContext()->smarty
            ->assign('widgetCode', '{widget name=\'paypal\' action=\'banner4x\'}')
            ->assign('confName', 'installmentWidgetCode')
            ->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/form/fields/widgetCode.tpl');
    }

    protected function getBannerStyleSection()
    {
        $colorSelect = new Select(
            self::COLOR,
            [
                new SelectOption('blue', $this->module->l('blue', $this->className)),
                new SelectOption('black', $this->module->l('black', $this->className)),
                new SelectOption('white', $this->module->l('white', $this->className)),
                new SelectOption('gray', $this->module->l('gray', $this->className)),
                new SelectOption('monochrome', $this->module->l('monochrome', $this->className)),
                new SelectOption('grayscale', $this->module->l('grayscale', $this->className)),
            ]
        );

        return Context::getContext()->smarty
            ->assign('colorSelect', $colorSelect)
            ->assign('banner', null)
            ->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/paypalBanner/bannerStyleSection.tpl');
    }
}
