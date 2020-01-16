// modules are defined as an array
// [ module function, map of requires ]
//
// map of requires is short require name -> numeric require
//
// anything defined in a previous bundle is accessed via the
// orig method which is the require for previous bundles
parcelRequire = (function (modules, cache, entry, globalName) {
  // Save the require from previous bundle to this closure if any
  var previousRequire = typeof parcelRequire === 'function' && parcelRequire;
  var nodeRequire = typeof require === 'function' && require;

  function newRequire(name, jumped) {
    if (!cache[name]) {
      if (!modules[name]) {
        // if we cannot find the module within our internal map or
        // cache jump to the current global require ie. the last bundle
        // that was added to the page.
        var currentRequire = typeof parcelRequire === 'function' && parcelRequire;
        if (!jumped && currentRequire) {
          return currentRequire(name, true);
        }

        // If there are other bundles on this page the require from the
        // previous one is saved to 'previousRequire'. Repeat this as
        // many times as there are bundles until the module is found or
        // we exhaust the require chain.
        if (previousRequire) {
          return previousRequire(name, true);
        }

        // Try the node require function if it exists.
        if (nodeRequire && typeof name === 'string') {
          return nodeRequire(name);
        }

        var err = new Error('Cannot find module \'' + name + '\'');
        err.code = 'MODULE_NOT_FOUND';
        throw err;
      }

      localRequire.resolve = resolve;
      localRequire.cache = {};

      var module = cache[name] = new newRequire.Module(name);

      modules[name][0].call(module.exports, localRequire, module, module.exports, this);
    }

    return cache[name].exports;

    function localRequire(x){
      return newRequire(localRequire.resolve(x));
    }

    function resolve(x){
      return modules[name][1][x] || x;
    }
  }

  function Module(moduleName) {
    this.id = moduleName;
    this.bundle = newRequire;
    this.exports = {};
  }

  newRequire.isParcelRequire = true;
  newRequire.Module = Module;
  newRequire.modules = modules;
  newRequire.cache = cache;
  newRequire.parent = previousRequire;
  newRequire.register = function (id, exports) {
    modules[id] = [function (require, module) {
      module.exports = exports;
    }, {}];
  };

  var error;
  for (var i = 0; i < entry.length; i++) {
    try {
      newRequire(entry[i]);
    } catch (e) {
      // Save first error but execute all entries
      if (!error) {
        error = e;
      }
    }
  }

  if (entry.length) {
    // Expose entry point to Node, AMD or browser globals
    // Based on https://github.com/ForbesLindesay/umd/blob/master/template.js
    var mainExports = newRequire(entry[entry.length - 1]);

    // CommonJS
    if (typeof exports === "object" && typeof module !== "undefined") {
      module.exports = mainExports;

    // RequireJS
    } else if (typeof define === "function" && define.amd) {
     define(function () {
       return mainExports;
     });

    // <script>
    } else if (globalName) {
      this[globalName] = mainExports;
    }
  }

  // Override the current require with this new one
  parcelRequire = newRequire;

  if (error) {
    // throw error from earlier, _after updating parcelRequire_
    throw error;
  }

  return newRequire;
})({"../../node_modules/micromodal/dist/micromodal.es.js":[function(require,module,exports) {
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

const MicroModal = (() => {
  const FOCUSABLE_ELEMENTS = ['a[href]', 'area[href]', 'input:not([disabled]):not([type="hidden"]):not([aria-hidden])', 'select:not([disabled]):not([aria-hidden])', 'textarea:not([disabled]):not([aria-hidden])', 'button:not([disabled]):not([aria-hidden])', 'iframe', 'object', 'embed', '[contenteditable]', '[tabindex]:not([tabindex^="-"])'];

  class Modal {
    constructor({
      targetModal,
      triggers = [],
      onShow = () => {},
      onClose = () => {},
      openTrigger = 'data-micromodal-trigger',
      closeTrigger = 'data-micromodal-close',
      disableScroll = false,
      disableFocus = false,
      awaitCloseAnimation = false,
      awaitOpenAnimation = false,
      debugMode = false
    }) {
      // Save a reference of the modal
      this.modal = document.getElementById(targetModal); // Save a reference to the passed config

      this.config = {
        debugMode,
        disableScroll,
        openTrigger,
        closeTrigger,
        onShow,
        onClose,
        awaitCloseAnimation,
        awaitOpenAnimation,
        disableFocus // Register click events only if pre binding eventListeners

      };
      if (triggers.length > 0) this.registerTriggers(...triggers); // pre bind functions for event listeners

      this.onClick = this.onClick.bind(this);
      this.onKeydown = this.onKeydown.bind(this);
    }
    /**
     * Loops through all openTriggers and binds click event
     * @param  {array} triggers [Array of node elements]
     * @return {void}
     */


    registerTriggers(...triggers) {
      triggers.filter(Boolean).forEach(trigger => {
        trigger.addEventListener('click', event => this.showModal(event));
      });
    }

    showModal() {
      this.activeElement = document.activeElement;
      this.modal.setAttribute('aria-hidden', 'false');
      this.modal.classList.add('is-open');
      this.scrollBehaviour('disable');
      this.addEventListeners();

      if (this.config.awaitOpenAnimation) {
        const handler = () => {
          this.modal.removeEventListener('animationend', handler, false);
          this.setFocusToFirstNode();
        };

        this.modal.addEventListener('animationend', handler, false);
      } else {
        this.setFocusToFirstNode();
      }

      this.config.onShow(this.modal, this.activeElement);
    }

    closeModal() {
      const modal = this.modal;
      this.modal.setAttribute('aria-hidden', 'true');
      this.removeEventListeners();
      this.scrollBehaviour('enable');

      if (this.activeElement) {
        this.activeElement.focus();
      }

      this.config.onClose(this.modal);

      if (this.config.awaitCloseAnimation) {
        this.modal.addEventListener('animationend', function handler() {
          modal.classList.remove('is-open');
          modal.removeEventListener('animationend', handler, false);
        }, false);
      } else {
        modal.classList.remove('is-open');
      }
    }

    closeModalById(targetModal) {
      this.modal = document.getElementById(targetModal);
      if (this.modal) this.closeModal();
    }

    scrollBehaviour(toggle) {
      if (!this.config.disableScroll) return;
      const body = document.querySelector('body');

      switch (toggle) {
        case 'enable':
          Object.assign(body.style, {
            overflow: '',
            height: ''
          });
          break;

        case 'disable':
          Object.assign(body.style, {
            overflow: 'hidden',
            height: '100vh'
          });
          break;

        default:
      }
    }

    addEventListeners() {
      this.modal.addEventListener('touchstart', this.onClick);
      this.modal.addEventListener('click', this.onClick);
      document.addEventListener('keydown', this.onKeydown);
    }

    removeEventListeners() {
      this.modal.removeEventListener('touchstart', this.onClick);
      this.modal.removeEventListener('click', this.onClick);
      document.removeEventListener('keydown', this.onKeydown);
    }

    onClick(event) {
      if (event.target.hasAttribute(this.config.closeTrigger)) {
        this.closeModal();
        event.preventDefault();
      }
    }

    onKeydown(event) {
      if (event.keyCode === 27) this.closeModal(event);
      if (event.keyCode === 9) this.maintainFocus(event);
    }

    getFocusableNodes() {
      const nodes = this.modal.querySelectorAll(FOCUSABLE_ELEMENTS);
      return Array(...nodes);
    }

    setFocusToFirstNode() {
      if (this.config.disableFocus) return;
      const focusableNodes = this.getFocusableNodes();
      if (focusableNodes.length) focusableNodes[0].focus();
    }

    maintainFocus(event) {
      const focusableNodes = this.getFocusableNodes(); // if disableFocus is true

      if (!this.modal.contains(document.activeElement)) {
        focusableNodes[0].focus();
      } else {
        const focusedItemIndex = focusableNodes.indexOf(document.activeElement);

        if (event.shiftKey && focusedItemIndex === 0) {
          focusableNodes[focusableNodes.length - 1].focus();
          event.preventDefault();
        }

        if (!event.shiftKey && focusedItemIndex === focusableNodes.length - 1) {
          focusableNodes[0].focus();
          event.preventDefault();
        }
      }
    }

  }
  /**
   * Modal prototype ends.
   * Here on code is responsible for detecting and
   * auto binding event handlers on modal triggers
   */
  // Keep a reference to the opened modal


  let activeModal = null;
  /**
   * Generates an associative array of modals and it's
   * respective triggers
   * @param  {array} triggers     An array of all triggers
   * @param  {string} triggerAttr The data-attribute which triggers the module
   * @return {array}
   */

  const generateTriggerMap = (triggers, triggerAttr) => {
    const triggerMap = [];
    triggers.forEach(trigger => {
      const targetModal = trigger.attributes[triggerAttr].value;
      if (triggerMap[targetModal] === undefined) triggerMap[targetModal] = [];
      triggerMap[targetModal].push(trigger);
    });
    return triggerMap;
  };
  /**
   * Validates whether a modal of the given id exists
   * in the DOM
   * @param  {number} id  The id of the modal
   * @return {boolean}
   */


  const validateModalPresence = id => {
    if (!document.getElementById(id)) {
      console.warn(`MicroModal: \u2757Seems like you have missed %c'${id}'`, 'background-color: #f8f9fa;color: #50596c;font-weight: bold;', 'ID somewhere in your code. Refer example below to resolve it.');
      console.warn(`%cExample:`, 'background-color: #f8f9fa;color: #50596c;font-weight: bold;', `<div class="modal" id="${id}"></div>`);
      return false;
    }
  };
  /**
   * Validates if there are modal triggers present
   * in the DOM
   * @param  {array} triggers An array of data-triggers
   * @return {boolean}
   */


  const validateTriggerPresence = triggers => {
    if (triggers.length <= 0) {
      console.warn(`MicroModal: \u2757Please specify at least one %c'micromodal-trigger'`, 'background-color: #f8f9fa;color: #50596c;font-weight: bold;', 'data attribute.');
      console.warn(`%cExample:`, 'background-color: #f8f9fa;color: #50596c;font-weight: bold;', `<a href="#" data-micromodal-trigger="my-modal"></a>`);
      return false;
    }
  };
  /**
   * Checks if triggers and their corresponding modals
   * are present in the DOM
   * @param  {array} triggers   Array of DOM nodes which have data-triggers
   * @param  {array} triggerMap Associative array of modals and their triggers
   * @return {boolean}
   */


  const validateArgs = (triggers, triggerMap) => {
    validateTriggerPresence(triggers);
    if (!triggerMap) return true;

    for (var id in triggerMap) validateModalPresence(id);

    return true;
  };
  /**
   * Binds click handlers to all modal triggers
   * @param  {object} config [description]
   * @return void
   */


  const init = config => {
    // Create an config object with default openTrigger
    const options = Object.assign({}, {
      openTrigger: 'data-micromodal-trigger'
    }, config); // Collects all the nodes with the trigger

    const triggers = [...document.querySelectorAll(`[${options.openTrigger}]`)]; // Makes a mappings of modals with their trigger nodes

    const triggerMap = generateTriggerMap(triggers, options.openTrigger); // Checks if modals and triggers exist in dom

    if (options.debugMode === true && validateArgs(triggers, triggerMap) === false) return; // For every target modal creates a new instance

    for (var key in triggerMap) {
      let value = triggerMap[key];
      options.targetModal = key;
      options.triggers = [...value];
      activeModal = new Modal(options); // eslint-disable-line no-new
    }
  };
  /**
   * Shows a particular modal
   * @param  {string} targetModal [The id of the modal to display]
   * @param  {object} config [The configuration object to pass]
   * @return {void}
   */


  const show = (targetModal, config) => {
    const options = config || {};
    options.targetModal = targetModal; // Checks if modals and triggers exist in dom

    if (options.debugMode === true && validateModalPresence(targetModal) === false) return; // stores reference to active modal

    activeModal = new Modal(options); // eslint-disable-line no-new

    activeModal.showModal();
  };
  /**
   * Closes the active modal
   * @param  {string} targetModal [The id of the modal to close]
   * @return {void}
   */


  const close = targetModal => {
    targetModal ? activeModal.closeModalById(targetModal) : activeModal.closeModal();
  };

  return {
    init,
    show,
    close
  };
})();

var _default = MicroModal;
exports.default = _default;
},{}],"../../node_modules/domready/ready.js":[function(require,module,exports) {
var define;
/*!
  * domready (c) Dustin Diaz 2014 - License MIT
  */
!function (name, definition) {

  if (typeof module != 'undefined') module.exports = definition()
  else if (typeof define == 'function' && typeof define.amd == 'object') define(definition)
  else this[name] = definition()

}('domready', function () {

  var fns = [], listener
    , doc = document
    , hack = doc.documentElement.doScroll
    , domContentLoaded = 'DOMContentLoaded'
    , loaded = (hack ? /^loaded|^c/ : /^loaded|^i|^c/).test(doc.readyState)


  if (!loaded)
  doc.addEventListener(domContentLoaded, listener = function () {
    doc.removeEventListener(domContentLoaded, listener)
    loaded = 1
    while (listener = fns.shift()) listener()
  })

  return function (fn) {
    loaded ? setTimeout(fn, 0) : fns.push(fn)
  }

});

},{}],"searchwp-modal-form.js":[function(require,module,exports) {
"use strict";

var _micromodal = _interopRequireDefault(require("micromodal"));

var _domready = _interopRequireDefault(require("domready"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_micromodal.default.init({
  onShow: function onShow(modal) {
    var $el = modal.querySelectorAll('[name="s"]')[0];
    $el.focus();
    $el.select();
  }
});

(0, _domready.default)(function () {
  // We're implementing our own click handlers for opening/closing modals so
  // we have a bit more control over how the events execute (e.g. preventing default)
  var showing = "";
  var triggers = document.querySelectorAll("[data-searchwp-modal-trigger]");
  var closers = document.querySelectorAll("[data-searchwp-modal-form-close]");
  triggers.forEach(function (trigger) {
    trigger.addEventListener("click", function (event) {
      event.preventDefault();
      var modal = event.currentTarget.getAttribute("data-searchwp-modal-trigger");
      showing = modal;

      _micromodal.default.show(modal, {
        onShow: function onShow(obj) {
          if (window.jQuery) {
            jQuery('body').trigger('searchwpModalOnShow', {
              modal: modal,
              el: jQuery('#' + modal),
              obj: obj
            });
          }
        },
        onClose: function onClose(obj) {
          if (window.jQuery) {
            jQuery('body').trigger('searchwpModalOnClose', {
              modal: modal,
              el: jQuery('#' + modal),
              obj: obj
            });
          }
        }
      });
    }, false);
  });
  closers.forEach(function (closer) {
    closer.addEventListener("click", function (event) {
      if (event.target.hasAttribute("data-searchwp-modal-form-close")) {
        event.preventDefault();

        _micromodal.default.close(showing);

        showing = "";
      }
    }, true);
  });
});
},{"micromodal":"../../node_modules/micromodal/dist/micromodal.es.js","domready":"../../node_modules/domready/ready.js"}]},{},["searchwp-modal-form.js"], null)
//# sourceMappingURL=/searchwp-modal-form.js.map