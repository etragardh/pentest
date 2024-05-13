"use strict";

(function () {
  function manageBreakdanceAdvancedAccordion() {
    const accordionContentSelector = ".bde-accordion__content-wrapper";
    const accordionSelector = ".bde-accordion";

    function getAdvancedAccordionParent(selector) {
      return document.querySelector(selector)?.closest(accordionSelector);
    }

    function getAdvancedAccordionNodeId(selector) {
      const advancedAccordionElement = getAdvancedAccordionParent(selector);

      if (advancedAccordionElement) {
        return advancedAccordionElement.dataset?.nodeId;
      }

      return null;
    }

    function activateTabFromStructurePanel(selector) {
      const advancedAccordionNodeId = getAdvancedAccordionNodeId(selector);

      const accordionContentElement = document
        .querySelector(selector)
        ?.closest(accordionContentSelector);

      if (
        window.breakdanceAdvancedAccordionInstances &&
        window.breakdanceAdvancedAccordionInstances[advancedAccordionNodeId]
      ) {
        window.breakdanceAdvancedAccordionInstances[
          advancedAccordionNodeId
        ].activateAccordionFromSelector(accordionContentElement);
      }
    }

    function update(selector) {
      const advancedAccordionNodeId = getAdvancedAccordionNodeId(selector);

      if (
        window.breakdanceAdvancedAccordionInstances &&
        window.breakdanceAdvancedAccordionInstances[advancedAccordionNodeId]
      ) {
        window.breakdanceAdvancedAccordionInstances[
          advancedAccordionNodeId
        ].update();
      }
    }

    return {
      activateTabFromStructurePanel,
      update,
    };
  }

  window.manageBreakdanceAdvancedAccordion = manageBreakdanceAdvancedAccordion;
})();
