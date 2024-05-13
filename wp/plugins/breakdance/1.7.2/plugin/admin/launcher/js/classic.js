(function ($) {
  function openInBreakdance(event) {
    event.preventDefault();
    const newTab = window.breakdanceUtils.isAuxClick( event );

    window.breakdanceUtils
      .autogenerateTitleIfNotSet()
      .saveClassic(() => {
        window.breakdanceUtils.redirectToBuilder(newTab);
      });
  }

  function disableBreakdance(event) {
    event.preventDefault();

    if ( window.breakdanceConfig.mode === 'breakdance' ) {
      return window.breakdanceUtils.disableAndExtractContent(() => {
        showClassicEditor();
      });
    }

    showClassicEditor();
  }

  function showClassicEditor() {
    $('.breakdance-launcher').hide();
    $('body').removeClass('is-breakdance-available');
    $(window).resize(); // Trigger TinyMCE resize
  }

  function addListeners() {
    $("[data-breakdance-action='edit']").on('click', openInBreakdance);
    $("[data-breakdance-action='disable']").on('click', disableBreakdance);
  }

  $("document").ready(() => {
    addListeners();
    if (!window.breakdanceConfig.isNew && window.breakdanceConfig.mode !== 'breakdance') {
      showClassicEditor();
    }
  });
}(jQuery));
