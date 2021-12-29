<?php

namespace PaypalAddons\services;

use Media;

class PaypalMedia
{
    /**
     * @return array
     */
    public function getJqueryPath()
    {
        if (version_compare(_PS_VERSION_, '1.7.7', '<')) {
            return Media::getJqueryPath();
        }

        return [];
    }
}
