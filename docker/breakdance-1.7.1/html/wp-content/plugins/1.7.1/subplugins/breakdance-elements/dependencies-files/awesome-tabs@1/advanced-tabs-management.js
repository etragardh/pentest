"use strict";

(function () {
  function BreakdanceAdvancedTabs() {
    /**
     * HELPER CODE
     */

    const tabsMenuSelector = ".bde-tabs-menu";
    const tabsPanelsSelector = ".bde-tabs-panels";
    const tabLinkSelector = ".bde-tab-link";
    const tabContentSelector = ".bde-tab-content";

    function getAdvancedTabsParent(selector) {
      return document.querySelector(selector)?.closest(".bde-advanced-tabs");
    }

    function getAdvancedTabsNodeId(selector) {
      const advancedTabsElement = getAdvancedTabsParent(selector);

      if (advancedTabsElement) {
        return advancedTabsElement.dataset?.nodeId;
      }

      return null;
    }

    function alertIfTabLinkAndTabContentDontMatch(advancedTabsNodeId) {
      const advancedTabs = document.querySelector(
        `[data-node-id="${advancedTabsNodeId}"]`
      );
      const tabsMenu = advancedTabs.querySelector(tabsMenuSelector);
      const tabsPanels = advancedTabs.querySelector(tabsPanelsSelector);

      if (!tabsMenu || !tabsPanels) return;

      const tabLinksCount = tabsMenu.children.length;
      const tabContentsCount = tabsPanels.children.length;

      if (tabLinksCount > tabContentsCount) {
        const plural = tabLinksCount - tabContentsCount > 1 ? "s" : "";

        window.parent.Breakdance.NotificationLogger.log.message(
          `You're missing ${
            tabLinksCount - tabContentsCount
          } "Tab Content" element${plural} in a "Advanced Tabs" element. `,
          8000
        );
      }

      if (tabLinksCount < tabContentsCount) {
        const plural = tabContentsCount - tabLinksCount > 1 ? "s" : "";

        window.parent.Breakdance.NotificationLogger.log.message(
          `You're missing ${
            tabContentsCount - tabLinksCount
          } "Tab Link" element${plural} in a "Advanced Tabs" element.`,
          8000
        );
      }
    }

    function getElementCount(selector, elementSelector) {
      return document
        .querySelector(selector)
        .parentNode.querySelectorAll(elementSelector).length;
    }

    /**
     * PUBLIC API
     */

    function init(id, selector, triggeringOnHover) {
      if (!window.breakdanceTabs) {
        window.breakdanceTabs = {};
      }

      window.breakdanceTabs[id] = new AwesomeTabs({
        prefix: `un-${id}-`,
        menuGroup: `${selector} ${tabsMenuSelector}`,
        panesGroup: `${selector} ${tabsPanelsSelector}`,
        trigger: triggeringOnHover ? "mouseover" : "click",
      });

      alertIfTabLinkAndTabContentDontMatch(getAdvancedTabsNodeId(selector));
    }

    function update(selector, alertIfMismatch = true) {
      const advancedTabsNodeId = getAdvancedTabsNodeId(selector);

      if (advancedTabsNodeId) {
        const setupError = window.breakdanceTabs[advancedTabsNodeId]?.setup();

        if (setupError) {
          window.parent.Breakdance.NotificationLogger.log.message(setupError);
        }

        if (alertIfMismatch) {
          alertIfTabLinkAndTabContentDontMatch(advancedTabsNodeId);
        }
      }
    }

    /**
     * Selector can be the TabLink or any of its children
     * @param selector
     */
    function activateTabFromTabLink(selector) {
      const advancedTabsNodeId = getAdvancedTabsNodeId(selector);
      const tabLinkElement = document
        .querySelector(selector)
        .closest(tabLinkSelector);

      if (advancedTabsNodeId) {
        window.breakdanceTabs[advancedTabsNodeId]?.activate(tabLinkElement);
      }
    }

    function activateTabFromTabContent(selector) {
      const advancedTabsNodeId = getAdvancedTabsNodeId(selector);

      const tabContentElement = document
        .querySelector(selector)
        ?.closest(tabContentSelector);

      const tabLinkElementId =
        tabContentElement && tabContentElement.getAttribute("aria-labelledby");
      const tabLinkElement = document.getElementById(tabLinkElementId);

      if (advancedTabsNodeId && tabLinkElement) {
        window.breakdanceTabs[advancedTabsNodeId]?.activate(tabLinkElement);
      }
    }

    function tabsMenuMounted(selector) {
      if (getElementCount(selector, tabsMenuSelector) > 1) {
        window.parent.Breakdance.NotificationLogger.log.message(
          `Advanced Tabs supports having only one "Tabs Menu" element. Please delete one.`
        );

        return;
      }

      update(selector, false);
    }

    function tabsPanelsMounted(selector) {
      if (getElementCount(selector, tabsPanelsSelector) > 1) {
        window.parent.Breakdance.NotificationLogger.log.message(
          `Advanced Tabs supports having only one "Tabs Contents" element. Please delete one.`
        );

        return;
      }

      update(selector, false);
    }

    function tabsMenuDeleted(selector) {
      if (getElementCount(selector, tabsMenuSelector) === 0) {
        window.parent.Breakdance.NotificationLogger.log.message(
          `Advanced Tabs must have a "Tabs Menu" element as a child. Please add one.`
        );
      }
    }

    function tabsPanelsDeleted(selector) {
      if (getElementCount(selector, tabsPanelsSelector) === 0) {
        window.parent.Breakdance.NotificationLogger.log.message(
          `Advanced Tabs must have a "Tabs Contents" element as a child. Please add one.`
        );
      }
    }

    return {
      init,
      update,
      activateTabFromTabLink,
      activateTabFromTabContent,
      alertIfTabLinkAndTabContentDontMatch,
      tabsMenuMounted,
      tabsPanelsMounted,
      tabsMenuDeleted,
      tabsPanelsDeleted,
    };
  }

  window.BreakdanceAdvancedTabs = BreakdanceAdvancedTabs;
})();
