<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\woo-checkout-payment-design",
    c(
        "payment",
        "Payment",
        [c(
        "sticky",
        "Sticky",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.layout', 'operand' => 'not equals', 'value' => '1'], 'hideForElements' => ['0' => 'EssentialElements\WooCheckoutPayment']],
        false,
        false,
        [],
      ), c(
        "sticky_offset",
        "Sticky Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.sticky', 'operand' => 'is set', 'value' => null], 'rangeOptions' => ['min' => 0, 'max' => 64, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'hideForElements' => ['0' => 'EssentialElements\WooCheckoutPayment']],
        true,
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
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\AtomV1CustomButtonDesign",
      "Button",
      "button",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\WooGlobalStylerOverride",
      "Override Global Styles",
      "override_global_styles",
       ['type' => 'popout']
     ), c(
        "payment_info",
        "Payment Info",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['preset' => ['slug' => 'EssentialElements\\woo-checkout-payment-design']]],
        false,
        false,
        [],
      ),
    true,
    null
);

