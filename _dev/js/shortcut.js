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
// init incontext

const Shortcut = {

  idProduct: null,

  combination: null,

  productQuantity: null,

  page: null,

  button: $('[paypal-button-container]'),

  controller: sc_init_url,

  controllerScOrder: scOrderUrl,

  init() {
    this.updateInfo();
    prestashop.on('updatedProduct', function(e, xhr, settings) {
      Shortcut.checkProductAvailability();
    });
  },

  updateInfo() {
    this.page = $('[data-container-express-checkout]').data('paypal-source-page');

    if (this.page == 'product') {
      this.productQuantity = $('input[name="qty"]').val();
      this.idProduct = $('[data-paypal-id-product]').val();
      this.combination = this.getCombination();
    }
  },

  getCombination() {
    let combination = [],
      re = /group\[([0-9]+)\]/;

    $.each($('#add-to-cart-or-refresh').serializeArray(), (key, item) => {
      if(res = item.name.match(re)) {
        combination.push(`${res[1]} : ${item.value}`);
      }
    });

    return combination;
  },

  initButton() {
    paypal.Buttons({
      createOrder: function(data, actions) {
        return Shortcut.getIdOrder();
      },

      onApprove: function(data, actions) {
        Shortcut.sendData(data);
      },

    }).render(this.button.selector);
  },

  sendData(data) {
    let form = document.createElement('form');
    let input = document.createElement('input');

    input.name = "paymentData";
    input.value = JSON.stringify(data);

    form.method = "POST";
    form.action = Shortcut.controllerScOrder;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  },

  getIdOrder() {
    let data = new Object();
    this.updateInfo();
    data['page'] = this.page;

    if (this.page == 'product') {
      data['idProduct'] = this.idProduct;
      data['quantity'] = this.productQuantity;
      data['combination'] = this.combination.join('|');
    }

    return fetch(this.controller + '&ajax=1&action=CreateOrder', {
      method: 'post',
      headers: {
        'content-type': 'application/json;charset=utf-8'
      },
      body: JSON.stringify(data)
    }).then(function(res) {
      return res.json();
    }).then(function(data) {
      if (data.success) {
        return data.idOrder;
      }
    });
  },

  checkProductAvailability() {
    let data = new Object();
    this.updateInfo();
    data['page'] = this.page;

    if (this.page == 'product') {
      data['idProduct'] = this.idProduct;
      data['quantity'] = this.productQuantity;
      data['combination'] = this.combination.join('|');
    }

    fetch(this.controller + '&ajax=1&action=CheckAvailability',
      {
        method: 'post',
        headers: {
          'content-type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify(data),
      }).then(function(res){
        return res.json();
    }).then(function (json) {
      if (json.success) {
        Shortcut.button.show();
      } else {
        Shortcut.button.hide();
      }
    });
  }
};


$(document).ready( () => {
  Shortcut.init();
  Shortcut.initButton();
});


