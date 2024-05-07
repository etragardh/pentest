(function() {
  const { mergeObjects, matchMedia } = BreakdanceFrontend.utils;

  const verticalTabsSelector = "is-vertical";

  class BreakdanceTabs {
    defaultOptions = {
      activeTab: 1,
      horizontalAt: "breakpoint_phone_landscape",
      isVertical: false
    };

    keys = {
      end: 35,
      home: 36,
      left: 37,
      up: 38,
      right: 39,
      down: 40,
      delete: 46
    };

    // Add or substract depending on key pressed
    direction = {
      37: -1,
      38: -1,
      39: 1,
      40: 1
    };

    constructor(selector, options = {}) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      this.isBuilder = !!window?.BreakdanceFrontend.utils.isBuilder();
      this.options = mergeObjects(this.defaultOptions, options);

      this.init();
    }

    bindListeners() {
      if (this.tabs) {
        this.tabs.forEach((tab, index) => {
          this.addListeners(tab, index);
        });
        if (this.tabSelect) {
          this.tabSelect.addEventListener("change", event => {
            this.changeEventListener(event);
          });
        }
      }
    }

    onResizeVertical() {
      if (!this.options.isVertical) return;
      const breakpoint = this.options.horizontalAt;
      const { BASE_BREAKPOINT_ID } = window.BreakdanceFrontend.data;

      if (
        (matchMedia(breakpoint) && this.options.isVertical) ||
        (breakpoint === BASE_BREAKPOINT_ID && this.options.isVertical)
      ) {
        this.tabsContainer.classList.remove(verticalTabsSelector);
        this.tabList.setAttribute("aria-orientation", "horizontal");
      } else {
        if (!this.tabsContainer.classList.contains(verticalTabsSelector)) {
          this.tabsContainer.classList.add(verticalTabsSelector);
          this.tabList.setAttribute("aria-orientation", "vertical");
        }
      }
    }

    addListeners(tab, index) {
      this.tabs[index].index = index;
      this.onClick = this.clickEventListener.bind(this);
      this.onKeydown = this.keydownEventListener.bind(this);

      tab.addEventListener("click", this.onClick);
      if (this.isBuilder) return;
      tab.addEventListener("keydown", this.onKeydown);
    }

    changeEventListener(event) {
      const tab = document.querySelector(`#${event.target.value}`);
      this.activateTab(tab, false);
    }

    clickEventListener(event) {
      const tab = event.target.closest(".js-tab");
      this.activateTab(tab, false);
    }

    keydownEventListener(event) {
      const key = event.keyCode;

      switch (key) {
        case this.keys.end:
          event.preventDefault();
          // Activate last tab
          this.activateTab(this.tabs[this.tabs.length - 1], true);
          break;
        case this.keys.home:
          event.preventDefault();
          // Activate first tab
          this.activateTab(this.tabs[0], true);
          break;

        // Up and down are in keydown
        // because we need to prevent page scroll >:)
        case this.keys.up:
        case this.keys.down:
        case this.keys.left:
        case this.keys.right:
          this.determineOrientation(event);
          break;
      }
    }

    determineOrientation(event) {
      const key = event.keyCode;

      const vertical =
        this.tabList.getAttribute("aria-orientation") == "vertical";
      let proceed = false;

      if (vertical) {
        if (key === this.keys.up || key === this.keys.down) {
          event.preventDefault();
          proceed = true;
        }
      } else {
        if (key === this.keys.left || key === this.keys.right) {
          proceed = true;
        }
      }

      if (proceed) {
        this.switchTabOnArrowPress(event);
      }
    }

    switchTabOnArrowPress(event) {
      const pressed = event.keyCode;

      this.onFocus = this.focusEventHandler.bind(this);

      this.tabs.forEach(tab => {
        tab.addEventListener("focus", event => this.onFocus(event));
      });

      if (this.direction[pressed]) {
        const target = event.target;
        if (target.index !== undefined) {
          if (this.tabs[target.index + this.direction[pressed]]) {
            this.tabs[target.index + this.direction[pressed]].focus();
          } else if (pressed === this.keys.left || pressed === this.keys.up) {
            this.focusLastTab();
          } else if (pressed === this.keys.right || pressed == this.keys.down) {
            this.focusFirstTab();
          }
        }
      }
    }

    activateTab(tab, setFocus) {
      if (tab) {
        setFocus = setFocus || false;
        // Deactivate all other tabs
        this.deactivateTabs();

        // Remove tabindex attribute
        tab.removeAttribute("tabindex");

        // Set the tab as selected
        tab.setAttribute("aria-selected", "true");

        tab.classList.add("is-active");

        // Get the value of aria-controls (which is an ID)
        const controls = tab.getAttribute("aria-controls");

        if (this.tabSelect) {
          this.tabSelect.value = tab.getAttribute("id");
          this.tabSelect[this.tabSelect.selectedIndex].setAttribute(
            "selected",
            "selected"
          );
        }

        // Remove hidden attribute from tab panel to make it visible
        const panel = document.getElementById(controls);

        if (!panel) return;

        panel.removeAttribute("hidden");
        panel.classList.add("is-active");

        // Set focus when required
        if (setFocus && !this.isBuilder) {
          tab.focus();
        }

        // Force-refresh animations to allow the ones inside hidden tabs to work.
        const event = new CustomEvent("breakdance_refresh_animations");
        dispatchEvent(event);
      }
    }

    refreshPanelAttributes() {
      const firstPanel = document.querySelector(`${this.selector} .js-panel`);
      const panelsContainer = firstPanel.closest(".bde-tabs-content-container");

      this.panels = panelsContainer.querySelectorAll(
        ":scope > .js-panel, :scope > .bde-advanced-tabs-content > .js-panel"
      );

      this.panels.forEach((panel, index) => {
        panel.setAttribute("id", `tab-panel-${this.tabsId}-${index + 1}`);
        panel.setAttribute(
          "aria-labelledby",
          `tab-${this.tabsId}-${index + 1}`
        );
      });
    }

    deactivateTabs() {
      if (this.tabs) {
        this.tabs.forEach(tab => {
          tab.setAttribute("tabindex", "-1");
          tab.setAttribute("aria-selected", "false");
          tab.removeEventListener("focus", event => this.onFocus(event));
          tab.removeEventListener("click", event => this.onClick(event));
          tab.removeEventListener("keydown", event => this.onKeydown(event));
          tab.classList.remove("is-active");
        });
      }

      if (this.tabSelect) {
        [...this.tabSelect.options].forEach(option =>
          option.removeAttribute("selected")
        );
      }

      if (this.tabSelect) {
        this.tabSelect.removeEventListener("change", event =>
          this.changeEventListener(event)
        );
      }

      if (this.panels) {
        this.panels.forEach(panel => {
          panel.setAttribute("hidden", "hidden");
          panel.classList.remove("is-active");
        });
      }
    }

    focusFirstTab() {
      this.tabs[0].focus();
    }

    focusLastTab() {
      this.tabs[this.tabs.length - 1].focus();
    }

    determineDelay() {
      const hasDelay = this.tabList.hasAttribute("data-delay");
      const delay = 0;

      if (hasDelay) {
        const delayValue = this.tabList.getAttribute("data-delay");
        if (delayValue) {
          this.delay = delayValue;
        } else {
          // If no value is specified, default to 300ms
          this.delay = 300;
        }
      }

      return delay;
    }

    focusEventHandler(event) {
      const target = event.target;
      setTimeout(() => this.checkTabFocus(target), this.delay);
    }

    checkTabFocus(target) {
      const focused = document.activeElement;

      if (target === focused) {
        this.activateTab(target, false);
      }
    }

    destroy() {
      if (!this.tabs) return;
      this.deactivateTabs();
      this.resetTabstoVerticalLayout();
      this.destroyResizeObserver();
    }

    destroyResizeObserver() {
      if (!this.resizeObserver) return;
      this.resizeObserver.unobserve(this.element);
    }

    resetTabstoVerticalLayout() {
      this.tabsContainer.classList.remove(verticalTabsSelector);
      this.tabList.setAttribute("aria-orientation", "vertical");
    }

    onResize(callback) {
      this.resizeObserver = new ResizeObserver(callback);
      this.resizeObserver.observe(this.element);

      return () => {
        this.resizeObserver.disconnect();
      };
    }

    update(options) {
      if (options) {
        this.options = mergeObjects(this.defaultOptions, options);
      }
      this.destroy();
      this.init();
    }

    updateAndActivateTabFromSelector(selector) {
      this.refreshPanelAttributes();
      const panel = selector.querySelector(".js-panel");
      const tabId = panel.getAttribute("aria-labelledby");
      if (!tabId) return;
      const tab = document.getElementById(tabId);
      this.activateTab(tab);
    }

    init() {
      if (!this.element) return;

      this.tabList = this.element.querySelector(".js-tablist");
      if (!this.tabList) return;

      this.tabs = this.tabList.querySelectorAll(".js-tab");
      this.tabsContainer = this.tabList.closest(".js-tabs-container");
      this.tabsId =
        this.tabList && this.tabList.dataset.tabsId
          ? this.tabList.dataset.tabsId
          : this.selector.match(/\d+$/)[0];
      this.tabSelect = this.element.querySelector(".js-tab-select");

      this.refreshPanelAttributes();
      this.bindListeners();
      this.activateTab(this.tabs[this.options.activeTab - 1]);

      if (this.options.isVertical) {
        this.onResize(() => this.onResizeVertical());
      }
    }
  }

  window.BreakdanceTabs = BreakdanceTabs;
})();
