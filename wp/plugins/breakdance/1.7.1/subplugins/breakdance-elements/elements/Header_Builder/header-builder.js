(function () {
  class BreakdanceHeaderBuilder {
    /**
     * @type {Element}
     */
    element = null;
    stickyScrollHideAfter = false;
    stickyHideUntilScrollDistance = false;
    stickyRevealOnScrollUp = false;
    isBuilder = false;
    lastScroll = 0;
    timeout = null;

    initialHiddenClass = "bde-header-builder--sticky-scroll-start-off-hidden";
    hiddenClass = "bde-header-builder--sticky-scroll-hide";
    stylesClass = "bde-header-builder--sticky-styles";

    /**
     * @param {string} selector
     * @param {string} id
     * @param {boolean} isBuilder
     */
    constructor(selector, id, isBuilder) {
      this.handleScroll = this.handleScroll.bind(this);

      this.isBuilder = isBuilder;
      this.element = document.querySelector(selector);
      this.stickyScrollHideAfter = this.getProp("stickyScrollHideAfter");
      this.stickyHideUntilScrollDistance = this.getProp(
        "stickyHideUntilScrollDistance"
      );
      this.stickyRevealOnScrollUp = this.getBooleanProp(
        "stickyRevealOnScrollUp"
      );

      this.handleScroll();
      this.setupListeners();
      this.createMarkers();
    }

    refresh() {
      this.handleSticky();
    }

    destroy() {
      this.unbindListeners();

      document
        .querySelectorAll(".bde-header-builder-marker")
        .forEach((marker) => marker.remove());
    }

    // DOM
    getProp(key) {
      return parseInt(this.element.dataset[key]) || false;
    }

    getBooleanProp(key) {
      return !!this.element.dataset[key];
    }

    // Listeners
    setupListeners() {
      if (!this.isSticky()) {
        this.clearStickyStyles();
        return;
      }

      window.addEventListener("scroll", this.handleScroll);
    }

    unbindListeners() {
      window.removeEventListener("scroll", this.handleScroll);
    }

    clearStickyStyles() {
      this.removeClass(this.initialHiddenClass);
      this.removeClass(this.hiddenClass);
      this.removeClass("bde-header-builder--sticky-styles");
      this.removeClass("is-animating");
      this.removeClass("is-before");
    }

    // Class Methods
    addClass(x) {
      this.element.classList.add(x);
    }

    hasClass(x) {
      return this.element.classList.contains(x);
    }

    removeClass(x) {
      this.element.classList.remove(x);
    }

    // Sticky Methods
    isSticky() {
      return this.hasClass("bde-header-builder--sticky");
    }

    isHidden() {
      return this.hasClass(this.hiddenClass);
    }

    shouldHide(scrollTop) {
      const hideAfterDistance =
        this.stickyScrollHideAfter != false &&
        scrollTop > this.stickyScrollHideAfter;
      const hideUntilDistance =
        this.stickyHideUntilScrollDistance != false &&
        scrollTop < this.stickyHideUntilScrollDistance;
      return hideAfterDistance || hideUntilDistance;
    }

    shouldShowAfterACertainDistance(scrollTop) {
      return (
        this.stickyHideUntilScrollDistance != false &&
        scrollTop > this.stickyHideUntilScrollDistance
      );
    }

    shouldShowOnScrollUp(scrollTop) {
      if (!this.stickyRevealOnScrollUp) {
        return false;
      }

      if (this.stickyHideUntilScrollDistance > scrollTop) return false;

      return scrollTop < this.lastScroll;
    }

    handleScroll() {
      this.removeClass(this.initialHiddenClass);
      this.handleSticky();
      this.handleBuilderBehaviorOnScroll();
    }

    handleSticky() {
      const scrollTop = document.documentElement.scrollTop;

      if (scrollTop > 0) {
        this.addClass(this.stylesClass);
      } else if (this.stickyHideUntilScrollDistance == false) {
        this.removeClass(this.stylesClass);
      }

      if (this.shouldShowAfterACertainDistance(scrollTop)) {
        this.removeClass(this.hiddenClass);
      }

      if (this.shouldHide(scrollTop)) {
        this.addClass(this.hiddenClass);
      } else if (this.isHidden()) {
        this.removeClass(this.hiddenClass);
      }

      if (this.shouldShowOnScrollUp(scrollTop)) {
        this.removeClass(this.hiddenClass);
      }

      this.lastScroll = scrollTop;
    }

    handleBuilderBehaviorOnScroll() {
      const scrollTop = document.documentElement.scrollTop;

      if (!this.isBuilder) return;

      if (this.shouldHide(scrollTop)) {
        this.addClass("is-before");
        this.removeClass("is-animating");
      } else {
        this.addClass("is-animating");
        this.removeClass("is-before");
      }

      // Detecting when the user has stopped scrolling
      window.clearTimeout(this.timeout);

      this.timeout = setTimeout(() => {
        this.removeClass("is-animating");
      }, 200);
    }

    // Markers
    createMarkers() {
      if (!this.isBuilder) return;

      if (
        this.stickyScrollHideAfter &&
        this.stickyHideUntilScrollDistance &&
        this.stickyScrollHideAfter < this.stickyHideUntilScrollDistance
      ) {
        this.createMarker(
          this.stickyScrollHideAfter,
          `Error: 'Hide' must be higher than 'Show'`
        );
      } else if (this.stickyScrollHideAfter) {
        this.createMarker(
          this.stickyScrollHideAfter,
          `Hide (${this.stickyScrollHideAfter}px)`
        );
      }

      if (this.stickyHideUntilScrollDistance) {
        this.createMarker(
          this.stickyHideUntilScrollDistance,
          `Show (${this.stickyHideUntilScrollDistance}px)`
        );
      }
    }

    createMarker(position, text) {
      const marker = document.createElement("span");

      marker.style.top = `${position}px`;
      marker.classList.add("bde-header-builder-marker");

      const textNode = document.createTextNode(text);

      marker.append(textNode);
      document.body.append(marker);
    }
  }

  window.BreakdanceHeaderBuilder = BreakdanceHeaderBuilder;
})();
