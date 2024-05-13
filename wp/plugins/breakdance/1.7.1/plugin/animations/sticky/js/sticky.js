/* global gsap, ScrollTrigger, imagesLoaded */
(function () {
  const { matchMedia, debounce, mergeObjects } = BreakdanceFrontend.utils;
  class BreakdanceSticky {
    defaultOptions = {
      position: "none",
      offset: 0,
      disable_at: null,
      relative_to_viewport: false,
    };

    stickyInitialized = false;
    animatingClass = "is-animating";

    constructor(selector, options) {
      gsap.registerPlugin(ScrollTrigger);

      this.update = debounce(this._update, 100);

      this.selector = selector;
      this.options = mergeObjects(this.defaultOptions, options);
      this.rootEl = document.documentElement;

      this.init();
    }

    getOffset() {
      if (!this.options.offset) return 0;

      const { BASE_BREAKPOINT_ID, breakpoints } =
        window.BreakdanceFrontend.data;

      const foundBreakpoint = breakpoints
        .filter((b) => b.id !== BASE_BREAKPOINT_ID)
        .find((b) => matchMedia(b.id));

      const currentBreakpoint = foundBreakpoint?.id || BASE_BREAKPOINT_ID;

      // version >= 1.1 - backwards compatibility for non-breakpoint values.
      const offset =
        this.options.offset[currentBreakpoint]?.number ||
        this.options.offset.number ||
        0;
      return Math.max(offset, 0);
    }

    canSticky() {
      const { BASE_BREAKPOINT_ID } = window.BreakdanceFrontend.data;
      const breakpoint = this.options.disable_at;

      if (!breakpoint) return true;
      if (breakpoint === BASE_BREAKPOINT_ID) return false;

      return !matchMedia(breakpoint);
    }

    getTrigger(relativeTo = "parent", selector) {
      if (relativeTo === "viewport") {
        return document.body;
      } else if (relativeTo === "custom" && selector) {
        try {
          return document.querySelector(selector);
        } catch (e) {}
      }

      return (
        this.element.closest(".bde-column") ||
        this.element.closest(".section-container") ||
        document.body
      );
    }

    getStickyOffset(options) {
      const offset = this.getOffset();
      const adminBar = document.querySelector("#wpadminbar");

      // Accommodate logged-in users, don't let the element get hidden behind the WP Admin Bar.
      if (adminBar && options.position === "top") {
        const barHeight = adminBar.getBoundingClientRect().height;
        if (offset < barHeight) {
          return offset + barHeight;
        }
      }

      return offset;
    }

    createSticky() {
      if (this.stickyInitialized) return;
      this.stickyInitialized = true;

      const { debug, position, relative_to, relative_selector } = this.options;

      if (position === "none") return;

      const offset = this.getStickyOffset(this.options);
      const height = this.element.offsetHeight;

      const keywords = {
        top: `top+=${height}px`,
        center: `center+=${height / 2}px`,
        bottom: `bottom`,
      };

      const start = `${position}-=${offset} ${position}`;
      const end = `bottom-=${offset} ${keywords[position]}`;
      const endTrigger = this.getTrigger(relative_to, relative_selector);
      let delayedCall;

      const computedStyle = getComputedStyle(this.element);

      if (computedStyle.zIndex === "auto") {
        gsap.set(this.element, {
          zIndex: "var(--bde-z-index-sticky)",
        });
      }

      this.stickyTween = gsap.to(this.element, {
        scrollTrigger: {
          trigger: this.element,
          pin: this.element,
          start,
          end,
          endTrigger,
          markers: debug,
          toggleClass: "is-sticky",
          pinSpacing: false,
          onUpdate: (self) => {
            if (self.getVelocity() === 0) return;
            this.element.classList.add(this.animatingClass);

            if (delayedCall) delayedCall.kill();
            delayedCall = gsap.delayedCall(0.3, () => {
              this.element.classList.remove(this.animatingClass);
            });
          },
        },
        ease: "linear",
      });
    }

    _update(options) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.destroy();
      this.init();
    }

    destroyResizeObserver() {
      if (!this.resizeObserver) return;
      this.resizeObserver.unobserve(document.body);
    }

    destroy() {
      this.stickyInitialized = false;
      if (!this.stickyTween) return;
      this.stickyTween.kill(true);
      this.stickyTween.scrollTrigger?.kill(true);
      this.stickyTween = null;
    }

    remove() {
      this.destroyResizeObserver();
      this.destroy();
    }

    onResize(callback) {
      this.resizeObserver = new ResizeObserver(callback);
      this.resizeObserver.observe(document.body);

      return () => {
        this.resizeObserver.disconnect();
      };
    }

    refresh() {
      ScrollTrigger.refresh();
    }

    initOrDestroy() {
      if (this.canSticky()) {
        this.createSticky();
      } else {
        this.destroy();
      }
    }

    init() {
      this.element = document.querySelector(this.selector);
      this.parent = this.element.closest(".section-container");

      this.onResize(() => this.initOrDestroy());
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

  window.BreakdanceSticky = BreakdanceSticky;
  BreakdanceSticky.dontLetScrollTriggerMutateScrollPosition();
})();
