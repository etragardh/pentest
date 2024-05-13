<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\AdvancedTabs",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class AdvancedTabs extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg fill="currentColor" viewBox="0 0 500 500">   <path d="M244.5 0c-17.11 0-31 13.89-31 31s13.89 31 31 31h63c17.11 0 31-13.89 31-31s-13.89-31-31-31h-63ZM406-1c-17.11 0-31 13.89-31 31s13.89 31 31 31h63c17.11 0 31-13.89 31-31S486.11-1 469-1h-63ZM0 485c0 8.278 6.721 15 15 15s15-6.722 15-15V15C30 6.72 23.279-.001 15-.001s-15 6.722-15 15V485ZM147 102c0 8.28 6.721 15 15 15s15-6.72 15-15V14c0-8.278-6.721-15-15-15s-15 6.722-15 15v88ZM470 484.999c0 8.279 6.721 15 15 15s15-6.721 15-15v-383c0-8.28-6.721-15.001-15-15.001s-15 6.722-15 15V485Z"/>   <path d="M15 470c-8.278 0-15 6.721-15 15s6.722 15 15 15h470c8.28 0 15.001-6.721 15.001-15s-6.722-15-15-15H15ZM15 0C6.721 0 0 6.721 0 15s6.721 15 15 15h147c8.279 0 15-6.721 15-15s-6.721-15-15-15H15ZM163 87c-8.279 0-15 6.721-15 15s6.721 15 15 15h322c8.279 0 15-6.721 15-15s-6.721-15-15-15H163ZM89 208.5c-8.279 0-15 6.721-15 15s6.721 15 15 15h322c8.279 0 15-6.721 15-15s-6.721-15-15-15H89ZM89 278.5c-8.279 0-15 6.721-15 15s6.721 15 15 15h322c8.279 0 15-6.721 15-15s-6.721-15-15-15H89ZM89 348.5c-8.279 0-15 6.721-15 15s6.721 15 15 15h322c8.279 0 15-6.721 15-15s-6.721-15-15-15H89Z"/> </svg>';
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
        return "false";
    }

    static function name()
    {
        return 'Advanced Tabs';
    }

    static function className()
    {
        return 'bde-advanced-tabs';
    }

    static function category()
    {
        return 'advanced';
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
        return ['content' => ['content' => ['tabs' => ['0' => ['title' => 'Image', 'content' => '<p>Neque vitae tempus quam pellentesque nec nam. Pretium aenean pharetra magna ac placerat vestibulum lectus mauris. Fermentum et sollicitudin ac orci phasellus egestas tellus rutrum. Volutpat blandit aliquam etiam erat. Ut tortor pretium viverra suspendisse potenti nullam ac tortor. Sit amet nisl suscipit adipiscing bibendum. Tellus in hac habitasse platea. Turpis egestas integer eget aliquet nibh. Morbi non arcu risus quis varius quam quisque id diam. Vitae ultricies leo integer malesuada nunc vel.</p>'], '1' => ['title' => 'Video', 'content' => '<p>Convallis tellus id interdum velit. Enim lobortis scelerisque fermentum dui faucibus in ornare quam. Sed id semper risus in hendrerit gravida. Amet facilisis magna etiam tempor orci eu. Ac feugiat sed lectus vestibulum mattis ullamcorper velit sed ullamcorper.&nbsp;</p>'], '2' => ['title' => 'FAQ', 'content' => '<p>Fringilla urna porttitor rhoncus dolor purus non enim praesent elementum. Eu turpis egestas pretium aenean pharetra. Cras ornare arcu dui vivamus arcu felis bibendum ut tristique. Morbi quis commodo odio aenean sed. Pulvinar mattis nunc sed blandit libero volutpat sed. Aliquam sem fringilla ut morbi.</p>', 'icon' => []], '3' => ['title' => 'More Information', 'content' => '<p>Convallis tellus id interdum velit. Enim lobortis scelerisque fermentum dui faucibus in ornare quam. Sed id semper risus in hendrerit gravida. Amet facilisis magna etiam tempor orci eu. Ac feugiat sed lectus vestibulum mattis ullamcorper velit sed ullamcorper.&nbsp;</p>']], 'active_tab' => 1]], 'design' => ['tabs' => ['style' => 'tabs', 'space_between' => null, 'position' => 'center', 'separator' => ['color' => null], 'background' => null, 'text' => null, 'bar' => ['radius' => null, 'separator' => null, 'shadow' => null], 'icon' => null, 'mobile_dropdown' => ['visible_at' => 'breakpoint_phone_landscape']], 'spacing' => ['spacing' => null, 'wrapper' => null], 'typography' => ['tab' => null], 'content' => ['padding' => null], 'size' => ['width' => null]]];
    }

    static function defaultChildren()
    {
        return [['slug' => 'EssentialElements\TabContent', 'children' => ['0' => ['slug' => 'EssentialElements\Heading', 'defaultProperties' => ['content' => ['content' => ['text' => 'McWay Falls']]]], '1' => ['slug' => 'EssentialElements\Text', 'defaultProperties' => ['content' => ['content' => ['text' => 'McWay Falls is an 80-foot-tall waterfall on the coast of Big Sur in central California that flows year-round from McWay Creek in Julia Pfeiffer Burns State Park, about 37 miles south of Carmel, into the Pacific Ocean. During high tide, it is a tidefall, a waterfall that empties directly into the ocean']], 'design' => ['spacing' => ['margin_bottom' => null, 'margin_top' => null]]]], '2' => ['slug' => 'EssentialElements\Image', 'defaultProperties' => ['content' => ['content' => ['image' => ['id' => -1, 'type' => 'external_image', 'url' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2340&q=80', 'alt' => '', 'caption' => '']]]]]], 'defaultProperties' => ['design' => ['container' => ['background' => '#E5F5FFFF', 'padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 20, 'unit' => 'px', 'style' => '20px'], 'right' => ['number' => 20, 'unit' => 'px', 'style' => '20px'], 'top' => ['number' => 20, 'unit' => 'px', 'style' => '20px'], 'bottom' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]], 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'topLeft' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'topRight' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'bottomLeft' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'bottomRight' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'editMode' => 'all']]]], 'layout' => ['align' => null, 'gap' => ['breakpoint_base' => ['number' => 24, 'unit' => 'px', 'style' => '24px']], 'vertical_align' => null]]]], ['slug' => 'EssentialElements\TabContent', 'children' => ['0' => ['slug' => 'EssentialElements\Video', 'defaultProperties' => ['content' => ['video' => ['video' => ['title' => 'McWay Falls, Big Sur, California | 4K Drone Video', 'provider' => 'youtube', 'url' => 'https://www.youtube.com/watch?v=M32nAlCFLyU', 'embedUrl' => 'https://www.youtube.com/embed/M32nAlCFLyU?feature=oembed', 'thumbnail' => 'https://i.ytimg.com/vi/M32nAlCFLyU/hqdefault.jpg', 'format' => 'video', 'type' => 'oembed', 'source' => 'youtube']]]]]]], ['slug' => 'EssentialElements\TabContent', 'children' => ['0' => ['slug' => 'EssentialElements\FrequentlyAskedQuestions', 'defaultProperties' => ['content' => ['settings' => ['questions' => ['0' => ['question' => 'Is this a lifetime license?', 'answer' => '<p>Yes. You can use it on an unlimited number of sites. There are no limits.</p><ul><li><p>Lorem ipsum sit dolor amet</p></li><li><p>Lorem ipsum sit dolor amet</p></li></ul><p></p>'], '1' => ['question' => 'Where can in edit my address?', 'answer' => '<p>If you created a new account after or while ordering you can edit both addresses (for billing and shipping) in your&nbsp;<a href="#" rel="noopener noreferrer nofollow">customer account</a>.<br></p>', 'button' => null], '2' => ['answer' => '<p>Unfortunately, we’re unable to offer free samples. As a retailer, we buy all magazines from their publishers at the regular trade price. However, you could contact the magazine’s publisher directly to ask if they can send you a free copy.</p>', 'question' => 'Can I order a free copy of a magazine to sample?', 'button' => ['text' => 'Click Here']]], 'accordion' => false, 'first_tab_open' => false, 'first_tab_opened' => false, 'items' => ['0' => ['answer' => '<p>We often send out our newsletter with news and great offers. We will never disclose your data to third parties and you can unsubscribe from the newsletter at any time.</p>', 'question' => 'Where can I subscribe to your newsletter?'], '1' => ['answer' => '<p>Unfortunately, we’re unable to offer free samples. As a retailer, we buy all magazines from their publishers at the regular trade price. However, you could contact the magazine’s publisher directly to ask if they can send you a free copy.</p>', 'question' => 'Can I order a free copy of magazine to sample?'], '2' => ['answer' => '<p>You can create a new account at the end of the order process or on the following page. You can view all of your orders and subscriptions in your customer account. You can also change your addresses and your password.</p>', 'question' => 'Where on your website can I open a customer account?'], '3' => ['answer' => '<p>No, you don’t have to create an account. But there are a few advantages if you create an account.</p><ul><li><p>You never have to enter your billing and shipping address again</p></li><li><p>Find all of your orders, subscriptions and addresses in your account</p></li><li><p>Download invoices of your orders</p></li></ul>', 'question' => 'Do I need to create an account to make an order?'], '4' => ['question' => 'Do you also have a physical store?', 'answer' => '<p>No, we don’t have a physical store location at the moment. We accept only orders through our online shop and we’re shipping all orders with the Swiss Post Service. Please visit our shipping section for more details.</p><p>From time to time you will find us at design fairs and popup markets in Switzerland. Subscribe to our newsletter and you’ll receive the latest news.</p>']]]], 'design' => ['main_container' => ['borders' => ['border' => null], 'max_width' => ['number' => 650, 'unit' => 'px', 'style' => '650px']], 'item_containers' => ['spacing' => ['padding' => null], 'borders' => null], 'icon' => ['icon_animation' => 'rotation', 'icon' => ['slug' => 'icon-chevron-right.', 'name' => 'chevron right', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>'], 'icon_rotation' => ['number' => 90, 'unit' => 'deg', 'style' => '90deg'], 'second_icon' => ['slug' => 'icon-times.', 'name' => 'times', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>'], 'icon_size' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'spacing' => null, 'typography' => null, 'colors' => ['question' => null, 'answer' => null], 'animation' => ['duration' => null, 'icon_rotation' => ['number' => 0, 'unit' => 'deg', 'style' => 0]], 'border' => ['bottom_border' => null], 'question' => ['container_width' => null, 'between_items' => null, 'icon' => null, 'background' => null, 'bottom_border' => null, 'button' => null], 'wrapper' => ['width' => null, 'background' => null], 'item' => ['icon' => ['icon' => ['slug' => 'icon-plus', 'name' => 'plus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" id="icon-plus" viewBox="0 0 32 32">
<path d="M31 12h-11v-11c0-0.552-0.448-1-1-1h-6c-0.552 0-1 0.448-1 1v11h-11c-0.552 0-1 0.448-1 1v6c0 0.552 0.448 1 1 1h11v11c0 0.552 0.448 1 1 1h6c0.552 0 1-0.448 1-1v-11h11c0.552 0 1-0.448 1-1v-6c0-0.552-0.448-1-1-1z"/>
</svg>'], 'active_icon' => ['slug' => 'icon-minus.', 'name' => 'minus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>']], 'horizontal_padding' => ['number' => 16, 'unit' => 'px', 'style' => '16px', 'breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'vertical_padding' => ['number' => 16, 'unit' => 'px', 'style' => '16px', 'breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'below_title' => null], 'borders' => ['wrapper_border' => false, 'border_color' => null, 'border_width' => ['number' => 0, 'unit' => 'px', 'style' => '0px']]]]]]], ['slug' => 'EssentialElements\TabContent']];
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [getPresetSection(
      "EssentialElements\\tabs_design",
      "Tabs",
      "tabs",
       ['type' => 'popout']
     ), c(
        "size",
        "Size",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 320, 'max' => 1200, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => '%', '3' => 'calc', '4' => 'custom'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "min_height",
        "Min Height",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "spacing",
        "Spacing",
        [getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Wrapper",
      "wrapper",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      )];
    }

    static function contentControls()
    {
        return [c(
        "content",
        "Content",
        [c(
        "tabs",
        "Tabs",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "title",
        "Title",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{title}', 'defaultTitle' => 'Untitled Tab', 'buttonName' => 'Add Tab']],
        false,
        false,
        [],
      ), c(
        "content",
        "Content",
        [],
        ['type' => 'add_registered_children', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "active_tab",
        "Active Tab",
        [],
        ['type' => 'number', 'layout' => 'inline', 'dropdownOptions' => ['populate' => ['path' => 'content.content.tabs.tabs', 'text' => 'title', 'value' => 'title']]],
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
        return ['0' =>  ['title' => 'Load Advanced Breakdance Tabs','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/advanced-tabs@1/advanced-tabs.js'],],'1' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/custom-tabs@1/tabs.js'],'styles' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/custom-tabs@1/tabs.css'],'title' => 'Load Breakdance Tabs',],'2' =>  ['inlineScripts' => ['new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: {{ content.content.active_tab|json_encode }}, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} } );'],'builderCondition' => 'return false;','title' => 'Init BreakdanceTabs in the frontend',],];
    }

    static function settings()
    {
        return ['proOnly' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
    window.breakdanceTabsInstances[%%ID%%].destroy();
  }

  window.breakdanceTabsInstances[%%ID%%] = new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: {{ content.content.active_tab|json_encode }}, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} });
}());',
],['script' => ' window.manageBreakdanceTabs && window.manageBreakdanceTabs().update(\'%%SELECTOR%%\')',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceTabsInstances) window.breakdanceTabsInstances = {};

    if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
      window.breakdanceTabsInstances[%%ID%%].destroy();
    }

    window.breakdanceTabsInstances[%%ID%%] = new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: {{ content.content.active_tab|json_encode }}, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} } );
  }());',
],],

'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
    window.breakdanceTabsInstances[%%ID%%].update();
  }
}());',
],],

'onBeforeDeletingElement' => [['script' => '  (function() {
    if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
      window.breakdanceTabsInstances[%%ID%%].destroy();
      delete window.breakdanceTabsInstances[%%ID%%];
    }
  }());',
],],];
    }

    static function nestingRule()
    {
        return ["type" => "container-restricted",   ];
    }

    static function spacingBars()
    {
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.wrapper.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.wrapper.margin_bottom.%%BREAKPOINT%%']];
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
        return 13;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.tabs[].title'], '1' => ['accepts' => 'string', 'path' => 'content.content.tabs[].content'], '2' => ['accepts' => 'string', 'path' => 'content.content.active_tab']];
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
        return ['design.tabs.responsive.visible_at', 'design.tabs.horizontal_at'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
