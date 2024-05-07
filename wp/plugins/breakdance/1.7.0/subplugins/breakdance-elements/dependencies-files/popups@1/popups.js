(function() {
  const { mergeObjects, matchMedia } = BreakdanceFrontend.utils;
  window.breakdancePopupInstances = {};
  window.breakdanceHasShownPopup = false;

  class BreakdancePopup {
    defaultOptions = {
      keepOpenOnHashlinkClicks: false,
      closeOnClickOutside: true,
      closeOnEscapeKey: true,
      closeAfterMilliseconds: null,
      showCloseButtonAfterMilliseconds: null,
      disableScrollWhenOpen: false,
      avoidMultiple: false,
      entranceAnimation: null,
      exitAnimation: null,
      limitPageLoad: null,
      limitSession: null,
      limitForever: null,
      breakpointConditions: [],
      triggers: [],
    };

    showCount = 0;
    idleTimeMilliseconds = 0;
    entranceAnimation = false;
    exitAnimation = false;
    lastScrollPosition = window.scrollY;

    boundEvents = {};

    openClass = "breakdance-popup-open";
    animatingClass = "breakdance-popup-animating";

    constructor(id, options) {
      if (window.breakdancePopupInstances[id]) {
        return;
      }
      this.id = id;
      this.options = mergeObjects(this.defaultOptions, options);
      this.triggers = this.options.triggers;
      this.storageKey = `breakdance_popup_${id}_count`;
      window.breakdancePopupInstances[id] = this;

      this.triggers.forEach((trigger) => {
        this.initTrigger(trigger.slug, trigger.options);
      });
      this.init();
    }

    init() {
      this.element = document.querySelector(
        `[data-breakdance-popup-id="${this.id}"]`
      );
      this.wrapper = this.element.closest(".bde-popup");

      if (!this.element) {
        return false;
      }

      if (!this.wrapper) {
        return false;
      }

      this.initCloseButton();

      this.wrapper.addEventListener("breakdance_popup_open", () => {
      });

      this.wrapper.addEventListener("breakdance_popup_close", () => {
        this.handleClose();
      });

      return true;
    }

    setOptions(options) {
      this.options = { ...this.options, ...options };
    }

    open(forceOpen = false) {
      return new Promise((resolve, reject) => {
        if (this.shouldHideAtBreakpoint()) {
          return resolve();
        }

        if (this.isOpen() || this.isAnimating()) {
          return resolve();
        }

        if (!forceOpen && this.hasReachedLimit()) {
          return resolve();
        }

        if (this.element.dataset?.breakdancePopupUsingProTriggers) {
          alert(
            `This popup is using a Pro-only trigger. The popup would show up now instead of this alert. Get Breakdance Pro to get access to all popup triggers.`
          );

          return resolve();
        }

        this.incrementShowCounts();

        this.registerCloseListeners();

        if (this.options.entranceAnimation) {
          this.playEntranceAnimation();
        } else {
          this.wrapper.classList.add(this.openClass);
        }

        this.wrapper.dispatchEvent(new CustomEvent("breakdance_popup_open"));
        return resolve();

      });
    }

    registerCloseListeners() {
      this.boundCloseIfEscapeOrClickOutside = this.closeIfEscapeOrClickOutside.bind(
        this
      );

      if (this.options.closeOnEscapeKey) {
        document.addEventListener(
          "keydown",
          this.boundCloseIfEscapeOrClickOutside
        );
      }

      this.boundMaybeAutomaticallyClosePopup = this.maybeAutomaticallyClosePopup.bind(this);
      this.element.addEventListener("click", this.boundMaybeAutomaticallyClosePopup);

      if (this.options.closeOnClickOutside) {
        document.addEventListener(
          "mousedown",
          this.boundCloseIfEscapeOrClickOutside
        );
      }

      if (this.options.closeAfterMilliseconds) {
        setTimeout(() => {
          if (this.isOpen()) {
            this.close();
          }
        }, this.options.closeAfterMilliseconds);
      }

      if (this.options.disableScrollWhenOpen) {
        document.querySelector("html").classList.add("breakdance-noscroll");
      }
    }

    removeCloseListeners() {
      if (this.boundCloseIfEscapeOrClickOutside) {
        document.removeEventListener(
          "keydown",
          this.boundCloseIfEscapeOrClickOutside
        );
        document.removeEventListener(
          "mousedown",
          this.boundCloseIfEscapeOrClickOutside
        );
        this.element.removeEventListener(
          "click",
          this.boundMaybeAutomaticallyClosePopup
        );
      }
    }

    hasReachedLimit() {
      if (
        this.options.avoidMultiple &&
        window.breakdanceHasShownPopup === true
      ) {
        return true;
      }

      if (
        this.options.limitPageLoad &&
        this.getShowCount("page_load") >= this.options.limitPageLoad
      ) {
        return true;
      }

      if (
        this.options.limitSession &&
        this.getShowCount("session") >= this.options.limitSession
      ) {
        return true;
      }

      if (
        this.options.limitForever &&
        this.getShowCount("local") >= this.options.limitForever
      ) {
        return true;
      }

      return false;
    }

    isOpen() {
      return this.wrapper && this.wrapper.classList.contains(this.openClass);
    }

    isAnimating() {
      return this.wrapper && this.wrapper.classList.contains(this.animatingClass);
    }

    closeIfEscapeOrClickOutside(event) {
      const shouldClose =
        (event.keyCode === 27 && this.isPopupOnTop()) ||
        event.target === this.wrapper;

      if (shouldClose) {
        event.preventDefault();
        event.stopPropagation();
        this.close();
      }
    }

    isPopupOnTop() {
      const topMostElement = document.elementFromPoint(
        this.wrapper.offsetLeft,
        this.wrapper.offsetTop
      );
      return topMostElement === this.wrapper;
    }

    maybeAutomaticallyClosePopup(event) {

      if (this.options.keepOpenOnHashlinkClicks) {
        return;
      }

      /*
      if a disable-popup-autoclose attribute is present on a parent 
      of the event target, don't automatically close the popup
      this feature is experimental and may not be available in future versions of Breakdance
      */
      const elementWitDisablePopupAutocloseAttribute = event.target.closest("[disable-popup-autoclose]");

      if (elementWitDisablePopupAutocloseAttribute) {
        return;
      }

      /*
      autoclose the popup when:
       - clicking on a link with an attribute of close-popup-on-click (this feature is experimental and may not be available in future versions of Breakdance)
       - clicking on a link with a href of #
       - clicking a #hashlink that leads somewhere on the page
      */

      if (event.target.closest('[close-popup-on-click]')) {
        this.close();
        return;
      }

      const link = event.target.closest("a");
      if (!link) return;

      if (link.getAttribute('href') === '#') {
        this.close();
        return;
      }
      
      const url = new URL(link);
      if (!url.hash) return;

      const hashAsIdOrName = url.hash.substring(1);

      if (hashAsIdOrName && document.querySelector(`#${hashAsIdOrName}, a[name=${hashAsIdOrName}]`)) {
        this.close();
        return;
      }

    }

    close() {
      return new Promise((resolve, reject) => {
        if (!this.isOpen() || this.isAnimating()) {
          return resolve();
        }

        this.removeCloseListeners();

        if (this.options.disableScrollWhenOpen) {
          document
            .querySelector("html")
            .classList.remove("breakdance-noscroll");
        }

        if (this.options.exitAnimation) {
          this.playExitAnimation();
        } else {
          this.wrapper.classList.remove(this.openClass);
        }

        this.wrapper.dispatchEvent(new CustomEvent("breakdance_popup_close"));
        return resolve();

      });
    }

    toggle() {
      if (this.isOpen()) {
        return this.close();
      }
      return this.open();
    }

    showPopupOnScrollPercentage(options, callback) {
      return event => {
        const targetPercent = options.percent / 100;
        const scrollTop = window.scrollY;
        const documentHeight = document.body.offsetHeight;
        const windowHeight = window.innerHeight;
        const scrollPercent = scrollTop / (documentHeight - windowHeight);
        if (scrollPercent > targetPercent) {
          this.open().then(callback);
        }
      };
    }

    showPopupOnScrollToSelector(options) {
      const element = document.querySelector(options.selector);
      if (!element) {
        return;
      }
      const ob = new IntersectionObserver(
        (entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting && !this.isOpen()) {
              this.open().then(() => {
                observer.unobserve(entry.target);
              });
            }
          });
        },
        {
          root: null,
          rootMargin: "0px",
          threshold: 0.2
        }
      );
      ob.observe(element);
    }

    showPopupOnScrollUp(callback) {
      return event => {
        const scrollTop = window.scrollY;
        if (scrollTop < this.lastScrollPosition) {
          this.open().then(callback);
        }
        this.lastScrollPosition = scrollTop;
      };
    }

    showPopupAfterInactivityDelay(delayInMilliseconds) {
      const idleTimeout = 1000;
      const idleInterval = setInterval(() => {
        if (this.idleTimeMilliseconds >= delayInMilliseconds) {
          this.open();
          clearInterval(idleInterval);
          if (boundResetIdleTime) {
            document.removeEventListener("mousemove", boundResetIdleTime);
            document.removeEventListener("keydown", boundResetIdleTime);
          }
        } else {
          this.idleTimeMilliseconds += idleTimeout;
        }
      }, idleTimeout);
      const boundResetIdleTime = this.resetIdleTime.bind(this);
      document.addEventListener("mousemove", boundResetIdleTime);
      document.addEventListener("keydown", boundResetIdleTime);
    }

    resetIdleTime() {
      this.idleTimeMilliseconds = 0;
    }

    showPopupOnExitIntent(callback) {
      return event => {
        const isExitIntent =
          !event.toElement && !event.relatedTarget && event.clientY < 10;
        if (isExitIntent) {
          this.open().then(callback);
        }
      };
    }

    showPopupOnClickEvent(options, callback) {
      return event => {
        if (options.clickType === "anywhere") {
          return this.open().then(callback);
        }
        if (options.selector !== null) {
          if (event.target.closest(options.selector)) {
            event.preventDefault();
            event.stopPropagation();
            return this.open().then(callback);
          }
        }
      };
    }

    showPopupOnLoad(delayInMilliseconds) {
      setTimeout(() => {
        this.open();
      }, delayInMilliseconds);
    }

    async playEntranceAnimation() {

      if (this.entranceAnimation) {
        this.entranceAnimation.destroy();
      }

      if (this.options.entranceAnimation !== null) {
        this.entranceAnimation = new BreakdancePopupAnimation(
          this.element,
          this.wrapper,
          this.options.entranceAnimation
        );
      }

      await this.entranceAnimation.play();

    }

    async playExitAnimation(pauseTweenAtEnd = false) {

      if (this.exitAnimation) {
        this.exitAnimation.destroy();
      }

      if (this.options.exitAnimation !== null) {
        this.exitAnimation = new BreakdancePopupAnimation(
          this.element,
          this.wrapper,
          this.options.exitAnimation,
          true
        );
      }

      await this.exitAnimation.play(true);

      if (pauseTweenAtEnd) {
        this.exitAnimation.tween.pause(0);
      }
      
      this.wrapper.classList.remove(this.openClass);
      this.exitAnimation.destroy();

    }

    initTrigger(slug, options = {}) {
      if (slug === "load") {
        const delay = options.delay ? options.delay * 1000 : 0;
        this.showPopupOnLoad(delay);
      }

      if (slug === "inactivity") {
        const delay = options.delay ? options.delay * 1000 : 0;
        this.showPopupAfterInactivityDelay(delay);
      }

      if (slug === "scroll" && options.scrollType === "selector") {
        this.showPopupOnScrollToSelector(options);
      }

      if (slug === "scroll" && options.scrollType === "percent") {
        const scrollEventListener = this.showPopupOnScrollPercentage(
          options,
          () => {
            window.removeEventListener("scroll", scrollEventListener);
          }
        );
        window.addEventListener("scroll", scrollEventListener);
      }

      if (slug === "scroll_up") {
        const scrollUpEventListener = this.showPopupOnScrollUp(() => {
          window.removeEventListener("scroll", scrollUpEventListener);
        });
        window.addEventListener("scroll", scrollUpEventListener);
      }

      if (slug === "exit_intent") {
        const exitIntentEventListener = this.showPopupOnExitIntent(() => {
          document.removeEventListener("mouseout", exitIntentEventListener);
        });
        document.addEventListener("mouseout", exitIntentEventListener);
      }

      if (slug === "click") {
        const boundShowPopupOnClickEvent = this.showPopupOnClickEvent(
          options,
          () => {
            if (options.clickType === "anywhere") {
              document.removeEventListener("click", boundShowPopupOnClickEvent);
            }
          }
        );
        document.addEventListener("click", boundShowPopupOnClickEvent);
      }
    }

    initCloseButton() {
      if (this.options.showCloseButtonAfterMilliseconds) {
        setTimeout(() => {
          if (!this.isOpen()) {
            return;
          }
          const closeButton = this.element.querySelector(
            ".breakdance-popup-close-button"
          );
          if (closeButton) {
            closeButton.classList.remove("hidden");
          }
        }, this.options.showCloseButtonAfterMilliseconds);
      }
    }

    incrementShowCounts() {
      window.breakdanceHasShownPopup = true;

      const sessionStorageValue = this.getShowCount("session");
      sessionStorage.setItem(
        this.storageKey,
        (sessionStorageValue + 1).toString()
      );

      const localStorageValue = this.getShowCount("local");
      localStorage.setItem(this.storageKey, (localStorageValue + 1).toString());

      this.showCount += 1;
    }

    getShowCount(type) {
      let showCount = 0;

      if (type === "page_load") {
        showCount = this.showCount;
      }

      if (type === "session") {
        const sessionStorageItem = sessionStorage.getItem(this.storageKey);
        showCount = sessionStorageItem ? parseInt(sessionStorageItem) : 0;
      }

      if (type === "local") {
        const localStorageItem = localStorage.getItem(this.storageKey);
        showCount = localStorageItem ? parseInt(localStorageItem) : 0;
      }

      return showCount;
    }

    shouldHideAtBreakpoint() {
      return this.options.breakpointConditions.some(condition => {
        const conditionApplies = condition.breakpoints.some(breakpointId => {
          return matchMedia(breakpointId);
        });

        if (condition.operand === "is one of") {
          return !conditionApplies;
        }

        if (condition.operand === "is none of") {
          return conditionApplies;
        }

        return false;
      });
    }

    static runAction(popupId, action = "open") {
      const isBuilder = !!window?.BreakdanceFrontend.utils.isBuilder();

      if (isBuilder) {
        return;
      }
      const breakdancePopupInstance = window.breakdancePopupInstances[popupId];
      if (
        breakdancePopupInstance &&
        typeof breakdancePopupInstance[action] === "function"
      ) {
        breakdancePopupInstance[action].call(breakdancePopupInstance, true);
      }
    }

    handleClose() {
      pauseYouTubeVideosInsideElement(this.wrapper);
      pauseVimeoVideosInsideElement(this.wrapper);
      pauseHtml5VideosInsideElement(this.wrapper);
    }
  }

  window.BreakdancePopup = BreakdancePopup;
  document.addEventListener("click", event => {
    const popupTrigger = event.target.closest("[data-breakdance-popup-action]");
    if (popupTrigger) {
      const {
        breakdancePopupReference,
        breakdancePopupAction
      } = popupTrigger.dataset;
      if (breakdancePopupReference) {
        event.preventDefault();
        event.stopPropagation();
        BreakdancePopup.runAction(
          breakdancePopupReference,
          breakdancePopupAction
        );
      }
    }
  });

  function pauseYouTubeVideosInsideElement(element) {
    // youtube requires &enablejsapi=1 to be in the embed URL for the below to work
    element.querySelectorAll(
      "iframe"
    ).forEach(
      (maybeYouTubeVideoFrame) => {
        maybeYouTubeVideoFrame.contentWindow.postMessage(
          `{"event":"command","func":"stopVideo","args":[]}`,
          "*"
        );
      }
    );
  }

  function pauseVimeoVideosInsideElement(element) {
    element.querySelectorAll(
      "iframe"
    ).forEach(
      (maybeVimeoVideoIframe) => {
        maybeVimeoVideoIframe.contentWindow.postMessage(
          '{"method":"pause","value":""}',
          '*'
        );
      }
    );
  }

  function pauseHtml5VideosInsideElement(element) {
    element.querySelectorAll(
      "video"
    ).forEach(
      (html5Video) => {
        html5Video.pause();
      }
    );
  }


})();
