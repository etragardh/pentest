<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\LoginForm",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class LoginForm extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-right-to-bracket" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M384 256c0-8.188-3.125-16.38-9.375-22.62l-128-128C237.5 96.22 223.7 93.47 211.8 98.44C199.8 103.4 192 115.1 192 128v64H48C21.49 192 0 213.5 0 240v32C0 298.5 21.49 320 48 320H192v64c0 12.94 7.797 24.62 19.75 29.56c11.97 4.969 25.72 2.219 34.88-6.938l128-128C380.9 272.4 384 264.2 384 256zM224 384V288H48C39.18 288 32 280.8 32 272v-32C32 231.2 39.18 224 48 224H224L223.1 128l128 128L224 384zM432 32h-96C327.2 32 320 39.16 320 48S327.2 64 336 64h96C458.5 64 480 85.53 480 112v288c0 26.47-21.53 48-48 48h-96c-8.844 0-16 7.156-16 16s7.156 16 16 16h96c44.13 0 80-35.88 80-80v-288C512 67.88 476.1 32 432 32z"></path></svg>';
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
        return 'Login Form';
    }

    static function className()
    {
        return 'bde-login-form';
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
        return ['content' => ['form' => ['labels' => ['username_label' => 'Username or Email Address', 'password_label' => 'Password', 'remember_me' => 'Remember me', 'lost_password' => 'Lost your password?'], 'lost_password' => true, 'remember_me' => true, 'success_message' => 'You are now logged in.', 'submit_text' => 'Log In']], 'design' => ['form' => ['theme' => 'default']]];
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
      ), c(
        "password_label",
        "Password Label",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "password_placeholder",
        "Password Placeholder",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "remember_me",
        "Remember Me",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "lost_password",
        "Lost Password",
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
        "lost_password",
        "Lost Password",
        [],
        ['type' => 'toggle'],
        false,
        false,
        [],
      ), c(
        "custom_lost_password_page",
        "Custom Lost Password Page",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => 'content.form.lost_password', 'operand' => 'is set', 'value' => true]],
        false,
        false,
        [],
      ), c(
        "remember_me",
        "Remember Me",
        [],
        ['type' => 'toggle'],
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
        "Redirect After Login",
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
        return ['0' =>  ['styles' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/css/form.css'],'title' => 'Styles',],'1' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/js/form.js'],'inlineScripts' => ['breakdanceForm.init(\'%%SELECTOR%% .breakdance-form\')'],'builderCondition' => 'return false;','title' => 'Frontend init',],];
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
        return ['0' => ['accepts' => 'string', 'path' => 'content.form.submit_text'], '1' => ['accepts' => 'string', 'path' => 'content.form.success_message']];
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
        return ['content.form.labels', 'content.form.custom_lost_password_page', 'content.form.lost_password', 'content.form.remember_me', 'content.form.submit_text'];
    }
}
