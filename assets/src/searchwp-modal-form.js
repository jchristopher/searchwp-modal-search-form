import MicroModal from "micromodal";
import whenDomReady from 'when-dom-ready';

MicroModal.init({
  onShow: function(modal) {
    let $el = modal.querySelectorAll('[name="s"]')[0];
    $el.focus();
    $el.select();
  }
});

whenDomReady(function() {
  // We're implementing our own click handlers for opening/closing modals so
  // we have a bit more control over how the events execute (e.g. preventing default)
  let showing = "";
  let triggers = document.querySelectorAll("[data-searchwp-modal-trigger]");
  let closers = document.querySelectorAll("[data-searchwp-modal-form-close]");

  triggers.forEach(function(trigger) {
    trigger.addEventListener(
      "click",
      function(event) {
        event.preventDefault();
        let modal = event.currentTarget.getAttribute(
          "data-searchwp-modal-trigger"
        );
        showing = modal;
        MicroModal.show(modal, {
          onShow: function(obj) {
            if (window.jQuery) {
              jQuery('body').trigger('searchwpModalOnShow', {
                modal: modal,
                el: jQuery('#' + modal),
                obj: obj
              });
            }
          },
          onClose: function(obj) {
            if (window.jQuery) {
              jQuery('body').trigger('searchwpModalOnClose', {
                modal: modal,
                el: jQuery('#' + modal),
                obj: obj
              });
            }
          }
        });
      },
      false
    );
  });

  closers.forEach(function(closer) {
    closer.addEventListener(
      "click",
      function(event) {
        if (
          event.target.hasAttribute("data-searchwp-modal-form-close")
        ) {
          event.preventDefault();
          MicroModal.close(showing);
          showing = "";
        }
      },
      true
    );
  });
});
