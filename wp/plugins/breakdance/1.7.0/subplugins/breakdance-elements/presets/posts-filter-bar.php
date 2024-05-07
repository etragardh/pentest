<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\posts-filter-bar",
    c(
        "filter_bar",
        "Filter Bar",
        [c(
        "enable",
        "Enable",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "type",
        "Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'dropdownOptions' => ['populate' => ['path' => '', 'text' => '', 'value' => '', 'fetchDataAction' => 'breakdance_get_taxonomies', 'fetchContextPath' => '', 'refetchPaths' => []]], 'condition' => ['0' => ['0' => ['path' => 'content.filter_bar.enable', 'operand' => 'is set', 'value' => '']]]],
        false,
        false,
        [],
      ), c(
        "all_filter",
        "All Filter",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'content.filter_bar.enable', 'operand' => 'is set', 'value' => '']]]],
        false,
        false,
        [],
      ), c(
        "all_label",
        "All Label",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'content.filter_bar.all_filter', 'operand' => 'is set', 'value' => ''], '1' => ['path' => 'content.filter_bar.enable', 'operand' => 'is set', 'value' => '']]]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['preset' => ['slug' => 'EssentialElements\\posts-filter-bar']]],
        false,
        false,
        [],
      ),
    true,
    null
);

