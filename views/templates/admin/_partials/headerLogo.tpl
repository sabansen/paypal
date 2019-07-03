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

{if isset($showWarningForUserBraintree) && $showWarningForUserBraintree}
  <div class="mb-20">
    <div class="alert alert-danger">
      {l s='Starting July 1st, 2019, Braintree payment solution is separated from PayPal module. There are 2 different
modules: PayPal official (v5.0.0) and Braintree official (v1.0.0). ' mod='paypal'}
      <br>
      {l s='You are using the v5.0.0 of PayPal module : the Braintree payment solution is not available via PayPal anymore. ' mod='paypal'}
      <br>
      {l s='You can continue to use Braintree by installing the new Braintree module available via addons.prestashop' mod='paypal'}
      <a href="https://addons.prestashop.com/">{l s='for free.' mod='paypal'}</a>
      <br>
      {l s='You will be able to migrate your account settings and orders created via Braintree once you install the new Braintree module' mod='paypal'}
      <br>
      {l s='Please note that we highly recommend to uninstall the PayPal module once you finish your Braintree settings migration.' mod='paypal'}
    </div>

    <div class="flex justify-content-center">
      <a class="btn btn-default"
        href="{$link->getAdminLink('AdminPayPalSetup', true, null, ['useWithoutBraintree' => 1])}">
        {l s='You would like to use PayPal without Braintree' mod='paypal'}
      </a>
    </div>
  </div>
{/if}

<div class="panel active-panel pp__flex pp__align-items-center">
	<div class="pp__pr-4">
		<img style="width: 135px" src="/modules/paypal/views/img/paypal.png">
	</div>
	<div class="pp__pl-5">
		<p>
			{l s='Activate the PayPal module to start selling to +250M PayPal customers around the globe' mod='paypal'}.
		</p>
		{if $page_header_toolbar_title !== 'Help' && $page_header_toolbar_title !== 'Logs'}
			<p>{l s='Activate in three easy steps' mod='paypal'}: </p>
			<p>
				<ul class="list-unstyled">
					<li>
						<a href="#pp_config_account" data-pp-link-settings>1) {l s='Connect below your existing PayPal account or create a new one' mod='paypal'}.</a>
					</li>
					<li>
						<a href="#pp_config_behavior" data-pp-link-settings>2) {l s='Adjust your Payment setting to either capture payments instantly (Capture), or after you confirm the order (Authorization)' mod='paypal'}.</a>
					</li>
					<li>
						<a href="#pp_config_environment" data-pp-link-settings>3) {l s='Make sure the module is set to Production mode' mod='paypal'}.</a>
					</li>
				</ul>
			</p>
			<p>{l s='Voilà! Your store is ready to accept payments!' mod='paypal'}</p>
		{/if}
	</div>
</div>

