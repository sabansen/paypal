<?php


namespace PaypalAddons\classes\API\Response;


class PaypalOrderRefundResponse extends Response
{
    /** @var string*/
    protected $refundId;

    /** @var string*/
    protected $status;

    /** @var float*/
    protected $totalAmount;

    /** @var string*/
    protected $currency;

    /** @var string*/
    protected $saleId;

    /** @var string*/
    protected $dateTransaction;

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
    }

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
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getSaleId()
    {
        return $this->saleId;
    }

    /**
     * @param string $saleId
     */
    public function setSaleId($saleId)
    {
        $this->saleId = $saleId;
        return $this;
    }

    public function __toString()
    {
        $message = '';
        $message .= 'ID refund: ' . $this->getRefundId() . '; ';
        $message .= 'ID sale: ' . $this->getSaleId() . '; ';
        $message .= 'Total amount: ' . $this->getTotalAmount() . '; ';
        $message .= 'Status: ' . $this->getStatus() . '; ';
        $message .= 'Transaction date: ' . $this->getDateTransaction() . '; ';

        return $message;
    }
}
