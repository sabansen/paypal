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

use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Request\V_1\GetWebhookEvents;
use PaypalAddons\classes\Constants\WebhookHandler;
use PaypalAddons\classes\Constants\WebHookType;
use PaypalAddons\classes\Exception\RefundCalculationException;
use PaypalAddons\services\ActualizeTotalPaid;
use PaypalAddons\services\ContainerService;
use PaypalAddons\services\PaymentTotalAmount;
use PaypalAddons\services\ServicePaypalOrder;
use PaypalAddons\services\StatusMapping;
use PaypalAddons\services\WebhookService;
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

    public function __construct()
    {
        parent::__construct();

        $this->servicePaypalOrder = new ServicePaypalOrder();
        $this->initContainer();
        $this->request = file_get_contents('php://input');
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
                if ($this->handleWebhook($this->getRequestData())) {
                    header('HTTP/1.1 200 OK');
                } else {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                }
            } else {
                $transaction = $this->getTransactionRef($this->getRequestData());
                $paypalOrder = $this->servicePaypalOrder->getPaypalOrderByTransaction($transaction);

                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    Tools::substr('Invalid webhook event. Data: ' . $this->getRequest(), 0, 999),
                    $transaction,
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
            $transaction = $this->getTransactionRef($this->getRequestData());
            $paypalOrder = $this->servicePaypalOrder->getPaypalOrderByTransaction($transaction);

            ProcessLoggerHandler::openLogger();
            ProcessLoggerHandler::logError(
                $message,
                $transaction,
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

    /**
     * @param $data array webevent data
     *
     * @return bool
     */
    protected function handleWebhook($data)
    {
        if ($this->alreadyHandled($data)) {
            return true;
        }

        if (false == isset($data['resource'])) {
            return false;
        }

        $msg = 'Webhook event : ' . $this->jsonEncode([
                'event_type' => $this->eventType($data),
                'webhook_id' => $this->getWebhookId($data),
                'data' => $data,
            ]);
        $msg = Tools::substr($msg, 0, 999);

        $transaction = $this->getTransactionRef($data);
        $paypalOrder = $this->servicePaypalOrder->getPaypalOrderByTransaction($transaction);

        if (Validate::isLoadedObject($paypalOrder) == false) {
            return false;
        }

        $orders = $this->servicePaypalOrder->getPsOrders($paypalOrder);

        ProcessLoggerHandler::openLogger();
        foreach ($orders as $order) {
            ProcessLoggerHandler::logInfo(
                $msg,
                $transaction,
                $order->id,
                $order->id_cart,
                $order->id_shop,
                'PayPal',
                (int) Configuration::get('PAYPAL_SANDBOX')
            );
        }
        ProcessLoggerHandler::closeLogger();

        $psOrderStatus = $this->getPsOrderStatus($data);

        if ($psOrderStatus > 0) {
            $this->servicePaypalOrder->setOrderStatus($paypalOrder, $psOrderStatus, false);
        }

        if ($this->isCaptureAuthorization($data)) {
            $capture = PaypalCapture::loadByOrderPayPalId($paypalOrder->id);

            if (Validate::isLoadedObject($capture)) {
                $capture->id_capture = $data['resource']['id'];
                $capture->result = $data['resource']['status'];
                $capture->capture_amount = $this->getAmount($data);
                $capture->save();
            }

            $webhookEvent = new \PayPal\Api\WebhookEvent();
            $webhookEvent->fromArray($data);
            $this->actualizeOrder($paypalOrder, $webhookEvent);
        }

        if ($psOrderStatus == $this->getStatusMapping()->getCanceledStatus()) {
            $this->removeEventInWaiting($paypalOrder);
        }

        $paypalWebhook = $this->getWebhookService()->createForOrder($paypalOrder, $psOrderStatus);
        $paypalWebhook->id_webhook = $this->getWebhookId($data);
        $paypalWebhook->event_type = $this->eventType($data);
        $paypalWebhook->data = $this->jsonEncode($data);
        $paypalWebhook->date_completed = date(PaypalWebhook::DATE_FORMAT);
        $paypalWebhook->save();

        return true;
    }

    protected function alreadyHandled($data)
    {
        $query = (new DbQuery())
            ->from(PaypalWebhook::$definition['table'])
            ->where('id_webhook = ' . $this->getWebhookId($data))
            ->select(PaypalWebhook::$definition['primary']);

        try {
            return (bool) Db::getInstance()->getValue($query);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $data
     *
     * @return int
     */
    protected function getPsOrderStatus($data)
    {
        $eventType = $this->eventType($data);

        if ($eventType == WebHookType::CAPTURE_REFUNDED) {
            $paymentTotal = $this->getPaymentTotal($data);

            if ($paymentTotal > 0) {
                return 0;
            }
        }

        return $this->getStatusMapping()->getPsOrderStatusByEventType($eventType);
    }

    /** @return StatusMapping*/
    protected function getStatusMapping()
    {
        return new StatusMapping();
    }

    /**
     * @param mixed $data
     *
     * @return float
     *
     * @throws RefundCalculationException
     */
    protected function getPaymentTotal($data)
    {
        $transaction = $this->getTransactionRef($data);
        $paypalOrder = $this->servicePaypalOrder->getPaypalOrderByTransaction($transaction);

        return $this->getPaymentTotalAmountService()->get($paypalOrder);
    }

    protected function getPaymentTotalAmountService()
    {
        return new PaymentTotalAmount();
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

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function eventType($data)
    {
        return isset($data['event_type']) ? (string) $data['event_type'] : '';
    }

    /**
     * @param mixed
     *
     * @return string
     */
    protected function getTransactionRef($data)
    {
        if (false == isset($data['resource'])) {
            return '';
        }

        if ($this->isCaptureAuthorization($data)) {
            return $this->getAuthorizationId($data);
        }

        if ($this->eventType($data) == WebHookType::CAPTURE_REFUNDED) {
            foreach ($data['resource']['links'] as $link) {
                if ($link['rel'] == 'up') {
                    return $this->getTransactionFromHref($link['href']);
                }
            }
        }

        return isset($data['resource']['id']) ? (string) $data['resource']['id'] : '';
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function getWebhookId($data)
    {
        return isset($data['id']) ? $data['id'] : '';
    }

    /**
     * @param $value mixed
     *
     * @return string
     */
    public function jsonEncode($value)
    {
        $result = json_encode($value);

        if (json_last_error() == JSON_ERROR_UTF8) {
            $result = json_encode($this->utf8ize($value));
        }

        return $result;
    }

    public function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return utf8_encode($mixed);
        }

        return $mixed;
    }

    /**
     * @param string $href
     *
     * @return string
     */
    protected function getTransactionFromHref($href)
    {
        $tmp = explode('/', $href);

        return (string) array_pop($tmp);
    }

    protected function initContainer()
    {
        $this->container = ContainerService::init();
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    protected function isCaptureAuthorization($data)
    {
        try {
            return (bool) $this->getAuthorizationId($data);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function getAuthorizationId($data)
    {
        try {
            return $data['resource']['supplementary_data']['related_ids']['authorization_id'];
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * @param mixed $data
     *
     * @return float
     */
    protected function getAmount($data)
    {
        try {
            return (float) $data['resource']['amount']['value'];
        } catch (Exception $e) {
            return 0;
        }
    }

    protected function getWebhookService()
    {
        return new WebhookService();
    }

    protected static function isInWhitelistForGeolocation()
    {
        return true;
    }

    protected function displayMaintenancePage()
    {
    }

    protected function actualizeOrder(PaypalOrder $paypalOrder, PayPal\Api\WebhookEvent $webhookEvent)
    {
        $orders = $this->servicePaypalOrder->getPsOrders($paypalOrder);

        if (count($orders) > 1) {
            // todo: implement
            return;
        }

        $order = array_shift($orders);

        try {
            $totalPaid = $webhookEvent->resource->amount->value;
        } catch (Exception $e) {
            return;
        }

        $this->getActualizeTotalPaid()->actualize($order, $totalPaid);
    }

    /**
     * @return ActualizeTotalPaid
     */
    protected function getActualizeTotalPaid()
    {
        return new ActualizeTotalPaid();
    }

    protected function removeEventInWaiting(PaypalOrder $paypalOrder)
    {
        $webhookEvents = $this->getWebhookService()->getPendingWebhooks($paypalOrder);

        if (empty($webhookEvents)) {
            return;
        }

        foreach ($webhookEvents as $webhookEvent) {
            $webhookEvent->delete();
        }
    }
}
