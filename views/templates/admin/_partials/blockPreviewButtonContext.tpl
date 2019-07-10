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
* @author PrestaShop SA <contact@prestashop.com>
  * @copyright 2007-2019 PrestaShop SA
  * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
  * International Registered Trademark & Property of PrestaShop SA
  *}

<div class="block-preview-button-context pp__flex">
	<div class="pp__py-2 pp__px-1">
		<div class="pp__mb-2">
			<input type="checkbox" {if isset($paypal_express_checkout_shortcut_cart) &&
				$paypal_express_checkout_shortcut_cart}checked{/if} name="paypal_express_checkout_shortcut_cart" value="1"
				   id="paypal_express_checkout_shortcut_cart">
			<label for="paypal_express_checkout_shortcut_cart" class="control-label">
				<span
					class="label-tooltip"
					data-toggle="tooltip"
					data-html="true"
					title=""
					data-original-title="<p style='text-align:left'>Activating Express Checkout Shortcut on Cart Page is recommended in specific cases only:</p>
						<p style='text-align:left'>- Multi Products web sites</p>"
					>
					{l s="Cart Page" mod="paypal"}
				</span>
			</label>
		</div>
		<img src="/modules/paypal/views/img/cart_page_button.png" class="img-responsive" alt="cart_page">
	</div>

	<div class="pp__py-2 pp__px-1">
		<div class="pp__mb-2">
			<input type="checkbox" {if isset($paypal_express_checkout_shortcut) &&
				$paypal_express_checkout_shortcut}checked{/if} name="paypal_express_checkout_shortcut"
				id="paypal_express_checkout_shortcut" value="1">
			<label for="paypal_express_checkout_shortcut"  class="control-label">
				<span
					class="label-tooltip"
					data-toggle="tooltip"
					data-html="true"
					title=""
					data-original-title="<p style='text-align:left'>Activating Express Checkout Shortcut on Product Pages is recommended in specific cases only:</p>
						<p style='text-align:left'>- Mono Product</p>
						<p style='text-align:left'>- Digital Goods</p>
						<p style='text-align:left'>Example:</p>
						<p style='text-align:left'>- Parking lot rental</p>
						<p style='text-align:left'>- Escape game reservation</p>
						<p style='text-align:left'>- Booking services</p>
						<p style='text-align:left'>- Single product sale</p>"
					>
					{l s="Product Pages" mod="paypal"}
				</span>
			</label>
		</div>
		<img src="/modules/paypal/views/img/product_page_button.png" alt="product_page_button">
	</div>
</div>

<div class="alert alert-info d-inline-block">
    {l s='Shortcut converts best when on both product pages & cart page' mod='paypal'}
</div>

