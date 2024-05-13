<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-width',
        'label' => 'Width (' . strtolower( (string) get_option( 'woocommerce_dimension_unit' ) ) . ')',
        'category' => 'Measurements',
        'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */
            function ($operand, $value): bool {
                global $product;
                if (!$product) {
                    return false;
                }
                /** @var \WC_Product $product */
                $product = $product;
                $width = $product->get_width();
                if (empty($width)) {
                    return false;
                }
                switch ($operand) {
                    case OPERAND_IS:
                        return $width === $value;
                    case OPERAND_IS_NOT:
                        return $width !== $value;
                    case OPERAND_GREATER_THAN:
                        return $width > $value;
                    case OPERAND_LESS_THAN:
                        return $width < $value;
                }

                return false;
            },
        'templatePreviewableItems' => false,
        'queryCallback' => /**
         * @param WordPressQueryVars $query
         * @param string $operand
         * @param string $value
         * @return WordPressQueryVars
         */
            function ($query, $operand, $value) {
                $compare = operandToQueryCompare($operand);
                $query['meta_query'][] = [
                    'compare' => $compare,
                    'key' => '_width',
                    'type' => 'numeric',
                    'value' => $value
                ];
                return $query;
            },
    ]
);

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-height',
        'label' => 'Height (' . strtolower( (string) get_option( 'woocommerce_dimension_unit' ) ) . ')',
        'category' => 'Measurements',
        'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */
            function ($operand, $value): bool {
                global $product;
                if (!$product) {
                    return false;
                }
                /** @var \WC_Product $product */
                $product = $product;
                $height = $product->get_height();
                if (empty($height)) {
                    return false;
                }
                switch ($operand) {
                    case OPERAND_IS:
                        return $height === $value;
                    case OPERAND_IS_NOT:
                        return $height !== $value;
                    case OPERAND_GREATER_THAN:
                        return $height > $value;
                    case OPERAND_LESS_THAN:
                        return $height < $value;
                }

                return false;
            },
        'templatePreviewableItems' => false,
        'queryCallback' => /**
         * @param WordPressQueryVars $query
         * @param string $operand
         * @param string $value
         * @return WordPressQueryVars
         */
            function ($query, $operand, $value) {
                $compare = operandToQueryCompare($operand);
                $query['meta_query'][] = [
                    'compare' => $compare,
                    'key' => '_height',
                    'type' => 'numeric',
                    'value' => $value
                ];
                return $query;
            },
    ]
);

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-length',
        'label' => 'Length (' . strtolower( (string) get_option( 'woocommerce_dimension_unit' ) ) . ')',
        'category' => 'Measurements',
        'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */
            function ($operand, $value): bool {
                global $product;
                if (!$product) {
                    return false;
                }
                /** @var \WC_Product $product */
                $product = $product;
                $length = $product->get_length();
                if (empty($length)) {
                    return false;
                }
                switch ($operand) {
                    case OPERAND_IS:
                        return $length === $value;
                    case OPERAND_IS_NOT:
                        return $length !== $value;
                    case OPERAND_GREATER_THAN:
                        return $length > $value;
                    case OPERAND_LESS_THAN:
                        return $length < $value;
                }

                return false;
            },
        'templatePreviewableItems' => false,
        'queryCallback' => /**
         * @param WordPressQueryVars $query
         * @param string $operand
         * @param string $value
         * @return WordPressQueryVars
         */
            function ($query, $operand, $value) {
                $compare = operandToQueryCompare($operand);
                $query['meta_query'][] = [
                    'compare' => $compare,
                    'key' => '_length',
                    'type' => 'numeric',
                    'value' => $value
                ];
                return $query;
            },
    ]
);

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-weight',
        'label' => 'Weight (' . strtolower( (string) get_option( 'woocommerce_weight_unit' ) ) . ')',
        'category' => 'Measurements',
        'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */

            function ($operand, $value): bool {
                global $product;
                if (!$product) {
                    return false;
                }
                /** @var \WC_Product $product */
                $product = $product;
                $weight = $product->get_weight();
                if (empty($weight)) {
                    return false;
                }
                switch ($operand) {
                    case OPERAND_IS:
                        return $weight === $value;
                    case OPERAND_IS_NOT:
                        return $weight !== $value;
                    case OPERAND_GREATER_THAN:
                        return $weight > $value;
                    case OPERAND_LESS_THAN:
                        return $weight < $value;
                }

                return false;
            },
        'templatePreviewableItems' => false,
        'queryCallback' => /**
         * @param WordPressQueryVars $query
         * @param string $operand
         * @param string $value
         * @return WordPressQueryVars
         */
            function ($query, $operand, $value) {
                $compare = operandToQueryCompare($operand);
                $query['meta_query'][] = [
                    'compare' => $compare,
                    'key' => '_weight',
                    'type' => 'numeric',
                    'value' => $value
                ];
                return $query;
            },
    ]
);
