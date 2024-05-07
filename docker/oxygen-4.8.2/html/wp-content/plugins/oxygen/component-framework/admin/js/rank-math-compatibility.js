( function () {
    wp.hooks.addFilter(
        'rank_math_content',
        'rank-math',
        function (data) {
            return data + rm_data.oxygen_markup;
        }
    );
} )(jQuery);
