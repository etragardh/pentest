<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\FacebookComments",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class FacebookComments extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-comments" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M416 176C416 78.8 322.9 0 208 0S0 78.8 0 176c0 41.48 17.07 79.54 45.44 109.6c-15.17 32.34-38.65 58.07-38.95 58.38c-6.514 6.836-8.309 16.91-4.568 25.67C5.754 378.4 14.26 384 23.66 384c54.19 0 97.76-20.73 125.9-39.17C168.1 349.4 187.7 352 208 352C322.9 352 416 273.2 416 176zM208 320c-16.96 0-34.04-2.098-50.75-6.232L143.7 310.4L132 318.1c-20.43 13.38-51.58 28.99-89.85 32.97c9.377-12.11 22.3-30.63 32.24-51.82l9.242-19.71L68.72 263.7C44.7 238.2 32 207.9 32 176C32 96.6 110.1 32 208 32S384 96.6 384 176S305 320 208 320zM606.4 435.4C627.6 407.1 640 372.9 640 336C640 238.8 554 160 448 160c-.3145 0-.6191 .041-.9336 .043C447.5 165.3 448 170.6 448 176c0 5.43-.4668 10.76-.9414 16.09C447.4 192.1 447.7 192 448 192c88.22 0 160 64.6 160 144c0 28.69-9.424 56.45-27.25 80.26l-13.08 17.47l11.49 18.55c6.568 10.61 13.18 19.74 18.61 26.74c-18.26-1.91-36.45-6.625-54.3-14.09l-12.69-5.305l-12.58 5.557C495.9 475 472.3 480 448 480c-75.05 0-137.7-46.91-154.9-109.7c-10.1 3.336-20.5 6.132-31.2 8.271C282.7 455.1 357.1 512 448 512c29.82 0 57.94-6.414 83.12-17.54C555 504.5 583.7 512 616.3 512c9.398 0 17.91-5.57 21.73-14.32c3.74-8.758 1.945-18.84-4.568-25.67C633.3 471.8 619.6 456.8 606.4 435.4z"></path></svg>';
    }

    static function tag()
    {
        return 'div';
    }

    static function tagOptions()
    {
        return [];
    }

    static function tagControlPath()
    {
        return false;
    }

    static function name()
    {
        return 'Facebook Comments';
    }

    static function className()
    {
        return 'bde-facebook-comments';
    }

    static function category()
    {
        return 'blocks';
    }

    static function badge()
    {
        return false;
    }

    static function slug()
    {
        return get_class();
    }

    static function template()
    {
        return file_get_contents(__DIR__ . '/html.twig');
    }

    static function defaultCss()
    {
        return file_get_contents(__DIR__ . '/default.css');
    }

    static function defaultProperties()
    {
        return ['design' => ['wrapper' => ['width' => ['number' => 600, 'unit' => 'px', 'style' => '600px']], 'size' => ['width' => ['breakpoint_base' => ['number' => 600, 'unit' => 'px', 'style' => '600px']]]], 'content' => ['comments' => ['comment_on' => 'page', 'count' => 5, 'sort_by' => 'reverse_time']]];
    }

    static function defaultChildren()
    {
        return false;
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [c(
        "size",
        "Size",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 220, 'max' => 1200]],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Spacing",
      "spacing",
       ['type' => 'popout']
     )];
    }

    static function contentControls()
    {
        return [c(
        "comments",
        "Comments",
        [c(
        "facebook_app",
        "Facebook App",
        [],
        ['type' => 'alert_box', 'layout' => 'vertical', 'alertBoxOptions' => ['style' => 'default', 'content' => '<p>You can set your Facebook App ID in the <a target="_blank" rel="noopener noreferrer nofollow" href="/wp-admin/admin.php?page=breakdance_settings&amp;tab=api_keys">API Key Settings</a></p>']],
        false,
        false,
        [],
      ), c(
        "count",
        "Count",
        [],
        ['type' => 'number', 'layout' => 'inline', 'items' => ['0' => ['value' => 'standard', 'text' => 'Standard'], '1' => ['text' => 'Box Count', 'value' => 'box_count'], '2' => ['text' => 'Button Count', 'value' => 'button_count'], '3' => ['text' => 'Button', 'value' => 'button']], 'rangeOptions' => ['step' => 1, 'min' => 5, 'max' => 100]],
        false,
        false,
        [],
      ), c(
        "sort_by",
        "Sort By",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'reverse_time', 'text' => 'Newest'], '1' => ['text' => 'Oldest', 'value' => 'time']]],
        false,
        false,
        [],
      ), c(
        "comment_on",
        "Comment on",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'page', 'text' => 'Current Page'], '1' => ['text' => 'Custom URL', 'value' => 'custom_url']]],
        false,
        false,
        [],
      ), c(
        "custom_url",
        "Custom URL",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => 'content.comments.comment_on', 'operand' => 'equals', 'value' => 'custom_url']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
      )];
    }

    static function settingsControls()
    {
        return [];
    }

    static function dependencies()
    {
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/elements-reusable-code/facebook-elements.js'],'title' => 'Breakdance facebook elements',],'1' =>  ['inlineScripts' => [' new BreakdanceFacebookSDK(\'%%SELECTOR%%\'); '],'builderCondition' => 'return false;','frontendCondition' => 'return true;','title' => 'Init frontend',],];
    }

    static function settings()
    {
        return ['bypassPointerEvents' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

'onPropertyChange' => [['script' => 'if (window.breakdanceFacebookInstances && window.breakdanceFacebookInstances[%%ID%%]) {
                    window.breakdanceFacebookInstances[%%ID%%].update();
                    }',
],],

'onMountedElement' => [['script' => 'if (!window.breakdanceFacebookInstances) window.breakdanceFacebookInstances = {};

                    if (window.breakdanceFacebookInstances && window.breakdanceFacebookInstances[%%ID%%]) {
                    window.breakdanceFacebookInstances[%%ID%%].destroy();
                    }
                        window.breakdanceFacebookInstances[%%ID%%] = new BreakdanceFacebookSDK(\'%%SELECTOR%%\');
                    ',
],],

'onMovedElement' => [['script' => 'if (window.breakdanceFacebookInstances && window.breakdanceFacebookInstances[%%ID%%]) {
                    window.breakdanceFacebookInstances[%%ID%%].update();
                    }
                    ',
],],

'onBeforeDeletingElement' => [['script' => 'if (window.breakdanceFacebookInstances && window.breakdanceFacebookInstances[%%ID%%]) {
                    window.breakdanceFacebookInstances[%%ID%%].destroy();
                    delete window.breakdanceFacebookInstances[%%ID%%];
                    }
                    ',
],],];
    }

    static function nestingRule()
    {
        return ["type" => "final",   ];
    }

    static function spacingBars()
    {
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%']];
    }

    static function attributes()
    {
        return false;
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 15000;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'url', 'path' => 'content.comments.custom_url']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return false;
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['content.comments'];
    }
}
