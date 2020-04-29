<?php


namespace PaypalAddons\classes\API\Request;


use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\Response;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersPatchRequest;
use PayPalHttp\HttpException;
use Symfony\Component\VarDumper\VarDumper;

class PaypalOrderPathRequest extends PaypalOrderCreateRequest
{
    /** @var string*/
    protected $idPayment;

    public function __construct(PayPalHttpClient $client, AbstractMethodPaypal $method, $idPayment)
    {
        parent::__construct($client, $method);
        $this->idPayment = $idPayment;
    }

    public function execute()
    {
        $response = new Response();
        $orderPath = new OrdersPatchRequest($this->idPayment);
        $orderPath->body = $this->buildRequestBody();

        try {
            $exec = $this->client->execute($orderPath);
            if ($exec->statusCode == 204) {
                $response->setSuccess(true);
            } else {
                $error = new Error();
                $error->setMessage('Failed order update');
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

    protected function buildRequestBody()
    {
        $body = [];
        $currency = $this->getCurrency();
        $productItmes = $this->getProductItems($currency);
        $discountItems = $this->getDiscountItems($currency);
        $wrappingItems = $this->getWrappingItems($currency);
        $items = array_merge($productItmes, $discountItems, $wrappingItems);
        $shippingInfo = $this->getShippingInfo();

        $body[] = [
            'op' => 'replace',
            'path' => '/purchase_units/@reference_id==\'default\'',
            'value' => [
                'amount' => $this->getAmount($currency),
                'items' => $items,
                'shipping' => $shippingInfo
            ]
        ];

        return $body;
    }
}
