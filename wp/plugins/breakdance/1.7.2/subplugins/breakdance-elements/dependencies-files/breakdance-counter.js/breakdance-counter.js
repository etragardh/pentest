(function () {
  const easeOutQuad = (t) => t * (2 - t);
  const { mergeObjects } = BreakdanceFrontend.utils;

  class BreakdanceCounter {
    defaultOptions = {
      start: 0,
      end: 100,
      duration: {
        number: 2000,
      },
      separator: "none",
      format_number: false,
      ease_count: false,
    };

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      this.digit = this.element.querySelector(".js-digit");
      this.options = mergeObjects(this.defaultOptions, options);
      this.init();
    }

    animateCountUp() {
      // Calculate how long each ‘frame’ should last if we want to update the animation 60 times per second
      this.frameDuration = 1000 / 60;
      // Use that to calculate how many frames we need to complete the animation
      this.totalFrames = Math.round(
        Math.max(this.options.duration.number / this.frameDuration, 1)
      );

      let frame = 0;
      const countTo = this.options.end;
      const countFrom = this.options.start;

      // Start the animation running 60 times per second
      this.counter = setInterval(() => {
        frame++;
        // Calculate our progress as a value between 0 and 1
        // Pass that value to our easing function to get our
        // progress on a curve
        const progress = this.options.ease_count
          ? easeOutQuad(frame / this.totalFrames)
          : frame / this.totalFrames;

        // Use the progress value to calculate the current count
        let currentCount = Math.floor(
          progress * (countTo - countFrom) + countFrom
        );

        // If the current count has changed, update the element
        if (parseInt(this.digit.innerHTML, 10) !== currentCount) {
          this.digit.innerHTML = this.formatNumber(currentCount);
        }

        // If we’ve reached our last frame, stop the animation
        if (frame === this.totalFrames) {
          clearInterval(this.counter);
        }
      }, this.frameDuration);
    }

    formatNumber(number) {
      const separators = {
        comma: ",",
        dot: ".",
        space: " ",
      };

      const separator = separators[this.options.separator] || "";

      const format = (num, separator) => {
        const n = String(num);
        const p = n.indexOf(".");
        return n.replace(/\d(?=(?:\d{3})+(?:\.|$))/g, (m, i) =>
          p < 0 || i < p ? `${m}${separator}` : m
        );
      };

      if (this.options.format_number) {
        return format(number, separator);
      }
      return number.toString();
    }

    update() {
      this.destroy();
      this.init();
    }

    destroy() {
      clearInterval(this.counter);
      this.ob.unobserve(this.element);
      this.digit.innerHTML = "";
    }

    initObserver() {
      if (!this.element) return;

      this.ob = new IntersectionObserver(
        (entries, observer) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              this.animateCountUp();
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

    init() {
      this.initObserver();
    }
  }
  window.BreakdanceCounter = BreakdanceCounter;
})();
