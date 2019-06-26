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
/******/ 	return __webpack_require__(__webpack_require__.s = "./assets/src/block.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/src/block.js":
/*!*****************************!*\
  !*** ./assets/src/block.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var __ = wp.i18n.__;\nvar registerBlockType = wp.blocks.registerBlockType;\nvar _wp$components = wp.components,\n    ServerSideRender = _wp$components.ServerSideRender,\n    PanelBody = _wp$components.PanelBody,\n    ExternalLink = _wp$components.ExternalLink,\n    SelectControl = _wp$components.SelectControl,\n    TextControl = _wp$components.TextControl;\nvar withSelect = wp.data.withSelect;\nvar InspectorControls = wp.editor.InspectorControls;\nvar Fragment = wp.element.Fragment;\nvar icon = \"search\";\nregisterBlockType(\"searchwp/modal-form\", {\n  title: __(\"Modal Form\", \"searchwpmodalform\"),\n  description: React.createElement(Fragment, null, React.createElement(\"p\", null, __(\"Insert a modal search form\", \"searchwpmodalform\")), React.createElement(ExternalLink, {\n    className: _SEARCHWP_MODAL_FORM_DATA.searchwp ? \"hidden\" : \"\",\n    href: \"https://searchwp.com/\"\n  }, __(\"Get SearchWP\", \"searchwpmodalform\"))),\n  icon: icon,\n  category: \"searchwp\",\n  keywords: [__(\"modal\", \"searchwpmodalform\"), __(\"search\", \"searchwpmodalform\"), __(\"overlay\", \"searchwpmodalform\")],\n  attributes: {\n    engine: {\n      type: \"string\",\n      default: \"default\"\n    },\n    template: {\n      type: \"string\",\n      default: \"Default\"\n    },\n    text: {\n      type: \"string\",\n      default: __(\"Search\", \"searchwpmodalform\")\n    },\n    type: {\n      type: \"string\",\n      default: \"link\"\n    }\n  },\n  edit: withSelect(function (select) {\n    return {\n      post_id: select(\"core/editor\").getCurrentPostId()\n    };\n  })(function (_ref) {\n    var post_id = _ref.post_id,\n        setAttributes = _ref.setAttributes,\n        attributes = _ref.attributes,\n        isSelected = _ref.isSelected;\n    var engine = attributes.engine,\n        template = attributes.template,\n        text = attributes.text,\n        type = attributes.type;\n    return React.createElement(\"div\", null, React.createElement(InspectorControls, null, React.createElement(PanelBody, {\n      initialOpen: true\n    }, React.createElement(TextControl, {\n      label: __(\"Text\", \"searchwpmodalform\"),\n      value: text,\n      onChange: function onChange(value) {\n        setAttributes({\n          text: value\n        });\n      }\n    }), React.createElement(SelectControl, {\n      label: __(\"Template\", \"searchwpmodalform\"),\n      value: \"\".concat(template),\n      options: _SEARCHWP_MODAL_FORM_DATA.templates,\n      onChange: function onChange(value) {\n        setAttributes({\n          template: value\n        });\n      }\n    }), React.createElement(SelectControl, {\n      label: __(\"Type\", \"searchwpmodalform\"),\n      value: \"\".concat(type),\n      options: [{\n        label: __(\"Link\", \"searchwpmodalform\"),\n        value: \"link\"\n      }, {\n        label: __(\"Button\", \"searchwpmodalform\"),\n        value: \"button\"\n      }],\n      onChange: function onChange(value) {\n        setAttributes({\n          type: value\n        });\n      }\n    }))), React.createElement(ServerSideRender, {\n      block: \"searchwp/modal-form\",\n      attributes: {\n        engine: engine,\n        template: template,\n        text: text,\n        type: type\n      }\n    }));\n  }),\n  save: function save() {\n    return null;\n  }\n});\n\n//# sourceURL=webpack:///./assets/src/block.js?");

/***/ })

/******/ });