<?php

namespace Breakdance\Themeless\Rules;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerConditionsReferrerRules'
);

function registerConditionsReferrerRules()
{
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'referer_url',
            'label' => 'Referrer URL',
            'category' => 'Referrer',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_CONTAINS, OPERAND_NOT_CONTAIN],
            'values' => function () {
                return false;
            },
            'callback' => function (string $operand, string $value): bool {
                $referrer = wp_get_raw_referer();
                if (!$referrer) {
                    return false;
                }
                switch ($operand) {
                    case OPERAND_CONTAINS:
                        return strpos($referrer, $value) !== false;
                    case OPERAND_NOT_CONTAIN:
                        return strpos($referrer, $value) === false;
                    case OPERAND_IS:
                        return $referrer === $value;
                    case OPERAND_IS_NOT:
                        return $referrer !== $value;
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
            'slug' => 'referer_type',
            'label' => 'Referrer Type',
            'category' => 'Referrer',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'valueInputType' => 'dropdown',
            'values' => function () {
                return [[
                    'label' => 'Referrer Type',
                    'items' => [
                        ['text' => 'Search Engine', 'value' => 'search_engine'],
                        ['text' => 'Internal', 'value' => 'internal'],
                        ['text' => 'External', 'value' => 'external'],
                    ]
                ]];
            },
            'callback' => function (string $operand, string $value): bool {
                switch ($value) {
                    case 'search_engine':
                        if (isSearchEngineReferer()) {
                            return $operand === OPERAND_IS;
                        }
                        return $operand === OPERAND_IS_NOT;
                    case 'internal':
                        if (isInternalReferer()) {
                            return $operand === OPERAND_IS;
                        }
                        return $operand === OPERAND_IS_NOT;
                    case 'external':
                        if (isExternalReferer()) {
                            return $operand === OPERAND_IS;
                        }
                        return $operand === OPERAND_IS_NOT;
                    default:
                        return false;
                }
            },
            'templatePreviewableItems' => false,
        ]
    );
}

/**
 * @return bool
 */
function isSearchEngineReferer() {
    $referrerHost = getReferrerHost();
    $searchEngineHosts = [
        'www.google.com',
        'www.bing.com',
        'www.yahoo.com',
        'www.baidu.com',
        'yandex.ru',
        'duckduckgo.com',
        'www.ask.com',
        'www.ecosia.org',
        'search.aol.com'
    ];
    return in_array($referrerHost, $searchEngineHosts);
}

/**
 * @return bool
 */
function isInternalReferer() {
    $referrerHost = getReferrerHost();
    $siteHost = getSiteHost();
    if (!$referrerHost || !$siteHost) {
        return false;
    }
    return $referrerHost === $siteHost;
}

/**
 * @return bool
 */
function isExternalReferer() {
    $referrerHost = getReferrerHost();
    $siteHost = getSiteHost();
    if (!$referrerHost || !$siteHost) {
        return false;
    }
    return $referrerHost !== $siteHost;
}

/**
 * @return string|false
 */
function getReferrerHost() {
    $referrer = wp_get_raw_referer();
    if (!$referrer) {
        return false;
    }
    return parse_url($referrer, PHP_URL_HOST) ?: false;
}

/**
 * @return string|false
 */
function getSiteHost() {
    $siteUrl = get_site_url();
    return parse_url($siteUrl, PHP_URL_HOST) ?: false;
}
