(function () {
  class BreakdanceLinkAction {
    constructor(link) {
      this.link = link;
      this.action = JSON.parse(this.link.dataset.action);

      if (!this.action) return;

      this.actionType = this.action.type;
      this.init();
    }

    initSliderLink() {
      this.slideClickHandler = this.slideClick.bind(this);

      if (!this.action.sliderOptions) return;

      const sliderId = this.action.sliderOptions.elementId;
      if (!sliderId) return;
      const slider = document.querySelector("." + sliderId);
      if (!slider) return;
      this.swiperInstanceId = sliderId;
      this.link.addEventListener("click", this.slideClickHandler);
    }

    destroySliderLink() {
      this.link.removeEventListener("click", this.slideClickHandler);
    }

    slideClick(event) {
      event.preventDefault();
      const index = this.action.sliderOptions.slideIndex
        ? parseInt(this.action.sliderOptions.slideIndex) - 1
        : 0;

      if (
        this.swiperInstanceId &&
        window.swiperInstances &&
        window.swiperInstances[this.swiperInstanceId]
      ) {
        const slider = window.swiperInstances[this.swiperInstanceId];
        const type = this.action.sliderOptions.actionType;

        if (type === "goto") {
          if (slider.loopedSlides) {
            slider.slideToLoop(index);
          } else {
            slider.slideTo(index);
          }
        } else if (type === "next") {
          slider.slideNext();
        } else if (type === "previous") {
          slider.slidePrev();
        }
      }
    }

    destroy() {
      if (this.actionType === "slider") {
        this.destroySliderLink();
      }
      if (this.action === "popup") {
        this.destroyPopupLink();
      }
    }

    init() {
      if (this.actionType === "slider") {
        this.initSliderLink();
      }
      if (this.actionType === "popup") {
        this.initPopupLink();
      }
    }

    static autoload(parent = null) {
      const element = parent ?? document;
      // Autoload link actions from macros
      const selector = ".breakdance-link[data-type='action']";
      const links = element.querySelectorAll(selector);

      links.forEach((link) => {
        new this(link);
      });
    }

    initPopupLink() {
      this.link.addEventListener("click", this.popupClick.bind(this));
    }

    destroyPopupLink() {
      this.link.removeEventListener("click", this.popupClick);
    }

    popupClick(event) {
      event.preventDefault();
      if (!this.action.popupOptions || !this.action.popupOptions.popupId) {
        return;
      }

      const popupId = parseInt(this.action.popupOptions.popupId);
      const popupAction = this.action.popupOptions.popupAction;
      BreakdancePopup.runAction(popupId, popupAction);
    }
  }

  document.addEventListener("DOMContentLoaded", () =>
    BreakdanceLinkAction.autoload()
  );
  window.BreakdanceLinkAction = BreakdanceLinkAction;
})();
