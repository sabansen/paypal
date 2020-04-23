<?php

namespace PaypalAddons\classes\API;

use PayPal\Api\Amount;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Exception\PayPalInvalidCredentialException;
use PayPal\Exception\PayPalMissingCredentialException;
use PayPal\Exception\PPConfigurationException;
use PayPal\Exception\PPConnectionException;
use PayPal\Exception\PPMissingCredentialException;
use PaypalAddons\classes\API\Response\PaypalOrderRefundResponse;
use PaypalAddons\classes\API\Response\Error;
use PaypalPPBTlib\AbstractMethod;
use Symfony\Component\VarDumper\VarDumper;


class PaypalOrderRefund
{
    /** @var $paypalOrder \PaypalOrder*/
    protected $paypalOrder;

    /** @var $method AbstractMethod*/
    protected $method;

    public function __construct(\PaypalOrder $paypalOrder)
    {
        $this->paypalOrder = $paypalOrder;
        $this->method = AbstractMethod::load($paypalOrder->method);
    }

    /**
     * @return PaypalOrderRefundResponse
     */
    public function execute()
    {
        $response = new PaypalOrderRefundResponse();
        try {
            $sale = Sale::get($this->paypalOrder->id_transaction, $this->method->_getCredentialsInfo($this->paypalOrder->sandbox));

            // Includes both the refunded amount (to Payer)
            // and refunded fee (to Payee). Use the $amt->details
            // field to mention fees refund details.
            $amt = new Amount();
            $amt->setCurrency($sale->getAmount()->getCurrency())
                ->setTotal($sale->getAmount()->getTotal());
            $refundRequest = new RefundRequest();
            $refundRequest->setAmount($amt);

            $refundSaleResponse = $sale->refundSale($refundRequest, $this->method->_getCredentialsInfo($this->paypalOrder->sandbox));

            $response->setSuccess(true)
                ->setRefundId($refundSaleResponse->id)
                ->setStatus($refundSaleResponse->state)
                ->setTotalAmount($refundSaleResponse->total_refunded_amount->value)
                ->setCurrency($refundSaleResponse->total_refunded_amount->currency)
                ->setSaleId($refundSaleResponse->sale_id)
                ->setDateTransaction($this->method->getDateTransaction($refundSaleResponse));

            return $response;
        } catch (PPConnectionException $e) {
            $responseError = new Error();
            $responseError->setMessage('Error connecting to ' . $e->getUrl());
        } catch (PPMissingCredentialException $e) {
            $responseError = new Error();
            $responseError->setMessage($e->errorMessage());
        } catch (PPConfigurationException $e) {
            $responseError = new Error();
            $responseError->setMessage('Invalid configuration. Please check your configurations');
        } catch (PayPalConnectionException $e) {
            $decoded_message = \Tools::jsonDecode($e->getData());
            $responseError = new Error();
            $responseError->setMessage($decoded_message->message);
        } catch (PayPalInvalidCredentialException $e) {
            $responseError = new Error();
            $responseError->setMessage($e->errorMessage());
        } catch (PayPalMissingCredentialException $e) {
            $responseError = new Error();
            $responseError->setMessage('Invalid configuration. Please check your configurations');
        } catch (\Exception $e) {
            $responseError = new Error();
            $responseError->setMessage($e->getMessage());
        }

        $response->setError($responseError);
        return $response;
    }
}
