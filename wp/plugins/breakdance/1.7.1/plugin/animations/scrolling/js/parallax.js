/* global gsap, ScrollTrigger, imagesLoaded */
(function () {
  const {
    is,
    matchMedia,
    toArray,
    debounce,
    onResize,
    mergeObjects,
    prefersReducedMotion
  } = BreakdanceFrontend.utils;

  class BreakdanceParallax {
    tweens = [];
    enabledClass = 'breakdance-animation-enabled';
    animatingClass = 'is-animating';

    defaultOptions = {
      enabled: false,
      opacity: null,
      rotation: null,
      scale: null,
      x: null,
      y: null,
      blur: null,
      advanced: {
        ease: 'linear',
        disable_at: null,
        relative_to: 'viewport',
        origin: null,
        debug: false
      }
    }

    initialized = false;

    constructor(selector, options) {
      gsap.registerPlugin(ScrollTrigger);

      this.createTween = this.createTween.bind(this);
      this.update = debounce(this._update, 100);

      this.selector = selector;
      this.options = mergeObjects(this.defaultOptions, options);
      this.rootEl = document.documentElement;

      this.init();
    }

    parseValue(value) {
      if (!value) return value;
      // Unit value is an object.
      return typeof value === 'object' ? value.style : value;
    }

    parseAnchors(trigger) {
      const [vBottom, vTop] = trigger || [0, 100];

      return {
        bottom: 100 - vBottom,
        top: 100 - vTop
      }
    }

    getTrigger(relativeTo = 'viewport', selector) {
        if (relativeTo === 'page') {
          return document.documentElement;
        } else if (relativeTo === 'custom' && selector) {
          try {
            return document.querySelector(selector);
          } catch (e) {}
        }

      return this.element;
    }

    getScrollTriggerObject(values) {
      const { bottom, top } = this.parseAnchors(values.trigger);
      const { debug, scrub, relative_to, relative_selector } = this.options.advanced;
      const speed = this.getDuration(scrub) || true;
      let delayedCall;

      const trigger = this.getTrigger(relative_to, relative_selector);

      return {
        trigger: trigger,
        start: `top ${bottom}%`, // Element x Viewport
        end: `bottom ${top}%`, // Element x Viewport
        scrub: speed,
        markers: debug,
        toggleClass: 'is-parallax-active',
        onUpdate: (self) => {
          if (self.getVelocity() === 0) return;
          this.element.classList.add(this.animatingClass);

          if (delayedCall) delayedCall.kill();
          delayedCall = gsap.delayedCall(0.3, () => {
            this.element.classList.remove(this.animatingClass);
          });
        }
      };
    }

    createTween([prop, values]) {
      const ease = this.options.advanced.ease;
      const scrollTrigger = this.getScrollTriggerObject(values);
      const startValue = this.parseValue(values.start);
      const middleValue = this.parseValue(values.middle);
      const endValue = this.parseValue(values.end);

      const from = { [prop]: startValue, ease };
      const middle = { [prop]: middleValue, ease };
      const to = { [prop]: endValue, ease };

      const tl = gsap.timeline({ scrollTrigger });

      const startEndSteps = !is.nil(startValue) && !is.nil(endValue);
      const allSteps = startEndSteps && !is.nil(middleValue);

      if (allSteps) {
        // Start -> Middle -> End
        tl.fromTo(this.element, from, middle);
        tl.to(this.element, to);
      } else if (startEndSteps) {
        // Start -> End
        tl.fromTo(this.element, from, to);
      } else if (!is.nil(startValue)) {
        // Start -> Original Value
        tl.from(this.element, from);
      } else {
        // Original Value -> End
        tl.to(this.element, to);
      }

      return tl;
    }

    canAnimate() {
      const breakpoint = this.options.advanced.disable_at;
      if (!breakpoint) return true;
      return !matchMedia(breakpoint);
    }

    getDuration(value) {
      if (!value) return value;
      if (value.unit === 's') return value.number;
      return value.number / 1000; // Convert MS to S
    }

    initTweens() {
      if (this.initialized) return;
      this.initialized = true;

      const { origin } = this.options.advanced;

      if (origin) {
        gsap.set(this.element, {
          transformOrigin: `${origin.x}% ${origin.y}%`
        });
      }

      this.tweens = Object.entries(this.options)
        .filter(([, obj]) => !is.nil(obj?.start) || !is.nil(obj?.end))
        .map(this.createTween);
    }

    removeDebugMarkers() {
      // Remove ScrollTrigger markers
      const markers = [
        'start',
        'end',
        'scroller-start',
        'scroller-end'
      ];

      markers.forEach((suffix) => {
        const className = `.gsap-marker-${suffix}`;
        toArray(className).forEach((elem) => elem.remove());
      });
    }

    _update(options) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.destroy();
      this.init();
    }

    destroyTweens() {
      if (!this.element) return;
      if (!this.tweens) return;

      this.tweens.forEach((tween) => {
        // Killing the tween should automatically kill scrollTrigger,
        // but that's always not the case for some reason.
        tween.kill();
        tween.scrollTrigger?.kill();
      });

      this.tweens = [];

      // Remove all inline styles
      gsap.set(this.element, {
        clearProps: 'all'
      });

      this.initialized = false;
    }

    refresh() {
      ScrollTrigger.refresh(true);
    }

    destroy() {
      this.destroyTweens();
      this.removeDebugMarkers();
    }

    initOrDestroy() {
      if (this.canAnimate()) {
        this.initTweens();
      } else {
        this.destroyTweens();
      }
    }

    init() {
      if (!this.options.enabled) return;
      if (prefersReducedMotion()) return;

      this.element = document.querySelector(this.selector);

      onResize(() => this.initOrDestroy());

      this.rootEl.classList.add(this.enabledClass);
    }

    static autoload() {
      const loaded = imagesLoaded(document.body);

      loaded.on('always', () => {
        // Refresh all instances.
        const event = new CustomEvent("breakdance_refresh_animations");
        dispatchEvent(event);

        // Refresh ScrollTrigger only once.
        ScrollTrigger.refresh(true);
      });
    }
  }

  window.BreakdanceParallax = BreakdanceParallax;

  // Autoload
  BreakdanceParallax.autoload();
}());
