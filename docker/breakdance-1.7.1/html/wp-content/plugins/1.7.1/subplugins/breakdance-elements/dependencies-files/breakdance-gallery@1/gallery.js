(function () {
  const { mergeObjects, debounce, getCurrentBreakpoint, onResize } = BreakdanceFrontend.utils;

  class BreakdanceGallery {
    defaultOptions = {
      type: "grid",
      mode: "single",
      row_height: { number: 300 },
      columns: {
        breakpoint_base: 3,
        breakpoint_phone_portrait: 1,
      },
      slider: {
        settings: null,
        pagination: null,
      },
      defaultActiveIndex: 0
    };

    items = [];
    filterActiveClass = "is-active";

    constructor(selector, options) {
      this.relayout = this.relayout.bind(this);
      this.resize = this.resize.bind(this);
      this.update = debounce(this._update, 100);
      this.onImagesLoaded = this.onImagesLoaded.bind(this);
      this.onFilterChange = this.onFilterChange.bind(this);
      this.selector = selector;
      this.options = mergeObjects(this.defaultOptions, options);
      this.init();
    }

    checkForImagesLoaded() {
      return this.options.type === "masonry";
    }

    getIsotopeSettings(layoutInstant = true) {
      return {
        layoutMode: this.getLayoutMode(),
        layoutInstant,
        packery: { gutter: '.ee-gallery-gutter' },
        masonry: { gutter: '.ee-gallery-gutter' }
      };
    }

    /*
     * Turn DOM Elements into Isotope-ready Items
     * This is required in order to make Isotope work in the Builder.
     */
    itemize(items) {
      return items.map((elem) => new Isotope.Item(elem, this.iso));
    }

    /*
     * Force Isotope to accept our DOM Elements.
     * This is required due to a bug in the library.
     * "elem instanceof HTMLCollection" always return false inside same-origin iframes
     */
    appendItems(elems) {
      const items = this.itemize(elems);

      if (!items.length) return;

      this.iso.items = items;
      this.iso.filteredItems = items;
    }

    relayout() {
      if (!this.iso) return;
      this.iso.layout();
    }

    getItemElems(elem) {
      return [].slice.call(elem.querySelectorAll(".ee-gallery-item"));
    }

    onImagesLoaded() {
      this.relayout();
    }

    determineFilter(category) {
      return category === "all" ? "*" : `[data-category="${category}"]`;
    }

    // Filters
    filterBy(node) {
      const category = (node.value || node.id).split("-").pop();

      if (this.options.type === "slider") {
        this.filterSlider(category);
      } else {
        const filter = this.determineFilter(category);
        this.iso.arrange({
          layoutInstant: false,
          filter,
        });
      }

      const button = this.filterButtons.find((button) => button.id === node.id);

      if (button) this.setActiveButton(button);
    }

    filterSlider(category) {
      const swiperEl = this.element.querySelector(".swiper");

      const itemsToShow = category === "all" ? this.items : this.items.filter((item) => {
        return item.dataset.category === category
      });
      const itemsToHide = this.items.filter(x => !itemsToShow.includes(x));

      itemsToShow.forEach(item => item.style.display = null);
      itemsToHide.forEach(item => item.style.display = "none");

      swiperEl.swiper.update();
      swiperEl.swiper.slideTo(0, 0);
    }

    setActiveButton(activeButton) {
      this.filterButtons.forEach((button) => {
        button.classList.remove(this.filterActiveClass);
      });

      activeButton.classList.add(this.filterActiveClass);
    }

    onFilterChange(event) {
      event.preventDefault();

      const target = event.currentTarget;
      this.filterBy(target);
      this.maybeJustifyItems();
    }

    getAll(selector) {
      return [].slice.call(this.element.querySelectorAll(selector));
    }

    // The filter bar is only available when the gallery type is "multiple".
    initFilters() {
      const mobileFilter = this.element.querySelector(".js-tab-select");

      this.filterButtons = this.getAll(".js-tab");

      this.filterButtons.forEach((button, index) => {
        if (index === this.options.defaultActiveIndex) {
          this.filterBy(button);
        }

        button.addEventListener("click", this.onFilterChange);
      });

      if (mobileFilter) {
        mobileFilter.addEventListener("change", this.onFilterChange);
      }
    }

    maybeJustifyItems() {
      if (this.options.type !== "justified") {
        return this.unjustifyItems();
      }

      const justifiedLayout = require("justified-layout"); // TODO: Remove dependency on browserify.

      const items = this.iso.filteredItems;

      const geometry = items.map((item) => {
        const [width, height] = item.element.dataset.lgSize.split("-");
        return { width, height };
      });

      const config = {
        containerWidth: this.galleryEl.clientWidth,
        containerPadding: 0,
        boxSpacing: this.getMasonryGap(),
        targetRowHeight: this.options.row_height.number,
      };

      const output = justifiedLayout(geometry, config);

      items.forEach((item, index) => {
        const box = output.boxes[index];
        item.element.style.width = `${box.width}px`;
        item.element.style.height = `${box.height}px`;
      });

      this.relayout();
    }

    unjustifyItems() {
      if (!this.iso) return;

      const items = this.iso.filteredItems;
      items.forEach((item, index) => {
        item.element.style.removeProperty("width");
        item.element.style.removeProperty("height");
      });
    }

    getMasonryGap() {
      if (!this.options.gap) return 0;

      const currBreakpoint = getCurrentBreakpoint();

      // version >= 1.1 - backwards compatibility for non-breakpoint values.
      const gap = this.options.gap[currBreakpoint.id]?.number || this.options.gap.number || 0;
      return Math.max(gap, 0);
    }

    getLayoutMode() {
      const type = this.options.type || "grid";

      const layoutModes = {
        grid: "masonry",
        masonry: "masonry",
        justified: "packery",
      };

      return layoutModes[type];
    }

    refresh(options = {}) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.maybeJustifyItems();

      const config = this.getIsotopeSettings();
      this.iso.arrange(config);
      this.init();
    }

    _update(options = {}) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.destroy();
      this.init();
    }

    // Fires on resize and when the element becomes visible after removing display: none
    resize() {
      this.maybeJustifyItems();
      this.relayout();
    }

    destroy() {
      this.destroyIsotope();
      this.disconnect();
    }

    destroyIsotope() {
      if (!this.iso) return;

      this.iso.destroy();
      this.iso = null;

      if (this.checkForImagesLoaded()) {
        imagesLoaded(this.element).off("progress", this.onImagesLoaded);
      }
    }

    initIsotope(layoutInstant) {
      const config = this.getIsotopeSettings(layoutInstant);

      this.iso = new Isotope(this.galleryEl, config);
      this.appendItems(this.items);

      this.relayout();
      this.maybeJustifyItems();

      if (this.checkForImagesLoaded()) {
        imagesLoaded(this.element).on("progress", this.onImagesLoaded);
      }
    }

    init(layoutInstant) {
      this.element = document.querySelector(this.selector);
      this.galleryEl = this.element.querySelector(".ee-gallery");
      this.items = this.getItemElems(this.element);

      // For performance purposes, the combination of "single" mode and "grid" type is css-only and does not rely on Isotope.
      const cssOnlyMode = this.options.mode === "single" && this.options.type === "grid";
      const isSlider = this.options.type === "slider";

     if (!isSlider && !cssOnlyMode) {
        this.initIsotope(layoutInstant);
      }

      this.initFilters();

      this.disconnect = onResize(this.resize);
    }
  }

  window.BreakdanceGallery = BreakdanceGallery;
})();
