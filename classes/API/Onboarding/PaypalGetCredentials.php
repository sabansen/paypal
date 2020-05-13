<?php


namespace PaypalAddons\classes\API\Onboarding;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\ResponseGetCredentials;
use Symfony\Component\VarDumper\VarDumper;

class PaypalGetCredentials
{
    /** @var */
    protected $httpClient;

    /** @var string*/
    protected $authToken;

    /** @var string*/
    protected $partnerId;

    public function __construct($authToken, $partnerId, $sandbox)
    {
        $this->httpClient = new Client(['base_url' => $sandbox ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com']);
        $this->authToken = $authToken;
        $this->partnerId = $partnerId;
    }

    public function execute()
    {
        $returnResponse = new ResponseGetCredentials();
        $uri = sprintf('/v1/customer/partners/%s/merchant-integrations/credentials', $this->partnerId);

        try {
            $response = $this->httpClient->get(
                $uri,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $this->authToken
                    ],
                ]
            );

            $responseDecode = json_decode($response->getBody()->getContents());
            $returnResponse->setSuccess(true)
                ->setClientId($responseDecode->client_id)
                ->setSecret($responseDecode->client_secret)
                ->setData($returnResponse);
        } catch (\Exception $e) {
            $error = new Error();
            $error->setMessage($e->getMessage())->setErrorCode($e->getCode());
            $returnResponse->setError($error)->setSuccess(false);
        }


        return $returnResponse;
    }
}
