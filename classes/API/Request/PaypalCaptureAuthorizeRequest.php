<?php


namespace PaypalAddons\classes\API\Request;


use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\ResponseCaptureAuthorize;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Payments\AuthorizationsCaptureRequest;
use PayPalHttp\HttpException;
use Symfony\Component\VarDumper\VarDumper;

class PaypalCaptureAuthorizeRequest extends RequestAbstract
{
    /** @var \PaypalOrder*/
    protected $paypalOrder;

    public function __construct(PayPalHttpClient $client, AbstractMethodPaypal $method, \PaypalOrder $paypalOrder)
    {
        parent::__construct($client, $method);
        $this->paypalOrder = $paypalOrder;
    }

    public function execute()
    {
        $response = new ResponseCaptureAuthorize();
        $captureAuhorize = new AuthorizationsCaptureRequest($this->paypalOrder->id_transaction);
        $captureAuhorize->prefer('return=representation');

        try {
            $exec = $this->client->execute($captureAuhorize);

            if (in_array($exec->statusCode, [200, 201, 202])) {
                $response->setSuccess(true)
                    ->setIdTransaction($exec->result->id)
                    ->setStatus($exec->result->status)
                    ->setDateTransaction($this->getDateTransaction($exec));
            } else {
                $error = new Error();
                $resultDecoded = json_decode($exec->message);
                $error->setMessage($resultDecoded->message);
                $response->setSuccess(false)->setError($error);
            }
        } catch (HttpException $e) {
            $error = new Error();
            $resultDecoded = json_decode($e->getMessage());
            $error->setMessage($resultDecoded->details[0]->description)->setErrorCode($e->getCode());
            $response->setSuccess(false)
                ->setError($error);
        } catch (\Exception $e) {
            $error = new Error();
            $error->setErrorCode($e->getCode())->setMessage($e->getMessage());
            $response->setError($error)->setSuccess(false);
        }

        return $response;
    }

    protected function getDateTransaction(\PayPalHttp\HttpResponse $exec)
    {
        $date = \DateTime::createFromFormat(\DateTime::ATOM, $exec->result->create_time);
        return $date->format('Y-m-d H:i:s');
    }
}
