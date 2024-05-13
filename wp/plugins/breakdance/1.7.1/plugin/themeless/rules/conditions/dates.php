<?php

namespace Breakdance\Themeless\Rules;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerDateAndTimeConditions'
);

function registerDateAndTimeConditions()
{
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'day-of-week',
            'label' => 'Day Of Week (WordPress)',
            'category' => 'Date & Time',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return [
                    [
                        'label' => 'Day',
                        'items' => [
                            ['text' => 'Monday', 'value' => 'Monday'],
                            ['text' => 'Tuesday', 'value' => 'Tuesday'],
                            ['text' => 'Wednesday', 'value' => 'Wednesday'],
                            ['text' => 'Thursday', 'value' => 'Thursday'],
                            ['text' => 'Friday', 'value' => 'Friday'],
                            ['text' => 'Saturday', 'value' => 'Saturday'],
                            ['text' => 'Sunday', 'value' => 'Sunday'],
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
                    // get day of week in English. wp_date will translate it
                    $dayOfWeek = getDateTime('now')->format('l');

                    switch ($operand) {
                        case OPERAND_ONE_OF:
                            return in_array($dayOfWeek, $values);
                        case OPERAND_NONE_OF:
                            return !in_array($dayOfWeek, $values);
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
            'slug' => 'current-time',
            'label' => 'Current Time (WordPress)',
            'category' => 'Date & Time',
            'operands' => [OPERAND_BEFORE, OPERAND_AFTER],
            'valueInputType' => 'timepicker',
            'values' => function () {
                return false;
            },
            'callback' => /**
             * @param mixed $operand
             * @param string $value format is 24h  (e.g 23:25)
             * @return bool
             */
                function ($operand, $value): bool {
                    $valueTime = getDateTime($value);

                    $currentTime = current_datetime();
                    switch ($operand) {
                        case OPERAND_BEFORE:
                            return $currentTime<= $valueTime;
                        case OPERAND_AFTER:
                            return $currentTime >= $valueTime;
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
            'slug' => 'current-date',
            'label' => 'Current Date (WordPress)',
            'category' => 'Date & Time',
            'operands' => [OPERAND_BEFORE, OPERAND_AFTER, OPERAND_IS, OPERAND_IS_NOT, OPERAND_ONE_OF, OPERAND_NONE_OF],
            'valueInputType' => 'datepicker',
            'values' => function () {
                return false;
            },
            'callback' => /**
             * @param mixed $operand
             * @param string|string[] $value
             * @return bool
             */
                function ($operand, $value): bool {
                    if (is_string($value)){
                        $valueTime = getDateTime($value);

                        $currentTime = current_datetime();
                        switch ($operand){
                            case OPERAND_BEFORE:
                                return $currentTime < $valueTime;
                            case OPERAND_AFTER:
                                return $currentTime > $valueTime;
                            default:
                                return false;
                        }
                    }

                    $currentDate = current_datetime()->format('Y-m-d');

                    switch ($operand) {
                        case OPERAND_IS:
                            return isInDateRange($value);
                        case OPERAND_IS_NOT:
                            return !isInDateRange($value);
                        case OPERAND_ONE_OF:
                            return in_array($currentDate, $value);
                        case OPERAND_NONE_OF:
                            return !in_array($currentDate, $value);
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' => false,
        ]
    );


    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating', 'query_builder'],
            'availableForType' => ['ALL'],
            'slug' => 'post-date',
            'label' => 'Post Date',
            'category' => 'Date & Time',
            'operands' => [OPERAND_AFTER, OPERAND_BEFORE],
            'valueInputType' => 'datepicker',
            'values' => function () {
                return false;
            },
            'callback' => /**
             * @param mixed $operand
             * @param string $value
             * @return bool
             */
                function ($operand, $value): bool {
                    global $post;
                    /** @var \WP_Post $post */
                    $post = $post;
                    $valueDate = getDateTime($value);
                    $postDate = getDateTime($post->post_date);
                    if ($operand === OPERAND_AFTER) {
                        return $postDate > $valueDate;
                    }
                    if ($operand === OPERAND_BEFORE) {
                        return $postDate < $valueDate;
                    }
                    return false;
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param string $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    $valueDate = getDateTime($value);
                    $dateQuery = $query['date_query'] ?? [];
                    if ($operand === OPERAND_BEFORE) {
                        $query['date_query']['before'] = $valueDate;
                    } elseif ($operand === OPERAND_AFTER) {
                        $query['date_query']['after'] = $valueDate;
                    }
                    $query['date_query'] = $dateQuery;
                    return $query;
                },
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating', 'query_builder'],
            'availableForType' => ['ALL'],
            'slug' => 'post-modified-date',
            'label' => 'Post Modified Date',
            'category' => 'Date & Time',
            'operands' => [OPERAND_AFTER, OPERAND_BEFORE],
            'valueInputType' => 'datepicker',
            'values' => function () {
                return false;
            },
            'callback' => /**
             * @param mixed $operand
             * @param string $value
             * @return bool
             */
                function ($operand, $value): bool {
                    global $post;
                    /** @var \WP_Post $post */
                    $post = $post;
                    $valueDate = getDateTime($value);
                    $postDate = getDateTime($post->post_modified);
                    if ($operand === OPERAND_AFTER) {
                        return $postDate > $valueDate;
                    }
                    if ($operand === OPERAND_BEFORE) {
                        return $postDate < $valueDate;
                    }
                    return false;
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param string $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    $valueDate = getDateTime($value);
                    $dateQuery = $query['date_query'] ?? [];
                    if ($operand === OPERAND_BEFORE) {
                        $query['date_query']['before'] = $valueDate;
                    } elseif ($operand === OPERAND_AFTER) {
                        $query['date_query']['after'] = $valueDate;
                    }
                    $query['date_query'] = $dateQuery;
                    return $query;
                },
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'current-month',
            'label' => 'Current Month (WordPress)',
            'category' => 'Date & Time',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return [
                    [
                        'label' => 'Month',
                        'items' => getMonthsForDropdown()
                    ]
                ];
            },
            'allowMultiselect' => true,
            'callback' => /**
             * @param mixed $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value): bool {
                    $month = getDateTime('now')->format('n');
                    switch ($operand) {
                        case OPERAND_ONE_OF:
                            return in_array($month, $value);
                        case OPERAND_NONE_OF:
                            return !in_array($month, $value);
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' => false,
        ]
    );
}

/**
 * @param string[] $values
 * @return bool
 */
function isInDateRange($values){
    $currentDate = current_datetime();

    // date range
    if (count($values) === 2) {
        // destructure using null as default values
        [$fromDateValue, $toDateValue] = $values + [null, null];

        if (!$fromDateValue || !$toDateValue){
            return false;
        }

        $fromDate = getDateTime($fromDateValue);
        $toDate = getDateTime($toDateValue);

        if ($fromDate > $toDate) {
            // if the "from" date is after the "to" date,
            // reverse them before the comparison
            $fromDate = getDateTime($toDateValue);
            $toDate = getDateTime($fromDateValue);
        }

        // set time to 23:59:59 include the to date
        $toDate->setTime(23, 59,59);

        return $fromDate <= $currentDate && $currentDate <= $toDate;
    }

    // specific day
     return $currentDate->format('Y-m-d') === ($values[0] ?? null);
}


/**
 * @return DropdownData[]
 */
function getMonthsForDropdown() {
    $months = [];
    for($m = 1; $m<= 12; ++$m) {
        $monthAsTimestamp = (string) mktime(0, 0, 0, $m, 1);

        $months[] = [
            // $monthAsTimestamp is unix time. Need to pass it with "@" for DateTime to use it
            'text' => getDateTime("@$monthAsTimestamp")->format('F'),
            'value' => getDateTime("@$monthAsTimestamp")->format('n')
        ];
    }

    return $months;
}

/**
 * @param string $time
 * @return \DateTime
 */
function getDateTime($time){
    return new \DateTime($time, wp_timezone());
}
