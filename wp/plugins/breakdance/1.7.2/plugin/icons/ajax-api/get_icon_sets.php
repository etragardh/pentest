<?php

namespace Breakdance\Icons\AjaxApi;

use function Breakdance\Icons\get_icon_sets;

\Breakdance\AJAX\register_handler('breakdance_get_icon_sets', function () {
    return get_icon_sets();
}, 'edit', true);
