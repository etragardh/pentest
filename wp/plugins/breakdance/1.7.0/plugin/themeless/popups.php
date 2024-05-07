<?php

namespace Breakdance\Themeless;

use Breakdance\Render\ScriptAndStyleHolder;
use function Breakdance\isRequestFromBuilderIframe;

/**
 * @param int $popupPostId
 * @param bool $removeTriggers
 * @return void
 */
function add_popup_init_script_to_page($popupPostId, $removeTriggers = false) {
    $settingsJson = (string) \Breakdance\Data\get_meta($popupPostId, 'breakdance_template_settings');
    /** @var TemplateSettings $settings */
    $settings = json_decode($settingsJson, true);
    if (empty($settings)) {
        return;
    }
    if ($removeTriggers) {
        $settings['triggers'] = [];
    }
    $triggerOptions = get_popup_options_as_json($settings);
    ScriptAndStyleHolder::getInstance()->append([
        'inlineScripts' => ['new BreakdancePopup('.$popupPostId.', ' . $triggerOptions . ');'],
    ]);
}

/**
 * @return string|false
 */
function get_breakdance_popups_for_request() {
    if (isRequestFromBuilderIframe()) {
        return "";
    }

    $popupHtml = implode('', array_map(static function($popup) {
        if (!array_key_exists('id', $popup)) {
            return '';
        }
        PopupController::getInstance()->markAsRendered($popup['id']);
        $popupDocumentTree = \Breakdance\Data\get_tree($popup['id']);
        if ($popupDocumentTree !== false && !empty($popupDocumentTree['root']['children'])) {
            add_popup_init_script_to_page($popup['id']);
        }
        return renderPopup($popup['id']);
    }, _getAllTemplatesForRequest(ThemelessController::getInstance()->popups)));

    // Do this in a loop in case the popups contain
    // references to other unregistered popups
    while($popupsToRender = PopupController::getInstance()->getPopupsThatHaveNotAlreadyBeenRendered()) {
        $popupHtml .= implode('', array_map(static function($popupId) {
            PopupController::getInstance()->markAsRendered($popupId);
            $popupDocumentTree = \Breakdance\Data\get_tree($popupId);
            if ($popupDocumentTree !== false && !empty($popupDocumentTree['root']['children'])) {
                add_popup_init_script_to_page($popupId, true);
            }
            return renderPopup($popupId);
        }, $popupsToRender));
    }

    if (!empty($popupHtml)) {
        return $popupHtml;
    }

    return false;
}


/**
 * @param int $popupPostId
 * @return string
 */
function renderPopup($popupPostId) {
    $renderedPost = \Breakdance\Render\getRenderedPost($popupPostId);
    if ($renderedPost['error']) {
        return $renderedPost['message'];
    }

    $renderedNodes = $renderedPost['renderedNodes'];

    if (!$renderedNodes) {
        return '';
    }

    ScriptAndStyleHolder::getInstance()->append($renderedNodes['dependencies']);
    return '<div class="breakdance">' . $renderedNodes['html'] . '</div>';
}

/**
 * @param TemplateSettings $settings
 * @return string
 */
function get_popup_options_as_json($settings) {

    if (!$settings || !array_key_exists('triggers', $settings)) {
        return '{}';
    }

    if (array_key_exists('ruleGroups', $settings)) {
        $appliedRuleGroups = array_filter($settings['ruleGroups'], static function($ruleGroup) {
            return \Breakdance\Themeless\doesRuleGroupApply($ruleGroup);
        });
        $breakpointConditions = get_breakpoint_conditions($appliedRuleGroups);
    }

    /** @var PopupOptions $popupOptions */
    $popupOptions = [
        'onlyShowOnce' => $settings['onlyShowOnce'] ?? false,
        'avoidMultiple' => $settings['avoidMultiple'] ?? false,
        'limitSession' => $settings['limitSession'] ?? null,
        'limitPageLoad' => $settings['limitPageLoad'] ?? null,
        'limitForever' => $settings['limitForever'] ?? null,
        'triggers' => $settings['triggers'] ?? [],
        'breakpointConditions' => $breakpointConditions ?? [],
    ];

    $popupOptionsAsJson = json_encode($popupOptions);
    if ($popupOptionsAsJson === false) {
        return '{}';
    }
    return $popupOptionsAsJson;
}

/**
 * @param TemplateRuleGroup[] $appliedRuleGroups
 * @return array{ operand: string, breakpoints: string[] }[]
 */
function get_breakpoint_conditions($appliedRuleGroups)
{
    // Extract breakpoint rules from the applied condition groups
    $breakpointConditions = array_filter(array_map(static function($ruleGroup) {
        /**
         * For some reason psalm things that array_search doesn't like string[]
         * @psalm-suppress InvalidScalarArgument
         */
        $breakpointConditionIndex = array_search('breakpoints', array_column($ruleGroup, 'ruleSlug'), true);
        if ($breakpointConditionIndex === false) {
            return false;
        }
        $breakpointCondition = $ruleGroup[$breakpointConditionIndex] ?? null;
        if ($breakpointCondition === null || !array_key_exists('value', $breakpointCondition) || !is_array($breakpointCondition['value'])) {
            return false;
        }

        /** @var string[] $breakpoints */
        $breakpoints = array_column($breakpointCondition['value'], 'value');
        return [
            'operand' => $breakpointCondition['operand'],
            'breakpoints' => $breakpoints,
        ];
    }, $appliedRuleGroups));

    if (empty($breakpointConditions)) {
        return [];
    }

    return $breakpointConditions;
}

add_action("wp_footer", "\Breakdance\Themeless\add_popups_to_footer");
/**
 * @param string $footerName
 * @return void
 */
function add_popups_to_footer() {

    $popups = get_breakdance_popups_for_request();

    if ($popups !== false) {
        echo $popups;
    }

}

/**
 * @return PopupTriggers[]
 */
function getPopupTriggers()
{
    return [
        [
            'text' => "Click",
            'value' => "click"
        ],
        [
            'text' => "Page Load",
            'value' => "load",
            'proOnly' => true
        ],
        [
            'text' => "Page Scroll",
            'value' => "scroll",
            'proOnly' => true
        ],
        [
            'text' => "Page Scroll Up",
            'value' => "scroll_up",
            'proOnly' => true
        ],
        [
            'text' => "User Inactivity",
            'value' => "inactivity",
            'proOnly' => true
        ],
        [
            'text' => "Mouse Moves to Exit",
            'value' => "exit_intent",
            'proOnly' => true
        ]
    ];
}
