<?php

namespace Breakdance\Subscription;

use function Breakdance\Conditions\getSettingsConditions;
use function Breakdance\Render\getElementFromNodeType;

// Make it the last to run
define('BREAKDANCE__PRO_ONLY_FILTERS_PRIORITY', 100000);
define('BREAKDANCE__PRO_ONLY_ACTIONS_PRIORITY', 100000);

add_action('breakdance_loaded', function () {
    if (isFreeMode()) {
        add_filter('breakdance_global_selectors_css', '\Breakdance\Subscription\ignoreSelectors');

        add_filter('breakdance_add_csv_export_url_args', '\Breakdance\Subscription\ignoreCsvExport');

        add_filter('breakdance_render_element_html', '\Breakdance\Subscription\logElementConditionsProOnlyNotice', BREAKDANCE__PRO_ONLY_FILTERS_PRIORITY, 2);

        add_filter('breakdance_render_rendered_html', '\Breakdance\Subscription\logTemplateConditionsProOnlyNotice', BREAKDANCE__PRO_ONLY_FILTERS_PRIORITY, 2);

        add_filter('breakdance_themeless_request_does_rule_apply', '\Breakdance\Subscription\makeAllProOnlyConditionRulesPass', BREAKDANCE__PRO_ONLY_FILTERS_PRIORITY, 3);

        add_action('breakdance_render_element_template', '\Breakdance\Subscription\logNoticeForProElementInFreeMode', BREAKDANCE__PRO_ONLY_FILTERS_PRIORITY, 1);

        add_filter('breakdance_render_element_class_list', '\Breakdance\Subscription\addFreeModeClassToProOnlyElement', BREAKDANCE__PRO_ONLY_FILTERS_PRIORITY, 2);

        add_filter('breakdance_render_form_html', '\Breakdance\Subscription\logFormsProOnlyNotice', BREAKDANCE__PRO_ONLY_FILTERS_PRIORITY, 3);
    }
});

/**
 * @return string
 */
function ignoreSelectors() {
    return '';
}

/**
 * @return string[]
 */
function ignoreCsvExport() {
    return ['breakdance_form_submissions_upgrade_pro' => 'true'];
}

/**
 * @param string $elementHtml
 * @param array $node
 * @return string
 * @throws \Exception
 */
function logElementConditionsProOnlyNotice($elementHtml, $node){
    if (conditionsContainProOnly(getSettingsConditions($node))){
        /** @var string $type */
        $type = $node['data']['type'] ?? '';
        $element = getElementFromNodeType($type);

        logNoticeBecauseProOnlyFeatureWasUsed(
            getProOnlyConditionMessage($element::name() ?: '')
        );
    }

    return $elementHtml;
}

/**
 * @param string $html
 * @param int $postId
 * @return string
 */
function logTemplateConditionsProOnlyNotice($html, $postId){
    $postType = get_post_type($postId);

    /** @var string[] $allBdTemplatePostTypes */
    $allBdTemplatePostTypes = BREAKDANCE_ALL_TEMPLATE_POST_TYPES;
    if (in_array($postType, $allBdTemplatePostTypes)){
        $postTypeObj = get_post_type_object($postType ?: '');

        /**
         * @var string
         */
        $postTypeLabel = $postTypeObj ? $postTypeObj->labels->singular_name : '';

        logNoticeBecauseProOnlyFeatureWasUsed(
            getProOnlyTemplateConditionMessageIfConditionsContainProOnly($postId, $postTypeLabel)
        );
    }

    return $html;
}

/**
 * @param bool $doesRulePass
 * @param TemplateCondition $condition
 * @param TemplateRule $rule
 * @return bool
 *
 * By making them all pass we inhibit their functionality
 */
function makeAllProOnlyConditionRulesPass($doesRulePass, $condition, $rule){
    if ($condition['proOnly'] ?? false){
        return true;
    }

    return $doesRulePass;
}

/**
 * @param \Breakdance\Elements\Element $element
 */
function logNoticeForProElementInFreeMode($element){

    if (\Breakdance\Themeless\Fallbacks\FallbackDefaultsRenderingStatusHolder::getInstance()->isFallbackDefaultTemplateCurrentlyRendering) {
        return;
    }

    if (($element::settings()['proOnly'] ?? false) && freeModeOnFrontend()){
        $elementName = $element::name();
        $warning = "
          <div class='breakdance-pro-only-element-notice'>
              <span>
                  The <b>$elementName</b> element is only available in Breakdance Pro.
              </span>
          </div>
        ";

        logNoticeBecauseProOnlyFeatureWasUsed($warning);
    }

}

/**
 * @param string[] $classList
 * @param \Breakdance\Elements\Element $element
 * @return string[]
 */
function addFreeModeClassToProOnlyElement($classList, $element) {

    if (\Breakdance\Themeless\Fallbacks\FallbackDefaultsRenderingStatusHolder::getInstance()->isFallbackDefaultTemplateCurrentlyRendering) {
        return $classList;
    }

    if (!($element::settings()['proOnly'] ?? false) || \Breakdance\Permissions\hasMinimumPermission('edit') || !freeModeOnFrontend()) {
        return $classList;
    }

    $classList[] = 'breakdance-free-version-frontend-element-hider';

    return $classList;
}


/**
 * @param string $html
 * @param array $props
 * @param array[] $fields
 * @return string
 */
function logFormsProOnlyNotice($html, $props, $fields){
    $proFeaturesUsed = proOnlyFeaturesTheFormIsUsing($props, $fields);

    if ($proFeaturesUsed){
        logNoticeBecauseProOnlyFeatureWasUsed(
            getProOnlyFormFeaturesMessage(
                $proFeaturesUsed
            )
        );
    }

    return $html;
}
