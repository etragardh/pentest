<?php

namespace Breakdance\GlobalSettings\WooCommerce;

use function Breakdance\Elements\PresetSections\getPresetSection;

/**
 * @return Control
 * @throws \Exception
 */
function WOO_SECTION()
{
    $section = getPresetSection("EssentialElements\\WooGlobalStylerOverride", "WooCommerce", "woocommerce", ['type' => 'accordion']);

    if (!class_exists('Woo_Variation_Swatches', false) && !defined('CFVSW_VER')) {
        unset($section['children'][5]); // Remove Variation Swatches section if no plugin is active.
    }

    return $section;
}

/**
 * @return string
 */
function WOO_TEMPLATE()
{
  return "
      {% set woocommerceEnabled = true %}

      {% if woocommerceEnabled %}
          {{ macros.wooGlobalStyler(settings.woocommerce, null, breakpoint, settings) }}
      {% endif %}
    ";
}
