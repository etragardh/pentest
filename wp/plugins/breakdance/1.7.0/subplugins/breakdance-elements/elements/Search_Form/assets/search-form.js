(function () {
  class BreakdanceSearchForm {
    constructor(selector) {
      this.selector = selector;
      this.element = this.queryElement(`${this.selector} .js-search-form`);
      this.type = this.element.dataset.type;
      this.expandButton = this.queryElement(
        `${this.selector} .js-search-form-expand-button`
      );
      this.closeButton = this.queryElement(
        `${this.selector} .js-search-form-close`
      );
      this.searchField = this.queryElement(
        `${this.selector} .js-search-form-field`
      );
      this.lightboxBg = this.queryElement(
        `${this.selector} .js-search-form-lightbox-bg`
      );

      this.init();
    }

    queryElement(element) {
      if (typeof element == "string") {
        return document.querySelector(element);
      }
      return element;
    }

    toggleForm() {
      this.element.classList.toggle("is-active");
      this.element.setAttribute("aria-hidden", "false");
      this.expandButton.setAttribute("aria-expanded", "true");

      this.searchField.focus();
    }

    closeFormEscape(event) {
      if (event.key === "Escape") {
        this.closeForm();
      }
    }

    closeForm() {
      this.element.classList.remove("is-active");
      this.element.setAttribute("aria-hidden", "true");
      this.expandButton.setAttribute("aria-expanded", "false");
      this.searchField.blur();
    }

    // Methods
    destroy() {
      if (this.type == "expand") {
        this.expandButton.removeEventListener("click", this.handleToggleForm);
      }

      if (this.type == "full-screen") {
        this.expandButton.removeEventListener("click", this.handleToggleForm);
        this.closeButton.removeEventListener("click", this.handleCloseForm);
        this.lightboxBg.removeEventListener("click", this.handleCloseForm);
        this.element.removeEventListener("keyup", this.handleCloseFromEscape);
      }
    }

    update() {
      this.destroy();
      this.init();
    }

    bindListeners() {
      this.handleToggleForm = this.toggleForm.bind(this);
      this.handleCloseForm = this.closeForm.bind(this);
      this.handleCloseFromEscape = this.closeFormEscape.bind(this);

      if (this.type == "expand") {
        this.expandButton.addEventListener("click", this.handleToggleForm);
      }

      if (this.type == "full-screen") {
        this.expandButton.addEventListener("click", this.handleToggleForm);
        this.closeButton.addEventListener("click", this.handleCloseForm);
        this.lightboxBg.addEventListener("click", this.handleCloseForm);
        this.element.addEventListener("keyup", this.handleCloseFromEscape);
      }
    }

    init() {
      this.bindListeners();
    }
  }

  window.BreakdanceSearchForm = BreakdanceSearchForm;
})();
