<?php


namespace PaypalAddons\classes\API\Request;



use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\PaypalResponseAccessToken;
use PayPalCheckoutSdk\Core\AccessTokenRequest;
use PayPalHttp\HttpException;
use Symfony\Component\VarDumper\VarDumper;

class PaypalAccessTokenRequest extends RequestAbstract
{
    public function execute()
    {
        $response = new PaypalResponseAccessToken();

        try {
            $accessToken = $this->client->execute(new AccessTokenRequest($this->client->environment));

            if ($accessToken->statusCode == 200) {
                $response->setSuccess(true)
                    ->setAccessToken($accessToken->result->access_token)
                    ->setData($accessToken);
            } else {
                $response->setSuccess(false)->setData($accessToken);
            }
        } catch (HttpException $e) {
            $error = new Error();
            $resultDecoded = json_decode($e->getMessage());
            $error->setMessage($resultDecoded->error_description)->setErrorCode($e->getCode());
            $response->setSuccess(false)
                ->setError($error);
        } catch (\Exception $e) {
            $error = new Error();
            $error->setErrorCode($e->getCode())
                ->setMessage($e->getMessage());
            $response->setSuccess(false)
                ->setError($error);
        }

        return $response;
    }
}
