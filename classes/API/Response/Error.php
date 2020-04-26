<?php


namespace PaypalAddons\classes\API\Response;


class Error
{
    protected $errorCode;

    protected $message;

    public function getMessage()
    {
        return $this->message;
    }

    public function getCode()
    {
        return $this->errorCode;
    }

    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
}
