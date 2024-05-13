<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\posts-list-design",
    c(
        "list",
        "List",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "layout",
        "Layout",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'list', 'text' => 'List'], '1' => ['text' => 'Grid', 'value' => 'grid'], '2' => ['text' => 'Slider', 'value' => 'slider']]],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\AtomV1SwiperSettings",
      "Slider",
      "slider",
       ['condition' => ['path' => '%%CURRENTPATH%%.layout', 'operand' => 'equals', 'value' => 'slider'], 'type' => 'popout']
     ), c(
        "items_per_row",
        "Items Per Row",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.layout', 'operand' => 'equals', 'value' => 'grid']],
        true,
        false,
        [],
      ), c(
        "one_item_at",
        "One Item At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.layout', 'operand' => 'equals', 'value' => 'grid']],
        false,
        false,
        [],
      ), c(
        "space_between_items",
        "Space Between Items",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.layout', 'operand' => 'not equals', 'value' => 'slider'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout', 'preset' => ['slug' => 'EssentialElements\\posts-list-design']]],
        false,
        false,
        [],
      ),
    true,
    null
);

