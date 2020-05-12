<?php


namespace PaypalAddons\classes\API\Onboarding;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\ResponseGetAuthToken;

class PaypalGetAuthToken
{
    /** @var */
    protected $httpClient;

    /** @var string*/
    protected $authCode;

    /** @var string*/
    protected $sharedId;

    /** @var string*/
    protected $sellerNonce;

    public function __construct($authCode, $sharedId, $sellerNonce, $sandbox)
    {
        $this->httpClient = new Client(['base_url' => $sandbox ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com']);
        $this->authCode = $authCode;
        $this->sharedId = $sharedId;
        $this->sellerNonce = $sellerNonce;
    }

    /**
     * @return ResponseGetAuthToken
     */
    public function execute()
    {
        $returnResponse = new ResponseGetAuthToken();
        $body = sprintf('grant_type=authorization_code&code=%s&code_verifier=%s', $this->authCode, $this->sellerNonce);

        try {
            $response = $this->httpClient->post(
                '/v1/oauth2/token',
                [
                    RequestOptions::BODY => $body,
                    RequestOptions::HEADERS => [
                        'Content-Type' => 'text/plain',
                        'Authorization' => 'Basic ' . base64_encode($this->sharedId)
                    ],
                ]
            );

            $responseDecode = json_decode($response->getBody()->getContents());
            $returnResponse->setSuccess(true)
                ->setData($returnResponse)
                ->setAuthToken($responseDecode->access_token)
                ->setRefreshToken($responseDecode->refresh_token)
                ->setTokenType($responseDecode->token_type)
                ->setNonce($responseDecode->nonce);
        } catch (\Exception $e) {
            $error = new Error();
            $error->setMessage($e->getMessage())->setErrorCode($e->getCode());
            $returnResponse->setError($error)->setSuccess(false);
        }


        return $returnResponse;
    }
}
