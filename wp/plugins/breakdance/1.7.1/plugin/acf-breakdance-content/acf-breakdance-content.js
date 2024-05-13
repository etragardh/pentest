(function() {
  function init() {
    attachEventListeners();
  }

  function attachEventListeners() {
    document.addEventListener("click", function(event) {
      if (event.target.matches(".breakdance-content-area--add-block")) {
        event.preventDefault();
        handleCreate(event);
      }

      if (event.target.matches(".breakdance-content-area--remove-block")) {
        event.preventDefault();
        if (confirm("This will delete the field content. Are you sure?")) {
          handleRemove(event);
        }
      }
    });
  }

  function makeAjaxRequest(field, postId, action) {
    const { ajaxNonce, ajaxUrl } = window.breakdanceConfig;
    const blockData = new FormData();
    blockData.append("field", field);
    blockData.append("postId", postId);
    blockData.append("action", action);
    const url = new URL(ajaxUrl);
    url.searchParams.append("_ajax_nonce", ajaxNonce);
    return fetch(url.toString(), {
      method: "POST",
      body: blockData
    }).then(response => {
      return response.json().then(data => {
        return data;
      });
    });
  }

  function handleCreate(event) {
    const createButton = event.target;
    const acfInput = createButton.closest(".acf-input");
    const spinner = acfInput.querySelector(".spinner");
    createButton.disabled = true;
    spinner.classList.add("is-active");
    const { field, postId } = createButton.dataset;
    makeAjaxRequest(field, postId, "breakdance_save_acf_block")
      .then(data => {
        const newTab = window.breakdanceUtils.isAuxClick(event);
        if (document.body.classList.contains("block-editor-page")) {
          window.breakdanceUtils
            .autogenerateTitleIfNotSet()
            .saveGutenberg(() => {
              window.breakdanceUtils.redirectToBuilder(
                newTab,
                data.block.editInBreakdanceLink
              );
            });
        } else {
          window.breakdanceUtils.autogenerateTitleIfNotSet().saveClassic(() => {
            window.breakdanceUtils.redirectToBuilder(
              newTab,
              data.block.editInBreakdanceLink
            );
          });
        }
      })
      .catch(error => {
        const acfField = createButton.closest(".acf-field");
        const { key } = acfField.dataset;
        acf.getField(key).showError("Error creating Breakdance block");
      })
      .finally(() => {
        spinner.classList.remove("is-active");
        createButton.removeAttribute("disabled");
      });
  }

  function handleRemove(event) {
    const removeButton = event.target;
    const acfInput = removeButton.closest(".acf-input");
    const spinner = acfInput.querySelector(".spinner");
    const { field, postId } = removeButton.dataset;
    removeButton.disabled = true;
    spinner.classList.add("is-active");
    makeAjaxRequest(field, postId, "breakdance_delete_acf_block")
      .then(() => {
        acfInput
          .querySelector(".breakdance-content-area--add-block")
          .classList.remove("hidden");
        acfInput
          .querySelector(".breakdance-content-area--remove-block")
          .classList.add("hidden");
        acfInput
          .querySelector(".breakdance-content-area--edit-block")
          .classList.add("hidden");
      })
      .catch(() => {
        const acfField = removeButton.closest(".acf-field");
        const { key } = acfField.dataset;
        acf.getField(key).showError("Error deleting Breakdance block");
      })
      .finally(() => {
        spinner.classList.remove("is-active");
        removeButton.removeAttribute("disabled");
      });
  }

  init();
})();
