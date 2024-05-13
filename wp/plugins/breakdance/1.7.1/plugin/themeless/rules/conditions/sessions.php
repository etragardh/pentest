<?php /** @noinspection PhpParamsInspection */

namespace Breakdance\Themeless\Rules;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerConditionsSessionRules'
);

function registerConditionsSessionRules()
{

    $isDisabled = \Breakdance\Data\get_global_option('breakdance_settings_disable_view_tracking_cookies') === 'yes';
    $appendToLabel = $isDisabled ? ' (DISABLED)' : "";

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'page_views',
            'label' => 'Page View Count' . $appendToLabel,
            'category' => 'Sessions',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
            'valueInputType' => 'number',
            'values' => function () {
                return false;
            },
            'callback' => function (string $operand, string $value): bool {
                $pageViews = getPageViews();
                switch ($operand) {
                    case OPERAND_GREATER_THAN:
                        return $pageViews > (int) $value;
                    case OPERAND_LESS_THAN:
                        return $pageViews < (int) $value;
                    case OPERAND_IS:
                        return $pageViews === (int) $value;
                    case OPERAND_IS_NOT:
                        return $pageViews !== (int) $value;
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
            'slug' => 'session_count',
            'label' => 'Session Count' . $appendToLabel,
            'category' => 'Sessions',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
            'valueInputType' => 'number',
            'values' => function () {
                return false;
            },
            'callback' => function (string $operand, string $value): bool {
                $sessionCount = getSessionCount();
                switch ($operand) {
                    case OPERAND_GREATER_THAN:
                        return $sessionCount > (int) $value;
                    case OPERAND_LESS_THAN:
                        return $sessionCount < (int) $value;
                    case OPERAND_IS:
                        return $sessionCount === (int) $value;
                    case OPERAND_IS_NOT:
                        return $sessionCount !== (int) $value;
                    default:
                        return false;
                }
            },
            'templatePreviewableItems' => false,
        ]
    );
}

/**
 * @return int
 */
function getSessionCount()
{
    return (int) ($_COOKIE['breakdance_session_count'] ?? 0);
}

/**
 * @return int
 */
function getPageViews()
{
    return (int) ($_COOKIE['breakdance_view_count'] ?? 0);
}

