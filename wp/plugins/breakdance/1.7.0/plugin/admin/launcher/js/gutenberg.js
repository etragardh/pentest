(function ($) {
  function openInBreakdance(event) {
    event.preventDefault();
    const newTab = window.breakdanceUtils.isAuxClick( event );

    window.breakdanceUtils
      .autogenerateTitleIfNotSet()
      .saveGutenberg(() => {
        window.breakdanceUtils.redirectToBuilder(newTab);
      });
  }

  function addButtonToEnableBreakdance() {
    const buttonId = "breakdance-mode-switch-edit-with-breakdance";

    if (exists(`#${buttonId}`)) return;

    $("<button></button>")
      .attr("id", buttonId)
      .text(window.breakdanceConfig.strings.openButton)
      .attr("data-breakdance-action", "edit")
      .addClass('breakdance-launcher-small-button')
      .appendTo(".edit-post-header-toolbar")
      .on('click', openInBreakdance);
  }

  function addBreakdanceLauncherToBlockEditor() {
    const { select } = wp.data;

    const editorAlreadyHasLauncherBlock = select('core/block-editor')
      .getGlobalBlockCount('breakdance/block-breakdance-launcher') > 0;

    if (editorAlreadyHasLauncherBlock) return;

    window.breakdanceUtils.addLauncherToGutenberg();
  }

  function exists(node) {
    return !!document.querySelector(node);
  }

  function gutenbergEditorReady(callback) {
    if (!window.breakdanceConfig.isGutenberg) {
      return;
    }

    const { subscribe, select } = wp.data;
    const unsubscribe = subscribe(() => {
      const isReady = select( 'core/editor' ).getEditedPostAttribute('status');
      if (!isReady) return;

      // Run in the next execution block.
      setTimeout(() => {
        callback();
        unsubscribe();
      });
    });
  }

  function gutenbergToolbarReady(callback) {
    const targetNode = document.getElementById('editor');

    const config = {
      childList: true,
      subtree: true
    };

    const observer = new MutationObserver(() => {
      if (!exists(".edit-post-header-toolbar")) {
        return;
      }

      callback();
    });

    observer.observe(targetNode, config);
  }

  function addBreakdanceUiToBlockEditor() {
    if (window.breakdanceConfig.hasFullAccess) {
      gutenbergToolbarReady(addButtonToEnableBreakdance);
    }
    gutenbergEditorReady(addBreakdanceLauncherToBlockEditor);
  }

  $("document").ready(addBreakdanceUiToBlockEditor);
}(jQuery));
