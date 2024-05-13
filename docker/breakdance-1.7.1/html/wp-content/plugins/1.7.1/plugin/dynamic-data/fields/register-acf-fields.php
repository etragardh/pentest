<?php

namespace Breakdance\DynamicData;

if ( !class_exists( '\acf' ) && !function_exists( 'acf_get_field_groups' ) ) {
    return; // Bail out if ACF is not installed.
}
$defaultFields = [AcfField::class];
$fieldsMap = [
    'link' => [AcfLinkField::class],
    'image' => [AcfImageField::class, AcfImageUrlField::class],
    'url' => [AcfField::class, AcfImageField::class, AcfOembedField::class],
    'email' => [AcfField::class, AcfEmailField::class],
    'oembed' => [AcfOembedField::class],
    'gallery' => [AcfGalleryField::class, AcfGalleryImageField::class],
    'google_map' => [AcfGoogleMapsField::class],
    'user' => [AcfUserField::class],
    'taxonomy' => [AcfTaxonomyField::class],
    'checkbox' => [AcfCheckboxField::class],
    'radio' => [AcfCheckboxField::class],
    'button_group' => [AcfCheckboxField::class],
    'select' => [AcfCheckboxField::class],
    'post_object' => [AcfPostField::class, AcfPostFeaturedImage::class],
    'repeater' => [AcfRepeaterField::class],
    'file' => [AcfFileField::class, AcfFileImageField::class, AcfFileOembedField::class],
    'page_link' => [AcfPageLinkField::class, AcfPageLinkPostField::class],
    'group' => [AcfGroupField::class],
];

$excludedFieldTypes = [
    'message',
    'tab',
    'accordion',
    'flexible_content',
    'clone',
];

$fields = \Breakdance\DynamicData\get_acf_fields(null, $excludedFieldTypes);

foreach($fields as $field) {
    $dynamicFields = $fieldsMap[$field['type']] ?? $defaultFields;
    foreach ($dynamicFields as $dynamicField) {
        if (class_exists($dynamicField)) {
            DynamicDataController::getInstance()->registerField(
                new $dynamicField($field)
            );
        }
    }
}

