<?php

namespace EssentialElements;

// Note the inheritance, hence this is not in ElementStudio
use function Breakdance\Elements\getRequiredPluginsNotActiveSsrMessage;
use function Breakdance\Elements\hasRequiredPluginsAndTheyAreAvailable;

class WooProductTitle extends \EssentialElements\Heading
{
    static function name()
    {
        return 'Product Title';
    }

    static function slug()
    {
       return get_class();
    }

    static function contentControls()
    {
        $controls = parent::contentControls();

        array_splice($controls[0]['children'], 0, 1);

        return $controls;
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

        \Breakdance\WooCommerce\renderProductPart($productId, function () {
            the_title();
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
            ['name' => 'bde-wooproducttitle', 'template' => 'yes'],
            ['name' => 'breakdance-woocommerce', 'template' => 'yes'],
            ['name' => 'product_title', 'template' => 'yes']
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

    // the original WooProductTitle had depensOnGlobalScript, but idk if it's necessary
    static function settings()
    {
        return ['requiredPlugins' => ['0' => 'WooCommerce']];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['none'];
    }
}
