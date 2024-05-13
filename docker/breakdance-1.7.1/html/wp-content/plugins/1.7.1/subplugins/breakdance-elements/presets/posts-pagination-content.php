<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\posts-pagination-content",
    c(
        "pagination",
        "Pagination",
        [c(
        "pagination",
        "Pagination",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'none', 'text' => 'None'], '1' => ['text' => 'Numbers', 'value' => 'numbers'], '2' => ['text' => 'Previous / Next', 'value' => 'prevnext'], '3' => ['text' => 'Numbers + Previous / Next', 'value' => 'numbersprevnext'], '4' => ['text' => 'Load More Button', 'value' => 'load_more'], '5' => ['text' => 'Infinite Scroll', 'value' => 'infinite']]],
        false,
        false,
        [],
      ), c(
        "previous_text",
        "Previous Text",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.pagination', 'operand' => 'is one of', 'value' => ['0' => 'prevnext', '1' => 'numbersprevnext']]],
        false,
        false,
        [],
      ), c(
        "next_text",
        "Next Text",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.pagination', 'operand' => 'is one of', 'value' => ['0' => 'prevnext', '1' => 'numbersprevnext']]],
        false,
        false,
        [],
      ), c(
        "show_all_page_numbers",
        "Show All Page Numbers",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.pagination', 'operand' => 'is one of', 'value' => ['0' => 'numbers', '1' => 'numbersprevnext']]],
        false,
        false,
        [],
      ), c(
        "load_more_text",
        "Load More Text",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.pagination', 'operand' => 'equals', 'value' => 'load_more']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout', 'preset' => ['slug' => 'EssentialElements\\posts-pagination-content']]],
        false,
        false,
        [],
      ),
    true,
    null
);

