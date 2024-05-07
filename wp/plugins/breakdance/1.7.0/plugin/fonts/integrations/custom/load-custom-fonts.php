<?php

namespace Breakdance\CustomFonts;

use Breakdance\Fonts\FontsController;
use function Breakdance\Preferences\get_preferences;

add_action('breakdance_register_fonts', '\Breakdance\CustomFonts\loadCustomFonts');

function loadCustomFonts(FontsController $fontsController)
{

    // By adding the fonts to the top of the array with registerFontAtTheStar
    // They end up 'last in array' as 'first in font list'
    // So we reverse the custom fonts to have them as newest-to-oldest instead
    $fonts = array_reverse(get_preferences()['customFonts']);

    foreach ($fonts as $font) {
        // Add the custom fonts at the start of the list
        $fontsController->registerFontAtTheStart(
            $font['id'],
            addQuotesToCssNameIfNecessary($font['cssName']),
            $font['family'],
            $font['fallbackString'],
            [
                'styles' => [$font['cssUrl']]
            ]
        );

    }

}
