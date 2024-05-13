<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\FrequentlyAskedQuestions",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class FrequentlyAskedQuestions extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'SquareQuestionIcon';
    }

    static function tag()
    {
        return 'div';
    }

    static function tagOptions()
    {
        return ['div'];
    }

    static function tagControlPath()
    {
        return false;
    }

    static function name()
    {
        return 'Frequently Asked Questions';
    }

    static function className()
    {
        return 'bde-frequently-asked-questions';
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
        return ['content' => ['settings' => ['questions' => ['0' => ['question' => 'Is this a lifetime license?', 'answer' => '<p>Yes. You can use it on an unlimited number of sites. There are no limits.</p><ul><li><p>Lorem ipsum sit dolor amet</p></li><li><p>Lorem ipsum sit dolor amet</p></li></ul><p></p>'], '1' => ['question' => 'Where can in edit my address?', 'answer' => '<p>If you created a new account after or while ordering you can edit both addresses (for billing and shipping) in your&nbsp;<a href="#" rel="noopener noreferrer nofollow">customer account</a>.<br></p>', 'button' => null], '2' => ['answer' => '<p>Unfortunately, we’re unable to offer free samples. As a retailer, we buy all magazines from their publishers at the regular trade price. However, you could contact the magazine’s publisher directly to ask if they can send you a free copy.</p>', 'question' => 'Can I order a free copy of a magazine to sample?', 'button' => ['text' => 'Click Here']]], 'accordion' => false, 'first_tab_open' => false, 'first_tab_opened' => false, 'items' => ['0' => ['answer' => '<p>We often send out our newsletter with news and great offers. We will never disclose your data to third parties and you can unsubscribe from the newsletter at any time.</p>', 'question' => 'Where can I subscribe to your newsletter?'], '1' => ['answer' => '<p>Unfortunately, we’re unable to offer free samples. As a retailer, we buy all magazines from their publishers at the regular trade price. However, you could contact the magazine’s publisher directly to ask if they can send you a free copy.</p>', 'question' => 'Can I order a free copy of magazine to sample?'], '2' => ['answer' => '<p>You can create a new account at the end of the order process or on the following page. You can view all of your orders and subscriptions in your customer account. You can also change your addresses and your password.</p>', 'question' => 'Where on your website can I open a customer account?'], '3' => ['answer' => '<p>No, you don’t have to create an account. But there are a few advantages if you create an account.</p><ul><li><p>You never have to enter your billing and shipping address again</p></li><li><p>Find all of your orders, subscriptions and addresses in your account</p></li><li><p>Download invoices of your orders</p></li></ul>', 'question' => 'Do I need to create an account to make an order?'], '4' => ['question' => 'Do you also have a physical store?', 'answer' => '<p>No, we don’t have a physical store location at the moment. We accept only orders through our online shop and we’re shipping all orders with the Swiss Post Service. Please visit our shipping section for more details.</p><p>From time to time you will find us at design fairs and popup markets in Switzerland. Subscribe to our newsletter and you’ll receive the latest news.</p>']]]], 'design' => ['main_container' => ['borders' => ['border' => null], 'max_width' => ['number' => 650, 'unit' => 'px', 'style' => '650px']], 'item_containers' => ['spacing' => ['padding' => null], 'borders' => null], 'icon' => ['icon_animation' => 'rotation', 'icon' => ['slug' => 'icon-chevron-right.', 'name' => 'chevron right', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>'], 'icon_rotation' => ['number' => 90, 'unit' => 'deg', 'style' => '90deg'], 'second_icon' => ['slug' => 'icon-times.', 'name' => 'times', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>'], 'icon_size' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'spacing' => null, 'typography' => null, 'colors' => ['question' => null, 'answer' => null], 'animation' => ['duration' => null, 'icon_rotation' => ['number' => 0, 'unit' => 'deg', 'style' => 0]], 'border' => ['bottom_border' => null], 'question' => ['container_width' => null, 'between_items' => null, 'icon' => null, 'background' => null, 'bottom_border' => null, 'button' => null], 'wrapper' => ['width' => ['breakpoint_base' => ['number' => 600, 'unit' => 'px', 'style' => '600px']], 'background' => null], 'item' => ['icon' => ['icon' => ['slug' => 'icon-plus', 'name' => 'plus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
<path d="M31 12h-11v-11c0-0.552-0.448-1-1-1h-6c-0.552 0-1 0.448-1 1v11h-11c-0.552 0-1 0.448-1 1v6c0 0.552 0.448 1 1 1h11v11c0 0.552 0.448 1 1 1h6c0.552 0 1-0.448 1-1v-11h11c0.552 0 1-0.448 1-1v-6c0-0.552-0.448-1-1-1z"/>
</svg>'], 'active_icon' => ['slug' => 'icon-minus.', 'name' => 'minus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>']], 'horizontal_padding' => ['number' => 16, 'unit' => 'px', 'style' => '16px', 'breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'vertical_padding' => ['number' => 16, 'unit' => 'px', 'style' => '16px', 'breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'below_title' => null], 'borders' => ['wrapper_border' => true, 'border_color' => '#1B1B1BFF', 'border_width' => ['breakpoint_base' => ['number' => 2, 'unit' => 'px', 'style' => '2px']]]]];
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
        "wrapper",
        "Wrapper",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "item",
        "Item",
        [c(
        "horizontal_padding",
        "Horizontal Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "vertical_padding",
        "Vertical Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\AtomV1ButtonDesign",
      "Button",
      "button",
       ['type' => 'popout']
     ), c(
        "icon",
        "Icon",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "active_icon",
        "Active Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 4, 'max' => 100, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px']]],
        true,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "active_color",
        "Active Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "active_background",
        "Active Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "below_title",
        "Below Title",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "above_button",
        "Above Button",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "after_item",
        "After Item",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "typography",
        "Typography",
        [c(
        "title_tag",
        "Title Tag",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'h1', 'text' => 'H1'], '1' => ['text' => 'H2', 'value' => 'h2'], '2' => ['text' => 'H3', 'value' => 'h3'], '3' => ['text' => 'H4', 'value' => 'h4'], '4' => ['text' => 'H5', 'value' => 'h5'], '5' => ['text' => 'H6', 'value' => 'h6']]],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_align",
      "Title",
      "title",
       ['type' => 'popout']
     ), c(
        "active_title",
        "Active Title",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_align",
      "Content",
      "content",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "borders",
        "Borders",
        [c(
        "below_only",
        "Below Only",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border_color",
        "Border Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border_width",
        "Border Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "border_radius",
        "Border Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.borders.below_only', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical'],
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
        "settings",
        "Settings",
        [c(
        "items",
        "Items",
        [c(
        "question",
        "Question",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
        false,
        false,
        [],
      ), c(
        "answer",
        "Answer",
        [],
        ['type' => 'richtext', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\AtomV1ButtonContent",
      "Button",
      "button",
       ['type' => 'popout']
     )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{question}', 'defaultTitle' => 'Question', 'buttonName' => 'Add Question']],
        false,
        false,
        [],
      ), c(
        "accordion",
        "Accordion",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "first_tab_opened",
        "First tab opened",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%elements/Frequently_Asked_Questions/assets/faq.js'],'title' => 'Tabs.js',],'1' =>  ['title' => 'FAQ Frontend','inlineScripts' => ['new BreakdanceFaq(\'%%SELECTOR%%\', { accordion: {{ content.settings.accordion|json_encode }}, openFirst: {{ content.settings.first_tab_opened|json_encode }}  });'],'builderCondition' => 'return false;','frontendCondition' => 'return true;',],];
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
        return [

'onMountedElement' => [['script' => '(function() {
  if (!window.breakdanceFaqInstances) window.breakdanceFaqInstances = {};

  if (window.breakdanceFaqInstances && window.breakdanceFaqInstances[%%ID%%]) {
    window.breakdanceFaqInstances[%%ID%%].destroy();
  }

  window.breakdanceFaqInstances[%%ID%%] = new BreakdanceFaq(\'%%SELECTOR%%\', { accordion: {{ content.settings.accordion|json_encode }}, openFirst: {{ content.settings.first_tab_opened|json_encode }}  });
}());',
],],

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceFaqInstances && window.breakdanceFaqInstances[%%ID%%]) {
    window.breakdanceFaqInstances[%%ID%%].destroy();
  }

  window.breakdanceFaqInstances[%%ID%%] = new BreakdanceFaq(\'%%SELECTOR%%\', { accordion: {{ content.settings.accordion|json_encode }}, openFirst: {{ content.settings.first_tab_opened|json_encode }}  });
}());',
],],

'onBeforeDeletingElement' => [['script' => '(function() {
  if (window.breakdanceFaqInstances && window.breakdanceFaqInstances[%%ID%%]) {
    window.breakdanceFaqInstances[%%ID%%].destroy();
    delete window.breakdanceFaqInstances[%%ID%%];
  }
}());',
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
        return 750;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.settings.items[].question'], '1' => ['accepts' => 'string', 'path' => 'content.settings.items[].answer'], '2' => ['accepts' => 'string', 'path' => 'content.settings.items[].button.text'], '3' => ['accepts' => 'string', 'path' => 'content.settings.items[].button.link']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'yes', 'dynamicBehaviorWorks' => 'yes'];
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return ['design.item.button.custom.size.full_width_at', 'design.item.button.style'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
