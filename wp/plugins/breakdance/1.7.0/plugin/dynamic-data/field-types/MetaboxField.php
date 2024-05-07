<?php

namespace Breakdance\DynamicData;

use function Breakdance\isRequestFromBuilderDynamicDataGet;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\isRequestFromBuilderSsr;

class MetaboxField extends StringField {

    /**
     * @var MetaboxField
     */
    public array $field;

    /**
     * @param MetaboxField $field
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
        return 'Metabox';
    }

    /**
     * @inheritDoc
     */
    public function subcategory()
    {
        return $this->field['group'];
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        if ($this->field['is_sub_field']) {
            return 'metabox_field_' . $this->field['group_id'] . '_' . $this->field['id'];
        }
        $isSettingsPage = $this->field['is_settings_page'] ?? false;
        if ($isSettingsPage) {
            return 'metabox_field_' . $this->field['settings_page'] . '_' . $this->field['id'];
        }
        return 'metabox_field_' . $this->field['id'];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        if (array_key_exists('type', $this->field) && $this->field['type'] === 'url') {
            return ['string', 'url'];
        }
        return ['string'];
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
    public function handler($attributes): StringData
    {
        $value = (string) self::getValue($this->field);
        return StringData::fromString($value);
    }

    /**
     * @param MetaboxField $field
     * @param string $postType
     * @return bool
     */
    static public function isFieldAvailableForPostType($field, $postType)
    {
        if (!function_exists('rwmb_get_object_fields')) {
            return false;
        }

        if (in_array($postType, (array) BREAKDANCE_DYNAMIC_DATA_PREVIEW_POST_TYPES)) {
            return true;
        }

        $isSettingsPage = $field['is_settings_page'] ?? false;
        if ($isSettingsPage) {
            return true;
        }

        /** @var MetaboxField[] $fields */
        $fields = rwmb_get_object_fields($postType);
        $fieldIds = array_keys($fields);

        return in_array($field['id'], $fieldIds, true);
    }

    /**
     * @param MetaboxField $field
     * @return false|mixed
     */
    public static function getValue($field) {
        if ($field['is_sub_field']) {
            if (isRequestFromBuilderDynamicDataGet()) {
                /**
                 * @psalm-suppress PossiblyFalseReference
                 */
                $parentField = DynamicDataController::getInstance()->getField('metabox_group_' . $field['group_id']);
                /**
                 * @psalm-suppress MixedMethodCall
                 */
                if ($parentField && empty($parentField->loop->get())) {
                    /**
                     * @psalm-suppress PossiblyFalseReference
                     * @psalm-suppress UndefinedMethod
                     */
                    $parentField->hasSubFields();
                }
            }
            /** @var MetaboxField $subFieldValues */
            $subFieldValues = LoopController::getInstance((string) $field['group_id'])->get();
            return $subFieldValues[$field['id']] ?? false;
        }

        $args = [];
        $postId = null;
        if ($field['is_settings_page']) {
            $args['object_type'] = 'setting';
            $postId = $field['settings_page'];
        }
        /**
         * @psalm-suppress UndefinedFunction
         */
        return rwmb_get_value($field['id'], $args, $postId);
    }
}
