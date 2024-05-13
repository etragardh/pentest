(function () {
  const { mergeObjects } = BreakdanceFrontend.utils;

  class BreakdanceAnimatedHeading {
    defaultOptions = {
      content: {
        changing_text: [
          {
            text: "awesome",
          },
          {
            text: "great",
          },
          {
            text: "cool",
          },
        ],
      },
      design: {
        effect: {
          type: "rotating",
          typing: {
            type_speed: { number: 100, unit: 'ms' },
            start_delay: { number: 0, unit: 'ms' },
            back_speed: { number: 100, unit: 'ms' },
            back_delay: { number: 100, unit: 'ms' },
            shuffle: false,
            disable_loop: false,
            hide_cursor: false,
            cursor: "|",
          },
          rotating: {
            duration: { number: 700, unit: 'ms' },
            delay: { number: 200, unit: 'ms' },
            easing_in: "easeOutSine",
            easing_out: "easeInExpo",
            transform_origin: {
              x: 50,
              y: 50,
            },
            opacity_in: [0, 1],
            opacity_out: 0,
            effect: "flipX",
          },
        },
      },
    };

    constructor(selector, options) {
      this.selector = selector;
      this.element = document.querySelector(
        `${this.selector} .bde-animated-heading__text`
      );
      this.options = mergeObjects(this.defaultOptions, options);
      this.init();
    }

    getDuration(value) {
      if (typeof value === "number") return;
      if (!value) return value;
      if (value.unit === "ms") return value.number;
      return value.number * 1000; // Convert S to MS
    }

    // Init anime.js
    initAnime() {
      const { design } = this.options;
      this.words = this.createWords();

      this.animeTimeline = anime.timeline({
        loop: true,
      });

      this.animateWords(design.effect.rotating.effect, this.words);
    }

    createWords() {
      let wordsArray = [];
      this.wordsWrapper = document.querySelector(
        `${this.selector} .bde-animated-heading__wrapper`
      );

      this.options.content.changing_text.forEach((item) => {
        if (item.text && item.text.length > 0) {
          const span = document.createElement("span");
          span.setAttribute("class", "bde-animated-heading__word");
          span.innerText = item.text.trim();
          this.wordsWrapper.appendChild(span);
          wordsArray.push(span);
        }
      });

      return wordsArray;
    }

    // Destroy anime.js
    destroyAnime() {
      if (!this.animeTimeline) return;
      // Clean up the timeline
      this.animeTimeline.remove(this.words);
      this.animeTimeline = null;
      this.wordsWrapper = null;
      this.words.forEach((word) => word.remove());
    }

    _anime(element, effectIn, effectOut) {
      const effect = this.options.design.effect.rotating;

      this.animeTimeline
        .add({
          ...effectIn,
          targets: element,
          opacity: effect.opacity_in,
          duration: this.getDuration(effect.duration),
          easing: effect.easing_in,
          changeBegin: () => {
            const newWidth = element.clientWidth;
            this.wordsWrapper.style.width = newWidth + "px";
          },
        })
        .add({
          ...effectOut,
          targets: element,
          opacity: effect.opacity_out,
          duration: this.getDuration(effect.duration),
          easing: effect.easing_out,
          delay: this.getDuration(effect.delay),
        });
    }

    animateWords(effectName, words) {
      switch (effectName) {
        case "slideDown":
          words.forEach((element) => {
            this._anime(
              element,
              { translateY: ["-150%", 0] },
              { translateY: "150%" }
            );
          });
          break;
        case "slideRight":
          words.forEach((element) => {
            this._anime(
              element,
              { translateX: ["-100%", 0] },
              { translateX: "100%" }
            );
          });
          break;
        case "slideUp":
          words.forEach((element) => {
            this._anime(
              element,
              { translateY: ["150%", 0] },
              { translateY: "-150%" }
            );
          });
          break;
        case "slideLeft":
          words.forEach((element) => {
            this._anime(
              element,
              { translateX: ["100%", 0] },
              { translateX: "-100%" }
            );
          });
          break;
        case "rotate":
          words.forEach((element) => {
            this._anime(element, { rotate: [45, 0] }, { rotate: [0, -45] });
          });
          break;
        case "flipX":
          words.forEach((element) => {
            this._anime(element, { rotateX: [-90, 0] }, { rotateX: [0, -90] });
          });
          break;
        case "flipY":
          words.forEach((element) => {
            this._anime(element, { rotateY: [-90, 0] }, { rotateY: [0, -90] });
          });
          break;
        case "zoomIn":
          words.forEach((element) => {
            this._anime(element, { scale: [0, 1] }, { scale: [1, 2] });
          });
          break;
        case "zoomOut":
          words.forEach((element) => {
            this._anime(element, { scale: [2, 1] }, { scale: [1, 0] });
          });
          break;
        default:
          console.warn(`Animated Heading: ${effectName} effect not found.`);
      }
    }

    // Init Typed.js
    initTyped() {
      const settings = this.options.design.effect.typing;
      const text = this.options.content.changing_text;
      const strings = text.map((word) => word.text);
      this.typedElement = document.querySelector(
        `${this.selector} .js-eeah-typed`
      );

      this.typedElement.innerHTML = "";

      this.typed = new Typed(this.typedElement, {
        strings: strings,
        smartBackspace: true,
        typeSpeed: this.getDuration(settings.type_speed),
        startDelay: this.getDuration(settings.start_delay),
        backSpeed: this.getDuration(settings.back_speed),
        backDelay: this.getDuration(settings.back_delay),
        shuffle: !!settings.shuffle,
        fadeOut: false,
        showCursor: !settings.hide_cursor,
        loop: !settings.disable_loop,
        cursorChar: settings.cursor,
      });

      this.typed.start();
    }

    // Destroy typed.js
    destroyTyped() {
      if (!this.typed) return;
      this.typed.destroy();
      this.typedElement.innerHTML = "";
      this.typed = null;
    }

    // Update plugin
    update(options = {}) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.destroy();
      this.init();
    }

    // Destory plugin
    destroy() {
      const { type } = this.options.design.effect;
      switch (type) {
        case "rotating":
          this.destroyAnime();
          break;
        case "typing":
          this.destroyTyped();
          break;
        default:
          console.warn(
            `Animated Heading: ${type} animation could not be destroyed.`
          );
      }
    }

    // Init plugin
    init() {
      const { type } = this.options.design.effect;
      switch (type) {
        case "rotating":
          this.initAnime();
          break;
        case "typing":
          this.initTyped();
          break;
        default:
          console.warn(`Animated Heading: ${type} animation not initialised.`);
      }
    }
  }
  window.BreakdanceAnimatedHeading = BreakdanceAnimatedHeading;
})();
