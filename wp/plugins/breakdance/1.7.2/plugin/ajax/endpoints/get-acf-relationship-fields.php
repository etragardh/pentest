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
        'breakdance_get_acf_relationship_fields',
        'Breakdance\AjaxEndpoints\getAcfRelationshipFields',
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
function getAcfRelationshipFields($requestData)
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
    $acfRelationshipFields = array_filter($fields, static function ($field) use ($postType) {
        return $field instanceof AcfField && $field->field['type'] === 'relationship' && $field->availableForPostType($postType);
    });

    return array_map(static function ($field) {
        /** @var AcfField $field */
        return [
            'value' =>  $field->field['name'],
            'text' => $field->label(),
        ];
    }, array_values($acfRelationshipFields));
}
