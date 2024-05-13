<?php

namespace Breakdance\DynamicData;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

abstract class Field
{

    /**
     * @return array|string[]
     */
    public function defaultAttributes()
    {
        return [];
    }

    /**
     * @return Control[]
     */
    public function controls()
    {
        return [];
    }

    /**
     * @return Control | null
     */
    public function fallbackControl()
    {
        return null;
    }

    /**
     * @return Control[]
     */
    public function getControls()
    {
        $advancedControls = [];
        /** @var Control[] $mergedControls */
        $mergedControls = [];

        if (!empty($this->fallbackControl())) {
            $advancedControls[] = $this->fallbackControl();
        }

        $advancedControls = array_merge($advancedControls, getAdvancedControlsForPipe());

        // If there's already an 'advanced' control section
        // on this field, merge with the default controls
        $advancedSectionIndex = array_search('advanced', array_column($this->controls(), 'slug'), true);
        if ($advancedSectionIndex !== false) {
            $mergedControls[$advancedSectionIndex]['children'] = array_merge($this->controls()[$advancedSectionIndex]['children'], $advancedControls);
        } else {
            $mergedControls[] = controlSection('advanced', 'Advanced', $advancedControls, null, 'popout');
        }

        return array_merge($this->controls(), $mergedControls);
    }

    /**
     * @return string
     */
    public function subcategory()
    {
        return '';
    }

    /**
     * @param string $postType
     * @return bool
     */
    public function availableForPostType($postType)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function proOnly()
    {
        return true;
    }

    /**
     * @return string
     */
    abstract public function label();

    /**
     * @return string
     */
    abstract public function category();

    /**
     * @return string
     */
    abstract public function slug();

    /**
     * @return DynamicFieldReturnType[]
     */
    abstract public function returnTypes();

    /**
     * @param mixed $attributes
     */
    abstract public function handler($attributes): FieldData;


}
