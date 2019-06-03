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
/******/ 	return __webpack_require__(__webpack_require__.s = "./_dev/js/paypal_bo.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./_dev/js/paypal_bo.js":
/*!******************************!*\
  !*** ./_dev/js/paypal_bo.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(function () {
  $('[data-display-popup]').on('click', function () {
    displayPopup($(this).data('method'), $(this).data('with-card'));
  });
  $('#change_product').click(function (event) {
    event.preventDefault();
    $('a[href=#paypal_conf]').click();
  });
  $('.main_form').insertAfter($('.configuration-block'));
  $('.bt_currency_form').insertAfter($('.main_form'));
  $('.form_shortcut').insertAfter($('.main_form'));
  $('.form_api_username').insertAfter($('.form_shortcut'));
  var $paypal_ec_in_context = $('input[name="paypal_ec_in_context"]');
  var $paypal_vaulting = $('input[name="paypal_vaulting"]');

  if ($paypal_ec_in_context.attr('checked') === "checked") {
    $('#config_logo-name').parents('.form-group').hide();
  }

  if ($paypal_vaulting.attr('checked') === "checked") {
    $('#card_verification_on').parents('.form-group').hide();
  }

  $paypal_ec_in_context.on('change', function () {
    toggleElement($(this).val(), $('#config_logo-name').parents('.form-group'));
  });
  $paypal_vaulting.on('change', function () {
    toggleElement($(this).val(), $('#card_verification_on').parents('.form-group'));
  });

  if ($('#config_logo-images-thumbnails').length && !ssl_active) {
    $('#config_logo-images-thumbnails').after(logoThumbnailsMessage);
  }
});
$('[data-check-requirements]').click(function () {
  $.ajax({
    url: 'ajax-tab.php',
    dataType: 'json',
    data: {
      ajax: true,
      controller: 'AdminModules',
      configure: 'paypal',
      action: 'CheckRequirements',
      token: token
    },
    success: function success(data) {
      if (data) {
        $('[data-action-response]').html(data);
      } else {
        $('[data-action-response]').html("<p class=\"alert alert-success\">".concat(checkRequirementsMessage, "</p>"));
      }
    }
  });
});

var toggleElement = function toggleElement(val, el) {
  if (val != 0) {
    el.hide();
  } else {
    el.show();
  }
};

var displayPopup = function displayPopup(method, withCard) {
  $('[data-method]').val(method);
  $('[data-with-card]').val(withCard);

  if ($('[data-fancybox]').data('fancybox') === method) {
    $.fancybox.open([{
      type: 'inline',
      autoScale: true,
      minHeight: 30,
      content: $("[data-fancybox=\"".concat(method, "\"]")).html()
    }]);
  }
};

/***/ })

/******/ });
//# sourceMappingURL=paypal_bo.js.map