<?php

namespace Breakdance\Icons\AjaxApi;

use function Breakdance\Icons\find_icons;

\Breakdance\AJAX\register_handler(
    'breakdance_find_icons',
    /**
     * @param string|null $search_term
     * @param string|null $icon_set_slug
     * @param int|null $offset
     * @param string[]|null $suggestions
     *
     * @return array
     */
    function ($search_term, $icon_set_slug, $offset, $suggestions) {
        return find_icons([
            'search_term' => $search_term,
            'icon_set_slug' => $icon_set_slug,
            'offset' => $offset,
            'suggestions' => $suggestions,
        ]);
    },
    'edit',
    true,
    [
        'args' => [
            'searchTerm' => FILTER_UNSAFE_RAW,
            'iconSetSlug' => FILTER_UNSAFE_RAW,
            'offset' => FILTER_VALIDATE_INT,
            'suggestions' => [
                'filter' => FILTER_DEFAULT,
                'flags' => FILTER_REQUIRE_ARRAY,
            ],
        ],
        'optional_args' => ['searchTerm', 'iconSetSlug', 'offset', 'suggestions'],
    ]
);
