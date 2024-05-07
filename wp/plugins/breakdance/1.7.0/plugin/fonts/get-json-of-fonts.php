<?php

namespace Breakdance\Fonts;

/**
 *
 * @return array
 */
function get_fonts_for_builder()
{
    return array_values(FontsController::getInstance()->getFonts());
}
