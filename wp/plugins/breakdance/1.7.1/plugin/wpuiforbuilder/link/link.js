(function ($) {
  function onLinkSelect() {
    var attrs = wpLink.getAttrs();
    var event = new CustomEvent("breakdanceLinkChooserSelect", {
      detail: JSON.parse(JSON.stringify(attrs)),
    });

    window.parent.document.dispatchEvent(event);
  }

  function onLinkClose() {
    var event = new Event("breakdanceLinkChooserClose");
    window.parent.document.dispatchEvent(event);
  }

  function openModal() {
    wpLink.open('link-chooser');
    window.parent.document.dispatchEvent(new Event("breakdanceLinkChooserReady"));
  }

  function bindListenersAndOpenModal() {
    $('#link-chooser').on('change', onLinkSelect);
    $(document).on('wplink-close', onLinkClose);
    openModal();
  }

  $(document).ready(bindListenersAndOpenModal);
}(jQuery));
