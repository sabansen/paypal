/**
 * 2007-2019 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author 2007-2019 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 * @copyright PayPal
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 *
 */


export  const Onboarding = {
  handleResponse (authCode, sharedId) {
    $.ajax({
      url: controllerUrl,
      type: 'POST',
      data: {
        ajax: true,
        action: 'handleOnboardingResponse',
        authCode: authCode,
        sharedId: sharedId,
      },
      success: function(response) {
        console.log(response);
      }
    });
  },

  test (authCode, sharedId) {

  },

  addPaypalLib () {
    let script = document.createElement('script');
    script.src = paypalOnboardingLib;
    document.body.appendChild(script);
  }
};
