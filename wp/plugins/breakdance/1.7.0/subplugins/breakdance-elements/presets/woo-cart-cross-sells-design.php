<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\Elements\PresetSections\PresetSectionsController::getInstance()->register(
    "EssentialElements\\woo-cart-cross-sells-design",
    c(
        "cross_sells",
        "Cross Sells",
        [c(
        "disable",
        "Disable",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'hideForElements' => ['0' => 'EssentialElements\WooCartCrossSells']],
        false,
        false,
        [],
      ), c(
        "hide_at_breakpoint",
        "Hide At Breakpoint",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.disable', 'operand' => 'is not set', 'value' => ''], 'hideForElements' => ['0' => 'EssentialElements\WooCartCrossSells']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography",
      "Title",
      "title",
       ['type' => 'popout']
     ), c(
        "space_after_title",
        "Space After Title",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\wooProductsListLayout",
      "Layout",
      "layout",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\wooProductsListElements",
      "Elements",
      "elements",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\wooProductWrapperDesign",
      "Product Wrapper",
      "product_wrapper",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'sectionOptions' => ['preset' => ['slug' => 'EssentialElements\\woo-cart-cross-sells-design']]],
        false,
        false,
        [],
      ),
    true,
    ['codeHelp' => '{{ macros.wooCartCrossSellDesign(\'%%SELECTOR%%\', %%TWIG_PATH%%, breakpoint, globalSettings) }}']
);

