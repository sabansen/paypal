<?php

namespace PaypalAddons\classes\API\Builder;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Refund;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;
use \PayPal\Api\ShippingAddress;
use PaypalPPBTlib\AbstractMethod;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;
use Symfony\Component\VarDumper\VarDumper;

class PaymentBuilder extends BuilderAbstract
{
    /**
     * @return Payment
     */
    public function build()
    {
        $payment = new Payment();
        $payment->setIntent($this->method->getIntent())
            ->setPayer($this->getPayer())
            ->setTransactions([$this->getTransaction()])
            ->setRedirectUrls($this->getRedirectUrls())
            ->setExperienceProfileId($this->method->getExperienceProfileId());

        return $payment;
    }

    /**
     * @return Transaction
     */
    protected function getTransaction()
    {
        return $this->paypalApiManager->get($this->method->getBuilderClass('transaction'))->build();
    }

    /**
     * @return RedirectUrls
     */
    protected function getRedirectUrls()
    {
        return $this->paypalApiManager->get($this->method->getBuilderClass('redirectUrls'))->build();
    }

    /**
     * @return Payer
     */
    protected function getPayer()
    {
        return $this->paypalApiManager->get($this->method->getBuilderClass('payer'))->build();
    }
}
