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
    <p class="h3">
        {l s='PayPal Account' mod='paypal'}
    </p>

    <p>
        {l s='In order to activate the module, you must connect your existing PayPal account or create a new one.' mod='paypal'}
    </p>

    {if isset($accountConfigured) && $accountConfigured}
        {if isset($method) && $method == 'EC'}
            <span class="mr-20">
				{l s='Connected account' mod='paypal'}
            </span>

            <strong>
                {if isset($paypal_api_user_name) && is_string($paypal_api_user_name)}
                    {$paypal_api_user_name|replace:'_api':'@'}
                {/if}
            </strong>
        {else}
            <span class="mr-20">
				{l s='Your account is connected now !' mod='paypal'}
            </span>
        {/if}


        <span class="btn btn-default ml-20" data-bt-logout>
				{l s='Logout' mod='paypal'}
        </span>
    {else}
        {if isset($urlOnboarding) && $urlOnboarding}
            <a href="{$urlOnboarding|addslashes}"
               class="btn btn-default">
                {l s='Connect or create PayPal account' mod='paypal'}
            </a>
        {else}
            <span class="btn btn-default" data-toggle="modal" data-target="#credentialBlock">
                {l s='Connect or create PayPal account' mod='paypal'}
            </span>
        {/if}

    {/if}
</div>

<div class="modal fade" id="credentialBlock" role="dialog" aria-labelledby="credentialBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4>{l s='API Credentials' mod='paypal'}</h4>
                <p>{l s='In order to accept PayPal Plus payments, please fill your API REST credentials.' mod='paypal'}</p>
                <ul>
                    <li>{l s='Access' mod='paypal'} <a target="_blank" href="https://developer.paypal.com/developer/applications/">{l s='https://developer.paypal.com/developer/applications/' mod='paypal'}</a></li>
                    <li>{l s='Log in or Create a business account' mod='paypal'}</li>
                    <li>{l s='Create a « REST API apps »' mod='paypal'}</li>
                    <li>{l s='Click « Show » en dessous de « Secret: »' mod='paypal'}</li>
                    <li>{l s='Copy/paste your « Client ID » and « Secret » below for each environment' mod='paypal'}</li>
                </ul>
                <hr/>
                <input type="hidden" class="method met" name="method" data-method-paypal/>
                <h4>{l s='Sandbox' mod='paypal'}</h4>
                <p>
                    <label for="sandbox_client_id">{l s='Client ID' mod='paypal'}</label>
                    <input type="text" id="sandbox_client_id" name="paypal_sandbox_clientid" value="{if isset($paypal_sandbox_clientid)}{$paypal_sandbox_clientid|escape:'htmlall':'UTF-8'}{/if}"/>
                </p>
                <p>
                    <label for="sandbox_secret">{l s='Secret' mod='paypal'}</label>
                    <input type="password" id="sandbox_secret" name="paypal_sandbox_secret" value="{if isset($paypal_sandbox_secret)}{$paypal_sandbox_secret|escape:'htmlall':'UTF-8'}{/if}"/>
                </p>
                <h4>{l s='Live' mod='paypal'}</h4>
                <ul>
                    <li>{l s='You can switch to "Live" environment on top right' mod='paypal'}</li>
                </ul>
                <p>
                    <label for="live_client_id">{l s='Client ID' mod='paypal'}</label>
                    <input type="text" id="live_client_id" name="paypal_live_clientid" value="{if isset($paypal_live_clientid)}{$paypal_live_clientid|escape:'htmlall':'UTF-8'}{/if}"/>
                </p>
                <p>
                    <label for="live_secret">{l s='Secret' mod='paypal'}</label>
                    <input type="password" id="live_secret" name="paypal_live_secret" value="{if isset($paypal_live_secret)}{$paypal_live_secret|escape:'htmlall':'UTF-8'}{/if}"/>
                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Cancel' mod='paypal'}</button>
                <button type="button" id="confirmCredentials" class="btn btn-primary">{l s='Confirm API Credentials' mod='paypal'}</button>
            </div>
        </div>
    </div>
</div>

