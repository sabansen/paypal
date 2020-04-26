<?php


namespace PaypalAddons\classes\API\Response;


class ResponseOrderCreate extends Response
{
    /** @var string*/
    protected $approveLink;

    /** @var string*/
    protected $paymentId;

    /** @var int*/
    protected $statusCode;

    /**
     * @return string
     */
    public function getApproveLink()
    {
        return $this->approveLink;
    }

    /**
     * @param string $approveLink
     */
    public function setApproveLink($approveLink)
    {
        $this->approveLink = $approveLink;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}
