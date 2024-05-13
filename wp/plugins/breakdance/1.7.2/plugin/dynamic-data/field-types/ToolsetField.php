<?php

namespace Breakdance\DynamicData;

class ToolsetField extends StringField {


    /**
     * @var ToolsetField $field
     */
    public $field;

    /**
     * @param ToolsetField $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @inheritDoc
     */
    public function label()
    {
        return $this->field['name'];
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Toolset';
    }

    /**
     * @inheritDoc
     */
    public function subcategory()
    {
        return $this->field['group'];
    }

    /**
     * @param string $postType
     * @return bool
     */
    public function availableForPostType($postType)
    {
        return self::isFieldAvailableForPostType($this->field, $postType);
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'toolset_field_' . $this->field['slug'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $value = self::getValue($this->field, ['output' => 'raw']);
        return StringData::fromString((string) $value);
    }

    /**
     * @param ToolsetField $field
     * @param array $userParams
     * @return string
     */
    public static function getValue($field, $userParams = []) {
        /**
         * @psalm-suppress UndefinedFunction
         * @var string $value
         */
        $value = types_render_field($field['slug'], $userParams);
        return $value;
    }

    /**
     * @param ToolsetField $field
     * @param string $postType
     * @return bool
     */
    static public function isFieldAvailableForPostType($field, $postType)
    {
        if (in_array($postType, (array) BREAKDANCE_DYNAMIC_DATA_PREVIEW_POST_TYPES)) {
            return true;
        }

        return in_array($postType, $field['post_types'], true);
    }
}

