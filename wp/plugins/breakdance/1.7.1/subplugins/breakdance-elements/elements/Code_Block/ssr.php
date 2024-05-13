<?php
/**
 * @var array $propertiesData
 */

/* helper function for security...
https://www.pritect.net/blog/wordpress-shortcode-injection-attack-vector
Some irresponsible plugin could  somehow let a shortcode display a breakdance elements
Let's ensure that isn't a security problem
 */

if (!function_exists('is_inside_shortcode')) {
    function is_inside_shortcode() {
        // TODO write this function?
        return apply_filters('breakdance_php_code_block_is_inside_shortcode', false);
    }
}

if (is_inside_shortcode()) {
    echo "For security reasons, Breakdance's PHP Code Block element is disabled inside shortcodes by default. "
        . "Override this limitation by filtering breakdance_php_code_block_is_inside_shortcode and returning false.";
} else {

    $code = "?>" . ($propertiesData['content']['content']['php_code'] ?? '');

    try {
        eval($code);
    } catch (\ParseError $e) {
        echo 'An error occurred inside the PHP Code Block element: <br />';
        echo 'Caught exception: ' . $e->getMessage() . "\n";
        echo 'Line: ';
        echo $e->getLine();
        echo "<br />";
    }

}

/* what do we do if the user outputs invalid html with missing closing tags, etc?
this won't break the builder, but it will break their frontend */

?>