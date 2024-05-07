<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\tooltip",
    c(
        "tooltip",
        "Tooltip",
        [c(
        "placement",
        "Placement",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'top', 'text' => 'Top'], '1' => ['text' => 'Top Start', 'value' => 'top-start'], '2' => ['text' => 'Top End', 'value' => 'top-end'], '3' => ['text' => 'Right', 'value' => 'right'], '4' => ['text' => 'Right Start', 'value' => 'right-start'], '5' => ['text' => 'Right End', 'value' => 'right-end'], '6' => ['text' => 'Bottom', 'value' => 'bottom'], '7' => ['text' => 'Bottom Start', 'value' => 'bottom-start'], '8' => ['text' => 'Bottom End', 'value' => 'bottom-end'], '9' => ['text' => 'Left', 'value' => 'left'], '10' => ['text' => 'Left Start', 'value' => 'left-start'], '11' => ['text' => 'Left End', 'value' => 'left-end']]],
        false,
        false,
        [],
      ), c(
        "offset",
        "Offset",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography",
      "Default",
      "default",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Headings",
      "headings",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Paragraphs",
      "paragraphs",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Links",
      "links",
       ['type' => 'popout']
     ), c(
        "space_after",
        "Space After",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "arrow",
        "Arrow",
        [c(
        "disable",
        "Disable",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "offset_x",
        "Offset X",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "offset_y",
        "Offset Y",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['preset' => ['slug' => 'EssentialElements\\tooltip']]],
        false,
        false,
        [],
      ),
    true,
    null
);

