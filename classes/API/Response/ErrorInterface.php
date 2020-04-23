<?php

namespace PaypalAddons\classes\API\Response;

interface ErrorInterface
{
    public function getCode();

    public function getMessage();
}
