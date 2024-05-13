<?php

namespace Breakdance\DynamicData;

class MetaboxGroupField extends RepeaterField {

    /**
     * @var MetaboxField
     */
    public $field;

    public $loop;


    /**
     * @var int
     */
    protected $currentIndex = 0;

    public function decrementCurrentIndexByOne() {
        $this->currentIndex--;
    }

    public function __construct($field)
    {
        $this->field = $field;
        $this->loop = \Breakdance\DynamicData\LoopController::getInstance($field['id']);
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
    public function slug()
    {
        $isSettingsPage = $this->field['is_settings_page'] ?? false;
        if ($isSettingsPage) {
            return 'metabox_group_' . $this->field['settings_page'] . '_' . $this->field['id'];
        }
        return 'metabox_group_' . $this->field['id'];
    }

    /**
     * @inheritDoc
     */
    public function hasSubFields($postId = null)
    {
        $subFields = rwmb_get_value($this->field['id'], [], $postId);
        if ($this->field['is_sub_field']) {
            $parentField = $this->parentField();
            if ($parentField) {
                $parentSubFields = $parentField->loop->get();
                if (empty($parentSubFields)) {
                    // attempt to initiate the parent field's loop and try again
                    $parentField->hasSubFields(null);
                    $parentSubFields = $parentField->loop->get();
                }
                $subFields = $parentSubFields[$this->field['id']] ?? [];
            }
        }
        $currentField = $subFields[$this->currentIndex] ?? false;
        if (!$currentField) {
            $this->loop->reset();
            $this->currentIndex = 0;
            return false;
        }
        $this->loop->set($subFields[$this->currentIndex]);
        $this->currentIndex += 1;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function setSubFieldIndex($index, $postId = null) {
        if ($this->field['is_sub_field']) {
            $parentField = $this->parentField();
            if ($parentField) {
                $parentSubFields = $parentField->loop->get();
                $fieldValue = $parentSubFields[$this->field['id']] ?? [];
                $this->loop->set($fieldValue[$this->currentIndex]);
            }
        } else {
            $fieldValue = rwmb_get_value($this->field['root_id'] ?? $this->field['id'], [], $postId);
            $this->loop->set($fieldValue[$this->currentIndex]);
        }
    }

    /**
     * @return Field|RepeaterField|false
     */
    public function parentField()
    {
        return DynamicDataController::getInstance()->getField('metabox_group_' . $this->field['group_id']);
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): RepeaterData
    {
        $subFields = rwmb_get_value($this->field['id']);
        if (!is_array($subFields)) {
            $subFields = [];
        }
        return RepeaterData::fromArray($subFields);
    }
}
