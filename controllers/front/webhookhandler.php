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

use PayPal\Api\WebhookEvent;
use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Request\V_1\GetWebhookEvents;
use PaypalAddons\classes\Constants\WebhookHandler;
use PaypalAddons\classes\Webhook\WebhookEventHandler;
use PaypalAddons\services\ServicePaypalOrder;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;

/**
 * Class PaypalAbstarctModuleFrontController
 */
class PaypalWebhookhandlerModuleFrontController extends PaypalAbstarctModuleFrontController
{
    /** @var ServicePaypalOrder */
    protected $servicePaypalOrder;

    /** @var array */
    protected $requestData;

    protected $request;

    protected $webhookEventHandler;

    public function __construct()
    {
        parent::__construct();

        $this->request = file_get_contents('php://input');
        $this->webhookEventHandler = new WebhookEventHandler();
    }

    public function run()
    {
        parent::init();

        if ($this->isCheckAvailability()) {
            header('HTTP/1.1 ' . WebhookHandler::STATUS_AVAILABLE);
            exit;
        }

        if (false == ($this->module->getWebhookOption()->isEnable() && $this->module->getWebhookOption()->isAvailable())) {
            return;
        }

        try {
            if ($this->requestIsValid()) {
                $webhookEvent = new WebhookEvent();
                $webhookEvent->fromArray($this->getRequestData());

                if ($this->webhookEventHandler->handle($webhookEvent)) {
                    header('HTTP/1.1 200 OK');
                } else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                }
            } else {
                $paypalOrder = $this->initPaypalOrder($this->getRequest());

                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    Tools::substr('Invalid webhook event. Data: ' . $this->getRequest(), 0, 999),
                    $paypalOrder->id_transaction,
                    Validate::isLoadedObject($paypalOrder) ? $paypalOrder->id_order : null,
                    Validate::isLoadedObject($paypalOrder) ? $paypalOrder->id_cart : null,
                    null,
                    null,
                    Validate::isLoadedObject($paypalOrder) ? $paypalOrder->sandbox : (int) Configuration::get('PAYPAL_SANDBOX'),
                    null
                );
                ProcessLoggerHandler::closeLogger();
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            }
        } catch (\Exception $e) {
            $message = 'Error code: ' . $e->getCode() . '.';
            $message .= 'Short message: ' . $e->getMessage() . '.';
            $paypalOrder = $this->initPaypalOrder($this->getRequestData());

            ProcessLoggerHandler::openLogger();
            ProcessLoggerHandler::logError(
                $message,
                $paypalOrder->id_transaction,
                Validate::isLoadedObject($paypalOrder) ? $paypalOrder->id_order : null,
                Validate::isLoadedObject($paypalOrder) ? $paypalOrder->id_cart : null,
                null,
                null,
                Validate::isLoadedObject($paypalOrder) ? $paypalOrder->sandbox : (int) Configuration::get('PAYPAL_SANDBOX'),
                null
            );
            ProcessLoggerHandler::closeLogger();

            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        }
    }

    protected function isCheckAvailability()
    {
        return (bool) Tools::isSubmit('checkAvailability');
    }

    /**
     * @return bool
     */
    protected function requestIsValid()
    {
        try {
            if (empty($this->getRequestData()['id'])) {
                return false;
            }

            $params = [
                'id' => $this->getRequestData()['id'],
            ];
            $events = $this->getWebhookEventRequest()->setParams($params)->execute()->getData();

            if (empty($events)) {
                return false;
            }

            $this->request = $events[0]->toJson();
            $this->requestData = $events[0]->toArray();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /** @return GetWebhookEvents*/
    protected function getWebhookEventRequest()
    {
        return new GetWebhookEvents(AbstractMethodPaypal::load());
    }

    protected function getRequestData()
    {
        if (false == empty($this->requestData)) {
            return $this->requestData;
        }

        $this->requestData = json_decode($this->getRequest(), true);

        return $this->requestData;
    }

    protected function getRequest()
    {
        return $this->request;
    }

    protected static function isInWhitelistForGeolocation()
    {
        return true;
    }

    protected function displayMaintenancePage()
    {
    }
}
