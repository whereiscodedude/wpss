this["wp"] = this["wp"] || {}; this["wp"]["i18n"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/i18n/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _defineProperty; });\nfunction _defineProperty(obj, key, value) {\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n\n  return obj;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectSpread.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _objectSpread; });\n/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defineProperty */ \"./node_modules/@babel/runtime/helpers/esm/defineProperty.js\");\n\nfunction _objectSpread(target) {\n  for (var i = 1; i < arguments.length; i++) {\n    var source = arguments[i] != null ? arguments[i] : {};\n    var ownKeys = Object.keys(source);\n\n    if (typeof Object.getOwnPropertySymbols === 'function') {\n      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {\n        return Object.getOwnPropertyDescriptor(source, sym).enumerable;\n      }));\n    }\n\n    ownKeys.forEach(function (key) {\n      Object(_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(target, key, source[key]);\n    });\n  }\n\n  return target;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/objectSpread.js?");

/***/ }),

/***/ "./node_modules/@tannin/compile/index.js":
/*!***********************************************!*\
  !*** ./node_modules/@tannin/compile/index.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return compile; });\n/* harmony import */ var _tannin_postfix__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @tannin/postfix */ \"./node_modules/@tannin/postfix/index.js\");\n/* harmony import */ var _tannin_evaluate__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @tannin/evaluate */ \"./node_modules/@tannin/evaluate/index.js\");\n\n\n\n/**\n * Given a C expression, returns a function which can be called to evaluate its\n * result.\n *\n * @example\n *\n * ```js\n * import compile from '@tannin/compile';\n *\n * const evaluate = compile( 'n > 1' );\n *\n * evaluate( { n: 2 } );\n * // ⇒ true\n * ```\n *\n * @param {string} expression C expression.\n *\n * @return {Function} Compiled evaluator.\n */\nfunction compile( expression ) {\n\tvar terms = Object(_tannin_postfix__WEBPACK_IMPORTED_MODULE_0__[\"default\"])( expression );\n\n\treturn function( variables ) {\n\t\treturn Object(_tannin_evaluate__WEBPACK_IMPORTED_MODULE_1__[\"default\"])( terms, variables );\n\t};\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@tannin/compile/index.js?");

/***/ }),

/***/ "./node_modules/@tannin/evaluate/index.js":
/*!************************************************!*\
  !*** ./node_modules/@tannin/evaluate/index.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return evaluate; });\n/**\n * Operator callback functions.\n *\n * @type {Object}\n */\nvar OPERATORS = {\n\t'!': function( a ) {\n\t\treturn ! a;\n\t},\n\t'*': function( a, b ) {\n\t\treturn a * b;\n\t},\n\t'/': function( a, b ) {\n\t\treturn a / b;\n\t},\n\t'%': function( a, b ) {\n\t\treturn a % b;\n\t},\n\t'+': function( a, b ) {\n\t\treturn a + b;\n\t},\n\t'-': function( a, b ) {\n\t\treturn a - b;\n\t},\n\t'<': function( a, b ) {\n\t\treturn a < b;\n\t},\n\t'<=': function( a, b ) {\n\t\treturn a <= b;\n\t},\n\t'>': function( a, b ) {\n\t\treturn a > b;\n\t},\n\t'>=': function( a, b ) {\n\t\treturn a >= b;\n\t},\n\t'==': function( a, b ) {\n\t\treturn a === b;\n\t},\n\t'!=': function( a, b ) {\n\t\treturn a !== b;\n\t},\n\t'&&': function( a, b ) {\n\t\treturn a && b;\n\t},\n\t'||': function( a, b ) {\n\t\treturn a || b;\n\t},\n\t'?:': function( a, b, c ) {\n\t\tif ( a ) {\n\t\t\tthrow b;\n\t\t}\n\n\t\treturn c;\n\t},\n};\n\n/**\n * Given an array of postfix terms and operand variables, returns the result of\n * the postfix evaluation.\n *\n * @example\n *\n * ```js\n * import evaluate from '@tannin/evaluate';\n *\n * // 3 + 4 * 5 / 6 ⇒ '3 4 5 * 6 / +'\n * const terms = [ '3', '4', '5', '*', '6', '/', '+' ];\n *\n * evaluate( terms, {} );\n * // ⇒ 6.333333333333334\n * ```\n *\n * @param {string[]} postfix   Postfix terms.\n * @param {Object}   variables Operand variables.\n *\n * @return {*} Result of evaluation.\n */\nfunction evaluate( postfix, variables ) {\n\tvar stack = [],\n\t\ti, getOperatorResult, term, value;\n\n\tfor ( i = 0; i < postfix.length; i++ ) {\n\t\tterm = postfix[ i ];\n\n\t\tgetOperatorResult = OPERATORS[ term ];\n\t\tif ( getOperatorResult ) {\n\t\t\ttry {\n\t\t\t\t// Pop from stack by number of function arguments.\n\t\t\t\tvalue = getOperatorResult.apply(\n\t\t\t\t\tnull,\n\t\t\t\t\tstack.splice( -1 * getOperatorResult.length )\n\t\t\t\t);\n\t\t\t} catch ( earlyReturn ) {\n\t\t\t\treturn earlyReturn;\n\t\t\t}\n\t\t} else if ( variables.hasOwnProperty( term ) ) {\n\t\t\tvalue = variables[ term ];\n\t\t} else {\n\t\t\tvalue = +term;\n\t\t}\n\n\t\tstack.push( value );\n\t}\n\n\treturn stack[ 0 ];\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@tannin/evaluate/index.js?");

/***/ }),

/***/ "./node_modules/@tannin/plural-forms/index.js":
/*!****************************************************!*\
  !*** ./node_modules/@tannin/plural-forms/index.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return pluralForms; });\n/* harmony import */ var _tannin_compile__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @tannin/compile */ \"./node_modules/@tannin/compile/index.js\");\n\n\n/**\n * Given a C expression, returns a function which, when called with a value,\n * evaluates the result with the value assumed to be the \"n\" variable of the\n * expression. The result will be coerced to its numeric equivalent.\n *\n * @param {string} expression C expression.\n *\n * @return {Function} Evaluator function.\n */\nfunction pluralForms( expression ) {\n\tvar evaluate = Object(_tannin_compile__WEBPACK_IMPORTED_MODULE_0__[\"default\"])( expression );\n\n\treturn function( n ) {\n\t\treturn +evaluate( { n: n } );\n\t};\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@tannin/plural-forms/index.js?");

/***/ }),

/***/ "./node_modules/@tannin/postfix/index.js":
/*!***********************************************!*\
  !*** ./node_modules/@tannin/postfix/index.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return postfix; });\nvar PRECEDENCE, OPENERS, TERMINATORS, PATTERN;\n\n/**\n * Operator precedence mapping.\n *\n * @type {Object}\n */\nPRECEDENCE = {\n\t'(': 9,\n\t'!': 8,\n\t'*': 7,\n\t'/': 7,\n\t'%': 7,\n\t'+': 6,\n\t'-': 6,\n\t'<': 5,\n\t'<=': 5,\n\t'>': 5,\n\t'>=': 5,\n\t'==': 4,\n\t'!=': 4,\n\t'&&': 3,\n\t'||': 2,\n\t'?': 1,\n\t'?:': 1,\n};\n\n/**\n * Characters which signal pair opening, to be terminated by terminators.\n *\n * @type {string[]}\n */\nOPENERS = [ '(', '?' ];\n\n/**\n * Characters which signal pair termination, the value an array with the\n * opener as its first member. The second member is an optional operator\n * replacement to push to the stack.\n *\n * @type {string[]}\n */\nTERMINATORS = {\n\t')': [ '(' ],\n\t':': [ '?', '?:' ],\n};\n\n/**\n * Pattern matching operators and openers.\n *\n * @type {RegExp}\n */\nPATTERN = /<=|>=|==|!=|&&|\\|\\||\\?:|\\(|!|\\*|\\/|%|\\+|-|<|>|\\?|\\)|:/;\n\n/**\n * Given a C expression, returns the equivalent postfix (Reverse Polish)\n * notation terms as an array.\n *\n * If a postfix string is desired, simply `.join( ' ' )` the result.\n *\n * @example\n *\n * ```js\n * import postfix from '@tannin/postfix';\n *\n * postfix( 'n > 1' );\n * // ⇒ [ 'n', '1', '>' ]\n * ```\n *\n * @param {string} expression C expression.\n *\n * @return {string[]} Postfix terms.\n */\nfunction postfix( expression ) {\n\tvar terms = [],\n\t\tstack = [],\n\t\tmatch, operator, term, element;\n\n\twhile ( ( match = expression.match( PATTERN ) ) ) {\n\t\toperator = match[ 0 ];\n\n\t\t// Term is the string preceding the operator match. It may contain\n\t\t// whitespace, and may be empty (if operator is at beginning).\n\t\tterm = expression.substr( 0, match.index ).trim();\n\t\tif ( term ) {\n\t\t\tterms.push( term );\n\t\t}\n\n\t\twhile ( ( element = stack.pop() ) ) {\n\t\t\tif ( TERMINATORS[ operator ] ) {\n\t\t\t\tif ( TERMINATORS[ operator ][ 0 ] === element ) {\n\t\t\t\t\t// Substitution works here under assumption that because\n\t\t\t\t\t// the assigned operator will no longer be a terminator, it\n\t\t\t\t\t// will be pushed to the stack during the condition below.\n\t\t\t\t\toperator = TERMINATORS[ operator ][ 1 ] || operator;\n\t\t\t\t\tbreak;\n\t\t\t\t}\n\t\t\t} else if ( OPENERS.indexOf( element ) >= 0 || PRECEDENCE[ element ] < PRECEDENCE[ operator ] ) {\n\t\t\t\t// Push to stack if either an opener or when pop reveals an\n\t\t\t\t// element of lower precedence.\n\t\t\t\tstack.push( element );\n\t\t\t\tbreak;\n\t\t\t}\n\n\t\t\t// For each popped from stack, push to terms.\n\t\t\tterms.push( element );\n\t\t}\n\n\t\tif ( ! TERMINATORS[ operator ] ) {\n\t\t\tstack.push( operator );\n\t\t}\n\n\t\t// Slice matched fragment from expression to continue match.\n\t\texpression = expression.substr( match.index + operator.length );\n\t}\n\n\t// Push remainder of operand, if exists, to terms.\n\texpression = expression.trim();\n\tif ( expression ) {\n\t\tterms.push( expression );\n\t}\n\n\t// Pop remaining items from stack into terms.\n\treturn terms.concat( stack.reverse() );\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@tannin/postfix/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/i18n/build-module/index.js":
/*!************************************************************!*\
  !*** ./node_modules/@wordpress/i18n/build-module/index.js ***!
  \************************************************************/
/*! exports provided: setLocaleData, __, _x, _n, _nx, sprintf */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"setLocaleData\", function() { return setLocaleData; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"__\", function() { return __; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"_x\", function() { return _x; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"_n\", function() { return _n; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"_nx\", function() { return _nx; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"sprintf\", function() { return sprintf; });\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var tannin__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tannin */ \"./node_modules/tannin/index.js\");\n/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! memize */ \"./node_modules/memize/index.js\");\n/* harmony import */ var memize__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(memize__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var sprintf_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! sprintf-js */ \"./node_modules/@wordpress/i18n/node_modules/sprintf-js/src/sprintf.js\");\n/* harmony import */ var sprintf_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(sprintf_js__WEBPACK_IMPORTED_MODULE_3__);\n\n\n/**\n * External dependencies\n */\n\n\n\n/**\n * Default locale data to use for Tannin domain when not otherwise provided.\n * Assumes an English plural forms expression.\n *\n * @type {Object}\n */\n\nvar DEFAULT_LOCALE_DATA = {\n  '': {\n    plural_forms: 'plural=(n!=1)'\n  }\n};\n/**\n * Log to console, once per message; or more precisely, per referentially equal\n * argument set. Because Jed throws errors, we log these to the console instead\n * to avoid crashing the application.\n *\n * @param {...*} args Arguments to pass to `console.error`\n */\n\nvar logErrorOnce = memize__WEBPACK_IMPORTED_MODULE_2___default()(console.error); // eslint-disable-line no-console\n\n/**\n * The underlying instance of Tannin to which exported functions interface.\n *\n * @type {Tannin}\n */\n\nvar i18n = new tannin__WEBPACK_IMPORTED_MODULE_1__[\"default\"]({});\n/**\n * Merges locale data into the Tannin instance by domain. Accepts data in a\n * Jed-formatted JSON object shape.\n *\n * @see http://messageformat.github.io/Jed/\n *\n * @param {?Object} data   Locale data configuration.\n * @param {?string} domain Domain for which configuration applies.\n */\n\nfunction setLocaleData(data) {\n  var domain = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'default';\n  i18n.data[domain] = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, DEFAULT_LOCALE_DATA, i18n.data[domain], data); // Populate default domain configuration (supported locale date which omits\n  // a plural forms expression).\n\n  i18n.data[domain][''] = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, DEFAULT_LOCALE_DATA[''], i18n.data[domain]['']);\n}\n/**\n * Wrapper for Tannin's `dcnpgettext`. Populates default locale data if not\n * otherwise previously assigned.\n *\n * @param {?string} domain  Domain to retrieve the translated text.\n * @param {?string} context Context information for the translators.\n * @param {string}  single  Text to translate if non-plural. Used as fallback\n *                          return value on a caught error.\n * @param {?string} plural  The text to be used if the number is plural.\n * @param {?number} number  The number to compare against to use either the\n *                          singular or plural form.\n *\n * @return {string} The translated string.\n */\n\nfunction dcnpgettext() {\n  var domain = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'default';\n  var context = arguments.length > 1 ? arguments[1] : undefined;\n  var single = arguments.length > 2 ? arguments[2] : undefined;\n  var plural = arguments.length > 3 ? arguments[3] : undefined;\n  var number = arguments.length > 4 ? arguments[4] : undefined;\n\n  if (!i18n.data[domain]) {\n    setLocaleData(undefined, domain);\n  }\n\n  return i18n.dcnpgettext(domain, context, single, plural, number);\n}\n/**\n * Retrieve the translation of text.\n *\n * @see https://developer.wordpress.org/reference/functions/__/\n *\n * @param {string}  text   Text to translate.\n * @param {?string} domain Domain to retrieve the translated text.\n *\n * @return {string} Translated text.\n */\n\n\nfunction __(text, domain) {\n  return dcnpgettext(domain, undefined, text);\n}\n/**\n * Retrieve translated string with gettext context.\n *\n * @see https://developer.wordpress.org/reference/functions/_x/\n *\n * @param {string}  text    Text to translate.\n * @param {string}  context Context information for the translators.\n * @param {?string} domain  Domain to retrieve the translated text.\n *\n * @return {string} Translated context string without pipe.\n */\n\nfunction _x(text, context, domain) {\n  return dcnpgettext(domain, context, text);\n}\n/**\n * Translates and retrieves the singular or plural form based on the supplied\n * number.\n *\n * @see https://developer.wordpress.org/reference/functions/_n/\n *\n * @param {string}  single The text to be used if the number is singular.\n * @param {string}  plural The text to be used if the number is plural.\n * @param {number}  number The number to compare against to use either the\n *                         singular or plural form.\n * @param {?string} domain Domain to retrieve the translated text.\n *\n * @return {string} The translated singular or plural form.\n */\n\nfunction _n(single, plural, number, domain) {\n  return dcnpgettext(domain, undefined, single, plural, number);\n}\n/**\n * Translates and retrieves the singular or plural form based on the supplied\n * number, with gettext context.\n *\n * @see https://developer.wordpress.org/reference/functions/_nx/\n *\n * @param {string}  single  The text to be used if the number is singular.\n * @param {string}  plural  The text to be used if the number is plural.\n * @param {number}  number  The number to compare against to use either the\n *                          singular or plural form.\n * @param {string}  context Context information for the translators.\n * @param {?string} domain  Domain to retrieve the translated text.\n *\n * @return {string} The translated singular or plural form.\n */\n\nfunction _nx(single, plural, number, context, domain) {\n  return dcnpgettext(domain, context, single, plural, number);\n}\n/**\n * Returns a formatted string. If an error occurs in applying the format, the\n * original format string is returned.\n *\n * @param {string}   format  The format of the string to generate.\n * @param {string[]} ...args Arguments to apply to the format.\n *\n * @see http://www.diveintojavascript.com/projects/javascript-sprintf\n *\n * @return {string} The formatted string.\n */\n\nfunction sprintf(format) {\n  try {\n    for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {\n      args[_key - 1] = arguments[_key];\n    }\n\n    return sprintf_js__WEBPACK_IMPORTED_MODULE_3___default.a.sprintf.apply(sprintf_js__WEBPACK_IMPORTED_MODULE_3___default.a, [format].concat(args));\n  } catch (error) {\n    logErrorOnce('sprintf error: \\n\\n' + error.toString());\n    return format;\n  }\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/i18n/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/i18n/node_modules/sprintf-js/src/sprintf.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/i18n/node_modules/sprintf-js/src/sprintf.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var __WEBPACK_AMD_DEFINE_RESULT__;/* global window, exports, define */\n\n!function() {\n    'use strict'\n\n    var re = {\n        not_string: /[^s]/,\n        not_bool: /[^t]/,\n        not_type: /[^T]/,\n        not_primitive: /[^v]/,\n        number: /[diefg]/,\n        numeric_arg: /[bcdiefguxX]/,\n        json: /[j]/,\n        not_json: /[^j]/,\n        text: /^[^\\x25]+/,\n        modulo: /^\\x25{2}/,\n        placeholder: /^\\x25(?:([1-9]\\d*)\\$|\\(([^\\)]+)\\))?(\\+)?(0|'[^$])?(-)?(\\d+)?(?:\\.(\\d+))?([b-gijostTuvxX])/,\n        key: /^([a-z_][a-z_\\d]*)/i,\n        key_access: /^\\.([a-z_][a-z_\\d]*)/i,\n        index_access: /^\\[(\\d+)\\]/,\n        sign: /^[\\+\\-]/\n    }\n\n    function sprintf(key) {\n        // `arguments` is not an array, but should be fine for this call\n        return sprintf_format(sprintf_parse(key), arguments)\n    }\n\n    function vsprintf(fmt, argv) {\n        return sprintf.apply(null, [fmt].concat(argv || []))\n    }\n\n    function sprintf_format(parse_tree, argv) {\n        var cursor = 1, tree_length = parse_tree.length, arg, output = '', i, k, match, pad, pad_character, pad_length, is_positive, sign\n        for (i = 0; i < tree_length; i++) {\n            if (typeof parse_tree[i] === 'string') {\n                output += parse_tree[i]\n            }\n            else if (Array.isArray(parse_tree[i])) {\n                match = parse_tree[i] // convenience purposes only\n                if (match[2]) { // keyword argument\n                    arg = argv[cursor]\n                    for (k = 0; k < match[2].length; k++) {\n                        if (!arg.hasOwnProperty(match[2][k])) {\n                            throw new Error(sprintf('[sprintf] property \"%s\" does not exist', match[2][k]))\n                        }\n                        arg = arg[match[2][k]]\n                    }\n                }\n                else if (match[1]) { // positional argument (explicit)\n                    arg = argv[match[1]]\n                }\n                else { // positional argument (implicit)\n                    arg = argv[cursor++]\n                }\n\n                if (re.not_type.test(match[8]) && re.not_primitive.test(match[8]) && arg instanceof Function) {\n                    arg = arg()\n                }\n\n                if (re.numeric_arg.test(match[8]) && (typeof arg !== 'number' && isNaN(arg))) {\n                    throw new TypeError(sprintf('[sprintf] expecting number but found %T', arg))\n                }\n\n                if (re.number.test(match[8])) {\n                    is_positive = arg >= 0\n                }\n\n                switch (match[8]) {\n                    case 'b':\n                        arg = parseInt(arg, 10).toString(2)\n                        break\n                    case 'c':\n                        arg = String.fromCharCode(parseInt(arg, 10))\n                        break\n                    case 'd':\n                    case 'i':\n                        arg = parseInt(arg, 10)\n                        break\n                    case 'j':\n                        arg = JSON.stringify(arg, null, match[6] ? parseInt(match[6]) : 0)\n                        break\n                    case 'e':\n                        arg = match[7] ? parseFloat(arg).toExponential(match[7]) : parseFloat(arg).toExponential()\n                        break\n                    case 'f':\n                        arg = match[7] ? parseFloat(arg).toFixed(match[7]) : parseFloat(arg)\n                        break\n                    case 'g':\n                        arg = match[7] ? String(Number(arg.toPrecision(match[7]))) : parseFloat(arg)\n                        break\n                    case 'o':\n                        arg = (parseInt(arg, 10) >>> 0).toString(8)\n                        break\n                    case 's':\n                        arg = String(arg)\n                        arg = (match[7] ? arg.substring(0, match[7]) : arg)\n                        break\n                    case 't':\n                        arg = String(!!arg)\n                        arg = (match[7] ? arg.substring(0, match[7]) : arg)\n                        break\n                    case 'T':\n                        arg = Object.prototype.toString.call(arg).slice(8, -1).toLowerCase()\n                        arg = (match[7] ? arg.substring(0, match[7]) : arg)\n                        break\n                    case 'u':\n                        arg = parseInt(arg, 10) >>> 0\n                        break\n                    case 'v':\n                        arg = arg.valueOf()\n                        arg = (match[7] ? arg.substring(0, match[7]) : arg)\n                        break\n                    case 'x':\n                        arg = (parseInt(arg, 10) >>> 0).toString(16)\n                        break\n                    case 'X':\n                        arg = (parseInt(arg, 10) >>> 0).toString(16).toUpperCase()\n                        break\n                }\n                if (re.json.test(match[8])) {\n                    output += arg\n                }\n                else {\n                    if (re.number.test(match[8]) && (!is_positive || match[3])) {\n                        sign = is_positive ? '+' : '-'\n                        arg = arg.toString().replace(re.sign, '')\n                    }\n                    else {\n                        sign = ''\n                    }\n                    pad_character = match[4] ? match[4] === '0' ? '0' : match[4].charAt(1) : ' '\n                    pad_length = match[6] - (sign + arg).length\n                    pad = match[6] ? (pad_length > 0 ? pad_character.repeat(pad_length) : '') : ''\n                    output += match[5] ? sign + arg + pad : (pad_character === '0' ? sign + pad + arg : pad + sign + arg)\n                }\n            }\n        }\n        return output\n    }\n\n    var sprintf_cache = Object.create(null)\n\n    function sprintf_parse(fmt) {\n        if (sprintf_cache[fmt]) {\n            return sprintf_cache[fmt]\n        }\n\n        var _fmt = fmt, match, parse_tree = [], arg_names = 0\n        while (_fmt) {\n            if ((match = re.text.exec(_fmt)) !== null) {\n                parse_tree.push(match[0])\n            }\n            else if ((match = re.modulo.exec(_fmt)) !== null) {\n                parse_tree.push('%')\n            }\n            else if ((match = re.placeholder.exec(_fmt)) !== null) {\n                if (match[2]) {\n                    arg_names |= 1\n                    var field_list = [], replacement_field = match[2], field_match = []\n                    if ((field_match = re.key.exec(replacement_field)) !== null) {\n                        field_list.push(field_match[1])\n                        while ((replacement_field = replacement_field.substring(field_match[0].length)) !== '') {\n                            if ((field_match = re.key_access.exec(replacement_field)) !== null) {\n                                field_list.push(field_match[1])\n                            }\n                            else if ((field_match = re.index_access.exec(replacement_field)) !== null) {\n                                field_list.push(field_match[1])\n                            }\n                            else {\n                                throw new SyntaxError('[sprintf] failed to parse named argument key')\n                            }\n                        }\n                    }\n                    else {\n                        throw new SyntaxError('[sprintf] failed to parse named argument key')\n                    }\n                    match[2] = field_list\n                }\n                else {\n                    arg_names |= 2\n                }\n                if (arg_names === 3) {\n                    throw new Error('[sprintf] mixing positional and named placeholders is not (yet) supported')\n                }\n                parse_tree.push(match)\n            }\n            else {\n                throw new SyntaxError('[sprintf] unexpected placeholder')\n            }\n            _fmt = _fmt.substring(match[0].length)\n        }\n        return sprintf_cache[fmt] = parse_tree\n    }\n\n    /**\n     * export to either browser or node.js\n     */\n    /* eslint-disable quote-props */\n    if (true) {\n        exports['sprintf'] = sprintf\n        exports['vsprintf'] = vsprintf\n    }\n    if (typeof window !== 'undefined') {\n        window['sprintf'] = sprintf\n        window['vsprintf'] = vsprintf\n\n        if (true) {\n            !(__WEBPACK_AMD_DEFINE_RESULT__ = (function() {\n                return {\n                    'sprintf': sprintf,\n                    'vsprintf': vsprintf\n                }\n            }).call(exports, __webpack_require__, exports, module),\n\t\t\t\t__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__))\n        }\n    }\n    /* eslint-enable quote-props */\n}()\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/i18n/node_modules/sprintf-js/src/sprintf.js?");

/***/ }),

/***/ "./node_modules/memize/index.js":
/*!**************************************!*\
  !*** ./node_modules/memize/index.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = function memize( fn, options ) {\n\tvar size = 0,\n\t\tmaxSize, head, tail;\n\n\tif ( options && options.maxSize ) {\n\t\tmaxSize = options.maxSize;\n\t}\n\n\tfunction memoized( /* ...args */ ) {\n\t\tvar node = head,\n\t\t\tlen = arguments.length,\n\t\t\targs, i;\n\n\t\tsearchCache: while ( node ) {\n\t\t\t// Perform a shallow equality test to confirm that whether the node\n\t\t\t// under test is a candidate for the arguments passed. Two arrays\n\t\t\t// are shallowly equal if their length matches and each entry is\n\t\t\t// strictly equal between the two sets. Avoid abstracting to a\n\t\t\t// function which could incur an arguments leaking deoptimization.\n\n\t\t\t// Check whether node arguments match arguments length\n\t\t\tif ( node.args.length !== arguments.length ) {\n\t\t\t\tnode = node.next;\n\t\t\t\tcontinue;\n\t\t\t}\n\n\t\t\t// Check whether node arguments match arguments values\n\t\t\tfor ( i = 0; i < len; i++ ) {\n\t\t\t\tif ( node.args[ i ] !== arguments[ i ] ) {\n\t\t\t\t\tnode = node.next;\n\t\t\t\t\tcontinue searchCache;\n\t\t\t\t}\n\t\t\t}\n\n\t\t\t// At this point we can assume we've found a match\n\n\t\t\t// Surface matched node to head if not already\n\t\t\tif ( node !== head ) {\n\t\t\t\t// As tail, shift to previous. Must only shift if not also\n\t\t\t\t// head, since if both head and tail, there is no previous.\n\t\t\t\tif ( node === tail ) {\n\t\t\t\t\ttail = node.prev;\n\t\t\t\t}\n\n\t\t\t\t// Adjust siblings to point to each other. If node was tail,\n\t\t\t\t// this also handles new tail's empty `next` assignment.\n\t\t\t\tnode.prev.next = node.next;\n\t\t\t\tif ( node.next ) {\n\t\t\t\t\tnode.next.prev = node.prev;\n\t\t\t\t}\n\n\t\t\t\tnode.next = head;\n\t\t\t\tnode.prev = null;\n\t\t\t\thead.prev = node;\n\t\t\t\thead = node;\n\t\t\t}\n\n\t\t\t// Return immediately\n\t\t\treturn node.val;\n\t\t}\n\n\t\t// No cached value found. Continue to insertion phase:\n\n\t\t// Create a copy of arguments (avoid leaking deoptimization)\n\t\targs = new Array( len );\n\t\tfor ( i = 0; i < len; i++ ) {\n\t\t\targs[ i ] = arguments[ i ];\n\t\t}\n\n\t\tnode = {\n\t\t\targs: args,\n\n\t\t\t// Generate the result from original function\n\t\t\tval: fn.apply( null, args )\n\t\t};\n\n\t\t// Don't need to check whether node is already head, since it would\n\t\t// have been returned above already if it was\n\n\t\t// Shift existing head down list\n\t\tif ( head ) {\n\t\t\thead.prev = node;\n\t\t\tnode.next = head;\n\t\t} else {\n\t\t\t// If no head, follows that there's no tail (at initial or reset)\n\t\t\ttail = node;\n\t\t}\n\n\t\t// Trim tail if we're reached max size and are pending cache insertion\n\t\tif ( size === maxSize ) {\n\t\t\ttail = tail.prev;\n\t\t\ttail.next = null;\n\t\t} else {\n\t\t\tsize++;\n\t\t}\n\n\t\thead = node;\n\n\t\treturn node.val;\n\t}\n\n\tmemoized.clear = function() {\n\t\thead = null;\n\t\ttail = null;\n\t\tsize = 0;\n\t};\n\n\tif ( false ) {}\n\n\treturn memoized;\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/memize/index.js?");

/***/ }),

/***/ "./node_modules/tannin/index.js":
/*!**************************************!*\
  !*** ./node_modules/tannin/index.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return Tannin; });\n/* harmony import */ var _tannin_plural_forms__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @tannin/plural-forms */ \"./node_modules/@tannin/plural-forms/index.js\");\n\n\n/**\n * Tannin constructor options.\n *\n * @property {?string}   contextDelimiter Joiner in string lookup with context.\n * @property {?Function} onMissingKey     Callback to invoke when key missing.\n *\n * @type {Object}\n *\n * @typedef {TanninOptions}\n */\n\n/**\n * Default Tannin constructor options.\n *\n * @type {TanninOptions}\n */\nvar DEFAULT_OPTIONS = {\n\tcontextDelimiter: '\\u0004',\n\tonMissingKey: null,\n};\n\n/**\n * Given a specific locale data's config `plural_forms` value, returns the\n * expression.\n *\n * @example\n *\n * ```\n * getPluralExpression( 'nplurals=2; plural=(n != 1);' ) === '(n != 1)'\n * ```\n *\n * @param {string} pf Locale data plural forms.\n *\n * @return {string} Plural forms expression.\n */\nfunction getPluralExpression( pf ) {\n\tvar parts, i, part;\n\n\tparts = pf.split( ';' );\n\n\tfor ( i = 0; i < parts.length; i++ ) {\n\t\tpart = parts[ i ].trim();\n\t\tif ( part.indexOf( 'plural=' ) === 0 ) {\n\t\t\treturn part.substr( 7 );\n\t\t}\n\t}\n}\n\n/**\n * Tannin constructor.\n *\n * @param {Object}        data    Jed-formatted locale data.\n * @param {TanninOptions} options Tannin options.\n */\nfunction Tannin( data, options ) {\n\tvar key;\n\n\tthis.data = data;\n\tthis.pluralForms = {};\n\n\toptions = options || {};\n\tthis.options = {};\n\tfor ( key in DEFAULT_OPTIONS ) {\n\t\tthis.options[ key ] = options[ key ] || DEFAULT_OPTIONS[ key ];\n\t}\n}\n\n/**\n * Returns the plural form index for the given domain and value.\n *\n * @param {string} domain Domain on which to calculate plural form.\n * @param {number} n      Value for which plural form is to be calculated.\n *\n * @return {number} Plural form index.\n */\nTannin.prototype.getPluralForm = function( domain, n ) {\n\tvar getPluralForm = this.pluralForms[ domain ],\n\t\tconfig, plural;\n\n\tif ( ! getPluralForm ) {\n\t\tconfig = this.data[ domain ][ '' ];\n\t\tplural = getPluralExpression(\n\t\t\tconfig[ 'Plural-Forms' ] ||\n\t\t\tconfig[ 'plural-forms' ] ||\n\t\t\tconfig.plural_forms\n\t\t);\n\n\t\tgetPluralForm = this.pluralForms[ domain ] = Object(_tannin_plural_forms__WEBPACK_IMPORTED_MODULE_0__[\"default\"])( plural );\n\t}\n\n\treturn getPluralForm( n );\n};\n\n/**\n * Translate a string.\n *\n * @param {string} domain   Translation domain.\n * @param {string} context  Context distinguishing terms of the same name.\n * @param {string} singular Primary key for translation lookup.\n * @param {string} plural   Fallback value used for non-zero plural form index.\n * @param {number} n        Value to use in calculating plural form.\n *\n * @return {string} Translated string.\n */\nTannin.prototype.dcnpgettext = function( domain, context, singular, plural, n ) {\n\tvar index, key, entry;\n\n\tif ( n === undefined ) {\n\t\t// Default to singular.\n\t\tindex = 0;\n\t} else {\n\t\t// Find index by evaluating plural form for value.\n\t\tindex = this.getPluralForm( domain, n );\n\t}\n\n\tkey = singular;\n\n\t// If provided, context is prepended to key with delimiter.\n\tif ( context ) {\n\t\tkey = context + this.options.contextDelimiter + singular;\n\t}\n\n\tentry = this.data[ domain ][ key ];\n\n\t// Verify not only that entry exists, but that the intended index is within\n\t// range and non-empty.\n\tif ( entry && entry[ index ] ) {\n\t\treturn entry[ index ];\n\t}\n\n\tif ( this.options.onMissingKey ) {\n\t\tthis.options.onMissingKey( singular, domain );\n\t}\n\n\t// If entry not found, fall back to singular vs. plural with zero index\n\t// representing the singular value.\n\treturn index === 0 ? singular : plural;\n};\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/tannin/index.js?");

/***/ })

/******/ });