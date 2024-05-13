<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\ContentToggle",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class ContentToggle extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'DuplicateIcon';
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
        return 'Content Toggle';
    }

    static function className()
    {
        return 'bde-content-toggle';
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
        return ['content' => ['label' => ['label_left' => 'Monthly', 'label_right' => 'Yearly']], 'design' => null];
    }

    static function defaultChildren()
    {
        return [['slug' => 'EssentialElements\ContentToggleContent', 'defaultProperties' => ['design' => ['layout' => ['gap' => ['breakpoint_base' => ['number' => 30, 'unit' => 'px', 'style' => '30px']], 'horizontal' => ['align' => ['breakpoint_base' => 'center']]]]], 'children' => ['0' => ['slug' => 'EssentialElements\PricingTable', 'defaultProperties' => ['content' => ['content' => ['title' => 'Starter', 'description' => 'Everything you need to succeed.', 'features' => ['0' => ['text' => 'Use on unlimited websites', 'not_included' => false], '1' => ['text' => 'WooCommerce integration', 'not_included' => false], '2' => ['text' => 'Design library', 'not_included' => false], '3' => ['text' => 'No bloat', 'not_included' => true]], 'icon' => ['slug' => 'icon-star.', 'name' => 'star', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"/></svg>'], 'button' => ['text' => 'Get Breakdance', 'link' => ['type' => 'url', 'url' => 'https://breakdance.com/']], 'price' => ['before_price' => 'normally $39', 'amount' => '29', 'currency_symbol' => '$', 'per_period' => 'per month', 'fractional_amount' => '', 'badge' => 'limited time offer'], 'accent' => true, 'accent_text' => 'popular']], 'design' => ['box' => ['width' => ['breakpoint_base' => ['number' => 340, 'unit' => 'px', 'style' => '340px']], 'padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'right' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'top' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'bottom' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]]], 'content_alignment' => ['breakpoint_base' => 'center'], 'border' => ['radius' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'shadow' => ['shadows' => ['0' => ['color' => '#00000021', 'x' => '0', 'y' => '0', 'blur' => '30', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 30px 0px #00000021'], 'borders' => ['shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#00000040', 'x' => '0', 'y' => '12', 'blur' => '42', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 12px 42px 0px #00000040']]]], 'price' => ['position' => null, 'amount' => null, 'badge' => null], 'spacing' => ['after_icon' => ['breakpoint_base' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'after_title' => ['breakpoint_base' => ['number' => 35, 'unit' => 'px', 'style' => '35px']], 'after_price_area' => ['breakpoint_base' => ['number' => 35, 'unit' => 'px', 'style' => '35px']], 'after_description' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'after_features' => ['breakpoint_base' => ['number' => 40, 'unit' => 'px', 'style' => '40px']], 'price_area' => ['amount_period' => null, 'before_amount' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'before_badge' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]], 'typography' => ['features' => ['color' => null]], 'features' => ['icons' => ['included_icon' => null, 'not_included_icon' => ['slug' => 'icon-times.', 'name' => 'times', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>'], 'background' => true, 'included_color' => '#27A216FF', 'not_included_color' => '#AD1111FF', 'padding' => ['breakpoint_base' => ['number' => 4, 'unit' => 'px', 'style' => '4px']], 'icon_size' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'radius' => ['breakpoint_base' => ['number' => 3, 'unit' => 'px', 'style' => '3px']], 'excluded_color' => '#000000FF'], 'text_indent' => null, 'space_between_items' => null, 'center' => false], 'button' => ['display_as' => null, 'style' => 'primary']]], 'children' => []], '1' => ['slug' => 'EssentialElements\PricingTable', 'defaultProperties' => ['content' => ['content' => ['title' => 'Professional', 'description' => 'Everything you need to succeed.', 'features' => ['0' => ['text' => 'Use on unlimited websites', 'not_included' => false], '1' => ['text' => 'WooCommerce integration', 'not_included' => false], '2' => ['text' => 'Design library', 'not_included' => false], '3' => ['text' => 'No bloat', 'not_included' => true]], 'icon' => ['slug' => 'icon-award.', 'name' => 'award', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M97.12 362.63c-8.69-8.69-4.16-6.24-25.12-11.85-9.51-2.55-17.87-7.45-25.43-13.32L1.2 448.7c-4.39 10.77 3.81 22.47 15.43 22.03l52.69-2.01L105.56 507c8 8.44 22.04 5.81 26.43-4.96l52.05-127.62c-10.84 6.04-22.87 9.58-35.31 9.58-19.5 0-37.82-7.59-51.61-21.37zM382.8 448.7l-45.37-111.24c-7.56 5.88-15.92 10.77-25.43 13.32-21.07 5.64-16.45 3.18-25.12 11.85-13.79 13.78-32.12 21.37-51.62 21.37-12.44 0-24.47-3.55-35.31-9.58L252 502.04c4.39 10.77 18.44 13.4 26.43 4.96l36.25-38.28 52.69 2.01c11.62.44 19.82-11.27 15.43-22.03zM263 340c15.28-15.55 17.03-14.21 38.79-20.14 13.89-3.79 24.75-14.84 28.47-28.98 7.48-28.4 5.54-24.97 25.95-45.75 10.17-10.35 14.14-25.44 10.42-39.58-7.47-28.38-7.48-24.42 0-52.83 3.72-14.14-.25-29.23-10.42-39.58-20.41-20.78-18.47-17.36-25.95-45.75-3.72-14.14-14.58-25.19-28.47-28.98-27.88-7.61-24.52-5.62-44.95-26.41-10.17-10.35-25-14.4-38.89-10.61-27.87 7.6-23.98 7.61-51.9 0-13.89-3.79-28.72.25-38.89 10.61-20.41 20.78-17.05 18.8-44.94 26.41-13.89 3.79-24.75 14.84-28.47 28.98-7.47 28.39-5.54 24.97-25.95 45.75-10.17 10.35-14.15 25.44-10.42 39.58 7.47 28.36 7.48 24.4 0 52.82-3.72 14.14.25 29.23 10.42 39.59 20.41 20.78 18.47 17.35 25.95 45.75 3.72 14.14 14.58 25.19 28.47 28.98C104.6 325.96 106.27 325 121 340c13.23 13.47 33.84 15.88 49.74 5.82a39.676 39.676 0 0 1 42.53 0c15.89 10.06 36.5 7.65 49.73-5.82zM97.66 175.96c0-53.03 42.24-96.02 94.34-96.02s94.34 42.99 94.34 96.02-42.24 96.02-94.34 96.02-94.34-42.99-94.34-96.02z"/></svg>'], 'button' => ['text' => 'Get Breakdance', 'link' => ['type' => 'url', 'url' => 'https://breakdance.com/']], 'price' => ['before_price' => 'normally $169', 'amount' => '149', 'currency_symbol' => '$', 'per_period' => 'per month', 'fractional_amount' => '', 'badge' => 'limited time offer'], 'accent' => true, 'accent_text' => 'popular']], 'design' => ['box' => ['width' => ['breakpoint_base' => ['number' => 340, 'unit' => 'px', 'style' => '340px']], 'padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'right' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'top' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'bottom' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]]], 'content_alignment' => ['breakpoint_base' => 'center'], 'border' => ['radius' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'shadow' => ['shadows' => ['0' => ['color' => '#00000021', 'x' => '0', 'y' => '0', 'blur' => '30', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 30px 0px #00000021'], 'borders' => ['shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#00000040', 'x' => '0', 'y' => '12', 'blur' => '42', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 12px 42px 0px #00000040']]]], 'price' => ['position' => null, 'amount' => null, 'badge' => null], 'spacing' => ['after_icon' => ['breakpoint_base' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'after_title' => ['breakpoint_base' => ['number' => 35, 'unit' => 'px', 'style' => '35px']], 'after_price_area' => ['breakpoint_base' => ['number' => 35, 'unit' => 'px', 'style' => '35px']], 'after_description' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'after_features' => ['breakpoint_base' => ['number' => 40, 'unit' => 'px', 'style' => '40px']], 'price_area' => ['amount_period' => null, 'before_amount' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'before_badge' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]], 'typography' => ['features' => ['color' => null]], 'features' => ['icons' => ['included_icon' => null, 'not_included_icon' => ['slug' => 'icon-times.', 'name' => 'times', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>'], 'background' => true, 'included_color' => '#27A216FF', 'not_included_color' => '#AD1111FF', 'padding' => ['breakpoint_base' => ['number' => 4, 'unit' => 'px', 'style' => '4px']], 'icon_size' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'radius' => ['breakpoint_base' => ['number' => 3, 'unit' => 'px', 'style' => '3px']], 'excluded_color' => '#000000FF'], 'text_indent' => null, 'space_between_items' => null, 'center' => false], 'button' => ['display_as' => null, 'style' => 'primary']]], 'children' => []]]], ['slug' => 'EssentialElements\ContentToggleContent', 'defaultProperties' => ['design' => ['layout' => ['horizontal' => ['align' => ['breakpoint_base' => 'center']], 'gap' => ['breakpoint_base' => ['number' => 30, 'unit' => 'px', 'style' => '30px']]]]], 'children' => ['0' => ['slug' => 'EssentialElements\PricingTable', 'defaultProperties' => ['content' => ['content' => ['title' => 'Starter', 'description' => 'Everything you need to succeed.', 'features' => ['0' => ['text' => 'Use on unlimited websites', 'not_included' => false], '1' => ['text' => 'WooCommerce integration', 'not_included' => false], '2' => ['text' => 'Design library', 'not_included' => false], '3' => ['text' => 'No bloat', 'not_included' => true]], 'icon' => ['slug' => 'icon-star.', 'name' => 'star', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"/></svg>'], 'button' => ['text' => 'Get Breakdance', 'link' => ['type' => 'url', 'url' => 'https://breakdance.com/']], 'price' => ['before_price' => 'normally $399', 'amount' => '199', 'currency_symbol' => '$', 'per_period' => 'per year', 'fractional_amount' => '', 'badge' => 'limited time offer'], 'accent' => true, 'accent_text' => 'popular']], 'design' => ['box' => ['width' => ['breakpoint_base' => ['number' => 340, 'unit' => 'px', 'style' => '340px']], 'padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'right' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'top' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'bottom' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]]], 'content_alignment' => ['breakpoint_base' => 'center'], 'border' => ['radius' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'shadow' => ['shadows' => ['0' => ['color' => '#00000021', 'x' => '0', 'y' => '0', 'blur' => '30', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 30px 0px #00000021'], 'borders' => ['shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#00000040', 'x' => '0', 'y' => '12', 'blur' => '42', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 12px 42px 0px #00000040']]]], 'price' => ['position' => null, 'amount' => null, 'badge' => null], 'spacing' => ['after_icon' => ['breakpoint_base' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'after_title' => ['breakpoint_base' => ['number' => 35, 'unit' => 'px', 'style' => '35px']], 'after_price_area' => ['breakpoint_base' => ['number' => 35, 'unit' => 'px', 'style' => '35px']], 'after_description' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'after_features' => ['breakpoint_base' => ['number' => 40, 'unit' => 'px', 'style' => '40px']], 'price_area' => ['amount_period' => null, 'before_amount' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'before_badge' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]], 'typography' => ['features' => ['color' => null]], 'features' => ['icons' => ['included_icon' => null, 'not_included_icon' => ['slug' => 'icon-times.', 'name' => 'times', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>'], 'background' => true, 'included_color' => '#27A216FF', 'not_included_color' => '#AD1111FF', 'padding' => ['breakpoint_base' => ['number' => 4, 'unit' => 'px', 'style' => '4px']], 'icon_size' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'radius' => ['breakpoint_base' => ['number' => 3, 'unit' => 'px', 'style' => '3px']], 'excluded_color' => '#000000FF'], 'text_indent' => null, 'space_between_items' => null, 'center' => false], 'button' => ['display_as' => null, 'style' => 'primary']]], 'children' => []], '1' => ['slug' => 'EssentialElements\PricingTable', 'defaultProperties' => ['content' => ['content' => ['title' => 'Professional', 'description' => 'Everything you need to succeed.', 'features' => ['0' => ['text' => 'Use on unlimited websites', 'not_included' => false], '1' => ['text' => 'WooCommerce integration', 'not_included' => false], '2' => ['text' => 'Design library', 'not_included' => false], '3' => ['text' => 'No bloat', 'not_included' => true]], 'icon' => ['slug' => 'icon-award.', 'name' => 'award', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M97.12 362.63c-8.69-8.69-4.16-6.24-25.12-11.85-9.51-2.55-17.87-7.45-25.43-13.32L1.2 448.7c-4.39 10.77 3.81 22.47 15.43 22.03l52.69-2.01L105.56 507c8 8.44 22.04 5.81 26.43-4.96l52.05-127.62c-10.84 6.04-22.87 9.58-35.31 9.58-19.5 0-37.82-7.59-51.61-21.37zM382.8 448.7l-45.37-111.24c-7.56 5.88-15.92 10.77-25.43 13.32-21.07 5.64-16.45 3.18-25.12 11.85-13.79 13.78-32.12 21.37-51.62 21.37-12.44 0-24.47-3.55-35.31-9.58L252 502.04c4.39 10.77 18.44 13.4 26.43 4.96l36.25-38.28 52.69 2.01c11.62.44 19.82-11.27 15.43-22.03zM263 340c15.28-15.55 17.03-14.21 38.79-20.14 13.89-3.79 24.75-14.84 28.47-28.98 7.48-28.4 5.54-24.97 25.95-45.75 10.17-10.35 14.14-25.44 10.42-39.58-7.47-28.38-7.48-24.42 0-52.83 3.72-14.14-.25-29.23-10.42-39.58-20.41-20.78-18.47-17.36-25.95-45.75-3.72-14.14-14.58-25.19-28.47-28.98-27.88-7.61-24.52-5.62-44.95-26.41-10.17-10.35-25-14.4-38.89-10.61-27.87 7.6-23.98 7.61-51.9 0-13.89-3.79-28.72.25-38.89 10.61-20.41 20.78-17.05 18.8-44.94 26.41-13.89 3.79-24.75 14.84-28.47 28.98-7.47 28.39-5.54 24.97-25.95 45.75-10.17 10.35-14.15 25.44-10.42 39.58 7.47 28.36 7.48 24.4 0 52.82-3.72 14.14.25 29.23 10.42 39.59 20.41 20.78 18.47 17.35 25.95 45.75 3.72 14.14 14.58 25.19 28.47 28.98C104.6 325.96 106.27 325 121 340c13.23 13.47 33.84 15.88 49.74 5.82a39.676 39.676 0 0 1 42.53 0c15.89 10.06 36.5 7.65 49.73-5.82zM97.66 175.96c0-53.03 42.24-96.02 94.34-96.02s94.34 42.99 94.34 96.02-42.24 96.02-94.34 96.02-94.34-42.99-94.34-96.02z"/></svg>'], 'button' => ['text' => 'Get Breakdance', 'link' => ['type' => 'url', 'url' => 'https://breakdance.com/']], 'price' => ['before_price' => 'normally $1499', 'amount' => '999', 'currency_symbol' => '$', 'per_period' => 'per year', 'fractional_amount' => '', 'badge' => 'limited time offer'], 'accent' => true, 'accent_text' => 'popular']], 'design' => ['box' => ['width' => ['breakpoint_base' => ['number' => 340, 'unit' => 'px', 'style' => '340px']], 'padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'right' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'top' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'bottom' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]]], 'content_alignment' => ['breakpoint_base' => 'center'], 'border' => ['radius' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'shadow' => ['shadows' => ['0' => ['color' => '#00000021', 'x' => '0', 'y' => '0', 'blur' => '30', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 30px 0px #00000021'], 'borders' => ['shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#00000040', 'x' => '0', 'y' => '12', 'blur' => '42', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 12px 42px 0px #00000040']]]], 'price' => ['position' => null, 'amount' => null, 'badge' => null], 'spacing' => ['after_icon' => ['breakpoint_base' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'after_title' => ['breakpoint_base' => ['number' => 35, 'unit' => 'px', 'style' => '35px']], 'after_price_area' => ['breakpoint_base' => ['number' => 35, 'unit' => 'px', 'style' => '35px']], 'after_description' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'after_features' => ['breakpoint_base' => ['number' => 40, 'unit' => 'px', 'style' => '40px']], 'price_area' => ['amount_period' => null, 'before_amount' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'before_badge' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]], 'typography' => ['features' => ['color' => null]], 'features' => ['icons' => ['included_icon' => null, 'not_included_icon' => ['slug' => 'icon-times.', 'name' => 'times', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg>'], 'background' => true, 'included_color' => '#27A216FF', 'not_included_color' => '#AD1111FF', 'padding' => ['breakpoint_base' => ['number' => 4, 'unit' => 'px', 'style' => '4px']], 'icon_size' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'radius' => ['breakpoint_base' => ['number' => 3, 'unit' => 'px', 'style' => '3px']], 'excluded_color' => '#000000FF'], 'text_indent' => null, 'space_between_items' => null, 'center' => false], 'button' => ['display_as' => null, 'style' => 'primary']]], 'children' => []]]]];
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [c(
        "layout",
        "Layout",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "fade_content",
        "Fade Content",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "switch",
        "Switch",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 30, 'max' => 300, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "height",
        "Height",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 1, 'max' => 80, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 30, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "toggle",
        "Toggle",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 5, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "inactive",
        "Inactive",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "active",
        "Active",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [c(
        "inactive",
        "Inactive",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "active",
        "Active",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "vertical_at",
        "Vertical at",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "label",
        "Label",
        [getPresetSection(
      "EssentialElements\\typography",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), c(
        "active_color",
        "Active Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
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
        [c(
        "below_toggle",
        "Below Toggle",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "after_labels",
        "After Labels",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), getPresetSection(
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
        "label",
        "Label",
        [c(
        "label_left",
        "Label Left",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'placeholder' => ''],
        false,
        false,
        [],
      ), c(
        "label_right",
        "Label Right",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "add_content",
        "Add Content",
        [],
        ['type' => 'add_registered_children', 'layout' => 'vertical'],
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
        return ['0' =>  ['title' => 'Breakdance Content Toggle','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-content-toggle@1/breakdance-content-toggle.js'],],'1' =>  ['title' => 'Breakdance Content Toggle Frontend','inlineScripts' => ['new BreakdanceContentToggle(\'%%SELECTOR%%\');'],],];
    }

    static function settings()
    {
        return ['bypassPointerEvents' => false, 'proOnly' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

'onPropertyChange' => [['script' => '(function() {
if (window.breakdanceContentToggleInstances && window.breakdanceContentToggleInstances[%%ID%%]) {
  window.breakdanceContentToggleInstances[%%ID%%].destroy();
}

window.breakdanceContentToggleInstances[%%ID%%] = new BreakdanceContentToggle(\'%%SELECTOR%%\');
}());',
],],

'onMountedElement' => [['script' => '(function() {
  if (!window.breakdanceContentToggleInstances) window.breakdanceContentToggleInstances = {};

  if (window.breakdanceContentToggleInstances && window.breakdanceContentToggleInstances[%%ID%%]) {
    window.breakdanceContentToggleInstances[%%ID%%].destroy();
  }

  window.breakdanceContentToggleInstances[%%ID%%] = new BreakdanceContentToggle(\'%%SELECTOR%%\');
}());',
],],

'onMovedElement' => [['script' => '(function() {
if (window.breakdanceContentToggleInstances && window.breakdanceContentToggleInstances[%%ID%%]) {
  window.breakdanceContentToggleInstances[%%ID%%].update();
}
}());',
],],

'onActivatedElement' => [['script' => '(function() {
              if (window.breakdanceContentToggleInstances && window.breakdanceContentToggleInstances[%%ID%%]) {
                window.breakdanceContentToggleInstances[%%ID%%].update();
              }
  }());',
],],

'onDeletedElement' => [['script' => '  (function() {
  if (window.breakdanceContentToggleInstances && window.breakdanceContentToggleInstances[%%ID%%]) {
    window.breakdanceContentToggleInstances[%%ID%%].destroy();
    delete window.breakdanceContentToggleInstances[%%ID%%];
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
        return 0;
    }

    static function dynamicPropertyPaths()
    {
        return false;
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
        return ['design.settings.force_vertical_stacking', 'design.layout.vertical_at', 'design.switch.vertical_at'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
