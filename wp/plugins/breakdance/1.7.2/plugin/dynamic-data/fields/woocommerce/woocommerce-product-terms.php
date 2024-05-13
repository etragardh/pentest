<?php

namespace Breakdance\DynamicData;

class WoocommerceProductTerms extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Product Terms';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'WooCommerce';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'product_terms';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control(
                'product',
                'Product',
                [
                    'type' => 'post_chooser',
                    'layout' => 'vertical',
                    'postChooserOptions' => [
                        'multiple' => false,
                        'showThumbnails' => false,
                        'postType' => 'Product'
                    ]
                ]
            ),
            \Breakdance\Elements\control('taxonomy', 'Taxonomy', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_values(array_map(function ($taxonomy) {
                    return ['text' => $taxonomy->label ?: $taxonomy->name, 'value' => $taxonomy->name];
                }, get_object_taxonomies('product', 'objects'))),
            ]),

            \Breakdance\Elements\control('link', 'Link', [
                'type' => 'toggle',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('separator', 'Separator', ['type' => 'text']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        global $post;
        $productId = $post->ID ?? null;
        if (!empty($attributes['product'])) {
            $productId = $attributes['product'];
        }

        if (!$productId || !array_key_exists('taxonomy', $attributes)) {
            return StringData::emptyString();
        }

        $link = filter_var($attributes['link'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $separator = $attributes['separator'] ?? ', ';
        $terms = get_the_term_list($productId, $attributes['taxonomy'], '', $separator);

        // a similar check is duplicated a few times
        if (is_wp_error($terms)) {
            $message = "WP_Error: " . $terms->get_error_message(); // should we actually get all the error messages and join them?
            return StringData::fromString($message);
        }

        if ($link) {
            return StringData::fromString($terms);
        }

        return StringData::fromString(strip_tags($terms));
    }
}
