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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="bootstrap">
    <div class="bootstrap alert alert-warning">
        {l s='Note: the new version of the module (v3.14.0) requires to add your credentials details if you need to change your account settings.' mod='paypal'}
    </div>
</div>

<p>
    {l s='To find your API Keys, please follow those steps:' mod='paypal'}
</p>
<ul>
    <li>
        {{l s='Log into your [a @href1@]account[/a]' mod='paypal'}|paypalreplace:['@href1@' => {'https://www.braintreegateway.com/login'}, '@target@' => {'target="blank"'}]}
    </li>
    <li>
        {l s='Click on Parameters (the working wheel logo)' mod='paypal'}
    </li>
    <li>
        {l s='Click on API' mod='paypal'}
    </li>
    <li>
        {l s='Click the "Generate New API Key"' mod='paypal'}
    </li>
    <li>
        {l s='Click on "View" in the "Private key" column' mod='paypal'}
    </li>
    <li>
        {l s='Copy your "Private Key", "Public Key" and "Merchant ID" and paste them below:' mod='paypal'}
    </li>
</ul>

<p>
    {{l s='To retrieve sandbox API Keys please repeat the steps by connecting to [a @href1@]sandbox account[/a] or creating a new [a @href2@]one[/a]' mod='paypal'}|paypalreplace:['@href1@' => {'https://sandbox.braintreegateway.com/login'}, '@href2@' => {'https://www.braintreepayments.com/sandbox'},  '@target@' => {'target="blank"'}]}
</p>

<div>
    <div class="h3">{l s='Live' mod='paypal'}</div>
    <hr>
    <dl>
        <dt><label for="paypal_braintree_pub_key_live">{l s='Public key' mod='paypal'} : </label></dt>
        <dd>
            <input
                    type='text' size="85"
                    name="paypal_braintree_pub_key_live"
                    id="paypal_braintree_pub_key_live"
                    value="{if isset($paypal_braintree_pub_key_live)}{$paypal_braintree_pub_key_live|escape:'htmlall':'utf-8'}{/if}"
                    autocomplete="off"
            />
        </dd>

        <dt><label for="paypal_braintree_priv_key_live">{l s='Private key' mod='paypal'} : </label></dt>
        <dd>
            <input
                    type='password'
                    size="85"
                    name="paypal_braintree_priv_key_live"
                    id="paypal_braintree_priv_key_live"
                    value="{if isset($paypal_braintree_priv_key_live)}{$paypal_braintree_priv_key_live|escape:'htmlall':'utf-8'}{/if}"
                    autocomplete="off"
            />
        </dd>

        <dt><label for="paypal_braintree_merchant_id_live">{l s='Merchant ID' mod='paypal'} : </label></dt>
        <dd>
            <input
                    type='text'
                    size="85"
                    name="paypal_braintree_merchant_id_live"
                    id="paypal_braintree_merchant_id_live"
                    value="{if isset($paypal_braintree_merchant_id_live)}{$paypal_braintree_merchant_id_live|escape:'htmlall':'utf-8'}{/if}"
                    autocomplete="off"
            />
        </dd>
    </dl>
</div>

<div class="paypal-clear"></div>

<div>
    <div class="h3">{l s='Sandbox' mod='paypal'}</div>
    <hr>

    <dl>
        <dt><label for="paypal_braintree_pub_key_sandbox">{l s='Public key' mod='paypal'} : </label></dt>
        <dd>
            <input
                    type='text' size="85"
                    name="paypal_braintree_pub_key_sandbox"
                    id="paypal_braintree_pub_key_sandbox"
                    value="{if isset($paypal_braintree_pub_key_sandbox)}{$paypal_braintree_pub_key_sandbox|escape:'htmlall':'utf-8'}{/if}"
                    autocomplete="off"
            />
        </dd>

        <dt><label for="paypal_braintree_priv_key_sandbox">{l s='Private key' mod='paypal'} : </label></dt>
        <dd>
            <input
                    type='password'
                    size="85"
                    name="paypal_braintree_priv_key_sandbox"
                    id="paypal_braintree_priv_key_sandbox"
                    value="{if isset($paypal_braintree_priv_key_sandbox)}{$paypal_braintree_priv_key_sandbox|escape:'htmlall':'utf-8'}{/if}"
                    autocomplete="off"
            />
        </dd>

        <dt><label for="paypal_braintree_merchant_id_sandbox">{l s='Merchant ID' mod='paypal'} : </label></dt>
        <dd>
            <input
                    type='text'
                    size="85"
                    name="paypal_braintree_merchant_id_sandbox"
                    id="paypal_braintree_merchant_id_sandbox"
                    value="{if isset($paypal_braintree_merchant_id_sandbox)}{$paypal_braintree_merchant_id_sandbox|escape:'htmlall':'utf-8'}{/if}"
                    autocomplete="off"
            />
        </dd>
    </dl>
</div>

<div class="paypal-clear"></div>