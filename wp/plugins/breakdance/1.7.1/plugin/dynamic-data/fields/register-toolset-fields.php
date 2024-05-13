<?php

namespace Breakdance\DynamicData;

if (!function_exists('wpcf_admin_fields_get_groups')) {
    return; // Bail out if Toolset is not installed.
}
$defaultFields = [ToolsetField::class];
$fieldsMap = [
    'image' => [ToolsetImageField::class, ToolsetImageUrlField::class],
    'post' => [ToolsetPostField::class],
    'url' => [ToolsetLinkField::class, ToolsetUrlField::class, ToolsetUrlImageField::class],
    'video' => [ToolsetVideoField::class],
    'embed' => [ToolsetOembedField::class],
    'repeater' => [ToolsetRepeaterField::class]
];

$toolset_fields = \Breakdance\DynamicData\get_toolset_fields(null,['audio']);

foreach($toolset_fields as $field) {
    $dynamicFields = $fieldsMap[$field['type']] ?? $defaultFields;
    foreach ($dynamicFields as $dynamicField) {
        if (class_exists($dynamicField)) {
            DynamicDataController::getInstance()->registerField(
                new $dynamicField($field)
            );
        }
    }
}
