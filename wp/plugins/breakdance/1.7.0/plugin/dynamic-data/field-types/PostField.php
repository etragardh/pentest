<?php

namespace Breakdance\DynamicData;

abstract class PostField extends StringField
{

    /**
     * @return \WP_Post|null
     */
    abstract public function getPost();

    public function controls()
    {
        return [
            \Breakdance\Elements\control('post_field', 'Post Field', [
                'type' => 'dropdown',
                'items' => [
                    ['text' => 'Post Title', 'value' => 'post_title'],
                    ['text' => 'Post Content', 'value' => 'post_content'],
                    ['text' => 'Post Excerpt', 'value' => 'post_excerpt'],
                    ['text' => 'Post Terms', 'value' => 'post_terms'],
                    ['text' => 'Post Date', 'value' => 'post_date'],
                    ['text' => 'Post Time', 'value' => 'post_time'],
                    ['text' => 'Comments Number', 'value' => 'comments_number'],
                    ['text' => 'Custom Field', 'value' => 'custom_field'],
                ],
            ]),
            \Breakdance\Elements\control('post_date_format', 'Format', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge(
                    [['text' => 'Default', 'value' => '']],
                    \Breakdance\DynamicData\get_date_formats(),
                    [['text' => 'Custom', 'value' => 'Custom'], ['text' => 'Human', 'value' => 'Human']]
                ),
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_date'
                ]
            ]),
            \Breakdance\Elements\control('post_date_format_custom', 'Custom Format', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_date_format',
                    'operand' => 'equals',
                    'value' => 'Custom'
                ]
            ]),
            \Breakdance\Elements\control('post_date_type', 'Type', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => 'Post Published', 'value' => 'published'],
                    ['text' => 'Post Modified', 'value' => 'modified'],
                ],
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_date'
                ]
            ]),
            \Breakdance\Elements\control('post_time_format', 'Format', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge(
                    [['text' => 'Default', 'value' => '']],
                    \Breakdance\DynamicData\get_time_formats(),
                    [
                        ['text' => 'Custom', 'value' => 'Custom'],
                        ['text' => 'Human', 'value' => 'Human']
                    ]
                ),
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_time'
                ]
            ]),
            \Breakdance\Elements\control('post_time_custom', 'Custom Format', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_time_format',
                    'operand' => 'equals',
                    'value' => 'Custom'
                ]
            ]),
            \Breakdance\Elements\control('post_terms_taxonomy', 'Taxonomy', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => \Breakdance\DynamicData\get_taxonomies_for_builder_post(),
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_terms'
                ]
            ]),
            \Breakdance\Elements\control('post_time_type', 'Type', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => 'Post Published', 'value' => 'published'],
                    ['text' => 'Post Modified', 'value' => 'modified'],
                ],
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_time'
                ]
            ]),
            \Breakdance\Elements\control('post_terms_link', 'Link', [
                'type' => 'toggle',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_terms'
                ]
            ]),
            \Breakdance\Elements\control('post_terms_separator', 'Separator', [
                'type' => 'text',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_terms'
                ]
            ]),
            \Breakdance\Elements\control('comments_number_zero', 'No comments', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'comments_number'
                ]
            ]),
            \Breakdance\Elements\control('comments_number_one', 'One comment', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'comments_number'
                ]
            ]),
            \Breakdance\Elements\control('comments_number_more', 'Multiple comments', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'comments_number'
                ]
            ]),
            \Breakdance\Elements\control('custom_field_key', 'Key', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'custom_field'
                ]
            ]),
        ];
    }

    /**
     * @param mixed $attributes
     */
    public function handler($attributes): StringData
    {
        if (!is_array($attributes) || !array_key_exists('post_field', $attributes)) {
            return StringData::emptyString();
        }

        $value = '';
        $post = $this->getPost();
        if (!$post) {
            return StringData::emptyString();
        }

        if (isset($attributes['post_field'])) {
            $value = (string) $post->{$attributes['post_field']};
        }

        if ($attributes['post_field'] === 'post_date') {
            $value = $this->getPostDateValue($post, $attributes);
        }

        if ($attributes['post_field'] === 'post_time') {
            $value = $this->getPostTimeValue($post, $attributes);
        }

        if ($attributes['post_field'] === 'post_terms') {
            $value = $this->getPostTermsValue($post, $attributes);
        }

        if ($attributes['post_field'] === 'comments_number') {
            $value = (string) get_comments_number_text(
                (string) ($attributes['comments_number_zero'] ?? 'No comments'),
                (string) ($attributes['comments_number_one'] ?? 'One comment'),
                (string) ($attributes['comments_number_more'] ?? '% comment'),
                $post->ID
            );
        }

        if ($attributes['post_field'] === 'custom_field') {
            if (!array_key_exists('custom_field_key', $attributes) || !is_string($attributes['custom_field_key'])) {
                return StringData::emptyString();
            }
            $value = (string) get_post_meta($post->ID, $attributes['custom_field_key'], true);
        }

        return StringData::fromString($value);
    }


    /**
     * @param \WP_Post $post
     * @param mixed $attributes
     * @return string
     */
    protected function getPostDateValue($post, $attributes)
    {
        if (!is_array($attributes)) {
            return '';
        }
        $postDateType = (string) ($attributes['post_date_type'] ?? 'modified');
        $format = (string) ($attributes['post_date_format'] ?? '');
        if ($format === 'Custom') {
            $format = (string) ($attributes['post_date_format_custom'] ?? '');
        }
        if ($postDateType === 'modified') {
            if ($format === 'Human') {
                return human_time_diff((int) get_the_modified_date('U', $post));
            }
            return (string) apply_filters('the_modified_date', get_the_modified_date($format, $post));
        }
        if ($format === 'Human') {
            return human_time_diff((int) get_the_date('U', $post));
        }
        return (string) apply_filters('the_date', get_the_date($format, $post));
    }

    /**
     * @param \WP_Post $post
     * @param mixed $attributes
     * @return string
     */
    protected function getPostTimeValue($post, $attributes)
    {
        if (!is_array($attributes)) {
            return '';
        }
        $format = (string) ($attributes['post_time_format'] ?? '');
        if ($format === 'Custom') {
            $format = (string) ($attributes['post_time_custom'] ?? '');
        }
        $postTimeType = (string) ($attributes['post_time_type'] ?? 'modified');
        if ($postTimeType === 'published') {
            if ($format === 'Human') {
                return (string) human_time_diff((int) get_the_time('U', $post));
            }
            return (string) apply_filters('the_time', get_the_time((string) $format, $post));
        }
        if ($format === 'Human') {
            return (string) human_time_diff((int) get_the_modified_time('U', $post));
        }
        return (string) apply_filters('the_modified_time', get_the_modified_time((string) $format, $post));
    }

    /**
     * @param \WP_Post $post
     * @param mixed $attributes
     * @return string
     */
    protected function getPostTermsValue($post, $attributes)
    {
        if (!is_array($attributes) || !array_key_exists('post_terms_taxonomy', $attributes)) {
            return '';
        }
        $separator = (string) ($attributes['post_terms_separator'] ?? ', ');
        $terms = get_the_term_list($post->ID, (string) $attributes['post_terms_taxonomy'], '', $separator);

        // a similar check is duplicated a few times
        if (is_wp_error($terms)) {
            /** @var \WP_Error $terms */
            $message = "WP_Error: " . $terms->get_error_message(); // should we actually get all the error messages and join them?
            return $message;
        }

        if (!is_string($terms)) {
            return '';
        }

        $link = filter_var($attributes['post_terms_link'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if ($link) {
            return $terms;
        }
        return (string) strip_tags($terms);
    }
}
