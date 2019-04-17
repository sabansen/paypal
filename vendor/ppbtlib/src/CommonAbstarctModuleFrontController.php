<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PaypalPPBTlib;
use \ModuleFrontController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class CommonAbstarctModuleFrontController extends ModuleFrontController
{
    /** @var string module name */
    public $name = 'paypal';

    /** @var  array Contain ajax response. */
    public $jsonValues;

    /** @var  array  POST and GET values defined in init function */
    public $values;

    /** @var  string Contain redirect URL.. */
    public $redirectUrl;

    /** @var  array An array of error information : error_msg, error_code, msg_long. */
    public $errors;

    /**
     * @see ModuleFrontController::run
     */
    public function run()
    {
        $this->init();
        if ($this->checkAccess()) {
            // postProcess handles ajaxProcess
            $this->postProcess();
        }
        if (!empty($this->redirectUrl)) {
            \Tools::redirect($this->redirectUrl);
        }
        if (!empty($this->jsonValues)) {
            $response = new JsonResponse($this->jsonValues);
            return $response->send();
        }
    }
}
