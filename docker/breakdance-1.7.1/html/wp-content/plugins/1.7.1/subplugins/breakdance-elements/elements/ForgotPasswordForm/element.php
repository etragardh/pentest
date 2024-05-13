<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\ForgotPasswordForm",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class ForgotPasswordForm extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-unlock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M384 224L128 224L127.1 131.3C127.1 82.55 162.1 39.02 211.4 32.81C269.9 25.3 320 70.91 320 128v16c0 8.837 7.163 15.1 16 15.1s16-7.163 16-15.1V132.5c.0001-64.31-45.47-122-109.1-131.1C163.9-9.981 96 51.21 96 128v96l-32 0c-35.35 0-64 28.65-64 64v160C0 483.3 28.65 512 64 512h320c35.35 0 64-28.65 64-63.1v-160C448 252.7 419.3 224 384 224zM416 448c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32v-160c0-17.64 14.36-32 32-32h320c17.64 0 32 14.36 32 32V448z"></path></svg>';
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
        return 'Forgot Password Form';
    }

    static function className()
    {
        return 'bde-forgot-password-form';
    }

    static function category()
    {
        return 'forms';
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
        return ['content' => ['form' => ['labels' => ['username_label' => 'Username or Email Address'], 'success_message' => 'Check your email for the confirmation link.', 'submit_text' => 'Get New Password']], 'design' => ['form' => ['theme' => 'default']]];
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
        return [getPresetSection(
      "EssentialElements\\form-container",
      "Container",
      "container",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\AtomV1FormDesign",
      "Form",
      "form",
       ['type' => 'popout']
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
        "form",
        "Form",
        [c(
        "labels",
        "Labels",
        [c(
        "username_label",
        "Username Label",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "username_placeholder",
        "Username Placeholder",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "submit_text",
        "Submit Text",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "success_message",
        "Success Message",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "redirect",
        "Redirect After Submit",
        [],
        ['type' => 'toggle'],
        false,
        false,
        [],
      ), c(
        "redirect_url",
        "Redirect URL",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => 'content.form.redirect', 'operand' => 'equals', 'value' => true]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'accordion']],
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
        return ['0' =>  ['styles' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/css/form.css'],'title' => 'Styles',],'1' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/js/form.js'],'inlineScripts' => ['breakdanceForm.init(\'%%SELECTOR%% .breakdance-form\')'],'builderCondition' => 'return false;','title' => 'Init frontend',],];
    }

    static function settings()
    {
        return false;
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return false;
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
        return 0;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.form.submit_text'], '1' => ['accepts' => 'string', 'path' => 'content.form.success_message'], '2' => ['accepts' => 'url', 'path' => 'content.form.redirect_url']];
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
        return ['design.form.fields.advanced.hide_labels', 'design.form.layout.vertical_at'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['content.form.labels'];
    }
}
