/**
 * 2007-2019 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author 202-ecommerce <tech@202-ecommerce.com>
 * @copyright 202-ecommerce
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

// Import functions for scrolling effect to necessary block on click
import {hoverConfig, hoverTabConfig} from './functions.js';

var CustomizeCheckout = {
  init() {
    // Scroll to necessary block
    $('[data-pp-link-settings]').on('click', (e) => {
      let el = $(e.target.attributes.href.value);
      if (el.length) {
        hoverConfig(el);
      } else {
        hoverTabConfig();
      }
    });

    // Remove effect after leaving cursor from the block
    $('.defaultForm').on('mouseleave', (e) => {
      $(e.currentTarget).removeClass('pp-settings-link-on');
    });

    if (typeof(paypalMethod) == 'string' && paypalMethod == 'MB') {
        CustomizeCheckout.checkConfigurations();
        $('input').change(CustomizeCheckout.checkConfigurations);
    }
  },

    checkConfigurations() {
      const paypalEcEnabled = $('input[name="paypal_mb_ec_enabled"]');
      const paypalApiCard = $('input[name="paypal_api_card"]');
      const EcOptions = [
          'paypal_express_checkout_in_context',
          'paypal_express_checkout_shortcut_cart',
          'paypal_api_advantages',
          'paypal_config_brand',
          'paypal_config_logo'
      ];
      const MbCardOptions = [
          'paypal_vaulting'
      ];

      if (paypalEcEnabled.prop('checked') == true) {
        EcOptions.forEach(CustomizeCheckout.showConfiguration);
        $('.message-context').show();
      } else {
          EcOptions.forEach(CustomizeCheckout.hideConfiguration);
          $('.message-context').hide();
      }

      if (paypalApiCard.prop('checked') == true) {
          MbCardOptions.forEach(CustomizeCheckout.showConfiguration);
      } else {
          MbCardOptions.forEach(CustomizeCheckout.hideConfiguration);
      }

    },

    // Hide block while switch inactive
    hideConfiguration(name) {
        let selector = `[name="${name}"]`;
        let configuration = $(selector);
        let formGroup = configuration.closest('.col-lg-9').closest('.form-group');

        formGroup.hide();
    },

    // Show block while switch is active
    showConfiguration(name) {
        let selector = `[name="${name}"]`;
        let configuration = $(selector);
        let formGroup = configuration.closest('.col-lg-9').closest('.form-group');

        formGroup.show();
    },

}

$(document).ready(() => CustomizeCheckout.init());
