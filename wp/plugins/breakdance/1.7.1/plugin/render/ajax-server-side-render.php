<?php

namespace Breakdance\Render;

use Exception;
use function Breakdance\ClassesSelectors\template;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_server_side_render',
        '\Breakdance\Render\serverSideRender',
        'edit',
        true,
        [
            'args' => [
                'properties' => FILTER_UNSAFE_RAW,
                'parentProperties' => FILTER_UNSAFE_RAW,
                'elementSlug' => FILTER_UNSAFE_RAW,
            ]
        ]
    );
});

/**
 * @param string $propertiesJsonString
 * @param string $parentPropertiesJsonString
 * @param string $elementSlug
 * @return SsrNode
 * @throws Exception (if element doesn't exist)
 */
function serverSideRender($propertiesJsonString, $parentPropertiesJsonString, $elementSlug)
{
    /**
     * Silence operator will be removed after https://github.com/soflyy/breakdance/issues/722 completion
     * @var PropertiesData
     */
    $properties = @json_decode($propertiesJsonString, true);
    /**
     * @var PropertiesData
     */
    $parentProperties = @json_decode($parentPropertiesJsonString, true);

    if ($elementSlug && class_exists($elementSlug)) {
        /**
         * @psalm-suppress MixedMethodCall
         * @var \Breakdance\Elements\Element
         */
        $element = new $elementSlug();
        $ssrResult = $element::ssr($properties ?: [], $parentProperties ?: [], true);

        /**
         * @var string $ssrResult
         * @psalm-suppress TooManyArguments
         */
        $ssrResult = apply_filters("breakdance_builder_ssr_rendered_html", $ssrResult, $properties, $elementSlug, $parentProperties);

        return [
            'html' => $ssrResult,
            'postsGeneratedCssFilePaths' => (object) ScriptAndStyleHolder::getInstance()->getPostsGeneratedCssFilePaths(),
            'dependencies' => ScriptAndStyleHolder::getInstance()->dependencies
        ];

    } else {
        throw new \Exception("Cant render element that doesnt exist -  {$elementSlug}");
    }
}
