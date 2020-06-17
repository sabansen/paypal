{*
* 2007-2020 PrestaShop
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
 *  @author 202 ecommerce <tech@202-ecommerce.com>
*  @copyright PayPal
*  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*
*}

<div>

    {if isset($method) && in_array($method, array('EC', 'PPP'))}
        <p class="h3">
            {if isset($accountConfigured) && $accountConfigured}<i class="icon-check text-success"></i>{/if}
            {l s='PayPal Account' mod='paypal'}
            {if isset($accountConfigured) && $accountConfigured}{l s='connected' mod='paypal'}{/if}
        </p>

        <p>
            {l s='In order to activate the module, you must connect your existing PayPal account or create a new one.' mod='paypal'}
        </p>
    {/if}

    {if isset($accountConfigured) && $accountConfigured}

        {if isset($method) && $method == 'MB'}
            {include './mbCredentialsForm.tpl'}
        {/if}

        {if isset($method) && in_array($method, ['EC', 'PPP'])}
            <span class="btn btn-default" id="logoutAccount">
              <i class="icon-signout"></i>
				      {l s='Logout' mod='paypal'}
            </span>
        {/if}
    {else}
        {if isset($method) && $method == 'MB'}
            {include './mbCredentialsForm.tpl'}
        {elseif isset($country_iso) && in_array($country_iso, ['IN', 'JP'])}
            <span class="btn btn-default" data-toggle="modal" data-target="#credentialBlockEC">
                {l s='Connect or create PayPal account' mod='paypal'}
            </span>
        {elseif isset($method) && in_array($method, ['EC', 'PPP'])}
          <a href="{$urlOnboarding|addslashes}"
             target="_blank"
             data-paypal-button
             data-paypal-onboard-complete="onboardCallback"
             class="btn btn-default">
              <i class="icon-signin"></i>
              {l s='Connect or create PayPal account' mod='paypal'}
          </a>
        {/if}

    {/if}
</div>

{if isset($country_iso) && in_array($country_iso, ['IN', 'JP'])}
    <div class="modal fade" id="credentialBlockEC" role="dialog" aria-labelledby="credentialBlockEC" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>{l s='API Credentials' mod='paypal'}</h4>
                    <p>{l s='In order to accept PayPal Plus payments, please fill in your API REST credentials.' mod='paypal'}</p>
                    <ul>
                        <li>{l s='Access' mod='paypal'}
                            <a target="_blank" href="https://www.{if $mode == 'SANDBOX'}sandbox.{/if}paypal.com/">https://www.{if $mode == 'SANDBOX'}sandbox.{/if}paypal.com/</a>
                        </li>
                        <li>{l s='Log in or Create a business account' mod='paypal'}</li>
                        <li>{l s='Access to' mod='paypal'} <a target="_blank" href="https://www.{if $mode == 'SANDBOX'}sandbox.{/if}paypal.com/businessprofile/mytools/apiaccess/firstparty/signature">{l s='API NVP/SOAP integration' mod='paypal'}</a></li>
                        <li>{l s='Click « Show » on the right of credentials' mod='paypal'}</li>
                        <li>{l s='Copy/paste your API credentials below for %s environment' sprintf=[$mode] mod='paypal'} </li>
                    </ul>
                    <hr/>

		                <input type="hidden" name="id_shop" value="{if isset($idShop)}{$idShop}{/if}"/>
                    <h4>{l s='API Credentials for' mod='paypal'} {$mode}</h4>
                    {include './ecCredentialFields.tpl'}

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Cancel' mod='paypal'}</button>
                    <button type="button" id="confirmCredentials" class="btn btn-primary">{l s='Confirm API Credentials' mod='paypal'}</button>
                </div>
            </div>
        </div>
    </div>
{/if}

