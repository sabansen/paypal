<?php


namespace PaypalAddons\classes\API\Response;


class PaypalResponseAccessToken extends Response
{
    /** @var string*/
    protected $accessToken;

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }
}
