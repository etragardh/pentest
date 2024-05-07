(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  class BreakdanceCircleCounter {
    options = {
      value: 75,
      max_value: 100,
      size: {
        size: 240,
        padding: 10,
        progress_bar: 20,
        background_bar: 10,
      },
      style: {
        value_position: "inside",
        padding: 18,
        progress_bar_style: "gradient",
        bar_style: "gradient",
        background: "transparent",
        background_bar: "#efefef",
        color: {
          progress_bar: "#5137c3",
        },
        line_cap: "round",
        gradient: {
          start: "#5137c3",
          end: "#D207C7FF",
        },
      },
    };
    degress = 0;
    animation = null;
    fps = 1000 / 200;
    percent = 0;
    onePercent = 0;
    posX = 0;
    posY = 0;
    animation = null;

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      this.canvas = this.element.querySelector(".js-canvas");
      this.circleValue = this.element.querySelector(".js-value");
      this.context = this.canvas.getContext("2d");
      this.options = mergeObjects(this.options, {
        ...options.content,
        ...options.design,
      });

      this.init();
    }

    getColor(color) {
      if (color.startsWith("var(--")) {
        // Infer the hexadecimal value from the element's computed style
        return window.getComputedStyle(this.element).getPropertyValue(color.slice(4, -1)).trim();
      }

      return color;
    }

    // Methods
    destroy() {
      this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
      clearInterval(this.animation);
      this.animation = null;
      this.degress = 0;
      this.posX = 0;
      this.posY = 0;
      this.percent = 0;
    }

    update(options = {}) {
      this.options = mergeObjects(options, this.options);
      this.destroy();
      this.init();
    }

    paint() {
      // increase the actual size of our canvas
      this.canvas.width = this.options.size.size * devicePixelRatio;
      this.canvas.height = this.options.size.size * devicePixelRatio;

      // ensure all drawing operations are scaled
      this.context.scale(devicePixelRatio, devicePixelRatio);

      // scale everything down using CSS
      this.canvas.style.width = this.options.size.size + "px";
      this.canvas.style.height = this.options.size.size + "px";

      this.onePercent = Number(360 / this.options.max_value);
      this.result = Number(this.onePercent * this.options.value);

      this.posX = Number(this.canvas.width / 2 / devicePixelRatio);
      this.posY = Number(this.canvas.height / 2 / devicePixelRatio);

      this.context.lineCap = this.options.style.line_cap;
      this.arcMove();
    }

    arcMove() {
      const { background_bar, color, bar_style, gradient, background } = this.options.style;

      this.animation = setInterval(() => {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.degress += 1;
        this.percent = this.degress / this.onePercent;
        if (this.circleValue) {
          this.circleValue.innerHTML = this.percent.toFixed();
        }

        this.context.beginPath();

        this.context.arc(
          this.posX,
          this.posY,
          this.options.size.size / 2 -
            this.options.size.progress_bar / 2 -
            this.options.size.padding,
          (Math.PI / 180) * 270,
          (Math.PI / 180) * (270 + 360)
        );

        this.context.strokeStyle = this.getColor(background_bar);
        this.context.fillStyle = this.getColor(background);
        this.context.lineWidth = this.options.size.background_bar;
        this.context.fill();
        this.context.stroke();
        this.context.beginPath();

        if (bar_style === "gradient") {
          this.gradient = this.context.createLinearGradient(
            0,
            0,
            this.options.size.size,
            this.options.size.size
          );

          this.gradient.addColorStop(0, this.getColor(gradient.start));
          this.gradient.addColorStop(1, this.getColor(gradient.end));

          this.context.strokeStyle = this.gradient;
        }

        if (bar_style === "color") {
          this.context.strokeStyle = this.getColor(color.progress_bar);
        }

        this.context.lineWidth = this.options.size.progress_bar;

        this.context.arc(
          this.posX,
          this.posY,
          this.options.size.size / 2 -
            this.options.size.progress_bar / 2 -
            this.options.size.padding,
          (Math.PI / 180) * 270,
          (Math.PI / 180) * (270 + this.degress)
        );

        this.context.stroke();

        if (this.degress >= this.result) {
          clearInterval(this.animation);
        }
      }, this.fps);
    }

    init() {
      this.paint();
    }
  }

  window.BreakdanceCircleCounter = BreakdanceCircleCounter;
})();
