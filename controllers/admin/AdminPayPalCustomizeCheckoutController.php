<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL 202 ecommerce
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL 202 ecommerce is strictly forbidden.
 * In order to obtain a license, please contact us: tech@202-ecommerce.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe 202 ecommerce
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL 202 ecommerce est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter 202-ecommerce <tech@202-ecommerce.com>
 * ...........................................................................
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 202-ecommerce
 * @license   Commercial license
 * @version   develop
 */
use PaypalAddons\classes\AdminPayPalController;

class AdminPayPalCustomizeCheckoutController extends AdminPayPalController
{
    public function __construct()
    {
        parent::__construct();
        $this->parametres = array(
            'paypal_express_checkout_in_context',
            'paypal_api_advantages',
            'paypal_config_brand',
            'paypal_express_checkout_shortcut',
            'paypal_express_checkout_shortcut_cart'
        );
    }

    public function initContent()
    {
        parent::initContent();
        $this->initForm();
        $this->context->smarty->assign('form', $this->renderForm());
        $this->content = $this->context->smarty->fetch($this->getTemplatePath() . 'customizeCheckout.tpl');
        $this->context->smarty->assign('content', $this->content);
    }

    public function initForm()
    {
        $tpl_vars = array(
            'paypal_express_checkout_shortcut' => (int)Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT'),
            'paypal_express_checkout_shortcut_cart' => (int)Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART')
        );
        $this->context->smarty->assign($tpl_vars);
        $htmlContent = $this->context->smarty->fetch($this->getTemplatePath() . '_partials/blockPreviewButtonContext.tpl');
        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Behavior'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('PayPal In-Context'),
                    'name' => 'paypal_express_checkout_in_context',
                    'hint' => $this->l('PayPal opens in a pop-up window, allowing your buyers to finalize their payment without leaving your website. Optimized, modern and reassuring experience which benefits from the same security standards than during a redirection to the PayPal website.'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => '1',
                                'name' => $this->l('IN-CONTEXT'),
                            ),
                            array(
                                'id' => '0',
                                'name' => $this->l('REDIRECT'),
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'html',
                    'label' => $this->l('PayPal Express CheckoutShortcut on'),
                    'hint' => $this->l('The PayPal shortcut is displayed directly in the cart or on your product pages, allowing a faster checkout experience for your buyers. It requires fewer pages, clicks and seconds in order to finalize the payment. PayPal provides you with the client’s billing and shipping information so that you don’t have to collect it yourself.'),
                    'name' => '',
                    'html_content' => $htmlContent
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show PayPal benefits to your customers'),
                    'name' => 'paypal_api_advantages',
                    'is_bool' => true,
                    'hint' => $this->l('You can increase your conversion rate by presenting PayPal benefits to your customers on payment methods selection page.'),
                    'values' => array(
                        array(
                            'id' => 'paypal_api_advantages_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ),
                        array(
                            'id' => 'paypal_api_advantages_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Brand name'),
                    'name' => 'paypal_config_brand',
                    'placeholder' => $this->l('Leave it empty to use your Shop name'),
                    'hint' => $this->l('A label that overrides the business name in the PayPal account on the PayPal pages.', get_class($this)),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right button',
            ),
        );

        $values = array();
        foreach ($this->parametres as $parametre) {
            $values[$parametre] = Configuration::get(Tools::strtoupper($parametre));
        }
        $this->tpl_form_vars = array_merge($this->tpl_form_vars, $values);
    }

    public function saveForm()
    {
        foreach ($this->parametres as $parametre) {
            \Configuration::updateValue(\Tools::strtoupper($parametre), pSQL(\Tools::getValue($parametre), ''));
        }
    }
}
