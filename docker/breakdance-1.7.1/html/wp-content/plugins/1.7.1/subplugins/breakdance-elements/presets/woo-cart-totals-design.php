<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\woo-cart-totals-design",
    c(
        "totals",
        "Totals",
        [c(
        "hide_title",
        "Hide Title",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'hideForElements' => ['0' => 'EssentialElements\Woopageshoppingcart']],
        false,
        false,
        [],
      ), c(
        "space_after_title",
        "Space After Title",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "wrapper",
        "Wrapper",
        [c(
        "max_width",
        "Max Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => []]],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "rows",
        "Rows",
        [c(
        "spacing",
        "Spacing",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 64, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        false,
        false,
        [],
      ), c(
        "separator",
        "Separator",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "separator_color",
        "Separator Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.separator', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "separator_height",
        "Separator Height",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.separator', 'operand' => 'is set', 'value' => null]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "button",
        "Button",
        [c(
        "space_above",
        "Space Above",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['value' => 'center', 'text' => 'Center', 'icon' => 'AlignCenterIcon'], '2' => ['value' => 'right', 'text' => 'Right', 'icon' => 'AlignRightIcon'], '3' => ['icon' => 'AlignJustifyIcon', 'text' => 'Justify', 'value' => 'justify']]],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\AtomV1CustomButtonDesign",
      "Styles",
      "styles",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Heading",
      "heading",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Labels",
      "labels",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Values",
      "values",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Price Amounts",
      "price_amounts",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Total Label",
      "total_label",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Total Amount",
      "total_amount",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects_with_hoverable_everything",
      "Links",
      "links",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\WooGlobalStylerOverride",
      "Override Global Styles",
      "override_global_styles",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'sectionOptions' => ['preset' => ['slug' => 'EssentialElements\\woo-cart-totals-design']]],
        false,
        false,
        [],
      ),
    true,
    ['codeHelp' => '{{ macros.wooCartTotalsDesign(\'%%SELECTOR%%\', %%TWIG_PATH%%, breakpoint, globalSettings) }}']
);

