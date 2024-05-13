<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\CountdownTimer",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class CountdownTimer extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-stopwatch-20" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M240 240C240 213.5 261.5 192 288 192C314.5 192 336 213.5 336 240V368C336 394.5 314.5 416 288 416C261.5 416 240 394.5 240 368V240zM288 224C279.2 224 272 231.2 272 240V368C272 376.8 279.2 384 288 384C296.8 384 304 376.8 304 368V240C304 231.2 296.8 224 288 224zM151.2 237.1C148.4 245.4 139.3 249.1 130.9 247.2C122.6 244.4 118 235.3 120.8 226.9L121.4 225.2C128 205.4 146.6 191.1 167.5 191.1C194.3 191.1 215.1 213.7 215.1 240.5V255.4C215.1 269.8 211.1 283.7 202.1 294.1L169.5 335.7C158.5 349.4 152.4 366.4 152 384H199.1C208.8 384 215.1 391.2 215.1 400C215.1 408.8 208.8 416 199.1 416H135.1C127.2 416 119.1 408.8 119.1 400V385.7C119.1 360.2 128.7 335.6 144.5 315.7L177.1 274.1C181.6 269.4 183.1 262.5 183.1 255.4V240.5C183.1 231.4 176.6 223.1 167.5 223.1C160.3 223.1 154 228.6 151.8 235.3L151.2 237.1zM304 0C312.8 0 320 7.164 320 16C320 24.84 312.8 32 304 32H240V96.61C289.4 100.4 333.1 121.4 367.7 153.6L404.7 116.7C410.9 110.4 421.1 110.4 427.3 116.7C433.6 122.9 433.6 133.1 427.3 139.3L389.1 177.5C416 212.6 432 256.4 432 304C432 418.9 338.9 512 224 512C109.1 512 16 418.9 16 304C16 194.5 100.6 104.8 208 96.61V32H144C135.2 32 128 24.84 128 16C128 7.164 135.2 0 144 0L304 0zM223.1 128C126.8 128 47.1 206.8 47.1 304C47.1 401.2 126.8 480 223.1 480C321.2 480 400 401.2 400 304C400 206.8 321.2 128 223.1 128z"></path></svg>';
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
        return 'Countdown Timer';
    }

    static function className()
    {
        return 'bde-countdown-timer';
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
        return ['content' => ['countdown_date' => ['month' => 'Apr', 'date' => 23, 'year' => 2038, 'hours' => 11, 'minutes' => 40, 'seconds' => 30, 'expired_message' => 'Offer Expired']], 'design' => ['countdown_styles' => ['days' => true, 'minutes' => true, 'seconds' => true, 'hours' => true, 'days_suffix' => 'Days', 'hours_suffix' => 'Hours', 'minutes_suffix' => 'Minutes', 'seconds_suffix' => 'Seconds', 'dividers' => true, 'divider_width' => ['number' => 1, 'unit' => 'px', 'style' => '1px'], 'divider_color' => '#ccc', 'gaps' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]]];
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
        "timer",
        "Timer",
        [c(
        "align",
        "Align",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'Left'], '1' => ['text' => 'Center', 'value' => 'center'], '2' => ['text' => 'Right', 'value' => 'flex-end']]],
        false,
        false,
        [],
      ), c(
        "divider_width",
        "Divider Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 48, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'condition' => ['path' => 'design.timer.divider_type', 'operand' => 'equals', 'value' => 'line']],
        false,
        false,
        [],
      ), c(
        "labels",
        "Labels",
        [c(
        "label_position",
        "Label Position",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'inside', 'text' => 'Inside'], '1' => ['text' => 'Outside', 'value' => 'outside']], 'condition' => ['path' => 'design.digits.animation', 'operand' => 'is none of', 'value' => ['0' => 'flip']]],
        false,
        false,
        [],
      ), c(
        "above_label",
        "Above Label",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 128]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "divider",
        "Divider",
        [c(
        "type",
        "Type",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'line', 'text' => 'Line'], '1' => ['text' => 'Colon', 'value' => 'colon']]],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.timer.divider_type', 'operand' => 'not equals', 'value' => 'none']],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 20, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'condition' => ['path' => 'design.timer.divider.type', 'operand' => 'equals', 'value' => 'line']],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 6, 'max' => 20, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'condition' => ['path' => 'design.timer.divider.type', 'operand' => 'equals', 'value' => 'colon']],
        false,
        false,
        [],
      ), c(
        "height",
        "Height",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 5, 'max' => 100, 'step' => 5], 'unitOptions' => ['types' => ['0' => '%'], 'defaultType' => '%'], 'condition' => ['path' => 'design.timer.divider.type', 'operand' => 'equals', 'value' => 'line']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.timer.labels.label_position', 'operand' => 'not equals', 'value' => 'outside']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "digits",
        "Digits",
        [c(
        "space_between",
        "Space Between",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 128]],
        false,
        false,
        [],
      ), c(
        "style",
        "Style",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Default'], '1' => ['value' => 'donut', 'text' => 'Donut'], '2' => ['text' => 'Flip', 'value' => 'flip']]],
        false,
        false,
        [],
      ), c(
        "default",
        "Default",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border",
        "Border",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'Circle', 'value' => 'circle'], '1' => ['text' => 'Round', 'value' => 'round'], '2' => ['text' => 'Custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px']], 'condition' => ['path' => 'design.digits.default.border', 'operand' => 'is none of', 'value' => ['0' => 'circle']]],
        false,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px']], 'condition' => ['path' => 'design.digits.default.border', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 300, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em']], 'condition' => ['path' => 'design.digits.default.border', 'operand' => 'equals', 'value' => 'circle']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.digits.style', 'operand' => 'is none of', 'value' => ['0' => 'flip', '1' => 'donut']]],
        false,
        false,
        [],
      ), c(
        "donut",
        "Donut",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 300, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em']]],
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
        "line",
        "Line",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "line_width",
        "Line Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 20, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        false,
        false,
        [],
      ), c(
        "line_cap",
        "Line Cap",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'square', 'text' => 'Square'], '1' => ['text' => 'Round', 'value' => 'round']]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.digits.style', 'operand' => 'equals', 'value' => 'donut']],
        false,
        false,
        [],
      ), c(
        "flip",
        "Flip",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 5, 'max' => 50, 'step' => 5], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
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
        "shadow",
        "Shadow",
        [],
        ['type' => 'shadow', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'condition' => ['path' => 'design.digits.style', 'operand' => 'equals', 'value' => 'flip'], 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography",
      "Digits",
      "digits",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Labels",
      "labels",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Expired Message",
      "expired_message",
       ['type' => 'popout']
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
      "Container",
      "container",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Expired Message",
      "expired_message",
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
        "timer",
        "Timer",
        [c(
        "timer_type",
        "Timer Type",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'fixed', 'text' => 'Fixed'], '1' => ['text' => 'Evergreen', 'value' => 'evergreen']]],
        false,
        false,
        [],
      ), c(
        "timezone",
        "Timezone",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'local', 'text' => 'User Local'], '1' => ['value' => 'Pacific/Midway', 'text' => '(UTC-11:00) Midway Island'], '2' => ['text' => '(UTC-11:00) Samoa', 'value' => 'Pacific/Samoa'], '3' => ['text' => '(UTC-10:00) Hawaii', 'value' => 'Pacific/Honolulu'], '4' => ['text' => '(UTC-09:00) Alaska', 'value' => 'US/Alaska'], '5' => ['text' => '(UTC-08:00) Pacific Time (US & Canada)', 'value' => 'America/Los_Angeles'], '6' => ['text' => '(UTC-08:00) Tijuana', 'value' => 'America/Tijuana'], '7' => ['text' => '(UTC-07:00) Arizona', 'value' => 'US/Arizona'], '8' => ['text' => '(UTC-07:00) Chihuahua', 'value' => 'America/Chihuahua'], '9' => ['text' => '(UTC-07:00) La Paz', 'value' => 'America/Chihuahua'], '10' => ['text' => '(UTC-07:00) Mazatlan', 'value' => 'America/Mazatlan'], '11' => ['text' => '(UTC-07:00) Mountain Time (US & Canada)', 'value' => 'US/Mountain'], '12' => ['text' => '(UTC-06:00) Central America', 'value' => 'America/Managua'], '13' => ['text' => '(UTC-06:00) Central Time (US & Canada)', 'value' => 'US/Central'], '14' => ['text' => 'UTC-06:00) Guadalajara', 'value' => 'America/Mexico_City'], '15' => ['text' => '(UTC-06:00) Mexico City', 'value' => 'America/Mexico_City'], '16' => ['text' => '(UTC-06:00) Monterrey', 'value' => 'America/Monterrey'], '17' => ['text' => '(UTC-06:00) Saskatchewan', 'value' => 'Canada/Saskatchewan'], '18' => ['text' => '(UTC-05:00) Bogota', 'value' => 'America/Bogota'], '19' => ['text' => '(UTC-05:00) Eastern Time (US & Canada)', 'value' => 'US/Eastern'], '20' => ['text' => '(UTC-05:00) Indiana (East)', 'value' => 'US/East-Indiana'], '21' => ['text' => '(UTC-05:00) Lima', 'value' => 'America/Lima'], '22' => ['text' => '(UTC-05:00) Quito', 'value' => 'America/Bogota'], '23' => ['text' => '(UTC-04:00) Atlantic Time (Canada)', 'value' => 'Canada/Atlantic'], '24' => ['text' => '(UTC-04:30) Caracas', 'value' => 'America/Caracas'], '25' => ['text' => '(UTC-04:00) La Paz', 'value' => 'America/La_Paz'], '26' => ['text' => '(UTC-04:00) Santiago', 'value' => 'America/Santiago'], '27' => ['text' => '(UTC-03:30) Newfoundland', 'value' => 'Canada/Newfoundland'], '28' => ['text' => '(UTC-03:00) Brasilia', 'value' => 'America/Sao_Paulo'], '29' => ['text' => '(UTC-03:00) Buenos Aires', 'value' => 'America/Argentina/Buenos_Aires'], '30' => ['text' => 'UTC-03:00) Georgetown', 'value' => 'America/Argentina/Buenos_Aires'], '31' => ['text' => '(UTC-03:00) Greenland', 'value' => 'America/Godthab'], '32' => ['text' => '(UTC-02:00) Mid-Atlantic', 'value' => 'America/Noronha'], '33' => ['text' => '(UTC-01:00) Azores', 'value' => 'Atlantic/Azores'], '34' => ['text' => '(UTC-01:00) Cape Verde Is.', 'value' => 'Atlantic/Cape_Verde'], '35' => ['text' => '(UTC+00:00) Casablanca', 'value' => 'Africa/Casablanca'], '36' => ['text' => '(UTC+00:00) Edinburgh', 'value' => 'Europe/London'], '37' => ['text' => '(UTC+00:00) Greenwich Mean Time : Dublin', 'value' => 'Etc/Greenwich'], '38' => ['text' => '(UTC+00:00) Lisbon', 'value' => 'Europe/Lisbon'], '39' => ['text' => '(UTC+00:00) London', 'value' => 'Europe/London'], '40' => ['text' => 'UTC+00:00) Monrovia', 'value' => 'Africa/Monrovia'], '41' => ['text' => 'UTC ', 'value' => 'UTC'], '42' => ['text' => '(UTC+01:00) Amsterdam', 'value' => 'Europe/Amsterdam'], '43' => ['text' => '(UTC+01:00) Belgrade', 'value' => 'Europe/Belgrade'], '44' => ['text' => '(UTC+01:00) Berlin', 'value' => 'Europe/Berlin'], '45' => ['text' => '(UTC+01:00) Bern', 'value' => 'Europe/Berlin'], '46' => ['text' => '(UTC+01:00) Bratislava', 'value' => 'Europe/Bratislava'], '47' => ['text' => '(UTC+01:00) Brussels', 'value' => 'Europe/Brussels'], '48' => ['text' => '(UTC+01:00) Budapest', 'value' => 'Europe/Budapest'], '49' => ['text' => '(UTC+01:00) Copenhagen', 'value' => 'Europe/Copenhagen'], '50' => ['text' => '(UTC+01:00) Ljubljana', 'value' => 'Europe/Ljubljana'], '51' => ['text' => '(UTC+01:00) Madrid', 'value' => 'Europe/Madrid'], '52' => ['text' => '(UTC+01:00) Paris', 'value' => 'Europe/Paris'], '53' => ['text' => '(UTC+01:00) Prague', 'value' => 'Europe/Prague'], '54' => ['text' => '(UTC+01:00) Rome', 'value' => 'Europe/Rome'], '55' => ['text' => 'UTC+01:00) Sarajevo', 'value' => 'Europe/Sarajevo'], '56' => ['text' => '(UTC+01:00) Skopje', 'value' => 'Europe/Skopje'], '57' => ['text' => '(UTC+01:00) Stockholm', 'value' => 'Europe/Stockholm'], '58' => ['text' => 'UTC+01:00) Vienna', 'value' => 'Europe/Vienna'], '59' => ['text' => '(UTC+01:00) Warsaw', 'value' => 'Europe/Warsaw'], '60' => ['text' => '(UTC+01:00) West Central Africa', 'value' => 'Africa/Lagos'], '61' => ['text' => '(UTC+01:00) Zagreb', 'value' => 'Europe/Zagreb'], '62' => ['text' => '(UTC+02:00) Athens', 'value' => 'Europe/Athens'], '63' => ['text' => '(UTC+02:00) Bucharest', 'value' => 'Europe/Bucharest'], '64' => ['text' => '(UTC+02:00) Cairo', 'value' => 'Africa/Cairo'], '65' => ['text' => '(UTC+02:00) Harare', 'value' => 'Africa/Harare'], '66' => ['text' => '(UTC+02:00) Helsinki', 'value' => 'Europe/Helsinki'], '67' => ['text' => '(UTC+02:00) Istanbul', 'value' => 'Europe/Istanbul'], '68' => ['text' => '(UTC+02:00) Jerusalem', 'value' => 'Asia/Jerusalem'], '69' => ['text' => '(UTC+02:00) Kyiv', 'value' => 'Europe/Helsinki'], '70' => ['text' => '(UTC+02:00) Pretoria', 'value' => 'Africa/Johannesburg'], '71' => ['text' => '(UTC+02:00) Riga', 'value' => 'Europe/Riga'], '72' => ['text' => 'UTC+02:00) Sofia', 'value' => 'Europe/Sofia'], '73' => ['text' => '(UTC+02:00) Tallinn', 'value' => 'Europe/Tallinn'], '74' => ['text' => '(UTC+02:00) Vilnius', 'value' => 'Europe/Vilnius'], '75' => ['text' => '(UTC+03:00) Baghdad', 'value' => 'Asia/Baghdad'], '76' => ['text' => '(UTC+03:00) Kuwait', 'value' => 'Asia/Kuwait'], '77' => ['text' => '(UTC+03:00) Minsk', 'value' => 'Europe/Minsk'], '78' => ['text' => '(UTC+03:00) Nairobi', 'value' => 'Africa/Nairobi'], '79' => ['text' => '(UTC+03:00) Riyadh', 'value' => 'Asia/Riyadh'], '80' => ['text' => '(UTC+03:00) Volgograd', 'value' => 'Europe/Volgograd'], '81' => ['text' => '(UTC+03:30) Tehran', 'value' => 'Asia/Tehran'], '82' => ['text' => '(UTC+04:00) Abu Dhabi', 'value' => 'Asia/Muscat'], '83' => ['text' => '(UTC+04:00) Baku', 'value' => 'Asia/Baku'], '84' => ['text' => 'UTC+04:00) Moscow', 'value' => 'Europe/Moscow'], '85' => ['text' => 'UTC+04:00) Muscat', 'value' => 'Asia/Muscat'], '86' => ['text' => '(UTC+04:00) St. Petersburg', 'value' => 'Europe/Moscow'], '87' => ['text' => '(UTC+04:00) Tbilisi', 'value' => 'Asia/Tbilisi'], '88' => ['text' => '(UTC+04:00) Yerevan', 'value' => 'Asia/Yerevan'], '89' => ['text' => '(UTC+04:30) Kabul', 'value' => 'Asia/Kabul'], '90' => ['text' => '(UTC+05:00) Islamabad', 'value' => 'Asia/Karachi'], '91' => ['text' => 'UTC+05:00) Karachi', 'value' => 'Asia/Karachi'], '92' => ['text' => '(UTC+05:00) Tashkent', 'value' => 'Asia/Tashkent'], '93' => ['text' => '(UTC+05:30) Chennai', 'value' => 'Asia/Calcutta'], '94' => ['text' => '(UTC+05:30) Kolkata', 'value' => 'Asia/Kolkata'], '95' => ['text' => '(UTC+05:30) Mumbai', 'value' => 'Asia/Calcutta'], '96' => ['text' => '(UTC+05:30) New Delhi', 'value' => 'Asia/Calcutta'], '97' => ['text' => '(UTC+05:30) Sri Jayawardenepura', 'value' => 'Asia/Calcutta'], '98' => ['text' => '(UTC+05:45) Kathmandu', 'value' => 'Asia/Katmandu'], '99' => ['text' => '(UTC+06:00) Almaty', 'value' => 'Asia/Almaty'], '100' => ['text' => '(UTC+06:00) Astana', 'value' => 'Asia/Dhaka'], '101' => ['text' => '(UTC+06:00) Dhaka', 'value' => 'Asia/Dhaka'], '102' => ['text' => '(UTC+06:00) Ekaterinburg', 'value' => 'Asia/Yekaterinburg'], '103' => ['text' => '(UTC+06:30) Rangoon', 'value' => 'Asia/Rangoon'], '104' => ['text' => '(UTC+07:00) Bangkok', 'value' => 'Asia/Bangkok'], '105' => ['text' => 'UTC+07:00) Hanoi', 'value' => 'Asia/Bangkok'], '106' => ['text' => '(UTC+07:00) Jakarta', 'value' => 'Asia/Jakarta'], '107' => ['text' => '(UTC+07:00) Novosibirsk', 'value' => 'Asia/Novosibirsk'], '108' => ['text' => '(UTC+08:00) Beijing', 'value' => 'Asia/Hong_Kong'], '109' => ['text' => '(UTC+08:00) Chongqing', 'value' => 'Asia/Chongqing'], '110' => ['text' => '(UTC+08:00) Hong Kong', 'value' => 'Asia/Hong_Kong'], '111' => ['text' => '(UTC+08:00) Krasnoyarsk', 'value' => 'Asia/Krasnoyarsk'], '112' => ['text' => '(UTC+08:00) Kuala Lumpur', 'value' => 'Asia/Kuala_Lumpur'], '113' => ['text' => '(UTC+08:00) Perth', 'value' => 'Australia/Perth'], '114' => ['text' => '(UTC+08:00) Singapore', 'value' => 'Asia/Singapore'], '115' => ['text' => '(UTC+08:00) Taipei', 'value' => 'Asia/Taipei'], '116' => ['text' => '(UTC+08:00) Ulaan Bataar', 'value' => 'Asia/Ulan_Bator'], '117' => ['text' => '(UTC+08:00) Urumqi', 'value' => 'Asia/Urumqi'], '118' => ['text' => '(UTC+09:00) Irkutsk', 'value' => 'Asia/Irkutsk'], '119' => ['text' => '(UTC+09:00) Osaka', 'value' => 'Asia/Tokyo'], '120' => ['text' => 'UTC+09:00) Sapporo', 'value' => 'Asia/Tokyo'], '121' => ['text' => '(UTC+09:00) Seoul', 'value' => 'Asia/Seoul'], '122' => ['text' => '(UTC+09:00) Tokyo', 'value' => 'Asia/Tokyo'], '123' => ['text' => '(UTC+09:30) Adelaide', 'value' => 'Australia/Adelaide'], '124' => ['text' => '(UTC+09:30) Darwin', 'value' => 'Australia/Darwin'], '125' => ['text' => '(UTC+10:00) Brisbane', 'value' => 'Australia/Brisbane'], '126' => ['text' => '(UTC+10:00) Canberra', 'value' => 'Australia/Canberra'], '127' => ['text' => '(UTC+10:00) Guam', 'value' => 'Pacific/Guam'], '128' => ['text' => '(UTC+10:00) Hobart', 'value' => 'Australia/Hobart'], '129' => ['text' => '(UTC+10:00) Melbourne', 'value' => 'Australia/Melbourne'], '130' => ['text' => '(UTC+10:00) Port Moresby', 'value' => 'Pacific/Port_Moresby'], '131' => ['text' => '(UTC+10:00) Sydney', 'value' => 'Australia/Sydney'], '132' => ['text' => '(UTC+10:00) Yakutsk', 'value' => 'Asia/Yakutsk'], '133' => ['text' => '(UTC+11:00) Vladivostok', 'value' => 'Asia/Vladivostok'], '134' => ['text' => '(UTC+12:00) Auckland', 'value' => 'Pacific/Auckland'], '135' => ['text' => '(UTC+12:00) Fiji', 'value' => 'Pacific/Fiji'], '136' => ['text' => 'UTC+12:00) International Date Line West', 'value' => 'Pacific/Kwajalein'], '137' => ['text' => '(UTC+12:00) Kamchatka', 'value' => 'Asia/Kamchatka'], '138' => ['text' => '(UTC+12:00) Magadan', 'value' => 'Asia/Magadan'], '139' => ['text' => '(UTC+12:00) Marshall Is.', 'value' => 'Pacific/Fiji'], '140' => ['text' => '(UTC+12:00) New Caledonia', 'value' => 'Asia/Magadan'], '141' => ['text' => '(UTC+12:00) Solomon Is.', 'value' => 'Asia/Magadan'], '142' => ['text' => 'UTC+12:00) Wellington', 'value' => 'Pacific/Auckland'], '143' => ['text' => '(UTC+13:00) Nukualofa', 'value' => 'Pacific/Tongatapu']], 'condition' => ['path' => 'content.timer.timer_type', 'operand' => 'not equals', 'value' => 'evergreen']],
        false,
        false,
        [],
      ), c(
        "year",
        "Year",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => '2021', 'text' => '2021'], '1' => ['text' => '2022', 'value' => '2022'], '2' => ['text' => '2023', 'value' => '2023'], '3' => ['text' => '2024', 'value' => '2024'], '4' => ['text' => '2025', 'value' => '2025'], '5' => ['text' => '2026', 'value' => '2026'], '6' => ['text' => '2027', 'value' => '2027'], '7' => ['text' => '2028', 'value' => '2028'], '8' => ['text' => '2029', 'value' => '2029'], '9' => ['text' => '2030', 'value' => '2030'], '10' => ['text' => '2031', 'value' => '2031']], 'condition' => ['path' => 'content.timer.timer_type', 'operand' => 'is none of', 'value' => ['0' => 'evergreen']]],
        false,
        false,
        [],
      ), c(
        "month",
        "Month",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Jan', 'label' => 'Label', 'value' => 'Jan'], '1' => ['text' => 'Feb', 'value' => 'Feb'], '2' => ['text' => 'Mar', 'value' => 'Mar'], '3' => ['text' => 'Apr', 'value' => 'Apr'], '4' => ['text' => 'May', 'value' => 'May'], '5' => ['text' => 'Jun', 'value' => 'Jun'], '6' => ['text' => 'Jul', 'value' => 'Jul'], '7' => ['text' => 'Aug', 'value' => 'Aug'], '8' => ['text' => 'Sep', 'value' => 'Sep'], '9' => ['text' => 'Oct', 'value' => 'Oct'], '10' => ['text' => 'Nov', 'value' => 'Nov'], '11' => ['text' => 'Dec', 'value' => 'Dec']], 'condition' => ['path' => 'content.timer.timer_type', 'operand' => 'is none of', 'value' => ['0' => 'evergreen']]],
        false,
        false,
        [],
      ), c(
        "days",
        "Days",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 31, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "hours",
        "Hours",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 23, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "minutes",
        "Minutes",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 59, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "seconds",
        "Seconds",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 59, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "time_units",
        "Time Units",
        [c(
        "days",
        "Days",
        [c(
        "hide",
        "Hide",
        [],
        ['type' => 'toggle', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "singular",
        "Singular",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "plural",
        "Plural",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "hours",
        "Hours",
        [c(
        "hide",
        "Hide",
        [],
        ['type' => 'toggle', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "singular",
        "Singular",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "plural",
        "Plural",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "minutes",
        "Minutes",
        [c(
        "hide",
        "Hide",
        [],
        ['type' => 'toggle', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "singular",
        "Singular",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "plural",
        "Plural",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "seconds",
        "Seconds",
        [c(
        "hide",
        "Hide",
        [],
        ['type' => 'toggle', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "singular",
        "Singular",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "plural",
        "Plural",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "expire",
        "Expire",
        [c(
        "expire_type",
        "Expire Type",
        [],
        ['type' => 'dropdown', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'message', 'text' => 'Show Message'], '1' => ['text' => 'Redirect', 'value' => 'redirect']]],
        false,
        false,
        [],
      ), c(
        "hide_timer_when_expired",
        "Hide timer when expired",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'content.expire.expire_type', 'operand' => 'equals', 'value' => 'message']],
        false,
        false,
        [],
      ), c(
        "message",
        "Message",
        [],
        ['type' => 'richtext', 'layout' => 'vertical', 'condition' => ['path' => 'content.expire.expire_type', 'operand' => 'equals', 'value' => 'message']],
        false,
        false,
        [],
      ), c(
        "redirect",
        "Redirect",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => 'content.expire.expire_type', 'operand' => 'equals', 'value' => 'redirect']],
        false,
        false,
        [],
      ), c(
        "show_message_preview",
        "Show Message Preview",
        [],
        ['type' => 'toggle', 'layout' => 'vertical', 'condition' => ['path' => 'content.expire.expire_type', 'operand' => 'equals', 'value' => 'message']],
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%elements/Countdown_Timer/assets/countdown-timer.js'],'builderCondition' => 'return true;','frontendCondition' => 'return true;','title' => 'Breakdance Countdown timer',],'1' =>  ['inlineScripts' => ['new BreakdanceCountdownTimer(\'%%SELECTOR%%\', { content : {{ content|json_encode }}, builder: false });
'],'builderCondition' => 'return false;','frontendCondition' => 'return true;','title' => 'Init in the frontend',],];
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
  if (window.breakdanceCountdownTimerInstances && window.breakdanceCountdownTimerInstances[%%ID%%]) {
    window.breakdanceCountdownTimerInstances[%%ID%%].destroy();
  }

  window.breakdanceCountdownTimerInstances[%%ID%%] = new BreakdanceCountdownTimer(\'%%SELECTOR%%\', { content : {{ content|json_encode }}, builder: true });
}());',
],],

'onBeforeDeletingElement' => [['script' => '(function() {
    if (window.breakdanceCountdownTimerInstances && window.breakdanceCountdownTimerInstances[%%ID%%]) {
      window.breakdanceCountdownTimerInstances[%%ID%%].destroy();
      delete window.breakdanceCountdownTimerInstances[%%ID%%];
    }
  }());',
],],

'onMovedElement' => [['script' => '  (function() {
    if (window.breakdanceImageHotspotInstances && window.breakdanceImageHotspotInstances[%%ID%%]) {
      window.breakdanceImageHotspotInstances[%%ID%%].update();
    }
  }());',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceCountdownTimerInstances) window.breakdanceCountdownTimerInstances = {};

    if (window.breakdanceCountdownTimerInstances && window.breakdanceCountdownTimerInstances[%%ID%%]) {
      window.breakdanceCountdownTimerInstances[%%ID%%].destroy();
    }

    window.breakdanceCountdownTimerInstances[%%ID%%] = new BreakdanceCountdownTimer(\'%%SELECTOR%%\', { content : {{ content|json_encode }}, builder: true });
  }());',
],],];
    }

    static function nestingRule()
    {
        return ["type" => "final",   ];
    }

    static function spacingBars()
    {
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.container.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.container.margin_bottom.%%BREAKPOINT%%']];
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
        return 1000;
    }

    static function dynamicPropertyPaths()
    {
        return [];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return ['looksGood' => 'unknown', 'optionsGood' => 'unknown', 'optionsWork' => 'unknown', 'dynamicBehaviorWorks' => 'no'];
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
