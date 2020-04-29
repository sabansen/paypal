<?php


namespace PaypalAddons\classes\API\Request;


use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\ResponseOrderGet;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalHttp\HttpException;
use Symfony\Component\VarDumper\VarDumper;

class PaypalOrderGetRequest extends RequestAbstract
{
    protected $idPayment;

    public function __construct(PayPalHttpClient $client, AbstractMethodPaypal $method, $idPayment)
    {
        parent::__construct($client, $method);
        $this->idPayment = $idPayment;
    }

    public function execute()
    {
        $response = new ResponseOrderGet();

        try {
            $exec = $this->client->execute(new OrdersGetRequest($this->idPayment));

            if (in_array($exec->statusCode, [200, 201, 202])) {
                $response->setSuccess(true)
                    ->setData($exec);
                $response->getAddress()
                    ->setAddress1($this->getAddress1($exec))
                    ->setAddress2($this->getAddress2($exec))
                    ->setCity($this->getCity($exec))
                    ->setPostCode($this->getPostCode($exec))
                    ->setCountryCode($this->getCountryCode($exec))
                    ->setStateCode($this->getStateCode($exec))
                    ->setPhone($this->getPhone($exec))
                    ->setFullName($this->getFullName($exec));
                $response->getClient()
                    ->setEmail($this->getEmail($exec))
                    ->setFirstName($this->getFirstName($exec))
                    ->setLastName($this->getLastName($exec));
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

    protected function getAddress1(\PayPalHttp\HttpResponse $exec)
    {
        return $exec->result->purchase_units[0]->shipping->address->address_line_1;
    }

    protected function getAddress2(\PayPalHttp\HttpResponse $exec)
    {
        $address = $exec->result->purchase_units[0]->shipping->address;
        if (isset($address->address_line_2)) {
            return $address->address_line_2;
        } else {
            return '';
        }

    }

    protected function getCity(\PayPalHttp\HttpResponse $exec)
    {
        return $exec->result->purchase_units[0]->shipping->address->admin_area_2;
    }

    protected function getPostCode(\PayPalHttp\HttpResponse $exec)
    {
        return $exec->result->purchase_units[0]->shipping->address->postal_code;
    }

    protected function getCountryCode(\PayPalHttp\HttpResponse $exec)
    {
        return $exec->result->purchase_units[0]->shipping->address->country_code;
    }

    protected function getStateCode(\PayPalHttp\HttpResponse $exec)
    {
        $address = $exec->result->purchase_units[0]->shipping->address;

        if (isset($address->admin_area_1)) {
            return $address->admin_area_1;
        } else {
            return '';
        }
    }

    protected function getPhone(\PayPalHttp\HttpResponse $exec)
    {
        $payer = $exec->result->payer;
        if (isset($payer->phone)) {
            return $payer->phone->phone_number->national_number;
        } else {
            return '';
        }
    }

    protected function getFullName(\PayPalHttp\HttpResponse $exec)
    {
        return $exec->result->purchase_units[0]->shipping->name->full_name;
    }

    protected function getEmail(\PayPalHttp\HttpResponse $exec)
    {
        return $exec->result->payer->email_address;
    }

    protected function getFirstName(\PayPalHttp\HttpResponse $exec)
    {
        return $exec->result->payer->name->given_name;
    }

    protected function getLastName(\PayPalHttp\HttpResponse $exec)
    {
        return $exec->result->payer->name->surname;
    }


}
