(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  class BreakdanceImageComparison {
    options = {
      settings: {
        auto_animation: false,
        starting_point_percent: 1,
        vertical_mode: false,
        start_on_hover: false,
        initial_position: 0.4,
        animation_speed: 1,
      },
    };

    constructor(selector, options) {
      this.selector = selector;
      this.wrap = document.querySelector(`${this.selector}`);
      this.element = document.querySelector(
        `${this.selector} .js-comparison-container`
      );
      this.imagesContainer = document.querySelector(
        `${this.selector} .js-image-container`
      );

      this.clone = this.element.cloneNode(true);
      this.options = mergeObjects(this.options, {
        ...options.content,
        ...options.design,
      });

      this.init();
    }

    show() {
      const { settings } = this.options;

      const pluginSettings = {
        orientation:
          settings.vertical_mode === true ? "vertical" : "horizontal",
        initialPosition: settings.initial_position,
        width: "100%",
        backgroundColor: "none",
        onPointerDown: settings.start_on_hover ? false : true,
        cursor: "ew-resize",
        dividingLine: "none",
        followEasingFactor: 0,
        interactive: true,
        autoAnimation: settings.auto_animation ? true : false,
        autoAnimationSpeed: settings.animation_speed
          ? settings.animation_speed
          : 5,
        autoAnimationPause: 1,
        autoAnimationEasing: "inOutCubic",
        controlOthers: false,
        controlledByOthers: false,
        controlledReverse: false,
        group: "",
        groupSync: false,
        loading: "lazy",
        viewportOffset: "100px",
        sleepMode: true,
        onReady: function () {},
      };

      this.comparison = new AnyImageComparisonSlider(
        this.element,
        pluginSettings
      );
    }

    replaceLabels() {
      const beforeLabel = this.wrap.dataset.beforeLabel;
      const afterLabel = this.wrap.dataset.afterLabel;

      document.querySelector(`${this.selector} .label-before`).innerHTML =
        beforeLabel;
      document.querySelector(`${this.selector} .label-after`).innerHTML =
        afterLabel;
    }

    // Methods
    destroy() {
      if (!this.element) return;
      this.element.parentNode.replaceChild(this.clone, this.element);
      this.replaceLabels();
      this.element = null;
      this.comparison = null;
    }

    update(options) {
      this.options = mergeObjects(this.options, {
        ...options.content,
        ...options.design,
      });
      this.destroy();
      this.init();
    }

    init() {
      this.show();
    }
  }

  window.BreakdanceImageComparison = BreakdanceImageComparison;
})();
