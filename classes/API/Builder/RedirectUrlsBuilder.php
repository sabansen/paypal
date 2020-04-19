<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\RedirectUrls;

class RedirectUrlsBuilder extends BuilderAbstract
{
    public function build()
    {
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->method->getPaymentReturnUrl())
            ->setCancelUrl($this->context->link->getPageLink('order', true));

        return $redirectUrls;
    }
}
