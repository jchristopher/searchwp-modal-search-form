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

var version = "0.3.2",
    classCallCheck = function (e, o) {
  if (!(e instanceof o)) throw new TypeError("Cannot call a class as a function");
},
    createClass = function () {
  function e(e, o) {
    for (var t = 0; t < o.length; t++) {
      var n = o[t];
      n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(e, n.key, n);
    }
  }

  return function (o, t, n) {
    return t && e(o.prototype, t), n && e(o, n), o;
  };
}(),
    toConsumableArray = function (e) {
  if (Array.isArray(e)) {
    for (var o = 0, t = Array(e.length); o < e.length; o++) t[o] = e[o];

    return t;
  }

  return Array.from(e);
},
    MicroModal = function () {
  var e = ["a[href]", "area[href]", 'input:not([disabled]):not([type="hidden"]):not([aria-hidden])', "select:not([disabled]):not([aria-hidden])", "textarea:not([disabled]):not([aria-hidden])", "button:not([disabled]):not([aria-hidden])", "iframe", "object", "embed", "[contenteditable]", '[tabindex]:not([tabindex^="-"])'],
      o = function () {
    function o(e) {
      var t = e.targetModal,
          n = e.triggers,
          i = void 0 === n ? [] : n,
          a = e.onShow,
          r = void 0 === a ? function () {} : a,
          s = e.onClose,
          l = void 0 === s ? function () {} : s,
          c = e.openTrigger,
          d = void 0 === c ? "data-micromodal-trigger" : c,
          u = e.closeTrigger,
          h = void 0 === u ? "data-micromodal-close" : u,
          f = e.disableScroll,
          v = void 0 !== f && f,
          m = e.disableFocus,
          g = void 0 !== m && m,
          b = e.awaitCloseAnimation,
          y = void 0 !== b && b,
          k = e.debugMode,
          w = void 0 !== k && k;
      classCallCheck(this, o), this.modal = document.getElementById(t), this.config = {
        debugMode: w,
        disableScroll: v,
        openTrigger: d,
        closeTrigger: h,
        onShow: r,
        onClose: l,
        awaitCloseAnimation: y,
        disableFocus: g
      }, i.length > 0 && this.registerTriggers.apply(this, toConsumableArray(i)), this.onClick = this.onClick.bind(this), this.onKeydown = this.onKeydown.bind(this);
    }

    return createClass(o, [{
      key: "registerTriggers",
      value: function () {
        for (var e = this, o = arguments.length, t = Array(o), n = 0; n < o; n++) t[n] = arguments[n];

        t.filter(Boolean).forEach(function (o) {
          o.addEventListener("click", function () {
            return e.showModal();
          });
        });
      }
    }, {
      key: "showModal",
      value: function () {
        this.activeElement = document.activeElement, this.modal.setAttribute("aria-hidden", "false"), this.modal.classList.add("is-open"), this.setFocusToFirstNode(), this.scrollBehaviour("disable"), this.addEventListeners(), this.config.onShow(this.modal);
      }
    }, {
      key: "closeModal",
      value: function () {
        var e = this.modal;
        this.modal.setAttribute("aria-hidden", "true"), this.removeEventListeners(), this.scrollBehaviour("enable"), this.activeElement && this.activeElement.focus(), this.config.onClose(this.modal), this.config.awaitCloseAnimation ? this.modal.addEventListener("animationend", function o() {
          e.classList.remove("is-open"), e.removeEventListener("animationend", o, !1);
        }, !1) : e.classList.remove("is-open");
      }
    }, {
      key: "closeModalById",
      value: function (e) {
        this.modal = document.getElementById(e), this.modal && this.closeModal();
      }
    }, {
      key: "scrollBehaviour",
      value: function (e) {
        if (this.config.disableScroll) {
          var o = document.querySelector("body");

          switch (e) {
            case "enable":
              Object.assign(o.style, {
                overflow: "",
                height: ""
              });
              break;

            case "disable":
              Object.assign(o.style, {
                overflow: "hidden",
                height: "100vh"
              });
          }
        }
      }
    }, {
      key: "addEventListeners",
      value: function () {
        this.modal.addEventListener("touchstart", this.onClick), this.modal.addEventListener("click", this.onClick), document.addEventListener("keydown", this.onKeydown);
      }
    }, {
      key: "removeEventListeners",
      value: function () {
        this.modal.removeEventListener("touchstart", this.onClick), this.modal.removeEventListener("click", this.onClick), document.removeEventListener("keydown", this.onKeydown);
      }
    }, {
      key: "onClick",
      value: function (e) {
        e.target.hasAttribute(this.config.closeTrigger) && (this.closeModal(), e.preventDefault());
      }
    }, {
      key: "onKeydown",
      value: function (e) {
        27 === e.keyCode && this.closeModal(e), 9 === e.keyCode && this.maintainFocus(e);
      }
    }, {
      key: "getFocusableNodes",
      value: function () {
        var o = this.modal.querySelectorAll(e);
        return Array.apply(void 0, toConsumableArray(o));
      }
    }, {
      key: "setFocusToFirstNode",
      value: function () {
        if (!this.config.disableFocus) {
          var e = this.getFocusableNodes();
          e.length && e[0].focus();
        }
      }
    }, {
      key: "maintainFocus",
      value: function (e) {
        var o = this.getFocusableNodes();

        if (this.modal.contains(document.activeElement)) {
          var t = o.indexOf(document.activeElement);
          e.shiftKey && 0 === t && (o[o.length - 1].focus(), e.preventDefault()), e.shiftKey || t !== o.length - 1 || (o[0].focus(), e.preventDefault());
        } else o[0].focus();
      }
    }]), o;
  }(),
      t = null,
      n = function (e, o) {
    var t = [];
    return e.forEach(function (e) {
      var n = e.attributes[o].value;
      void 0 === t[n] && (t[n] = []), t[n].push(e);
    }), t;
  },
      i = function (e) {
    if (!document.getElementById(e)) return console.warn("MicroModal v" + version + ": ❗Seems like you have missed %c'" + e + "'", "background-color: #f8f9fa;color: #50596c;font-weight: bold;", "ID somewhere in your code. Refer example below to resolve it."), console.warn("%cExample:", "background-color: #f8f9fa;color: #50596c;font-weight: bold;", '<div class="modal" id="' + e + '"></div>'), !1;
  },
      a = function (e) {
    if (e.length <= 0) return console.warn("MicroModal v" + version + ": ❗Please specify at least one %c'micromodal-trigger'", "background-color: #f8f9fa;color: #50596c;font-weight: bold;", "data attribute."), console.warn("%cExample:", "background-color: #f8f9fa;color: #50596c;font-weight: bold;", '<a href="#" data-micromodal-trigger="my-modal"></a>'), !1;
  },
      r = function (e, o) {
    if (a(e), !o) return !0;

    for (var t in o) i(t);

    return !0;
  };

  return {
    init: function (e) {
      var t = Object.assign({}, {
        openTrigger: "data-micromodal-trigger"
      }, e),
          i = [].concat(toConsumableArray(document.querySelectorAll("[" + t.openTrigger + "]"))),
          a = n(i, t.openTrigger);
      if (!0 !== t.debugMode || !1 !== r(i, a)) for (var s in a) {
        var l = a[s];
        t.targetModal = s, t.triggers = [].concat(toConsumableArray(l)), new o(t);
      }
    },
    show: function (e, n) {
      var a = n || {};
      a.targetModal = e, !0 === a.debugMode && !1 === i(e) || (t = new o(a)).showModal();
    },
    close: function (e) {
      e ? t.closeModalById(e) : t.closeModal();
    }
  };
}();

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

      _micromodal.default.show(modal);
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