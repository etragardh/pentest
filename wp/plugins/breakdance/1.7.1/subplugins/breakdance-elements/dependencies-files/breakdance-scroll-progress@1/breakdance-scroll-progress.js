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

  class BreakdanceScrollProgress {
    defaultOptions = {
      type: "line",
      direction: "right",
      selector: null,
      track: null,
    };

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      this.percentageSpan = this.element.querySelector(
        ".js-current-percentage"
      );

      this.options = mergeObjects(this.defaultOptions, options);

      this.scrollTarget =
        this.options.selector &&
        this.options.track == "custom" &&
        isSelectorValid(this.options.selector)
          ? document.querySelector(this.options.selector)
          : document.body;

      this.init();
    }

    updateProgressCircle(progress) {
      const pathLength = this.progressPath.getTotalLength();
      let progressAsStroke = pathLength - (progress / 100) * pathLength;
      if (this.options.direction == "left")
        progressAsStroke = progressAsStroke * -1;
      this.progressPath.style.strokeDashoffset = progressAsStroke;
      if (this.percentageSpan) {
        this.percentageSpan.innerHTML = `${progress}%`;
      }
    }

    updateProgressLine(progress) {
      this.element.style.setProperty(
        "--bde-scroll-progress-current",
        `${progress}%`
      );
      if (this.percentageSpan) {
        this.percentageSpan.innerHTML = `${progress}%`;
      }
    }

    updateScrollValue() {
      if (typeof ScrollTrigger !== "function") return;

      const start = this.options.track == "custom" ? "top bottom" : "top top";

      this.scrollTrigger = ScrollTrigger.create({
        trigger: this.scrollTarget,
        start,
        end: "bottom bottom",
        scrub: true,
        onUpdate: (self) => {
          this.updateTracker(self.progress);
        },
      });
    }

    updateTracker(value) {
      let percentageValue = (value * 100).toFixed(0);
      if (this.options.type == "circle") {
        this.updateProgressCircle(percentageValue);
      } else {
        this.updateProgressLine(percentageValue);
      }
    }

    initCircularTracker() {
      this.progressPath = this.element.querySelector(
        ".js-progress-svg path.progress"
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

    update() {
      this.destroy();
      this.init();
    }

    destroy() {
      this.scrollTrigger.kill(true);
      this.scrollTrigger = null;
      this.scrollTarget = null;
    }

    init() {
      if (this.options.type == "circle") this.initCircularTracker();
      this.updateScrollValue();
    }
  }
  window.BreakdanceScrollProgress = BreakdanceScrollProgress;
})();
