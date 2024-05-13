<?php

namespace Breakdance\DynamicData;

abstract class StringField extends Field
{

    /**
     * @inheritDoc
     */
    abstract public function handler($attributes): StringData;

    public function fallbackControl()
    {
        return \Breakdance\Elements\control('fallback', 'Fallback', [
            'type' => 'text',
            'layout' => 'vertical',
            'textOptions' => ['multiline' => false]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getControls()
    {
        /** @var Control[] $mergedControls */
        $mergedControls = [];
        $advancedControls = [
            \Breakdance\Elements\control('beforeContent', 'Prepend', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('afterContent', 'Append', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
            \Breakdance\Elements\control('truncate', 'Limit Characters', [
                'type' => 'number',
                'layout' => 'vertical',
            ]),
        ];

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
            $mergedControls[] = \Breakdance\Elements\controlSection('advanced', 'Advanced', $advancedControls, null, 'popout');
        }

        return array_merge($this->controls(), $mergedControls);
    }

    /**
     * @return string[]
     */
    public function returnTypes()
    {
        return ['string'];
    }
}
