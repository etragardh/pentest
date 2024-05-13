(function () {
  const { mergeObjects, matchMedia, onResize } = BreakdanceFrontend.utils;

  class BreakdanceFilter {
    defaultOptions = {
      wrapperSelector: ".ee-posts",
      itemSelector: ".ee-post",
      sizerSelector: ".ee-post-sizer",
      gutterSelector: ".ee-post-gutter",
      defaultActiveIndex: 0,
      layout: "grid",
      horizontalAt: "breakpoint_phone_landscape",
      isVertical: false
    };
    items = [];
    filterActiveClass = "is-active";

    constructor(selector, options) {
      this.onResize = this.onResize.bind(this);
      this.onFilterChange = this.onFilterChange.bind(this);
      this.selector = selector;
      this.options = mergeObjects(this.defaultOptions, options);

      this.init();
    }

    isSlider() {
      return this.options.layout === "slider";
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

    getItemElems() {
      return Array.from(this.wrapperEl.querySelectorAll(this.options.itemSelector));
    }

    setActiveButton(activeButton) {
      this.filterButtons.forEach((button) => {
        button.classList.remove(this.filterActiveClass);
      });

      activeButton.classList.add(this.filterActiveClass);
    }

    // Filters
    getFilterValue(node) {
      if (node.tagName === "SELECT") {
        const selected = node.options[node.selectedIndex];
        return selected.dataset.value;
      }

      return node.dataset.value;
    }

    filterBy(node) {
      const filter = this.getFilterValue(node);
      const button = this.filterButtons.find((button) => button.id === node.id);

      const filterByTerms = (item) => {
        const curFilters = (item.dataset.filters ?? '').split(",");
        return curFilters.includes(filter);
      };

      if (this.isSlider()) {
        this.filterBySlider(filter, filterByTerms);
      } else {
        this.filterByIsotope(filter, filterByTerms);
      }

      if (button) {
        this.setActiveButton(button);
      }
    }

    filterBySlider(filter, filterFn) {
      const swiperEl = this.element.querySelector('.swiper');
      const itemsToShow = filter === "all" ? this.items : this.items.filter(filterFn);
      const itemsToHide = this.items.filter(x => !itemsToShow.includes(x));

      itemsToShow.forEach(item => item.style.display = null);
      itemsToHide.forEach(item => item.style.display = 'none');

      swiperEl.swiper.update();
      swiperEl.swiper.slideTo(0, 0);
    }

    filterByIsotope(filter, filterFn) {
      if (filter === "all") {
        this.iso.arrange({
          layoutInstant: false,
          filter: "*"
        });
      } else {
        this.iso.arrange({
          layoutInstant: false,
          filter(element) {
            return filterFn(element);
          },
        });
      }
    }

    onFilterChange(event) {
      event.preventDefault();
      this.filterBy(event.currentTarget);
    }

    initFilters() {
      this.filterButtons = Array.from(this.element.querySelectorAll(".js-tab"));
      const mobileFilter = this.element.querySelector(".js-tab-select");

      this.filterButtons.forEach((button, index) => {
        if (index === this.options.defaultActiveIndex) {
          this.setActiveButton(button);
        }

        button.addEventListener("click", this.onFilterChange, { signal: this.ac.signal });
      });

      if (mobileFilter) {
        mobileFilter.addEventListener("change", this.onFilterChange, { signal: this.ac.signal });
      }
    }

    resizeVertical() {
      if (!this.options.isVertical) return;
      const breakpoint = this.options.horizontalAt;
      const { BASE_BREAKPOINT_ID } = window.BreakdanceFrontend.data;

      const tabList = this.element.querySelector(".js-tablist");
      const tabsContainer = tabList.closest(".js-tabs-container");

      if (
        matchMedia(breakpoint) ||
        breakpoint === BASE_BREAKPOINT_ID
      ) {
        tabsContainer.classList.remove('is-vertical');
        tabList.setAttribute("aria-orientation", "horizontal");
      } else {
        tabsContainer.classList.add('is-vertical');
        tabList.setAttribute("aria-orientation", "vertical");
      }
    }

    onResize() {
      this.relayout();
      this.resizeVertical();
    }

    destroy() {
      if (this.iso) {
        this.iso.destroy();
        this.iso = null;
      }

      if (this.ac) {
        this.ac.abort();
        this.ac = null;
      }

      this.disconnect();
    }

    update(options) {
      this.options = mergeObjects(this.defaultOptions, options);
      this.destroy();
      this.init();
    }

    initIsotope() {
      // Remove display: none from initially hidden items
      this.items.forEach(item => item.style.display = null);

      this.iso = new Isotope(this.wrapperEl, {
        itemSelector: this.options.itemSelector,
        percentPosition: true,
        isJQueryFiltering: false,
        masonry: {
          columnWidth: this.options.sizerSelector,
          gutter: this.options.gutterSelector,
        }
      });

      this.appendItems(this.items);

      // Hide initially hidden items by default
      this.iso.arrange({
        layoutInstant: true,
        filter: ":not(.initially-hidden)"
      });

      this.element.addEventListener("breakdance_infinite_scroll_loaded", (event) => {
        this.iso.appended(event.detail);
        this.iso.layout();
      }, { signal: this.ac.signal });

      // Force Isotope to relayout when clicking on a link or button.
      // This is required if the link or button causes the element to change size.
      this.wrapperEl.querySelectorAll("a, button").forEach((el) => {
        el.addEventListener("click", () => this.iso.layout(), { signal: this.ac.signal });
      });
    }

    init() {
      this.element = document.querySelector(this.selector);
      this.wrapperEl = this.element.querySelector(this.options.wrapperSelector);
      this.items = this.getItemElems();

      if (this.ac) this.ac.abort();
      this.ac = new AbortController();

      if (!this.isSlider()) {
        this.initIsotope();
      }

      this.initFilters();

      this.disconnect = onResize(this.onResize);
      this.resizeVertical();
    }
  }

  window.BreakdanceFilter = BreakdanceFilter;
})();
