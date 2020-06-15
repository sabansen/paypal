<?php


namespace PaypalAddons\classes\API\Response;


class ResponseOrderRefund extends Response
{
    /** @var string*/
    protected $idTransaction;

    /** @var string*/
    protected $status;

    /** @var float*/
    protected $amount;

    /** @var string*/
    protected $dateTransaction;

    /** @var bool*/
    protected $alreadyRefunded;

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
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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

    public function getMessage()
    {
        $message = '';
        $message .= 'Refund Transaction Id: ' . $this->getIdTransaction() . '; ';
        $message .= 'Total amount: ' . $this->getAmount() . '; ';
        $message .= 'Status: ' . $this->getStatus() . '; ';
        $message .= 'Transaction date: ' . $this->getDateTransaction() . '; ';

        return $message;
    }

    /**
     * @return bool
     */
    public function isAlreadyRefunded()
    {
        return (bool) $this->alreadyRefunded;
    }

    /**
     * @param bool $alreadyRefunded
     * @return ResponseOrderRefund
     */
    public function setAlreadyRefunded($alreadyRefunded)
    {
        $this->alreadyRefunded = (bool) $alreadyRefunded;
        return $this;
    }


}
