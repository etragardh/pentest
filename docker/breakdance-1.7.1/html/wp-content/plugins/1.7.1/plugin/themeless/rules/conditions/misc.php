<?php

namespace Breakdance\Themeless\Rules;

use Breakdance\Lib\Vendor\Sinergi\BrowserDetector\Browser;
use Breakdance\Lib\Vendor\Sinergi\BrowserDetector\Os;
use function Breakdance\Config\Breakpoints\get_breakpoints;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerMiscConditions'
);

function registerMiscConditions()
{
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'browser-name',
            'label' => 'Browser',
            'category' => 'Other',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                $browser = new Browser();

                return [
                    [
                        'label' => 'Browser',
                        'items' => [
                            ['text' => 'Google Chrome', 'value' => Browser::CHROME],
                            ['text' => 'Mozilla Firefox', 'value' => Browser::FIREFOX],
                            ['text' => 'Safari', 'value' => Browser::SAFARI],
                            ['text' => 'Microsoft Edge', 'value' => Browser::EDGE],
                            ['text' => 'Internet Explorer', 'value' => Browser::IE],
                            ['text' => 'Samsung Browser', 'value' => Browser::SAMSUNG_BROWSER],
                            ['text' => 'Opera', 'value' => Browser::OPERA],
                        ]
                    ]
                ];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $values
             * @return bool
             */
                function ($operand, $values): bool {
                    if (!$values) {
                        return false;
                    }

                    $browser = new Browser();

                    switch ($operand) {
                        case OPERAND_ONE_OF:
                            return in_array($browser->getName(), $values);
                        case OPERAND_NONE_OF:
                            return !in_array($browser->getName(), $values);
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' => false,
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'operating-system-name',
            'label' => 'Operating System',
            'category' => 'Other',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return [
                    [
                        'label' => 'Operating System / Device',
                        'items' => [
                            ['text' => 'Windows', 'value' => Os::WINDOWS],
                            ['text' => 'Mac OS', 'value' => Os::OSX],
                            ['text' => 'Linux', 'value' => Os::LINUX],
                            ['text' => 'iOS (iPhone)', 'value' => Os::IOS],
                            ['text' => 'Android', 'value' => Os::ANDROID],
                            ['text' => 'Chrome OS', 'value' => Os::CHROME_OS],
                        ]
                    ]
                ];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $values
             * @return bool
             */
                function ($operand, $values): bool {
                    if (!$values) {
                        return false;
                    }

                    $os = new Os();

                    switch ($operand) {
                        case OPERAND_ONE_OF:
                            return in_array($os->getName(), $values);
                        case OPERAND_NONE_OF:
                            return !in_array($os->getName(), $values);
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' => false,
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['All'],
            'availableForPostType' => ['breakdance_popup'],
            'slug' => 'breakpoints',
            'label' => 'Breakpoint',
            'category' => 'Other',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return [
                    [
                        'label' => 'Breakpoint',
                        'items' => array_map(function ($breakpoint) {
                            return ['text' => $breakpoint['label'], 'value' => $breakpoint['id']];
                        },\Breakdance\Config\Breakpoints\get_breakpoints())
                    ]
                ];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $values
             * @return bool
             */
                function ($operand, $values): bool {
                    // Always return true as Breakpoint
                    // conditions must be handled on the frontend
                    return true;
                },
            'templatePreviewableItems' => false,
        ]
    );
}
