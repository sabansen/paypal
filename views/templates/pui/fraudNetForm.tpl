{*
* 2007-2022 PayPal
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
*  @author 2007-2022 PayPal
*  @copyright PayPal
*  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*
*}

{literal}
<script type="application/json" fncls="fnparams-dede7cc5-15fd-4c75-a9f4-36c430ee3a99">
  {
    "f":"{/literal}{$sessionId}{literal}",
    "s":"{/literal}{$sourceId}{literal}",
    "sandbox": {/literal}{$isSandbox}{literal}
  }
</script>
{/literal}

<script type="text/javascript" src="https://c.paypal.com/da/r/fb.js"></script>

{*<script>*}
{*  window.addEventListener('load', function() {*}
{*      var script = document.createElement('script');*}
{*      script.src = 'https://c.paypal.com/da/r/fb.js';*}
{*      document.body.appendChild(script);*}
{*  })*}
{*</script>*}

<form
        class="form"
        action="{Context::getContext()->link->getModuleLink('paypal','puiInit',['sessionId' => $sessionId], true)}"
        method="POST"
>

  <div class="form-group row">
    <div class="col-lg-2">
      <label class="form-label" for="paypal_pui_firstname">{l s='First name' mod='paypal'}</label>

    </div>

    <div class="col-lg-6">
      <input
              required
              class="form-control"
              type="text"
              name="paypal_pui_firstname"
              id="paypal_pui_firstname"
              value="{if isset($userData)}{$userData->getFirstName()}{/if}">
    </div>
  </div>

  <div class="form-group row">
    <div class="col-lg-2">
      <label class="form-label" for="paypal_pui_lastname">{l s='Last name' mod='paypal'}</label>
    </div>

    <div class="col-lg-6">
      <input
              required
              class="form-control"
              type="text"
              name="paypal_pui_lastname"
              id="paypal_pui_lastname"
              value="{if isset($userData)}{$userData->getLastName()}{/if}">
    </div>
  </div>

  <div class="form-group row">
    <div class="col-lg-2">
      <label class="form-label" for="paypal_pui_email">{l s='Email' mod='paypal'}</label>
    </div>

    <div class="col-lg-6">
      <input
              required
              class="form-control"
              type="text"
              name="paypal_pui_email"
              id="paypal_pui_email"
              value="{if isset($userData)}{$userData->getEmail()}{/if}">
    </div>
  </div>

  <div class="form-group row">
    <div class="col-lg-2">
      <label class="form-label" for="paypal_pui_birhday">{l s='Birth day' mod='paypal'}</label>
    </div>

    <div class="col-lg-6">
      <input
              required
              class="form-control"
              type="date"
              name="paypal_pui_birhday"
              id="paypal_pui_birhday"
              {literal}pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"{/literal}
              value="{if isset($userData)}{$userData->getBirth()}{/if}">
    </div>
  </div>

  <div class="form-group row">
    <div class="col-lg-2">
      <label class="form-label" for="paypal_pui_phone">{l s='Phone' mod='paypal'}</label>
    </div>

    <div class="col-lg-6">
      <input
              required
              class="form-control"
              type="text"
              name="paypal_pui_phone"
              id="paypal_pui_phone"
              placeholder="{l s='Example: 6912345678' mod='paypal'}"
              value="{if isset($userData)}{$userData->getPhone()}{/if}">
    </div>
  </div>

  <div class="alert alert-info">
      {{l s='By clicking on the button, you agree to the [a @href1@]terms of payment[/a] and [a @href2@]performance of a risk check[/a] from the payment partner, Ratepay.' mod='paypal'}|paypalreplace:['@href1@' => {'https://www.ratepay.com/legal-payment-terms'}, '@target@' => {'target="blank"'}, '@href2@' => {'https://www.ratepay.com/legal-payment-dataprivacy'}, '@target@' => {'target="blank"'}] nofilter}
      {{l s='You also agree to PayPal’s [a @href1@]privacy statement[/a].' mod='paypal'}|paypalreplace:['@href1@' => {'https://www.paypal.com/us/webapps/mpp/ua/privacy-full?_ga=1.129822860.1014894959.1637147141'}, '@target@' => {'target="blank"'}] nofilter}
      {l s='If your request to purchase Upon invoice is accepted, the purchase price claim will be assigned to Ratepay, and you may only pay Ratepay, not the merchant.' mod='paypal'}
  </div>

  <div class="form-group row">
    <div class="col-lg-12">
      <button class="btn btn-primary">{l s='Submit' mod='paypal'}</button>
    </div>
  </div>
</form>
