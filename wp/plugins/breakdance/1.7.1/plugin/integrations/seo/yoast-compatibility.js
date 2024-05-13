(function ($) {
  _BreakdanceYoast = function () {
    if (typeof YoastSEO !== "undefined") {
      YoastSEO.app.registerPlugin("_BreakdanceYoast", { status: "ready" });
      YoastSEO.app.registerModification(
        "content",
        this.replaceContentWithBreakdanceData,
        "_BreakdanceYoast",
        5
      );
    }
  };

  _BreakdanceYoast.prototype.replaceContentWithBreakdanceData = function () {
    return breakdance.content;
  };

  $(document).ready(function () {
    new _BreakdanceYoast();
  });
})(jQuery);
