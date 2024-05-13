<?php

namespace Breakdance\DynamicData;

class WoocommerceProductImage extends ImageField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Product Image';
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
        return 'product_image';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
        \Breakdance\Elements\control('product', 'Product', [
                'type' => 'post_chooser',
                'layout' => 'vertical',
                'postChooserOptions' => [
                    'multiple' => false,
                    'showThumbnails' => false,
                    'postType' => 'Product'
                ]
            ]
        )];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        global $post;
        $productId = $post->ID ?? null;
        if (!empty($attributes['product'])) {
            $productId = $attributes['product'];
        }

        $product = wc_get_product($productId);
        if (!$product) {
            return ImageData::emptyImage();
        }

        $imageId = $product->get_image_id();
        return ImageData::fromAttachmentId($imageId);
    }
}

