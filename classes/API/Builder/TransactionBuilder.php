<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\Amount;
use PayPal\Api\ItemList;
use PayPal\Api\Transaction;

class TransactionBuilder extends BuilderAbstract
{
    /**
     * @return Transaction
     */
    public function build()
    {
        $transaction = new Transaction();
        $itemList = $this->getItemList();
        $amount = $this->getAmount();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setInvoiceNumber(uniqid());

        return $transaction;
    }

    /**
     * @return ItemList
     */
    protected function getItemList()
    {
        return $this->paypalApiManager->get($this->method->getBuilderClass('itemList'))->build();
    }

    /**
     * @return Amount
     */
    protected function getAmount()
    {
        return $this->paypalApiManager->get($this->method->getBuilderClass('amount'))->build();
    }
}
