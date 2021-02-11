{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author 2007-2019 PayPal
 *  @author 2007-2013 PrestaShop SA <contact@prestashop.com>
 *  @author 2014-2019 202 ecommerce <tech@202-ecommerce.com>
*  @copyright PayPal
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*}

<div paypal-installment-settings>
    <div class="paypal-form-group pp__flex-align-center pp_mb-20">
        <div class="label">
            {l s='Enable the display of 4x banners' mod='paypal'}
        </div>

        <div class="configuration">
            <div class="pp__switch-field">
                <input class="pp__switch-input" type="radio" id="PAYPAL_ENABLE_INSTALLMENT_on" name="PAYPAL_ENABLE_INSTALLMENT" value="1" checked/>
                <label for="PAYPAL_ENABLE_INSTALLMENT_on" class="pp__switch-label on">Yes</label>
                <input class="pp__switch-input" type="radio" id="PAYPAL_ENABLE_INSTALLMENT_off" name="PAYPAL_ENABLE_INSTALLMENT" value="0" />
                <label for="PAYPAL_ENABLE_INSTALLMENT_off" class="pp__switch-label off">No</label>
            </div>
        </div>
    </div>

    <div class="paypal-form-group pp_mb-20">
        <div class="label">

        </div>

        <div class="configuration">
            <div>
                <div class="pp_mb-10">
                    <input
                            type="checkbox"
                            id="PAYPAL_INSTALLMENT_HOME_PAGE"
                            name="PAYPAL_INSTALLMENT_HOME_PAGE"
                            value="1"
                            {if isset($PAYPAL_INSTALLMENT_HOME_PAGE) && $PAYPAL_INSTALLMENT_HOME_PAGE}checked{/if}
                    >
                    <label for="PAYPAL_INSTALLMENT_HOME_PAGE">
                        {l s='Home Page' mod='paypal'}
                    </label>
                </div>

                <div class="pp_mb-10">
                    <input
                            type="checkbox"
                            id="PAYPAL_INSTALLMENT_CATEGORY_PAGE"
                            name="PAYPAL_INSTALLMENT_CATEGORY_PAGE"
                            value="1"
                            {if isset($PAYPAL_INSTALLMENT_CATEGORY_PAGE) && $PAYPAL_INSTALLMENT_CATEGORY_PAGE}checked{/if}
                    >
                    <label for="PAYPAL_INSTALLMENT_CATEGORY_PAGE">
                        {l s='Category Page' mod='paypal'}
                    </label>
                </div>

                <div class="pp_mb-10">
                    <input
                            type="checkbox"
                            id="PAYPAL_INSTALLMENT_PRODUCT_PAGE"
                            name="PAYPAL_INSTALLMENT_PRODUCT_PAGE"
                            value="1"
                            {if isset($PAYPAL_INSTALLMENT_PRODUCT_PAGE) && $PAYPAL_INSTALLMENT_PRODUCT_PAGE}checked{/if}
                    >
                    <label for="PAYPAL_INSTALLMENT_PRODUCT_PAGE">
                        {l s='Product Page' mod='paypal'}
                    </label>
                </div>

                <div>
                    <input
                            type="checkbox"
                            id="PAYPAL_INSTALLMENT_CART_PAGE"
                            name="PAYPAL_INSTALLMENT_CART_PAGE"
                            value="1"
                            {if isset($PAYPAL_INSTALLMENT_CART_PAGE) && $PAYPAL_INSTALLMENT_CART_PAGE}checked{/if}
                    >
                    <label for="PAYPAL_INSTALLMENT_CART_PAGE">
                        {l s='Cart/Checkout' mod='paypal'}
                    </label>
                </div>

            </div>
        </div>
    </div>

    <div class="paypal-form-group pp__flex-align-center pp_mb-20">
        <div class="label">
            {l s='Advanced options' mod='paypal'}
        </div>

        <div class="configuration">
            <div class="pp__switch-field">
                <input class="pp__switch-input" type="radio" id="PAYPAL_ADVANCED_OPTIONS_INSTALLMENT_on" name="PAYPAL_ADVANCED_OPTIONS_INSTALLMENT" value="1" checked/>
                <label for="PAYPAL_ADVANCED_OPTIONS_INSTALLMENT_on" class="pp__switch-label on">Yes</label>
                <input class="pp__switch-input" type="radio" id="PAYPAL_ADVANCED_OPTIONS_INSTALLMENT_off" name="PAYPAL_ADVANCED_OPTIONS_INSTALLMENT" value="0" />
                <label for="PAYPAL_ADVANCED_OPTIONS_INSTALLMENT_off" class="pp__switch-label off">No</label>
            </div>
        </div>
    </div>

    <div class="paypal-form-group pp__flex-align-center pp_mb-20">
        <div class="label">
            {l s='Widget code' mod='paypal'}
        </div>

        <div class="configuration">
            <div class="bootstrap widget-group">
                <input
                        type="text"
                        class="form-control"
                        id="installmentWidgetCode"
                        value="{literal}{widget name='paypal' action='banner4x'}{/literal}"
                        readonly>

                <span
                        class="input-group-addon"
                        style="cursor: pointer"
                        onclick="document.getElementById('installmentWidgetCode').select(); document.execCommand('copy')">
                <i class="icon-copy"></i>
                </span>
            </div>

        </div>
    </div>

    <div class="paypal-form-group pp__flex-align-center pp_mb-20">
        <div class="label">
            {l s='The styles for the home page and category pages' mod='paypal'}
        </div>

        <div class="configuration">
            <div class="installment-preview-wrap">
                <div class="bootstrap preview-setting">
                    <select name="PAYPAL_INSTALLMENT_COLOR" data-type="">
                        <option value="blue">blue</option>
                        <option value="black" selected="">black</option>
                        <option value="white">white</option>
                        <option value="gray">gray</option>
                        <option value="monochrome">monochrome</option>
                        <option value="grayscale">grayscale</option>
                    </select>
                </div>
                
                <div class="preview-container">
                    
                </div>
            </div>
            

        </div>
    </div>
</div>
