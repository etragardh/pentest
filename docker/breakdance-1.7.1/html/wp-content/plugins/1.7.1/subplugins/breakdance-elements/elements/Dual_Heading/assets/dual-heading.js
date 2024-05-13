(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  class BreakdanceDualHeading {
    options = {
      primary: {
        brackets: { bottom: true },
        color: "#DA2E2E",
        duration: 3000,
        iterations: 4,
        multiline: true,
        stroke_width: 4,
        rtl: false,
        type: "underline",
      },
      secondary: {
        brackets: { bottom: true },
        duration: 3000,
        iterations: 4,
        color: "#EFB500",
        multiline: true,
        stroke_width: 4,
        rtl: false,
        type: "highlight",
      },
    };

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      this.options = mergeObjects(this.options, options);
      this.init();
    }

    initObserver() {
      if (!this.element) return;

      this.ob = new IntersectionObserver(
        (entries, observer) => {
          if (!entries) return;
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              this.initRoughNotation();
              observer.unobserve(this.element);
            }
          });
        },
        {
          root: null,
          rootMargin: "0px",
          threshold: 0.5,
        }
      );
      this.ob.observe(this.element);
    }

    // Init RoughNotation.js
    initRoughNotation() {
      const annotate = RoughNotation.annotate;

      this.notationElements = document.querySelectorAll(
        `${this.selector} .js-annotate`
      );

      if (!this.notationElements) return;

      this.notationElements.forEach((item) => {
        const style = item.dataset.style;
        const brackets = this.options[style].brackets;

        item.notation = annotate(item, {
          brackets: Object.keys(brackets).filter((k) => brackets[k]),
          type: this.options[style].type,
          color: this.options[style].color,
          animate: 1,
          animationDuration: this.options[style].duration,
          strokeWidth: this.options[style].stroke_width,
          multiline: this.options[style].multiline,
          iterations: this.options[style].iterations,
          rtl: this.options[style].rtl,
        });

        item.notation.show();
      });
    }

    // Update plugin
    destroyRoughNotation() {
      if (!this.notationElements) return;

      this.notationElements.forEach((item) => {
        item.notation.remove();
      });
      this.notationElements = null;
    }

    // Update plugin
    update(options = {}) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.destroy();
      this.init();
    }

    // Destory plugin
    destroy() {
      this.destroyRoughNotation();
      this.ob.unobserve(this.element);
    }

    // Init plugin
    init() {
      this.initObserver();
    }
  }
  window.BreakdanceDualHeading = BreakdanceDualHeading;
})();
