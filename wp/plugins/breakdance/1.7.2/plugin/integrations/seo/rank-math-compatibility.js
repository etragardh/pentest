(function ($) {
  _BreakdanceRankMath = function () {
    if (typeof rankMath !== "undefined") {
      wp.hooks.addFilter(
        "rank_math_content",
        "rank-math",
        this.replaceContentWithBreakdanceData
      );
    }
  };

  _BreakdanceRankMath.prototype.replaceContentWithBreakdanceData = function () {
    return breakdance.content;
  };

  $(document).ready(function () {
    new _BreakdanceRankMath();
  });
})(jQuery);
