<?php


namespace PaypalAddons\classes\API\Response;


class ResponseAuthorizationVoid extends ResponseOrderRefund
{
    /**
     * @return string
     */
    public function getMessage()
    {
        $message = 'Authorization ' . $this->getIdTransaction() . ' is voided';
        return $message;
    }
}
