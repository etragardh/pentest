<?php
/**
 * @var array $propertiesData
 */

$shortcode = $propertiesData['content']['shortcode']['full_shortcode'];

// https://developer.wordpress.org/reference/functions/get_shortcode_regex/
$regexMatches = preg_match('/' . get_shortcode_regex() . '/s', $shortcode, $match);
$name = $match[2];
$arguments = $match[3];

if ($regexMatches) {
    // get_shortcode_regex doesn't tell us whether a shortcode is self-enclosing ([shortcode]) or if it has content
    // So we make all shortcodes have CHILDREN.
    // This leads to some shortcodes that don't expect "content" to not show it, since they don't render it at all.
    // I could check with my own regex to see, but there are multiple edge cases and I don't think it's worth it.
    $shortcodeWithContentReplacesForChildren = "[{$name} {$arguments}]%%CHILDREN%%[/{$name}]";

    echo do_shortcode($shortcodeWithContentReplacesForChildren);
} else {
    echo "<div style='color: red; font-size: 40px;'>Invalid shortcode</div> \n\n %%CHILDREN%%";
}
