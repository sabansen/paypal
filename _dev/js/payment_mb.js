/**
 * 2007-2019 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

const PayPalMB = {

    ppp: null,

    config: {},

    init(approvalUrlPPP, selectorId, mode, payer) {
        this.config = {
            "approvalUrl": approvalUrlPPP,
            "placeholder": selectorId,
            "mode": mode,
            "payerEmail": payer.email,
            "payerFirstName": payer.firstName,
            "payerLastName": payer.lastName,
            "payerTaxId": payer.taxId
        }
    },

    initCheckout() {
        this.ppp = PAYPAL.apps.PPP(this.config);
    }

}

PayPalMB.init(approvalUrlPPP, "ppplus-mb", paypalMode, payerInfo);