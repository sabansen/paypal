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

    config: null,

    paymentId: null,

    setConfig(paymentInfo, selectorId) {
        this.config = {
            "approvalUrl": paymentInfo.approvalUrlPPP,
            "placeholder": selectorId,
            "mode": paymentInfo.paypalMode,
            "payerEmail": paymentInfo.payerInfo.email,
            "payerFirstName": paymentInfo.payerInfo.first_name,
            "payerLastName": paymentInfo.payerInfo.last_name,
            "payerTaxId": paymentInfo.payerInfo.tax_id,
            "payerTaxIdType": paymentInfo.payerInfo.tax_id_type,
            "language": paymentInfo.language,
            "country": paymentInfo.country,
            "disallowRememberedCards": paymentInfo.disallowRememberedCards,
            "rememberedCards": paymentInfo.rememberedCards,
            "onError": this.handleError,
            "merchantInstallmentSelectionOptional": paymentInfo.merchantInstallmentSelectionOptional == 1,
            "merchantInstallmentSelection": 1
        };

        this.paymentId = paymentInfo.paymentId;
    },

    initCheckout() {
        this.setLoader("#ppplus-mb");
        this.getPaymentInfo().then(
            paymentInformation => {
                this.setConfig(paymentInformation, "ppplus-mb");

                if (this.config.country == 'BR' && this.config.payerTaxId == '') {
                    let message = typeof(EMPTY_TAX_ID) != 'undefined' ? EMPTY_TAX_ID : 'Payer tax id is empty';
                    this.showMessage(message, '#ppplus-mb', 'danger');
                    return;
                }

                this.ppp = PAYPAL.apps.PPP(this.config);
            }
        ).catch(error => {
            console.log(error);
        });
    },

    showMessage(message, selector, type) {
        let messageContainer = $(`<div class="alert alert-${type}" />`);
        messageContainer.text(message);
        $(selector).html(messageContainer);
    },

    setLoader(selector) {
        let loader = '<div class="pp__flex pp__justify-content-center"><div class="paypal-loader"></div></div>';
        $(selector).html(loader);
    },

    doPayment() {
        if (this.ppp != null) {
          this.ppp.doContinue();
        }
    },

    getPaymentInfo() {
        let promise = new Promise((resolve, reject) => {
            $.ajax({
                url: ajaxPatch,
                type: "POST",
                dataType: "JSON",
                data: {
                    ajax: true,
                    action: 'getPaymentInfo',
                },
                before () {

                },
                success (response) {
                    if (("success" in response) && (response["success"] == true)) {
                        resolve(response.paymentInfo);
                    }
                }
            });
        });

        return promise;
    },

    messageListener(event) {
        try {
            let data = JSON.parse(event.data);
            if (data.action == "checkout" && data.result.state == "APPROVED") {
                data['paymentId'] = PayPalMB.paymentId;
                PayPalMB.sendData(data, ajaxPatch);
            }
        } catch (exc) {
            console.log(exc);
        }
    },

    handleError(error) {
        let message;
        $('.paypal-loader-container').hide();
        $('#ppplus-mb').css({
          'height': '610px'
        });

        for (let key in error) {
            // skip loop if the property is from prototype
            if (!error.hasOwnProperty(key)) {
                continue;
            }

            let errorMessage = error[key];
            switch (errorMessage) {
                case "Invalid 'payerTaxId'.":
                    if (typeof(INVALID_PAYER_TAX_ID) == 'undefined') {
                        message = errorMessage;
                    } else {
                        message = INVALID_PAYER_TAX_ID;
                    }

                    PayPalMB.showMessage(message, '#ppplus-mb-error-message', 'danger');
            }
        }

        console.log(error, typeof error);
    },

    sendData(data, action) {
        let messageSuccess;

        if (typeof(PAYMENT_SUCCESS) != 'undefined') {
            messageSuccess = PAYMENT_SUCCESS;
        } else {
            messageSuccess = 'Payment successful! You will be redirected to the payment confirmation page in a couple of seconds.';
        }

        PayPalMB.showMessage(messageSuccess, '#ppplus-mb', 'success');
        $('#ppplus-mb').css('height', '100%');
        let form = document.createElement('form');
        let input = document.createElement('input');

        input.name = "paymentData";
        input.value = JSON.stringify(data);

        form.method = "POST";
        form.action = action;

        form.appendChild(input);
        form.style = 'display: none';
        document.body.appendChild(form);
        form.submit();
    }

}


$(document).ready(() => {
    $('.payment-options input[name="payment-option"]').click((event) => {
        let paymentOption = $(event.target);
        if (paymentOption.attr('data-module-name') == "paypal_plus_mb") {
            PayPalMB.initCheckout();
        }
    });

    // Order payment button action for paypal plus
    $('#payment-confirmation button').on('click', (event) => {
        let selectedOption = $('input[name=payment-option]:checked');
        if (selectedOption.attr("data-module-name") == "paypal_plus_mb") {
            event.preventDefault();
            event.stopPropagation();
            PayPalMB.doPayment();
        }
    });

    if (window.addEventListener) {
        window.addEventListener("message", PayPalMB.messageListener, false);
    } else if (window.attachEvent) {
        window.attachEvent("onmessage", PayPalMB.messageListener);
    } else {
        throw new Error("Can't attach message listener");
    }
});
