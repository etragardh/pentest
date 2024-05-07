<?php

namespace Breakdance\Elements;

/**
 * @param string $slug
 * @param string $label
 * @return void
 */
function registerCategory($slug, $label)
{
    ElementCategoriesController::getInstance()->registerCategory($slug, $label);
}

/**
 *
 * @return array{slug:string,label:string}[]
 */
function get_element_categories()
{
    return ElementCategoriesController::getInstance()->categories;
}

class ElementCategoriesController
{

    use \Breakdance\Singleton;

    /** @var array{slug:string,label:string}[] */
    public $categories = [];

    /**
     *
     * @param string $slug
     * @param string $label
     * @return void
     */
    public function registerCategory($slug, $label)
    {
        $this->categories[] = ['slug' => $slug, 'label' => $label];
    }

}


registerCategory('basic', 'Basic');
registerCategory('blocks', 'Blocks');
registerCategory('site', 'Site');
registerCategory('advanced', 'Advanced');
registerCategory('dynamic', 'Dynamic');
registerCategory('forms', 'Forms');
registerCategory('woocommerce', 'WooCommerce');
registerCategory('other', 'Other');
