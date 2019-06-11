$(document).ready( () => {
  $('[data-display-popup]').on('click', function() {

    displayPopup($(this).data('method'), $(this).data('with-card'))
  });

  $('#change_product').click(function(event) {
    event.preventDefault();
    $('a[href=#paypal_conf]').click();
  });

  $('.main_form').insertAfter($('.configuration-block'));
  $('.bt_currency_form').insertAfter($('.main_form'));
  $('.form_shortcut').insertAfter($('.main_form'));
  $('.form_api_username').insertAfter($('.form_shortcut'));

  const $paypal_ec_in_context = $('input[name="paypal_ec_in_context"]');
  const $paypal_vaulting = $('input[name="paypal_vaulting"]');

  if ($paypal_ec_in_context.attr('checked') === "checked") {
    $('#config_logo-name').parents('.form-group').hide();
  }
  if ($paypal_vaulting.attr('checked') === "checked") {
    $('#card_verification_on').parents('.form-group').hide();
  }
  $paypal_ec_in_context.on('change', function() {
    toggleElement($(this).val(), $('#config_logo-name').parents('.form-group'));
  });
  $paypal_vaulting.on('change', function() {
    toggleElement($(this).val(), $('#card_verification_on').parents('.form-group'));
  });

  if ($('#config_logo-images-thumbnails').length && !ssl_active) {
    $('#config_logo-images-thumbnails').after(logoThumbnailsMessage);
  }
});

$('[data-check-requirements]').click( () => {
  $.ajax({
    url: 'ajax-tab.php',
    dataType: 'json',
    data : {
      ajax : true,
      controller: 'AdminModules',
      configure:'paypal',
      action : 'CheckRequirements',
      token: token
    },
    success: function(data) {
      if(data) {
        $('[data-action-response]').html(data);
      } else {
        $('[data-action-response]').html(`<p class="alert alert-success">${checkRequirementsMessage}</p>`);
      }
    }
  });
});

const toggleElement = (val, el) => {
  if (val != 0) {
    el.hide();
  } else {
    el.show();
  }
}

const displayPopup = (method, withCard) => {
  $('[data-method-paypal]').val(method);
  $('[data-with-card-paypal]').val(withCard);

  if ($('[data-fancybox]').data('fancybox') === method) {
    $.fancybox.open([
      {
        type: 'inline',
        autoScale: true,
        minHeight: 30,
        content: $(`[data-fancybox="${method}"]`).html(),
      }
    ]);
  }
}
