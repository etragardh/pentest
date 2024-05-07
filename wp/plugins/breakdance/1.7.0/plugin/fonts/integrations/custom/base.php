<?php

// @psalm-ignore-file

namespace Breakdance\CustomFonts;

include __DIR__ . '/load-custom-fonts.php';
include __DIR__ . '/save-font-family.php';
include __DIR__ . '/save-font-file.php';

/**
 * @param string $fontFamily
 * @return string
 */
function addQuotesToCssNameIfNecessary(string $fontFamily): string {
    return strpos($fontFamily, " ") === false ? $fontFamily : '"'.trim($fontFamily, '"').'"';
}

/* this shit needs a refactor */
