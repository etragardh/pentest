<?php
namespace Breakdance\AjaxEndpoints;

use Breakdance\DynamicData\DynamicDataController;
use Breakdance\DynamicData\MetaboxField;
use Breakdance\Themeless\ThemelessController;
use function Breakdance\Themeless\getTemplateById;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_metabox_fields',
        'Breakdance\AjaxEndpoints\getMetaBoxFields',
        'edit',
        true,
        [
            'args' => [
                'requestData' => [
                    'filter' => FILTER_DEFAULT,
                    'flags' => FILTER_REQUIRE_ARRAY
                ]
            ]
        ]
    );
});

/**
 * @param array{context: array{postId: int}} $requestData
 * @return DropdownData[]
 */
function getMetaBoxFields($requestData)
{
    $postId = filter_var($requestData['context']['postId'], FILTER_VALIDATE_INT);
    if ($postId === false) {
        return [];
    }
    $allTemplateTypes = ThemelessController::getInstance()->getAllTemplates();
    $template = getTemplateById($allTemplateTypes, $postId);

    if ($template) {
        $postType = $template['settings']['type'] ?? false;
    } else {
        $postType = get_post_type($postId);
    }

    if (!$postType) {
        return [];
    }

    $fields = DynamicDataController::getInstance()->fields;
    $metaboxFields = array_filter($fields, static function ($field) use ($postType) {
        return $field instanceof MetaboxField;
    });

    return array_map(static function ($field) {
        /** @var \Breakdance\DynamicData\MetaboxField $field */
        return [
            'value' =>  (string) $field->field['id'],
            'text' => (string) $field->label() . ' (' .  (string) $field->field['group'] . ')',
        ];
    }, array_values($metaboxFields));
}
