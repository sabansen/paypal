<?php
/**
 * 2007-2022 PayPal
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
 *  @author 2007-2022 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\services\PaypalContext;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Prepare EC payment
 */
class PaypalPuiInitModuleFrontController extends PaypalAbstarctModuleFrontController
{
    /* @var $method AbstractMethodPaypal*/
    protected $method;

    public function init()
    {
        parent::init();
        PaypalContext::getContext()->set('client-session-id', Tools::getValue('sessionId'));
        $this->method = AbstractMethodPaypal::load('PPP');
    }
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        try {
            $this->redirectUrl = $this->method->init()->getApproveLink();
        } catch (PayPal\Exception\PPConnectionException $e) {
            $this->_errors['error_msg'] = $this->module->l('Error connecting to ', pathinfo(__FILE__)['filename']) . $e->getUrl();
        } catch (PayPal\Exception\PPMissingCredentialException $e) {
            $this->_errors['error_msg'] = $e->errorMessage();
        } catch (PayPal\Exception\PPConfigurationException $e) {
            $this->_errors['error_msg'] = $this->module->l('Invalid configuration. Please check your configuration file', pathinfo(__FILE__)['filename']);
        } catch (PaypalAddons\classes\PaypalException $e) {
            $this->_errors['error_code'] = $e->getCode();
            $this->_errors['error_msg'] = $e->getMessage();
            $this->_errors['msg_long'] = $e->getMessageLong();
        } catch (Exception $e) {
            $this->_errors['error_code'] = $e->getCode();
            $this->_errors['error_msg'] = $e->getMessage();
        }

        if (!empty($this->_errors)) {
            $this->redirectUrl = Context::getContext()->link->getModuleLink($this->name, 'error', $this->_errors);
        }
    }
}
