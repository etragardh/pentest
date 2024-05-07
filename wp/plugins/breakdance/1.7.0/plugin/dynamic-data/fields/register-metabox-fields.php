<?php

namespace Breakdance\DynamicData;

if ( !function_exists( 'rwmb_get_registry' ) ) {
    return; // Bail out if Metabox is not installed.
}

function getFieldsForType($type) {
    $defaultFields = [MetaboxField::class];
    $fieldsMap = [
        'checkbox_list' => [MetaboxCheckboxField::class],
        'url' => [MetaboxField::class, MetaboxImageField::class, MetaboxOembedField::class],
        'single_image' => [MetaboxImageField::class, MetaboxImageUrlField::class],
        'image' => [MetaboxImageField::class, MetaboxGalleryField::class, MetaboxImageUrlField::class],
        'image_advanced' => [MetaboxImageField::class, MetaboxGalleryField::class, MetaboxImageUrlField::class],
        'video' => [MetaboxOembedField::class],
        'map' => [MetaboxMapField::class],
        'post' => [MetaboxPostField::class],
        'oembed' => [MetaboxOembedField::class],
        'taxonomy' => [MetaboxTaxonomyField::class],
        'taxonomy_advanced' => [MetaboxTaxonomyField::class],
        'user' => [MetaboxUserField::class],
        'group' => [MetaboxGroupField::class],
        'file' => [MetaboxFileField::class, MetaboxVideoFileField::class, MetaboxImageFileField::class],
        'file_input' => [MetaboxFileField::class, MetaboxVideoFileField::class, MetaboxImageFileField::class],
    ];
    return $fieldsMap[$type] ?? $defaultFields;
}

$excludedFieldTypes = [
    'fieldset_text',
    'file_advanced',
    'file_upload',
];

$fields = \Breakdance\DynamicData\get_metabox_fields(null, $excludedFieldTypes);
registerMetaboxFields($fields);

function registerMetaboxFields($fields, $parentField = null) {
    foreach($fields as $field) {
        if ($parentField) {
            $field['group'] = $parentField['name'];
            $field['group_id'] = $parentField['id'];
            $field['root_id'] = $parentField['root_id'] ?? $parentField['id'];
            $field['is_sub_field'] = true;
        }
        $fieldType = $field['type'] ?? 'text';
        $dynamicFields = getFieldsForType($fieldType);
        foreach ($dynamicFields as $dynamicField) {
            if (class_exists($dynamicField)) {
                DynamicDataController::getInstance()->registerField(
                    new $dynamicField($field)
                );
            }
        }
        if ($fieldType === 'group') {
            registerMetaboxFields($field['fields'], $field);
        }
    }
}

