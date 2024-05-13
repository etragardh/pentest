<?php

namespace EssentialElements;

// Note the inheritance, hence this is not in ElementStudio
use function Breakdance\Elements\getRequiredPluginsNotActiveSsrMessage;
use function Breakdance\Elements\hasRequiredPluginsAndTheyAreAvailable;

class ProductExcerpt extends \EssentialElements\RichText
{
    static function name()
    {
        return 'Product Excerpt';
    }

    static function slug()
    {
       return get_class();
    }

    static function contentControls()
    {
        return [];
    }

    static function template()
    {
        return '%%SSR%%';
    }

    /**
     * @param mixed $propertiesData
     * @param mixed $parentPropertiesData
     * @param bool $isBuilder
     * @param int $repeaterItemNodeId
     * @return string
     */
    static function ssr($propertiesData, $parentPropertiesData = [], $isBuilder = false, $repeaterItemNodeId = null)
    {
        ob_start();

        if (!hasRequiredPluginsAndTheyAreAvailable(self::settings())){
            return getRequiredPluginsNotActiveSsrMessage(self::settings()['requiredPlugins'], self::name());
        }

        $productId = $parentPropertiesData['content']['content']['product'] ?? null;

        \Breakdance\WooCommerce\renderProductPart($productId, function ($product) {
            $short_description = apply_filters("woocommerce_short_description", $product->get_short_description());
            echo $short_description;
        });

        return ob_get_clean();
    }
        
    static function attributes()
    {
        return [];
    }

    static function category()
    {
        return 'woocommerce';
    }

    static function order()
    {
        return 0;
    }

    static function badge()
    {
        return ['backgroundColor' => 'var(--brandWooCommerceBackground)', 'textColor' => 'var(--brandWooCommerce)', 'label' => 'Woo'];
    }

    static function additionalClasses()
    {
        return [
            ['name' => 'bde-wooproductexcerpt', 'template'=> 'yes'],
            ['name' => 'breakdance-woocommerce', 'template' => 'yes'],
            ['name' => 'breakdance-rich-text-styles', 'template' => 'yes']
        ];
    }

    static function nestingRule()
    {
        return ["type" => "final",  "restrictedToBeADescendantOf" => ['EssentialElements\Productbuilder'],];
    }

    static function addPanelRules()
    {
        return false;
    }

    // the original Wooproductexcerpt had depensOnGlobalScript, but idk if it's necessary
    static function settings()
    {
        return ['requiredPlugins' => ['0' => 'WooCommerce']];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['none'];
    }
}
