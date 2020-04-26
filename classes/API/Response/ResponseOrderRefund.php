<?php


namespace PaypalAddons\classes\API\Response;


class ResponseOrderRefund extends Response
{
    /** @var string*/
    protected $refundId;

    /** @var string*/
    protected $status;

    /** @var float*/
    protected $amount;

    /** @var string*/
    protected $dateTransaction;

    /**
     * @return string
     */
    public function getRefundId()
    {
        return $this->refundId;
    }

    /**
     * @param string $refundId
     */
    public function setRefundId($refundId)
    {
        $this->refundId = $refundId;
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
        $message .= 'ID refund: ' . $this->getRefundId() . '; ';
        $message .= 'Total amount: ' . $this->getAmount() . '; ';
        $message .= 'Status: ' . $this->getStatus() . '; ';
        $message .= 'Transaction date: ' . $this->getDateTransaction() . '; ';

        return $message;
    }
}
