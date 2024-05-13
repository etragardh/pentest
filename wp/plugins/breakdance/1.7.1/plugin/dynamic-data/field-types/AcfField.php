<?php

namespace Breakdance\DynamicData;

use function Breakdance\isRequestFromBuilderDynamicDataGet;
use function Breakdance\Util\WP\isAnyArchive;

class AcfField extends StringField
{

    /**
     * @var ACFField
     */
    public array $field;

    /**
     * @param ACFField $field
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
        return $this->field['label'];
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'ACF';
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
        return 'acf_field_' . $this->field['key'];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        if ($this->field['type'] === 'url') {
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
        return (bool) self::isFieldAvailableForPostType($this->field, $postType);
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
     * @param ACFField $field
     * @param string $postType
     * @return bool
     */
    public static function isFieldAvailableForPostType($field, string $postType)
    {
        if (!function_exists('acf_get_field_groups')) {
            return false;
        }

        if ($field['is_option_page']) {
            return true;
        }

        if (in_array($postType, (array) BREAKDANCE_DYNAMIC_DATA_PREVIEW_POST_TYPES)) {
            return true;
        }

        if (!in_array($postType, get_post_types())) {
            // if this is not a registered post type,
            // it is likely an archive template or similar
            // so lets display all available fields
            return true;
        }

        $filterFieldsBy = ['post_type' => $postType];

        // If there's a current post ID in the request, also
        // check if the field is available for the specific post
        $postId = (int) filter_input(INPUT_POST, 'id');
        if ($postId) {
            $filterFieldsBy['post_id'] = $postId;
        }

        /** @var ACFGroup[] $groups */
        $groups = acf_get_field_groups($filterFieldsBy);
        foreach ($groups as $group) {
            if ($field['group_id'] === $group['ID']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ACFField $field
     * @param boolean $formatValue
     * @return false|mixed
     */
    public static function getValue($field, $formatValue = true) {
        if (!function_exists('get_field') || !function_exists('get_sub_field')) {
            return false;
        }

        // if field is from an option page, pass the post ID as 'option'
        $postId = $field['is_option_page'] === true ? 'option' : false;

        // if field is from a taxonomy archive, pass the post ID as '{taxonomy}_{termId}'
        if ($field['is_tax_field'] ?? false) {

            $archiveObject = get_queried_object();
            
            if ($archiveObject instanceof \WP_Term) {
                $postId = $archiveObject->taxonomy . '_' . $archiveObject->term_id;
            }

        }


        $parentType = $field['parent_type'] ?? false;
        if ($parentType === 'repeater') {
            if (!acf_get_loop('active') && isRequestFromBuilderDynamicDataGet() && array_key_exists('parent_repeater', $field)) {
                /**
                 * @psalm-suppress PossiblyFalseReference
                 */
                $parentRepeater = DynamicDataController::getInstance()->getField('acf_repeater_' . $field['parent_repeater']);
                if ($parentRepeater) {
                    /**
                     * @psalm-suppress PossiblyFalseReference
                     * @psalm-suppress UndefinedMethod
                     */
                    $parentRepeater->hasSubFields($postId);
                }
            }

            return get_sub_field($field['name'], $formatValue);
        }

        if ($parentType === 'group') {
            $parentKey = $field['parent_key'] ?? '';
            /** @var ACFFieldObject|false $parent */
            $parent = DynamicDataController::getInstance()->getField('acf_group_' . $parentKey);
            if ($parent) {
                /** @var mixed $parentValue */
                $parentValue = AcfField::getValue($parent->field, $formatValue);
                if (is_array($parentValue)) {
                    if ($formatValue  && array_key_exists($field['name'], $parentValue)) {
                        return $parentValue[$field['name']];
                    }
                    if (!$formatValue  && array_key_exists($field['key'], $parentValue)) {
                        return $parentValue[$field['key']];
                    }
                }
            }
            return false;
        }

        return get_field($field['name'], $postId, $formatValue);
    }
}

