<?php

namespace Breakdance\DynamicData;

class AuthorName extends StringField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Author Name';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Author';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'author_name';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('name_field', 'Field', [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge([
                    ['text' => 'Display Name', 'value' => 'display_name'],
                    ['text' => 'First Name', 'value' => 'first_name'],
                    ['text' => 'Last Name', 'value' => 'last_name'],
                ])
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultAttributes()
    {
        return [
            'name_field' => 'display_name',
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        global $post;
        $data = new StringData();
        $data->value = '';

        if (!$post || !array_key_exists('name_field', $attributes)) {
            return $data;
        }

        $author_id = $post->post_author;

        if ($attributes['name_field'] === 'display_name') {
            $data->value = get_the_author_meta('display_name', $author_id);
        }

        if ($attributes['name_field'] === 'first_name') {
            $data->value = get_the_author_meta('first_name', $author_id);
        }

        if ($attributes['name_field'] === 'last_name') {
            $data->value = get_the_author_meta('last_name', $author_id);
        }

        return $data;
    }
}
