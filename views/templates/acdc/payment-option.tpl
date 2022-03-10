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

<!-- Start views/templates/acdc/payment-option.tpl. Module Paypal -->
{assign var=scInitController value=Context::getContext()->link->getModuleLink('paypal', 'ScInit')}
{assign var=validationController value=Context::getContext()->link->getModuleLink('paypal', 'pppValidation')}

{include file = "{$psPaypalDir}/views/templates/_partials/javascript.tpl" assign=javascriptBlock}
{block name='head'}
    {$javascriptBlock nofilter}
{/block}

<table border="0" align="center" valign="top" bgcolor="#FFFFFF" style="width: 39%">
  <tr>
    <td colspan="2">
      <div id="paypal-acdc-button-container"></div>
    </td>
  </tr>
  <tr><td colspan="2">&nbsp;</td></tr>
</table>

<div align="center"> or </div>

<!-- Advanced credit and debit card payments form -->
<div class="paypal-acdc-card_container">
  <form id="card-form">

    <div>
      <label for="card-number">Card Number</label>
      <div id="card-number" class="card_field"></div>
    </div>

    <div>
      <label for="expiration-date">Expiration Date</label>
      <div id="expiration-date" class="card_field"></div>
    </div>

    <div>
      <label for="cvv">CVV</label>
      <div id="cvv" class="card_field"></div>
    </div>

    <div>
      <button value="submit" id="submit" class="btn">Pay</button>
    </div>
  </form>
</div>


<script>
    function waitPaypalAcdcSDKIsLoaded() {
        if (typeof totPaypalAcdcSdk === 'undefined' || typeof ACDC === 'undefined') {
            setTimeout(waitPaypalAcdcSDKIsLoaded, 200);

            return;
        }

        acdcObj = new ACDC({
            button: '#paypal-acdc-button-container',
            controller: '{$scInitController nofilter}',
            validationController: '{$validationController nofilter}'
        });
        acdcObj.initButton();
        acdcObj.initHostedFields();
    }

    waitPaypalAcdcSDKIsLoaded();
</script>

<!-- End views/templates/acdc/payment-option.tpl. Module Paypal -->
