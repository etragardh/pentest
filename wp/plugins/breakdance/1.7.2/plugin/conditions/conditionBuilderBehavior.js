window.addEventListener("breakdanceFiltersAPIReady", function(_e) {
  window.Breakdance.filtersAPI.addFilter("elementShouldShow", function(
    shouldShow,
    elementProperties
  ) {
    if (elementProperties?.settings?.conditions?.builder_preview) {
      return elementProperties.settings.conditions.builder_preview !== "hide";
    }

    return shouldShow;
  });
});
