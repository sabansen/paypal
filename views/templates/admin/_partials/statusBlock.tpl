{*
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div>
    <p>
        {l s='Merchant Country:' mod='paypal'} <b>{$merchantCountry|escape:'utf':'htmlall'}</b>
    </p>

    <p>
        {l s='To  modify country:' mod='paypal'}
        <a href="{$link->getAdminLink('AdminLocalization', true)}"
           target="_blank">
            {l s='International > Localization' mod='paypal'}
        </a>
    </p>

    <p>
        <span class="btn btn-default" id="btn-check-requirements">{l s='Check requirements' mod='paypal'}</span>
    </p>

    <ul class="list-unstyled">
        <li>
            {if isset($sslActivated) && $sslActivated}
                <i class="icon-check" style="color: green"></i>
            {else}
                <i class="icon-remove" style="color: red"></i>
            {/if}
            {l s='SSL is activated' mod='paypal'}

        </li>

        <li>
            {if isset($tlsVersion) && $tlsVersion['status']}
                <i class="icon-check" style="color: green"></i>
                {l s='PHP cURL extension must be enabled on your server' mod='paypal'}
            {elseif isset($tlsVersion) && $tlsVersion['status'] == false}
                <i class="icon-remove" style="color: red"></i>
                {l s='PHP cURL extension must be enabled on your server' mod='paypal'}. {$tlsVersion['error_message']}
            {/if}

        </li>

        <li>
            {if isset($accountConfigured) && $accountConfigured}
                <i class="icon-check" style="color: green"></i>
            {else}
                <i class="icon-remove" style="color: red"></i>
            {/if}
            {l s='You must connect your Braintree account' mod='paypal'}
        </li>
    </ul>
</div>



