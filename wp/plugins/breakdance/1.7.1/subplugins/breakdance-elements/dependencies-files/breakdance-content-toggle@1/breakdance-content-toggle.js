(function () {
  const contentElementSelector = ".bde-content-toggle-content";
  const toggleElementSelector = ".bde-content-toggle";
  const checkboxSelector = ".js-content-toggle-checkbox";
  const labelSelector = ".js-content-toggle-label";

  class BreakdanceContentToggle {
    constructor(selector) {
      this.selector = selector;
      this.element = document.querySelector(`${this.selector}`);
      this.checkbox = this.element.querySelector(`${checkboxSelector}`);
      this.switchLabels = this.element.querySelectorAll(`${labelSelector}`);
      this.contentTabs = this.element.querySelectorAll(
        `${contentElementSelector}`
      );
      this.clickHandler = this.toggleClick.bind(this);
      this.checkboxChangeHandler = this.onCheckboxChange.bind(this);
      // this index needs to be 0 or 1
      this.initialIndex = 0;
      this.init();
    }

    deactivate() {
      this.switchLabels.forEach((label) => {
        label.classList.remove("is-active");
      });

      if (this.contentTabs) {
        this.contentTabs.forEach((tab) => {
          tab.classList.remove("is-active");
        });
      }
    }

    activateTab(index) {
      if (!this.contentTabs[index] || !this.switchLabels[index]) return;
      this.deactivate();
      this.initialIndex = index;
      this.switchLabels[index].classList.add("is-active");
      this.contentTabs[index].classList.add("is-active");
      this.checkbox.checked = index;
    }

    queryContentTabs() {
      this.contentTabs = this.element.querySelectorAll(
        `${contentElementSelector}`
      );
    }

    toggleClick(event) {
      const index =
        event.target.parentElement.firstElementChild === event.target ? 0 : 1;
      this.activateTab(index);
    }

    onCheckboxChange(event) {
      const index = event.currentTarget.checked ? 1 : 0;
      this.activateTab(index);
    }

    bindListeners() {
      if (!this.switchLabels.length) return;
      this.switchLabels.forEach((label) => {
        label.addEventListener("click", this.clickHandler);
      });
      this.checkbox.addEventListener("change", this.checkboxChangeHandler);
    }

    destroyListeners() {
      this.switchLabels.forEach((label) => {
        label.removeEventListener("click", this.clickHandler);
      });
      this.checkbox.removeEventListener("change", this.checkboxChangeHandler);
    }

    update(index) {
      this.destroy();
      this.queryContentTabs();
      this.init(index);
    }

    destroy() {
      this.deactivate();
      this.destroyListeners();
      this.contentTabs = null;
    }

    init(index) {
      this.bindListeners();
      const activeIndex =
        index === 0 || index === 1 ? index : this.initialIndex;
      this.activateTab(activeIndex);
    }

    static activateTabFromStructurePanel(selector) {
      const element = document.querySelector(selector);
      const parent = element?.closest(toggleElementSelector);
      const tab = element?.closest(contentElementSelector);
      if (!parent || !tab) return;

      const toggleElementNodeId = parent.dataset?.nodeId;
      const index = Array.prototype.indexOf.call(parent.children, tab) - 1;

      if (
        toggleElementNodeId &&
        window.breakdanceContentToggleInstances &&
        window.breakdanceContentToggleInstances[toggleElementNodeId]
      ) {
        window.breakdanceContentToggleInstances[toggleElementNodeId]?.update(
          index
        );
      }
    }
  }

  window.BreakdanceContentToggle = BreakdanceContentToggle;
})();
