<?php

namespace Breakdance\DynamicData;

class AcfGroupField extends AcfField {

    public function slug()
    {
        return 'acf_group_' . $this->field['key'];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['string'];
    }

    /**
     * @inheritDoc
     */
    public function parentField()
    {
        $parentType = $this->field['parent_type'] ?? 'group';
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

    public function availableForPostType($postType)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        return StringData::emptyString();
    }
}
