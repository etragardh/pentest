<?php

namespace Breakdance\DynamicData;

class AcfRepeaterField extends RepeaterField
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
    public function slug()
    {
        return 'acf_repeater_' . $this->field['key'];
    }

    /**
     * @inheritDoc
     */
    public function parentField()
    {
        $parentType = $this->field['parent_type'] ?? 'repeater';
        return DynamicDataController::getInstance()->getField('acf_' . $parentType. '_' . $this->field['parent_key']);
    }

    /**
     * @param $postId
     * @return bool
     */
    public function hasSubFields($postId = false)
    {
        $parentType = $this->field['parent_type'] ?? null;
        if (in_array($parentType, ['repeater','group']) && !acf_get_loop('active')) {
            $parentField = $this->parentField();
            if ($parentField) {
                $parentField->hasSubFields($postId);
            }
        }
        return has_sub_fields($this->field['name'], $postId);
    }

    /**
     * @inheritDoc
     */
    public function setSubFieldIndex($index) {
        acf_update_loop( 'active', 'i', $index);
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): RepeaterData
    {
        $postId = $this->field['is_option_page'] === true ? 'option' : false;
        $repeaterField = get_field_object($this->field['name'], $postId);
        if (!$repeaterField || !$repeaterField['value']) {
            return RepeaterData::fromArray([]);
        }
        return RepeaterData::fromArray($repeaterField['value']);
    }
}
