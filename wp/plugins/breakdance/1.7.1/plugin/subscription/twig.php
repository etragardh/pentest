<?php

namespace Breakdance\Subscription;

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'freeModeOnFrontend',
    'Breakdance\Subscription\freeModeOnFrontend',
    '() => false'
);

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'isPopupUsingProOnlyTriggers',
    'Breakdance\Subscription\isPopupUsingProOnlyTriggers',
    '() => false'
);
