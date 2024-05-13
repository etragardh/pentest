<?php
namespace Breakdance\AjaxEndpoints;

use Breakdance\DynamicData\AcfField;
use Breakdance\DynamicData\DynamicDataController;
use Breakdance\Themeless\ThemelessController;
use function Breakdance\DynamicData\get_dynamic_data_post_type;
use function Breakdance\Themeless\getTemplateById;
use function Breakdance\Themeless\getTemplateByIdIfItExistsAndHasSettings;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_acf_breakdance_content_fields',
        'Breakdance\AjaxEndpoints\getAcfBreakdanceContentFields',
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
function getAcfBreakdanceContentFields($requestData)
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
    $acfBreakdanceContentFields = array_filter($fields, static function ($field) use ($postType) {
        return $field instanceof AcfField && $field->field['type'] === 'breakdance_content' && $field->availableForPostType($postType);
    });

    return array_map(static function ($field) {
        return [
            'value' =>  $field->slug(),
            'text' => $field->label(),
        ];
    }, array_values($acfBreakdanceContentFields));
}
