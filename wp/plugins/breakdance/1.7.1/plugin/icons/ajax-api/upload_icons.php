<?php

namespace Breakdance\Icons\AjaxApi;

use function Breakdance\Icons\upload_icons;

\Breakdance\AJAX\register_handler('breakdance_upload_icons', function (array $icons, array $iconSet) {
    /**
     * @psalm-suppress MixedArgument
     * @psalm-suppress InvalidArgument
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    upload_icons($icons, $iconSet);
}, 'edit', true, [
    'args' => [
        'icons' => [
            'filter' => FILTER_DEFAULT,
            'flags' => FILTER_REQUIRE_ARRAY,
        ],
        'iconSet' => [
            'filter' => FILTER_DEFAULT,
            'flags' => FILTER_REQUIRE_ARRAY,
        ],
    ],
]);
