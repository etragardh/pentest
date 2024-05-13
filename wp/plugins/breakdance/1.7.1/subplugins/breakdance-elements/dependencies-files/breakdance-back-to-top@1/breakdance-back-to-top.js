(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  const isSelectorValid = (selector) => {
    const queryCheck = (s) =>
      document.createDocumentFragment().querySelector(s);

    try {
      queryCheck(selector);
    } catch {
      return false;
    }
    return true;
  };

  class BreakdanceBackToTop {
    defaultOptions = {
      type: "progress",
      show: "always",
      advanced: {
        scroll_offset: 0,
        scroll_to_selector: null,
      },
    };

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      this.button = document.querySelector(
        `${this.selector} .js-ee-back-to-top`
      );
      this.rootElement = document.documentElement;
      this.options = mergeObjects(this.defaultOptions, options);
      this.init();
    }

    destroy() {
      this.button.removeEventListener("click", this.handleClick);
      document.removeEventListener("scroll", this.handleScroll);
      if (this.options.type === "progress") {
        document.removeEventListener("scroll", this.handlePercentageScroll);
      }
    }

    init() {
      // handle button events
      this.handleClick = this.clickHandler.bind(this);
      this.button.addEventListener("click", this.handleClick);
      // handle scroll animations
      this.handleScroll = this.scrollHandler.bind(this);
      document.addEventListener("scroll", this.handleScroll);
      this.handleScroll();
      // handle scroll indicator
      if (this.options.type === "progress") {
        this.initIndicator();
        this.handlePercentageScroll = this.updateScrollPercent.bind(this);
        document.addEventListener("scroll", this.handlePercentageScroll);
      }
    }

    update() {
      this.destroy();
      this.init();
    }

    updateScrollPercent() {
      const heightOfWindow = window.innerHeight;
      const contentScrolled = window.pageYOffset;
      const bodyHeight = document.body.offsetHeight;
      let percentageVal = 0;

      if (bodyHeight - contentScrolled <= heightOfWindow) {
        percentageVal = 100;
      } else {
        const total = bodyHeight - heightOfWindow;
        const got = contentScrolled;
        percentageVal = parseInt((got / total) * 100);
      }
      const pathLength = this.progressPath.getTotalLength();
      const progress = pathLength - (percentageVal / 100) * pathLength;
      this.progressPath.style.strokeDashoffset = progress;
    }

    calculateOffset() {
      const offset = this.options.advanced.scroll_offset;
      const customSelector = this.options.advanced.scroll_to_selector;

      if (customSelector !== null && isSelectorValid(customSelector)) {
        const element = document.querySelector(customSelector);
        if (!element) return offset;
        const bodyRect = document.body.getBoundingClientRect().top;
        const elementRect = element.getBoundingClientRect().top;
        const elementPosition = elementRect - bodyRect;
        return elementPosition - offset;
      } else {
        return offset;
      }
    }

    initIndicator() {
      this.progressPath = this.element.querySelector(
        ".js-progress-svg path.tracker"
      );

      const pathLength = this.progressPath.getTotalLength();
      this.progressPath.style.transition =
        this.progressPath.style.WebkitTransition = "none";
      this.progressPath.style.strokeDasharray = pathLength + " " + pathLength;
      this.progressPath.style.strokeDashoffset = pathLength;
      this.progressPath.getBoundingClientRect();
      this.progressPath.style.transition =
        this.progressPath.style.WebkitTransition =
          "stroke-dashoffset 10ms linear";
    }

    clickHandler() {
      const offsetPosition = this.calculateOffset();

      this.rootElement.scrollTo({
        top: offsetPosition,
        behavior: "smooth",
      });
    }

    scrollHandler() {
      const offsetBottom = this.options.show === "half" ? 0.5 : 0.8;
      const scrollTotal =
        this.rootElement.scrollHeight - this.rootElement.clientHeight;
      if (this.rootElement.scrollTop / scrollTotal > offsetBottom) {
        this.button.classList.add("is-visible");
      } else if (this.rootElement.scrollTop === 0) {
        this.button.classList.add("is-top");
      } else {
        this.button.classList.remove("is-visible");
        this.button.classList.remove("is-top");
      }
    }
  }
  window.BreakdanceBackToTop = BreakdanceBackToTop;
})();
