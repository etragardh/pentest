<?php

namespace Breakdance\Icons\AjaxApi;

use function Breakdance\Icons\delete_icon_set;

\Breakdance\AJAX\register_handler('breakdance_delete_icon_set', function (string $iconSetSlug) {
    delete_icon_set($iconSetSlug);
}, 'edit', true, [
    'args' => [
        'iconSetSlug' => FILTER_UNSAFE_RAW,
    ],
]);
