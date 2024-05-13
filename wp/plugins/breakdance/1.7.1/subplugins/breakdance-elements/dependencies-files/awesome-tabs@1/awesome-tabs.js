// Forked from https://github.com/jenstornell/tabbis.js

const { mergeObjects } = BreakdanceFrontend.utils;

function query(node, context = document) {
  return context.querySelector(node);
}

function getChildren(el) {
  if (!el) {
    return [];
  }

  return Array.from(el.children);
}

class AwesomeTabs {
  constructor(options) {
    this.options = {
      keyboardNavigation: true,
      memory: "",
      prefix: "",
      menuGroup: "[data-tabs-menu]",
      panesGroup: "[data-tabs-panes]",
      tabActive: "[data-active]",
      tabActiveFallback: 0,
      trigger: "click"
    };

    this.options = mergeObjects(this.options, options);

    this.setupMemory();
    this.setup();
  }

  setup() {
    const tabsMenu = query(this.options.menuGroup);
    const tabContentsWrapper = query(this.options.panesGroup);

    if (!tabsMenu) {
      return `You're missing a "Tabs Menu" element inside a "Advanced Tabs" element`;
    }

    if (!tabContentsWrapper) {
      return `You're missing a "Tabs Panels" element inside a "Advanced Tabs" element`;
    }

    if (!tabsMenu || !tabContentsWrapper) return;

    const tabLinks = getChildren(tabsMenu);
    const tabContents = getChildren(tabContentsWrapper);
    const activeIndex = this.getActiveIndex(tabContentsWrapper);

    tabContentsWrapper.setAttribute("role", "tablist");

    // Reset items
    this.resetLinks(tabLinks);
    this.resetPanes(tabContents);

    tabLinks.forEach((tabLink, tabIndex) => {
      const tabContent = tabContents[tabIndex];

      // Add attributes
      this.addLinkAttributes(tabLink);
      this.addTabAttributes(tabLink, tabContent);

      // Trigger event
      tabLink.addEventListener(this.options.trigger, event => {
        this.activate(event.currentTarget);
      });

      // Key event
      if (this.options.keyboardNavigation) {
        tabLink.addEventListener("keydown", this.eventKey);
      }
    });

    if (activeIndex !== null) {
      this.activateTabLink(tabLinks[activeIndex]);
      this.activateTabContent(tabContents[activeIndex]);
    }
  }

  eventKey(event) {
    if ([13, 37, 38, 39, 40].includes(event.keyCode)) {
      event.preventDefault();
    }

    const target = event.currentTarget;

    if (event.keyCode == 13) {
      target.click();
    } else if ([39, 40].includes(event.keyCode)) {
      this.goTo(target, 1);
    } else if ([37, 38].includes(event.keyCode)) {
      this.goTo(target, -1);
    }
  }

  getIndex(el) {
    return getChildren(el.parentElement).indexOf(el);
  }

  goTo(target, direction) {
    const children = getChildren(target.parentElement);

    if (!children) return;

    this.resetTabindex(children);
    const el = children[this.pos(target, children, direction)];

    el.focus();
    el.setAttribute("tabindex", "0");
  }

  resetTabindex(children) {
    children.forEach(child => {
      child.setAttribute("tabindex", "-1");
    });
  }

  pos(tab, children, direction) {
    let pos = this.getIndex(tab);
    pos += direction;

    if (children.length <= pos) {
      pos = 0;
    } else if (pos == -1) {
      pos = children.length - 1;
    }

    return pos;
  }

  // Set active
  getActiveIndex(panes) {
    const memory = this.memory;

    if (typeof this.memory === "number") {
      return memory;
    }

    if (!panes) return;

    let element = query(this.options.tabActive, panes);

    if (!element) {
      element = query('[aria-selected="true"]', panes);
    }

    if (element) {
      return this.getIndex(element);
    }

    return this.options.tabActiveFallback;
  }

  // ATTRIBUTES
  addLinkAttributes(tab) {
    const tabIndex = this.getIndex(tab);
    const prefix = this.options.prefix;

    tab.setAttribute("role", "tab");
    tab.setAttribute("aria-controls", `${prefix}tabpanel-${tabIndex}`);
    tab.setAttribute("id", `${prefix}tab-${tabIndex}`);
  }

  addTabAttributes(tab, pane) {
    if (!pane) return;

    const labelledBy = tab.getAttribute("id") || "";
    const ariaControls = tab.getAttribute("aria-controls") || "";

    pane.setAttribute("role", "tabpanel");
    pane.setAttribute("aria-labelledby", labelledBy);
    pane.setAttribute("id", ariaControls);
    pane.setAttribute("tabindex", "0");
  }

  activate(tab) {
    if (!tab) return;

    const pane = document.querySelector(
      `#${tab.getAttribute("aria-controls")}`
    );

    if (!pane) return;

    this.resetLinks(getChildren(tab.parentElement));
    this.resetPanes(getChildren(pane.parentElement));

    this.activateTabLink(tab);
    this.activateTabContent(pane);

    this.saveMemory(tab);
  }

  activateTabLink(tab) {
    if (!tab) return;

    tab.setAttribute("aria-selected", "true");
    tab.setAttribute("tabindex", "0");
  }

  activateTabContent(pane) {
    if (!pane) return;

    pane.removeAttribute("hidden");
  }

  resetLinks(tabs) {
    tabs.forEach(el => el.setAttribute("aria-selected", "false"));
    this.resetTabindex(tabs);
  }

  resetPanes(panes) {
    panes.forEach(el => el.setAttribute("hidden", ""));
  }

  // MEMORY
  setupMemory() {
    if (!this.options.memory) return;
    const store = localStorage.getItem(this.options.memory);
    if (!store) return;
    this.memory = parseInt(store);
  }

  loadMemory() {
    if (!this.options.memory) return;
    return this.memory;
  }

  saveMemory(tab) {
    if (!this.options.memory) return;
    this.memory = this.getIndex(tab);
    localStorage.setItem(this.options.memory, JSON.stringify(this.memory));
  }
}

window.AwesomeTabs = AwesomeTabs;
