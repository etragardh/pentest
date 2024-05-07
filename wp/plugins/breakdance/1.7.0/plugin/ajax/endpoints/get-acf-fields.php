<?php
namespace Breakdance\AjaxEndpoints;

use Breakdance\DynamicData\AcfField;
use Breakdance\DynamicData\DynamicDataController;
use Breakdance\Themeless\ThemelessController;
use function Breakdance\Themeless\getTemplateById;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_acf_fields',
        'Breakdance\AjaxEndpoints\getAcfFields',
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
 * @return array{value: string, text: string}[]
 */
function getAcfFields($requestData)
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
    $acfFields = array_filter($fields, static function ($field) use ($postType) {
        return $field instanceof AcfField;
    });

    return array_map(static function ($field) {
        /** @var AcfField $field */
        return [
            'value' =>  $field->field['name'],
            'text' => $field->label() . ' (' .  $field->field['group'] . ')',
        ];
    }, array_values($acfFields));
}
