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
$(document).ready(function () {
  var sourcePage = $('[data-container-express-checkout]').data('paypal-source-page');

  switch (sourcePage) {
    case 'product':
      var vars = getProductVars();
      EcCheckProductAvailability(sourcePage, vars['qty'], vars['id_product'], vars['id_product_attribute']);
      prestashop.on('updatedProduct', function (e, xhr, settings) {
        EcCheckProductAvailability(sourcePage, vars['qty'], vars['id_product'], e.id_product_attribute);
      });
      break;

    case 'cart':
      prestashop.on('updateCart', function () {
        return EcCheckProductAvailability(sourcePage);
      });
      break;
  }

  if (typeof ec_sc_in_context !== 'undefined' && ec_sc_in_context) {
    window.paypalCheckoutReady = function () {
      paypal.checkout.setup(merchant_id, {
        environment: ec_sc_environment
      });
    };
  }
});

var getProductVars = function getProductVars() {
  var vars = new Object();
  vars['qty'] = $('input[name="qty"]').val();
  vars['id_product'] = $('[data-paypal-id-product]').val();
  vars['id_product_attribute'] = $('[data-paypal-id-product-attribute]').val();
  return vars;
}; // Click on shortcut button


$('[data-paypal-shortcut-btn]').on('click', function () {
  var sourcePage = $('[data-container-express-checkout]').data('paypal-source-page');
  $('[data-paypal-url-page]').val(document.location.href);

  switch (sourcePage) {
    case 'product':
      var vars = getProductVars(),
          combination = [],
          re = /group\[([0-9]+)\]/;
      $('[data-paypal-qty]').val(vars['qty']);
      $.each($('#add-to-cart-or-refresh').serializeArray(), function (key, item) {
        if (res = item.name.match(re)) {
          combination.push("".concat(res[1], " : ").concat(item.value));
        }
      });
      $('[data-paypal-combination]').val(combination.join('|'));

      if (typeof ec_sc_in_context !== 'undefined' && ec_sc_in_context) {
        ECSInContext(sourcePage, combination, vars['qty'], vars['id_product']);
      } else {
        $('[data-paypal-payment-form-cart]').submit();
      }

      break;

    case 'cart':
      if (typeof ec_sc_in_context !== 'undefined' && ec_sc_in_context) {
        ECSInContext();
      } else {
        $('[data-paypal-payment-form-cart]').submit();
      }

      break;
  }
});

var EcCheckProductAvailability = function EcCheckProductAvailability(sourcePage) {
  var qty = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var productId = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var productIdAttr = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
  $.ajax({
    url: sc_init_url,
    type: 'POST',
    data: "checkAvailability=1&source_page=".concat(sourcePage).concat(productId ? "&id_product=".concat(productId) : '').concat(qty ? "&quantity=".concat(qty) : '').concat(productIdAttr ? "&product_attribute=".concat(productIdAttr) : ''),
    success: function success(json) {
      if (json.success) {
        $('[data-container-express-checkout]').show();
      } else {
        $('[data-container-express-checkout]').hide();
      }
    }
  });
};

var ECSInContext = function ECSInContext() {
  var sourcePage = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var combination = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var qty = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var productId = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
  paypal.checkout.initXO();
  $.support.cors = true;
  $.ajax({
    url: ec_sc_action_url,
    type: 'GET',
    data: "getToken=1&source_page=".concat(sourcePage).concat(productId ? "&id_product=".concat(productId) : '').concat(qty ? "&quantity=".concat(qty) : '').concat(combination ? "&combination=".concat(combination.join('|')) : ''),
    success: function success(json) {
      if (json.success) {
        var url = paypal.checkout.urlPrefix + json.token;
        paypal.checkout.startFlow(url);
      } else {
        paypal.checkout.closeFlow();
        window.location.replace(json.redirect_link);
      }
    },
    error: function error(responseData) {
      alert("Error in ajax post ".concat(responseData.statusText));
      paypal.checkout.closeFlow();
    }
  });
};

/***/ })

/******/ });
//# sourceMappingURL=shortcut.js.map