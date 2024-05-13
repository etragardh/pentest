<?php

namespace Breakdance\WooCommerce;

class WooActions {
    public static $addedHooks = [];
    public static $removedHooks = [];

    /**
     * Call an specific function for an array of hooks
     * @param string $functionName
     * @param array $hooks
     * @return static
     */
    public static function callMany($functionName, $hooks)
    {
        foreach ($hooks as $hook) {
            call_user_func_array($functionName, $hook);
        }

        return new static;
    }

    /**
     * Add an array of hooks
     * @param array $hooks
     * @return static
     */
    public static function add($hooks)
    {
        static::$addedHooks = $hooks;
        static::callMany('add_action', static::$addedHooks);
        return new static;
    }

    /**
     * Conditionally remove/add hooks from the single product template
     * @param $props
     * @return static
     */
    public static function filterProduct($props)
    {
        $actions = static::filter([
            [
                'hook' => ['woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15],
                'condition' => $props['disable_upsells'] ?? false
            ],
            [
                'hook' => ['woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20],
                'condition' => $props['disable_related'] ?? false
            ],
        ]);

        static::remove($actions);
        return new static;
    }

    /**
     * Conditionally remove/add hooks from the products list
     * @param $props
     * @return static
     */
    public static function filterCatalog($props, $filterbar = null)
    {
        $actions = getActionsForCatalog($props, $filterbar);
        static::add($actions['add']);
        static::remove($actions['remove']);
        return new static;
    }

    /**
     * @param $hooks
     * @return array
     */
    public static function filter($hooks) {
        $filtered = array_filter($hooks, function ($hook) {
            return $hook['condition'] !== false;
        });

        return array_map(function ($hook) {
            return $hook['hook'];
        }, $filtered);
    }

    /**
     * Remove an array of hooks
     * @param array $hooks
     * @return static
     */
    public static function remove($hooks)
    {
        static::$removedHooks = $hooks;
        static::callMany('remove_action', static::$removedHooks);
        return new static;
    }

    /**
     * Run something before resetting hooks
     * @param callable $callback
     * @return static
     */
    public static function then(callable $callback)
    {
        if (is_callable($callback)) {
            $callback();
        }

        static::reset();
        return new static;
    }

    /**
     * Return hooks to their original state
     * @return static
     */
    public static function reset()
    {
        static::callMany('remove_action', static::$addedHooks);
        static::callMany('add_action', static::$removedHooks);
        static::$addedHooks = [];
        static::$removedHooks = [];
        return new static;
    }
}
