/*
 * 2007-2022 PayPal
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
 *  @author 2007-2022 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
// init incontext

import {Tools} from './tools.js';

const ApmButton = function(conf) {

    this.method = typeof conf['method'] != 'undefined' ? conf['method'] : null;

    this.button = typeof conf['button'] != 'undefined' ? conf['button'] : null;

    this.controller = typeof conf['controller'] != 'undefined' ? conf['controller'] : null;

    this.validationController = typeof conf['validationController'] != 'undefined' ? conf['validationController'] : null;
};

ApmButton.prototype.initButton = function() {
  totPaypalApmSdkButtons.Buttons({
    fundingSource: this.method,

    createOrder: function(data, actions) {
      return this.getIdOrder();
    }.bind(this),

    onApprove: function(data, actions) {
      this.sendData(data);
    }.bind(this),

  }).render(this.button);

  Tools.disableTillConsenting(
    document.querySelector(this.button),
    document.getElementById('conditions_to_approve[terms-and-conditions]')
  );
};

ApmButton.prototype.getIdOrder = function() {

  let url = new URL(this.controller);
  url.searchParams.append('ajax', '1');
  url.searchParams.append('action', 'CreateOrder');

  return fetch(url.toString(), {
    method: 'post',
    headers: {
      'content-type': 'application/json;charset=utf-8'
    },
    body: JSON.stringify({page: 'cart'})
  }).then(function(res) {
    return res.json();
  }).then(function(data) {
    if (data.success) {
      return data.idOrder;
    }
  });
};

ApmButton.prototype.sendData = function(data) {
  let form = document.createElement('form');
  let input = document.createElement('input');

  input.name = "paymentData";
  input.value = JSON.stringify(data);

  form.method = "POST";
  form.action = this.validationController;

  form.appendChild(input);
  document.body.appendChild(form);
  form.submit();
};

window.ApmButton = ApmButton;


