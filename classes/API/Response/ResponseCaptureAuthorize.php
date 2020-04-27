<?php


namespace PaypalAddons\classes\API\Response;


class ResponseCaptureAuthorize extends Response
{
    /** @var string*/
    protected $idTransaction;

    /** @var string*/
    protected $status;

    /** @var string*/
    protected $dateTransaction;

    /**
     * @return string
     */
    public function getIdTransaction()
    {
        return $this->idTransaction;
    }

    /**
     * @param string $idTransaction
     */
    public function setIdTransaction($idTransaction)
    {
        $this->idTransaction = $idTransaction;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateTransaction()
    {
        return $this->dateTransaction;
    }

    /**
     * @param string $dateTransaction
     */
    public function setDateTransaction($dateTransaction)
    {
        $this->dateTransaction = $dateTransaction;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $message = 'Authorizaton is captured; ';
        $message .= 'Transaction Id: ' . $this->getIdTransaction() . '; ';
        $message .= 'Status: ' . $this->getStatus() . ';';

        return $message;
    }
}
