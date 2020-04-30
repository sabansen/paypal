<?php


namespace PaypalAddons\classes\API;


use PaypalAddons\classes\API\Request\RequestAbstract;

interface PaypalApiManagerInterface
{
    /**
     * @return RequestAbstract
     */
    public function getAccessTokenRequest();

    /**
     * @return RequestAbstract
     */
    public function getOrderRequest();

    /**
     * @return RequestAbstract
     */
    public function getOrderCaptureRequest($idPayment);

    /**
     * @return RequestAbstract
     */
    public function getOrderAuthorizeRequest($idPayment);

    /**
     * @return RequestAbstract
     */
    public function getOrderRefundRequest(\PaypalOrder $paypalOrder);

    /**
     * @return RequestAbstract
     */
    public function getOrderPartialRefundRequest(\PaypalOrder $paypalOrder, $amount);

    /**
     * @return RequestAbstract
     */
    public function getAuthorizationVoidRequest(\PaypalOrder $orderPayPal);

    /**
     * @return RequestAbstract
     */
    public function getCaptureAuthorizeRequest(\PaypalOrder $paypalOrder);

    /**
     * @return RequestAbstract
     */
    public function getOrderGetRequest($idPayment);

    /**
     * @return RequestAbstract
     */
    public function geOrderPatchRequest($idPayment);
}
