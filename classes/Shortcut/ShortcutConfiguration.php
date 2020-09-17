<?php
/**
 * 2007-2020 PayPal
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2020 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\classes\Shortcut;


class ShortcutConfiguration
{
    const SHOW_ON_SIGNUP_STEP = 'PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_SIGNUP';

    const SHOW_ON_CART_PAGE = 'PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART';

    const SHOW_ON_PRODUCT_PAGE = 'PAYPAL_EXPRESS_CHECKOUT_SHORTCUT';

    const CUSTOMIZE_STYLE = 'PAYPAL_EXPRESS_CHECKOUT_CUSTOMIZE_SHORTCUT_STYLE';

    const DISPLAY_MODE_PRODUCT = 'PAYPAL_EXPRESS_CHECKOUT_DISPLAY_MODE_PRODUCT';

    const DISPLAY_MODE_SIGNUP = 'PAYPAL_EXPRESS_CHECKOUT_DISPLAY_MODE_SIGNUP';

    const DISPLAY_MODE_CART = 'PAYPAL_EXPRESS_CHECKOUT_DISPLAY_MODE_CART';

    const DISPLAY_MODE_TYPE_HOOK = 1;

    const DISPLAY_MODE_TYPE_WIDGET = 2;

    const HOOK_PRODUCT_ACTIONS = 'displayProductActions';

    const HOOK_REASSURANCE = 'displayReassurance';

    const HOOK_AFTER_PRODUCT_THUMBS = 'displayAfterProductThumbs';

    const HOOK_AFTER_PRODUCT_ADDITIONAL_INFO = 'displayProductAdditionalInfo';

    const HOOK_FOOTER_PRODUCT = 'displayFooterProduct';

    const HOOK_EXPRESS_CHECKOUT = 'displayExpressCheckout';

    const HOOK_SHOPPING_CART_FOOTER = 'displayShoppingCartFooter';

    const PRODUCT_PAGE_HOOK = 'PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_HOOK_PRODUCT';

    const CART_PAGE_HOOK = 'PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_HOOK_CART';
}
