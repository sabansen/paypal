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
include_once(_PS_MODULE_DIR_.'paypal/vendor/autoload.php');

use Symfony\Component\HttpFoundation\JsonResponse;
use PaypalAddons\classes\AdminPayPalController;

class AdminPayPalSetupController extends AdminPayPalController
{
    public function __construct()
    {
        parent::__construct();
        $this->parametres = array(
            'paypal_api_intent',
            'paypal_sandbox',
            'paypal_api_user_name_sandbox',
            'paypal_api_user_name_live',
            'paypal_sandbox_clientid',
            'paypal_live_clientid',
            'paypal_sandbox_secret',
            'paypal_live_secret'
        );
    }

    public function init()
    {
        parent::init();
        if (Tools::getValue('useWithoutBraintree')) {
            Configuration::updateValue('PAYPAL_USE_WITHOUT_BRAINTREE', 1);
        }
    }

    public function initContent()
    {
        parent::initContent();
        $tpl_vars = array();
        $this->initAccountSettingsBlock();
        $formAccountSettings = $this->renderForm();
        $this->clearFieldsForm();
        $tpl_vars['formAccountSettings'] = $formAccountSettings;


        $this->initPaymentSettingsBlock();
        $formPaymentSettings = $this->renderForm();
        $this->clearFieldsForm();
        $tpl_vars['formPaymentSettings'] = $formPaymentSettings;

        if ($this->method == 'EC') {
            $this->initApiUserNameForm();
            $formApiUserName = $this->renderForm();
            $this->clearFieldsForm();
            $tpl_vars['formMerchantAccounts'] = $formApiUserName;
        }


        $this->initEnvironmentSettings();
        $formEnvironmentSettings = $this->renderForm();
        $this->clearFieldsForm();
        $tpl_vars['formEnvironmentSettings'] = $formEnvironmentSettings;

        $this->initStatusBlock();
        $formStatus = $this->renderForm();
        $this->clearFieldsForm();
        $tpl_vars['formStatus'] = $formStatus;

        $this->context->smarty->assign($tpl_vars);
        $this->content = $this->context->smarty->fetch($this->getTemplatePath() . 'setup.tpl');
        $this->context->smarty->assign('content', $this->content);
        $this->addJS('modules/' . $this->module->name . '/views/js/adminSetup.js');
    }

    public function initAccountSettingsBlock()
    {
        $this->fields_form['form']['form'] = array(
            'legend' => array(
                'title' => $this->l('Account settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'html',
                    'html_content' => $this->getHtmlBlockAccountSetting(),
                    'name' => '',
                    'col' => 12,
                    'label' => '',
                )
            ),
            'id_form' => 'pp_config_account'
        );
    }

    public function getHtmlBlockAccountSetting()
    {
        if ($this->method == 'EC') {
            $tpl_vars = $this->getTplVarsForEC();
        } else {
            $tpl_vars = $this->getTplVarsForPPP();
        }

        $tpl_vars['method'] = $this->method;
        $this->context->smarty->assign($tpl_vars);
        $html_content = $this->context->smarty->fetch($this->getTemplatePath() . '_partials/accountSettingsBlock.tpl');
        return $html_content;
    }

    public function getTplVarsForPPP()
    {
        $method = AbstractMethodPaypal::load($this->method);
        $tpl_vars = array(
            'paypal_sandbox_clientid' => Configuration::get('PAYPAL_SANDBOX_CLIENTID'),
            'paypal_live_clientid' => Configuration::get('PAYPAL_LIVE_CLIENTID'),
            'paypal_sandbox_secret' => Configuration::get('PAYPAL_SANDBOX_SECRET'),
            'paypal_live_secret' => Configuration::get('PAYPAL_LIVE_SECRET'),
            'accountConfigured' => $method == null? false : $method->isConfigured(),
        );

        return $tpl_vars;
    }

    public function getTplVarsForEC()
    {
        $method = AbstractMethodPaypal::load($this->method);
        $urlParameters = array(
            'paypal_set_config' => 1,
            'method' => 'EC',
            'with_card' => 0,
            'modify' => 1
        );
        $tpl_vars = array(
            'accountConfigured' => $method == null? false : $method->isConfigured(),
            'urlOnboarding' => $this->context->link->getAdminLink('AdminPayPalSetup', true, null, $urlParameters),
        );

        if ((int)Configuration::get('PAYPAL_SANDBOX')) {
            $tpl_vars['paypal_api_user_name'] = Configuration::get('PAYPAL_USERNAME_SANDBOX');
        } else {
            $tpl_vars['paypal_api_user_name'] = Configuration::get('PAYPAL_USERNAME_LIVE');
        }

        return $tpl_vars;
    }

    public function initPaymentSettingsBlock()
    {
        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Payment settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Payment action'),
                    'name' => 'paypal_api_intent',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'sale',
                                'name' => $this->l('Sale')
                            ),
                            array(
                                'id' => 'authorization',
                                'name' => $this->l('Authorize')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'html',
                    'name' => '',
                    'html_content' => $this->module->displayInformation($this->l('We recommend Authoreze process only for lean manufacturers and craft products sellers.'))
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right button',
            ),
        );

        $values = array(
            'paypal_api_intent' => Configuration::get('PAYPAL_API_INTENT'),
        );
        $this->tpl_form_vars = array_merge($this->tpl_form_vars, $values);
    }

    public function initApiUserNameForm()
    {
        $mode = (int)Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';
        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('API user names'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('API user name'),
                    'name' => 'paypal_api_user_name',
                    'readonly' => true,
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right button',
            ),
        );

        $values['paypal_api_user_name'] = Configuration::get('PAYPAL_USERNAME_' . $mode);
        $this->tpl_form_vars = array_merge($this->tpl_form_vars, $values);
    }

    public function initEnvironmentSettings()
    {
        $this->context->smarty->assign('sandbox', (int)\Configuration::get('PAYPAL_SANDBOX'));
        $html_content = $this->context->smarty->fetch($this->getTemplatePath() . '_partials/switchSandboxBlock.tpl');
        $this->fields_form['form']['form'] = array(
            'legend' => array(
                'title' => $this->l('Environment Settings'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'html',
                    'html_content' => $html_content,
                    'name' => '',
                    'col' => 12,
                    'label' => '',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'paypal_sandbox',
                    'col' => 12,
                    'label' => '',
                )
                ),
                'id_form' => 'pp_config_environment'
        );
        $values = array(
            'paypal_sandbox' => !(int)Configuration::get('PAYPAL_SANDBOX')
        );
        $this->tpl_form_vars = array_merge($this->tpl_form_vars, $values);
    }

    public function initStatusBlock()
    {
        $countryDefault = new \Country((int)\Configuration::get('PS_COUNTRY_DEFAULT'), $this->context->language->id);
        $method = AbstractMethodPaypal::load($this->method);

        $tpl_vars = array(
            'merchantCountry' => $countryDefault->name,
            'tlsVersion' => $this->_checkTLSVersion(),
            'accountConfigured' => $method == null ? false : $method->isConfigured(),
            'sslActivated' => $this->module->isSslActive()
        );

        $this->context->smarty->assign($tpl_vars);
        $html_content = $this->context->smarty->fetch($this->getTemplatePath() . '_partials/statusBlock.tpl');
        $this->fields_form[]['form'] = array(
            'legend' => array(
                'title' => $this->l('Status'),
                'icon' => 'icon-cogs',
            ),
            'input' => array(
                array(
                    'type' => 'html',
                    'html_content' => $html_content,
                    'name' => '',
                    'col' => 12,
                    'label' => '',
                )
            )
        );
    }

    public function displayAjaxLogoutAccount()
    {
        $response = new JsonResponse();
        $content = array(
            'status' => false,
            'redirectUrl' => ''
        );
        if (Tools::getValue('token') == Tools::getAdminTokenLite($this->controller_name)) {
            $method = \AbstractMethodPaypal::load($this->method);
            $method->logOut();
            $content['status'] = true;
            $content['redirectUrl'] = $this->context->link->getAdminLink($this->controller_name);
        }

        $response->setContent(\Tools::jsonEncode($content));
        return $response->send();
    }

    public function displayAjaxCheckCredentials()
    {
        $this->initStatusBlock();
        $response = new JsonResponse($this->renderForm());
        return $response->send();
    }

    public function saveForm()
    {
        parent::saveForm();

        if ($this->method == 'PPP') {
            $method = AbstractMethodPaypal::load($this->method);
            if ($method->isConfigured()) {
                $experience_web = $method->createWebExperience();
                if ($experience_web) {
                    Configuration::updateValue('PAYPAL_PLUS_EXPERIENCE', $experience_web->id);
                } else {
                    $this->log($this->l('An error occurred while creating your web experience. Check your credentials.'));
                }
            }
        }

        $this->module->checkPaypalStats();
    }

}
