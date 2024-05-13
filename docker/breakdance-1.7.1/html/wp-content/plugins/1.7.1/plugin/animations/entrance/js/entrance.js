/* global gsap, ScrollTrigger, imagesLoaded */
(function () {
  const {
    mergeObjects,
    onResize,
    matchMedia,
    prefersReducedMotion,
    isBuilder
  } = BreakdanceFrontend.utils;

  class BreakdanceEntrance {
    enabledClass = 'breakdance-animation-enabled';
    beforeClass = 'is-before';
    animatingClass = 'is-animating';
    completedClass = 'is-animated';

    defaultOptions = {
      animation_type: null,
      duration: { number: 500, unit: 'ms', style: '500ms' },
      delay: { number: 0, unit: 'ms', style: '0ms' },
      advanced: {
        distance: { number: 100, unit: "px", style: "100px" },
        offset: { number: 0, unit: 'px', style: '0px' },
        ease: 'power1.out',
        anchorPlacement: 'top bottom',
        once: false,
        disable_at: null
      }
    };

    initialized = false;

    constructor(selector, options) {
      gsap.registerPlugin(ScrollTrigger);

      this.cleanup = this.cleanup.bind(this);

      this.selector = selector;
      this.options = mergeObjects(this.defaultOptions, options);
      this.rootEl = document.documentElement;

      this.init();
    }

    getAnimations() {
      const distance = this.options.advanced.distance.style;
      const unit = this.options.advanced.distance.unit;

      return {
        fade: [
          { autoAlpha: 0 },
          { autoAlpha: 1 }
        ],

        slideUp: [
          { y: distance },
          { y: `0${unit}` }
        ],
        slideDown: [
          { y: `-=${distance}` },
          { y: `0${unit}` }
        ],
        slideLeft: [
          { x: `-=${distance}` },
          { x: `0${unit}` }
        ],
        slideRight: [
          { x: distance },
          { x: `0${unit}` }
        ],

        flipUp: [
          { perspective: 2500, rotateX: `-=${distance}` },
          { rotateX: `0${unit}` }
        ],

        flipDown: [
          { perspective: 2500, rotateX: distance },
          { rotateX: `0${unit}` }
        ],
        flipLeft: [
          { perspective: 2500, rotateY: `-=${distance}` },
          { rotateY: `0${unit}` }
        ],
        flipRight: [
          { perspective: 2500, rotateY: distance },
          { rotateY: `0${unit}` }
        ],

        zoomIn: [
          { scale: 0.6 },
          { scale: 1 }
        ],
        zoomOut: [
          { scale: 1.2 },
          { scale: 1 }
        ],
      };
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

    cleanup() {
      // Clear all inline styles, otherwise they will throw off animations for images.
      gsap.set(this.element, { clearProps: 'all' });
    }

    getOffset(offset, anchor) {
      const defaultOffset = 120;

      // Top-bottom placement looks terrible if the element is animated with offset zero.
      if (anchor === 'top bottom' && offset.number === 0) {
        return defaultOffset;
      }

      return offset.number;
    }

    createTween() {
      const type = this.options.animation_type;
      const animations = this.getAnimations();

      if (!animations[type]) {
        console.error(`[ENTRANCE] The selected ${type} animation is invalid.`);
        return;
      }

      const [from, to] = animations[type];

      const { ease, once, anchorPlacement } = this.options.advanced;
      const duration = this.getDuration(this.options.duration);
      const delay = this.getDuration(this.options.delay);
      const offset = this.getOffset(this.options.advanced.offset, anchorPlacement);
      const [elementStart, viewportStart] = anchorPlacement.split(' ');

      const anim = gsap.timeline({
        delay,
        paused: true
      });

      this.element.classList.add(this.beforeClass);

      if (isBuilder()) {
        this.hideEl();
      }

      this.startTrigger = ScrollTrigger.create({
        trigger: this.element,
        start: `${elementStart}+=${offset} ${viewportStart}`,
        toggleActions: "play none none none",
        once,
        onEnter: () => anim.play()
      });

      anim.fromTo(this.element, {
        ...from,
        autoAlpha: 0,
      },
      {
        ...to,
        duration,
        delay,
        ease,
        autoAlpha: 1,
        clearProps: 'all',
        immediateRender: false,
        onStart: () => {
          this.element.classList.add(this.animatingClass);
          this.element.classList.remove(this.beforeClass);
        },
        onComplete: () => {
          this.element.classList.remove(this.animatingClass);
          this.element.classList.add(this.completedClass);
        },
        onReverseComplete: () => {
          this.element.classList.add(this.beforeClass);
          this.element.classList.remove(this.animatingClass);
        },
      });

      if (!once) {
        this.goToBeginningOnReverse(anim);
      }

      return anim;
    }

    goToBeginningOnReverse(animation) {
      // Reset animation to the beginning once its goes offscreen at the bottom.
      this.endTrigger = ScrollTrigger.create({
        trigger: this.element,
        start: `top-=10 bottom`,
        onLeaveBack: () => {
          // Reset the animation to the beginning.
          animation.pause(0);

          // Clear all inline styles.
          this.cleanup();

          // Trick the builder into making the element selectable.
          // This is needed because activating the element sets its visibility to hidden.
          if (isBuilder()) {
            this.hideEl();
          }

          this.element.classList.add(this.beforeClass);
          this.element.classList.remove(this.completedClass);
          this.element.classList.remove(this.animatingClass);
        }
      });
    }

    hideEl() {
      gsap.set(this.element, { autoAlpha: 0 });
    }

    update(options) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.destroy();
      this.init();
    }

    destroy() {
      this.initialized = false;

      if (!this.element) return;

      this.element.classList.add(this.completedClass);

      if (!this.tween) return;

      this.tween.kill();
      this.tween = null;
      this.startTrigger?.kill();
      this.endTrigger?.kill();

      // Remove all inline styles
      gsap.set(this.element, { clearProps: 'all' });

      removeEventListener('breakdance_refresh_animations', this.cleanup);
    }

    refresh() {
      ScrollTrigger.refresh();
    }

    initTween() {
      if (this.initialized) return;
      this.initialized = true;

      this.element.classList.remove(this.completedClass);
      this.tween = this.createTween();
    }

    initOrDestroy() {
      if (this.canAnimate()) {
        this.initTween();
      } else {
        this.destroy();
      }
    }

    init() {
      if (!this.options.animation_type) return;

      this.element = document.querySelector(this.selector);

      if (prefersReducedMotion()) {
        this.element.classList.add(this.completedClass);
        return;
      }

      onResize(() => this.initOrDestroy());
      addEventListener('breakdance_refresh_animations', this.cleanup);

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

    static dontLetScrollTriggerMutateScrollPosition() {
      window.addEventListener("load", () => {
        if (!location.hash) return;
        if (window.bdeAnimationScrolled) return;
        const scrollElem = document.querySelector(location.hash);
        if (!scrollElem) return;

        // Safari fix: Scroll to the element after the next repaint.
        requestAnimationFrame(() => {
          scrollElem.scrollIntoView({
            behavior: "smooth",
          });
        });

        // Prevent scrolling to the same element twice.
        window.bdeAnimationScrolled = true;
      });
    }
  }

  window.BreakdanceEntrance = BreakdanceEntrance;

  BreakdanceEntrance.autoload();
  BreakdanceEntrance.dontLetScrollTriggerMutateScrollPosition();
}());
