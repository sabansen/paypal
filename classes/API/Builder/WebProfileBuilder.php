<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\InputFields;
use PayPal\Api\WebProfile;

class WebProfileBuilder extends BuilderAbstract
{
    /**
     * @return WebProfile
     * */
    public function build()
    {
        // Parameters for input fields customization.
        $inputFields = new InputFields();
        // Enables the buyer to enter a note to the merchant on the PayPal page during checkout.
        $inputFields->setAllowNote(false)
            // Determines whether or not PayPal displays shipping address fields on the experience pages. Allowed values: 0, 1, or 2. When set to 0, PayPal displays the shipping address on the PayPal pages. When set to 1, PayPal does not display shipping address fields whatsoever. When set to 2, if you do not pass the shipping address, PayPal obtains it from the buyer’s account profile. For digital goods, this field is required, and you must set it to 1.
            ->setNoShipping(1)
            // Determines whether or not the PayPal pages should display the shipping address and not the shipping address on file with PayPal for this buyer. Displaying the PayPal street address on file does not allow the buyer to edit that address. Allowed values: 0 or 1. When set to 0, the PayPal pages should not display the shipping address. When set to 1, the PayPal pages should display the shipping address.
            ->setAddressOverride(1);
        // #### Payment Web experience profile resource
        $webProfile = new WebProfile();
        // Name of the web experience profile. Required. Must be unique
        $webProfile->setName(\Tools::substr(\Configuration::get('PS_SHOP_NAME'), 0, 30) . uniqid())
            // Parameters for input field customization.
            ->setInputFields($inputFields)
            // Indicates whether the profile persists for three hours or permanently. Set to `false` to persist the profile permanently. Set to `true` to persist the profile for three hours.
            ->setTemporary(false);

        return $webProfile;
    }
}
