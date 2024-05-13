"use strict";

(function () {
  function manageBreakdanceTabs() {
    const tabContentSelector = ".bde-advanced-tabs-content";
    const tabsSelector = ".bde-advanced-tabs";

    function getAdvancedTabsParent(selector) {
      return document.querySelector(selector)?.closest(tabsSelector);
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
      const tabsMenu = advancedTabs.querySelectorAll(".js-tab");
      const tabsPanels = advancedTabs.querySelectorAll(".js-panel");

      if (!tabsMenu || !tabsPanels) return;

      const tabLinksCount = tabsMenu.length;
      const tabContentsCount = tabsPanels.length;

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
        const plural = tabContentsCount - tabLinksCount > 1 ? "Tabs" : "Tab";

        window.parent.Breakdance.NotificationLogger.log.message(
          `You're missing ${
            tabContentsCount - tabLinksCount
          } "${plural}" in a "Advanced Tabs" element.`,
          8000
        );
      }
    }

    function activateTabFromStructurePanel(selector) {
      const advancedTabsNodeId = getAdvancedTabsNodeId(selector);

      const tabsContentElement = document
        .querySelector(selector)
        ?.closest(tabContentSelector);

      if (
        window.breakdanceTabsInstances &&
        window.breakdanceTabsInstances[advancedTabsNodeId]
      ) {
        window.breakdanceTabsInstances[
          advancedTabsNodeId
        ].updateAndActivateTabFromSelector(tabsContentElement);
      }
    }

    function update(selector) {
      const advancedTabsNodeId = getAdvancedTabsNodeId(selector);

      if (
        window.breakdanceTabsInstances &&
        window.breakdanceTabsInstances[advancedTabsNodeId]
      ) {
        window.breakdanceTabsInstances[advancedTabsNodeId].update();
      }
    }

    return {
      alertIfTabLinkAndTabContentDontMatch,
      activateTabFromStructurePanel,
      update,
    };
  }

  window.manageBreakdanceTabs = manageBreakdanceTabs;
})();
