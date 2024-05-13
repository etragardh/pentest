(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  class BreakdanceTooltip {
    defaultOptions = {
      placement: "bottom",
      offset: 10,
      preview: false,
      hideArrow: false,
    };

    constructor(selector, id, options) {
      this.selector = selector;
      this.id = id;
      this.element = document.querySelector(`${this.selector}`);
      this.options = mergeObjects(this.defaultOptions, options);
      this.isBuilder = !!window.parent.Breakdance;
      this.init();
    }

    destroy() {
      if (!this.tippyInstance) return;
      this.tippyInstance.destroy();
    }

    update() {
      this.destroy();
      this.init();
    }

    init() {
      if (typeof tippy !== "function") return;
      this.tippyInstance = tippy(this.element, {
        allowHTML: true,
        arrow: this.options.hideArrow ? false : true,
        offset: [0, this.options.offset],
        placement: this.options.placement,
        interactive: this.isBuilder ? false : true,
        theme: `bde-tooltip-theme-${this.id}`,
        trigger: this.options.preview ? "manual" : "mouseenter focus",
        hideOnClick: this.options.preview ? false : true,
        showOnCreate: this.options.preview ? true : false,
      });
    }
  }
  window.BreakdanceTooltip = BreakdanceTooltip;
})();
