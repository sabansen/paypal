/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./_dev/js/shortcut.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./_dev/js/shortcut.js":
/*!*****************************!*\
  !*** ./_dev/js/shortcut.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

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
// init incontext
document.addEventListener("DOMContentLoaded", function () {
  var ec_sc_qty_wanted = $('#quantity_wanted').val();
  var ec_sc_productId = $('#paypal_payment_form_cart input[name="id_product"]').val();
  EcCheckProductAvailability(ec_sc_qty_wanted, ec_sc_productId, $('#es_cs_product_attribute').val());
  prestashop.on('updatedProduct', function (e, xhr, settings) {
    EcCheckProductAvailability(ec_sc_qty_wanted, ec_sc_productId, e.id_product_attribute);
  });

  if (typeof ec_sc_in_context != "undefined" && ec_sc_in_context) {
    window.paypalCheckoutReady = function () {
      paypal.checkout.setup(merchant_id, {
        environment: ec_sc_environment
      });
    };
  }
});

function EcCheckProductAvailability(qty, productId, id_product_attribute) {
  $.ajax({
    url: sc_init_url,
    type: "POST",
    data: 'checkAvailability=1&source_page=product&id_product=' + productId + '&quantity=' + qty + '&product_attribute=' + id_product_attribute,
    success: function success(json) {
      if (json.success) {
        $('#container_express_checkout').show();
      } else {
        $('#container_express_checkout').hide();
      }
    },
    error: function error(responseData, textStatus, errorThrown) {}
  });
}

function setInput() {
  $('#paypal_quantity').val($('[name="qty"]').val());
  var combination = [];
  var re = /group\[([0-9]+)\]/;
  $.each($('#add-to-cart-or-refresh').serializeArray(), function (key, item) {
    if (res = item.name.match(re)) {
      combination.push(res[1] + ':' + item.value);
    }
  });
  $('#paypal_url_page').val(document.location.href);
  $('#paypal_combination').val(combination.join('|'));

  if (typeof ec_sc_in_context != "undefined" && ec_sc_in_context) {
    ECSInContext(combination);
  } else {
    $('#paypal_payment_form_cart').submit();
  }
}

function ECSInContext(combination) {
  paypal.checkout.initXO();
  $.support.cors = true;
  $.ajax({
    url: ec_sc_action_url,
    type: "GET",
    data: 'getToken=1&source_page=product&id_product=' + $('#paypal_payment_form_cart input[name="id_product"]').val() + '&quantity=' + $('[name="qty"]').val() + '&combination=' + combination.join('|'),
    success: function success(json) {
      if (json.success) {
        var url = paypal.checkout.urlPrefix + json.token;
        paypal.checkout.startFlow(url);
      } else {
        paypal.checkout.closeFlow();
        window.location.replace(json.redirect_link);
      }
    },
    error: function error(responseData, textStatus, errorThrown) {
      alert("Error in ajax post" + responseData.statusText);
      paypal.checkout.closeFlow();
    }
  });
}

/***/ })

/******/ });
//# sourceMappingURL=shortcut.js.map