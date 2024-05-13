<?php

namespace Breakdance\CustomCSS;

use Breakdance\Elements\PresetSections\PresetSectionsController;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\custom_css",
    controlSection('custom_css', 'Custom CSS', [
        getResponsiveCssControl(),
    ]),
    true
);


/**
 * @return Control
 * TODO this needs to be a preset in itself so that we can re-use it
 */
function getResponsiveCssControl()
{
    return responsiveControl(
        'css',
        'Custom CSS',
        [
            'placeholder' => "%%SELECTOR%% {\n  background-color: red; \n}",
            'codeOptions' => ['language' => 'css', 'autofillOnEmpty' => "%%SELECTOR%% {
  PLACECURSORHERE
}"],
            'type' => "code",
            'layout' => 'vertical'
        ]
    );
}
