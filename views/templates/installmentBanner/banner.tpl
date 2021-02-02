{*
* 2007-2021 PayPal
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
*  @author 2007-2021 PayPal
*  @copyright PayPal
*  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*
*}

{include file='../_partials/javascript.tpl'}

<div installment-container>
  <div banner-container>
    <div
            data-pp-message
            data-pp-placement="{$placement}"
            data-pp-style-layout="{$layout}"
            data-pp-style-ratio="20x1"
            {if isset($amount)}data-pp-amount="{$amount}"{/if}
            {if isset($color)}data-pp-style-color="{$color}"{/if}
    ></div>
  </div>

  <div script-container>

  </div>
</div>

<script>
  var Banner = {
      init: function() {
          var script = document.createElement('script');
          var scriptContainer = document.querySelector('[installment-container] [script-container]');

          script.setAttribute('src', installmentLib);
          scriptContainer.innerHTML = '';
          scriptContainer.appendChild(script);
      }
  };

  document.addEventListener('initPaypalBanner', Banner.init)
</script>
