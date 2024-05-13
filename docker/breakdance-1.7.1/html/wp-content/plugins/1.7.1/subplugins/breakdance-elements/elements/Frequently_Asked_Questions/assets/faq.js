(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  class BreakdanceFaq {
    options = {
      accordion: false,
      openFirst: false,
    };

    constructor(selector, options) {
      this.selector = selector;
      this.options = mergeObjects(this.options, options || {});
      this.init();
    }

    toggleItem(event, item = false) {
      const targetEl = item ? item : event.target;
      const faqEl = targetEl.closest(".bde-faq__item");
      const faqButton = faqEl.querySelector(".bde-faq__question");
      const faqContent = faqEl.querySelector(".bde-faq__answer");
      const isActive = faqEl.classList.contains("is-active");

      if (this.options.accordion === true) {
        this.closeAllItems();
      }

      faqEl.classList.toggle("is-active");
      faqButton.setAttribute("aria-expanded", !isActive);
      faqContent.hidden = isActive;
    }

    closeItem(item) {
      const faqEl = item.closest(".bde-faq__item");
      const faqButton = faqEl.querySelector(".bde-faq__question");
      const faqContent = faqEl.querySelector(".bde-faq__answer");

      faqEl.classList.remove("is-active");
      faqButton.setAttribute("aria-expanded", false);
      faqContent.hidden = true;
    }

    closeAllItems() {
      this.elements.forEach((item) => this.closeItem(item));
    }

    update(options = {}) {
      this.options = mergeObjects(this.options, options);
      this.destroy();
      this.init();
    }

    openFirst() {
      if (!this.elements[0]) return;
      this.toggleItem(false, this.elements[0]);
    }

    destroy() {
      this.elements = document.querySelectorAll(
        `${this.selector} .js-faq-item`
      );

      if (!this.elements) return;

      this.closeAllItems();
      this.elements.forEach((item) =>
        item.removeEventListener("click", this.onClick)
      );
    }

    bindClickListeners() {
      this.onClick = this.toggleItem.bind(this);
      this.elements.forEach((item) => {
        item.addEventListener("click", this.onClick);
      });
    }

    init() {
      this.elements = document.querySelectorAll(
        `${this.selector} .js-faq-item`
      );

      if (this.options.openFirst === true) {
        this.openFirst();
      }

      this.bindClickListeners();
    }
  }

  window.BreakdanceFaq = BreakdanceFaq;
})();
