<?php

namespace Breakdance\Fonts;

include __DIR__ . '/get-json-of-fonts.php';
include __DIR__ . '/integrations/google-fonts/constants.php';
include __DIR__ . '/integrations/google-fonts/google-fonts.php';
include __DIR__ . '/integrations/custom/base.php';
include __DIR__ . '/fonts.php';

/*
 - if this ever gets refactored...
   fallback string should be an array of strings
   add a filter to allow modification of the fallback string for Google/Adobe fonts
`*/
