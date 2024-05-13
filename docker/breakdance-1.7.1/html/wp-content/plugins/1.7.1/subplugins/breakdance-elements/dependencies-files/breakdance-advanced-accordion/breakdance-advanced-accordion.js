(function () {
  const accordionButtonSelector =
    ".bde-accordion__content-wrapper > .bde-accordion__title-tag > button[aria-expanded]";

  const { mergeObjects, isBuilder } = BreakdanceFrontend.utils;
  class BreakdanceAdvancedAccordion {
    defaultOptions = {
      accordion: false,
      openFirst: false,
      ignoreClickEvent: false
    };

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      this.options = mergeObjects(this.defaultOptions, options);
      this.isBuilder = isBuilder();
      this.init();
    }

    init() {
      this.accordions = this.getAccordions();
      this.handleOnClick = this.onClick.bind(this);

      // Builder: remove click listener in favor of `activateAccordionFromSelector`
      if (!this.options.ignoreClickEvent) {
        this.accordions.forEach((accordion) => {
          accordion.addEventListener("click", this.handleOnClick);
        });
      }

      if (this.options.openFirst) {
        this.toggleAccordion(this.accordions[0], "open");
      }

      if (this.isBuilder && this.accordions.length == 1) {
        this.toggleAccordion(this.accordions[0], "open");
      }
    }

    getAccordions() {
      // Return all accordions that are direct children of the selector.
      // This is to prevent nested accordions from being selected.
      return document.querySelectorAll(`${this.selector} > ${accordionButtonSelector}`);
    }

    onClick(event) {
      if (this.options.accordion) {
        this.closeAllAccordions();
      }
      this.toggleAccordion(event.currentTarget);
    }

    toggleAccordion(accordion, action = "toggle") {
      const controlsId = accordion.getAttribute("aria-controls");
      const contentEl = this.element.querySelector(`#${controlsId}`);

      switch (action) {
        case "toggle":
          if (!accordion.classList.contains("is-active")) {
            contentEl.removeAttribute("hidden");
            accordion.classList.add("is-active");
            accordion.setAttribute("aria-expanded", "true");
          } else {
            contentEl.setAttribute("hidden", "");
            accordion.classList.remove("is-active");
            accordion.setAttribute("aria-expanded", "false");
          }
          break;

        case "close":
          contentEl.setAttribute("hidden", "");
          accordion.classList.remove("is-active");
          accordion.setAttribute("aria-expanded", "false");
          break;

        case "open":
          contentEl.removeAttribute("hidden");
          accordion.classList.add("is-active");
          accordion.setAttribute("aria-expanded", "true");
          break;

        default:
          console.warn(`Something went wrong while opening accordion.`);
      }
    }

    closeAllAccordions() {
      this.accordions.forEach((accordion) => {
        this.toggleAccordion(accordion, "close");
      });
    }

    destroy() {
      this.accordions.forEach((accordion) => {
        this.toggleAccordion(accordion, "close");
        accordion.removeEventListener("click", this.handleOnClick);
      });
      this.accordions = null;
    }

    update() {
      this.accordions = this.getAccordions();

      this.destroy();
      this.init();
    }

    activateAccordionFromSelector(selector) {
      this.update();
      this.closeAllAccordions();
      const buttonEl = selector.querySelector("button[aria-expanded]");
      const index = [].indexOf.call(this.accordions, buttonEl);

      if (index !== -1) {
        this.toggleAccordion(this.accordions[index], "open");
      } else {
        this.toggleAccordion(this.accordions[0], "open");
      }
    }
  }

  window.BreakdanceAdvancedAccordion = BreakdanceAdvancedAccordion;
})();
