{*
* 2007-2020 PayPal
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
*  @author 2007-2020 PayPal
*  @copyright PayPal
*  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*
*}

{if $showPsCheckoutInfo}
    {include './_partials/messages/prestashopCheckoutInfo.tpl'}
{/if}

{if $showRestApiIntegrationMessage}
    {include './_partials/messages/restApiIntegrationMessage.tpl'}
{/if}

{if isset($need_rounding) && $need_rounding}
  {include './_partials/messages/roundingSettingsMessage.tpl'}
{/if}

{include './_partials/headerLogo.tpl'}

<div class="pp__flex">
    {if isset($formAccountSettings)}
      <div class="pp__flex-item-1 pp__mr-1 stretchHeightForm">
          {$formAccountSettings nofilter}{* the variable contains html code *}
      </div>
    {/if}

    {if isset($formEnvironmentSettings)}
      <div class="pp__flex-item-1 pp__mr-1 stretchHeightForm">
          {$formEnvironmentSettings nofilter}{* the variable contains html code *}
      </div>
    {/if}

    {if isset($formPaymentSettings)}
      <div class="pp__flex-item-1 pp__mr-1 stretchHeightForm">
          {$formPaymentSettings nofilter}{* the variable contains html code *}
      </div>
    {/if}

    {if isset($formStatus)}
      <div class="pp__flex-item-1 stretchHeightForm">
          {$formStatus nofilter}{* the variable contains html code *}
      </div>
    {/if}
</div>
