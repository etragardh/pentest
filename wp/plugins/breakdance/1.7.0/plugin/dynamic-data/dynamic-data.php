<?php

namespace Breakdance\DynamicData;

use Breakdance\Themeless\ThemelessController;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Themeless\getTemplateById;

class DynamicDataController
{

    use \Breakdance\Singleton;

    /** @var Field[] */
    public $fields = [];

    /**
     * Order fields by categories
     * @var string[]
     */
    public $order = [
        'Post',
        'ACF',
        'Metabox',
        'Toolset',
        'Featured Image',
        'Archive',
        'Site Info',
        'Author',
        'Current User',
        'WooCommerce',
        'URL & Query',
        'Utility',
    ];

    /**
     * @param Field $field
     * @return void
     */
    public function registerField(Field $field)
    {
        $this->fields[] = $field;
    }

    /**
     * @param string? $postType
     * @return DynamicField[]
     */
    public function getFieldsForPostType($postType = null)
    {
        $fieldsForPost = $this->fields;

        if ($postType) {
            $fieldsForPost = array_filter($this->fields, static function (Field $field) use ($postType) {
                return $field->availableForPostType($postType);
            });
        }

        return array_map(static function (Field $field) {
            /** @var DynamicField $dynamicField */
            $dynamicField = [
                'category' => $field->category(),
                'subcategory' => $field->subcategory(),
                'label' => $field->label(),
                'slug' => $field->slug(),
                'returnTypes' => $field->returnTypes(),
                'defaultAttributes' => (object) $field->defaultAttributes(),
                'controls' => $field->getControls(),
                'proOnly' => $field->proOnly()
            ];
            return $dynamicField;
        }, array_values($fieldsForPost));
    }

    /**
     * @param string $type
     * @return Field[]
     */
    public function getFieldsByReturnType($type)
    {
        return array_filter($this->fields, function($field) use ($type) {
            return in_array($type, $field->returnTypes(), true);
        });
    }

    /**
     *
     * @param string $fieldSlug
     * @return Field|false
     */
    public function getField($fieldSlug)
    {
        $foundFields = array_filter(
            $this->fields,
            function ($field) use ($fieldSlug) {
                return $field->slug() === $fieldSlug;
            }
        );

        $foundField = array_pop($foundFields);

        return $foundField ?: false;
    }

    /**
     * Sort comparison function
     * @param Field $a
     * @param Field $b
     * @return int
     */
    public function sort($a, $b)
    {
        /** @var int $orderA */
        $orderA = array_search($a->category(), $this->order) ?: 1;
        /** @var int $orderB */
        $orderB = array_search($b->category(), $this->order) ?: 0;
        return $orderA - $orderB;
    }

    public function reorderFields()
    {
        usort($this->fields, array($this, 'sort'));
    }
}


function include_fields()
{
    $filenames = array_merge(
        glob(dirname(__FILE__) . "/fields/*/*.php"),
        glob(dirname(__FILE__) . "/fields/*.php"),
    );

    foreach ($filenames as $filename) {
        if (file_exists($filename)) {
            require_once $filename;
        }
    }

    DynamicDataController::getInstance()->reorderFields();
}

function registerField(Field $field) {
    DynamicDataController::getInstance()->registerField($field);
}

add_action('breakdance_loaded', function () {
    add_action('wp_loaded', '\Breakdance\DynamicData\include_fields');
});

