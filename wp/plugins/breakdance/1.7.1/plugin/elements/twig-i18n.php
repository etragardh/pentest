<?php

namespace Breakdance\Elements;

// TODO: should we uprate the JS functions to run with WP.i18n?
\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'esc_html__',
    'Breakdance\Elements\twig_esc_html__',
    '(text, domain) => text'
);

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'esc_attr__',
    'Breakdance\Elements\twig_esc_attr__',
    '(text, domain) => text'
);
\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'esc_html_x',
    'Breakdance\Elements\twig_esc_html_x',
    '(text) => text'
);

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'esc_attr_x',
    'Breakdance\Elements\twig_esc_attr_x',
    '(text) => text'
);

/**
 * @param string $text
 * @param string $domain
 * @return string
 */
function twig_esc_html__($text, $domain){
    return esc_html__($text, $domain);
}

/**
 * @param string $text
 * @param string $domain
 * @return string
 */
function twig_esc_attr__($text, $domain){
    return esc_attr__($text, $domain);
}

/**
 * @param string $text
 * @param string $domain
 * @return string
 */
function twig_esc_html_x($text, $domain){
    return esc_html_x($text, $domain);
}

/**
 * @param string $text
 * @param string $domain
 * @return string
 */
function twig_esc_attr_x($text, $domain){
    return esc_attr_x($text, $domain);
}
