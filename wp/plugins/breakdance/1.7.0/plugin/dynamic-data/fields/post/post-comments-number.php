<?php

namespace Breakdance\DynamicData;

class PostCommentsNumber extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Comments Number';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Post';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'post_comments_number';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('zero', 'No comments', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('one', 'One comment', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('more', 'Multiple comments', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $value = get_comments_number_text(
            $attributes['zero'] ?? 'No comments',
            $attributes['one'] ?? 'One comment',
            $attributes['more'] ?? '% comment'
        );
        return StringData::fromString($value);
    }

    /**
     * @inheritDoc
     */
    function proOnly() {
        return false;
    }

}
