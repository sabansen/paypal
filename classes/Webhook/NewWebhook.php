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
 *  @author 2007-2021 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\classes\Webhook;


use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Request\V_1\CreateWebHook;
use PaypalAddons\classes\API\Request\V_1\GetWebHooks;
use PaypalAddons\classes\API\Request\V_1\UpdateWebHookEventType;
use Symfony\Component\VarDumper\VarDumper;

class NewWebhook
{
    /** @var AbstractMethodPaypal*/
    protected $method;

    public function __construct($method = null)
    {
        $this->setMethod($method);
    }

    public function execute()
    {
        $method = $this->getMethod();
        $response = (new GetWebHooks($method))->execute();

        if ($response->isSuccess() == false) {
            return (new CreateWebHook($method))->execute();
        }

        if (empty($response->getData())) {
            return (new CreateWebHook($method))->execute();
        }

        $webhookHandler = (new WebhookHandlerUrl())->get();

        /** @var Webhook $webhook*/
        foreach ($response->getData() as $webhook) {
            if ($webhook->getUrl() == $webhookHandler) {
                return (new UpdateWebHookEventType($method, $webhook))->execute();
            }
        }

        return (new CreateWebHook($method))->execute();
    }

    public function getMethod()
    {
        if ($this->method instanceof AbstractMethodPaypal) {
            return $this->method;
        }

        return AbstractMethodPaypal::load();
    }

    public function setMethod($method)
    {
        if ($method instanceof AbstractMethodPaypal) {
            $this->method = $method;
        }

        return $this;
    }
}
