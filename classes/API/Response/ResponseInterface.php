<?php

namespace PaypalAddons\classes\API\Response;

interface ResponseInterface
{
    public function isSuccess();

    /**
     * @return ErrorInterface
     */
    public function getError();

    public function getData();
}
