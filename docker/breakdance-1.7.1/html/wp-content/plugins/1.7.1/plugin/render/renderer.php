<?php

namespace Breakdance\Render;

use Breakdance\Data\RecursivePropertyIterator;
use Breakdance\Data\RecursiveTreeNodeIterator;
use function Breakdance\Config\Breakpoints\mediaQueryString;
use function Breakdance\Data\get_global_settings_array;
use function Breakdance\DynamicData\breakdanceDoShortcode;
use function Breakdance\Elements\getRequiredPluginsNotActiveSsrMessage;
use function Breakdance\Elements\hasRequiredPluginsAndTheyAreAvailable;
use function Breakdance\Filesystem\HelperFunctions\generate_error_msg_from_fs_wp_error;
use function Breakdance\Filesystem\HelperFunctions\is_fs_error;
use function Breakdance\Filesystem\write_file_to_bucket;
use function Breakdance\String\camel;
use function Breakdance\String\lower;
use function Breakdance\Themeless\getTemplateSettingsFromDatabase;
use function Breakdance\Themeless\ThemeDisabler\is_theme_disabled;
use Breakdance\Filesystem\Consts;
use function Breakdance\Util\get_menu_page_url;
use function Breakdance\Util\Timing\start;
use function Breakdance\Util\Timing\finish;

/**
 * returns the HTML
 * @side-effect fills the ScriptAndStyleHolder with CSS and shit needed by the HTML
 * @side-effect generates the CSS cache if necessary
 * @param int $postId
 * @param int|null $repeaterItemNodeId within a post loop, this is the ID of the looped item
 * @return string|false
 * @throws \Exception
 */
function render($postId, $repeaterItemNodeId = null)
{
    $timing = start("renderPost-{$postId}-{$repeaterItemNodeId}", 'Total post rendering time');
    if (
        \Breakdance\isRequestFromBuilderIframe()
        && isset($_GET['breakdance_open_document'])
        && (int) $_GET['breakdance_open_document'] === $postId
    ) {
        finish($timing);
        return "<div id='breakdance-canvas-wrapper'></div>";
    }


    $renderedPost = getRenderedPost($postId, $repeaterItemNodeId);

    if ($renderedPost['error']) {
        finish($timing);
        return $renderedPost['message'];
    }
    $renderedNodes = $renderedPost['renderedNodes'];

    if (!$renderedNodes) {
        $postType = get_post_type($postId);

        if ($postType === BREAKDANCE_TEMPLATE_POST_TYPE && \Breakdance\isRequestFromBuilderIframe()) {
            $templateData = getTemplatesInfoForBuilder($postId);

            finish($timing);

            // this is a blank template rendered in the builder, so we should still send the template data back
            return "
                <div
                    data-rendered-with-template
                    data-template-id='$postId'
                    data-template-name='" . $templateData['postTitle'] . "'
                    data-template-is-fallback='" . $templateData['isTemplateFallback'] . "'
                    data-template-page-url='" . $templateData['templatesPageUrl'] . "'
                ></div>";
        }

        finish($timing);

        return false;
    }

    ScriptAndStyleHolder::getInstance()->append($renderedNodes['dependencies']);

    /**
     * @psalm-suppress TooManyArguments
     */
    $renderedNodesHtml = (string)apply_filters('breakdance_render_rendered_html', $renderedNodes['html'], $postId, $repeaterItemNodeId);

    if (
        \Breakdance\isRequestFromBuilderIframe() ||
        (\Breakdance\DesignLibrary\isRequestFromDesignLibraryModal() && \Breakdance\Permissions\hasPermission('full'))
    ) {
        $foreignDocumentBuilderUrl = \Breakdance\Admin\get_builder_loader_url((string) $postId);

        $postType = get_post_type($postId);

        $templateData = getTemplatesInfoForBuilder($postId);

        if ($postType === BREAKDANCE_TEMPLATE_POST_TYPE) {
            finish($timing);

            return "
                <div
                data-rendered-with-template
                data-template-id='$postId'
                data-template-name='" . $templateData['postTitle'] . "'
                data-template-is-fallback='" . $templateData['isTemplateFallback'] . "'
                data-template-page-url='" . $templateData['templatesPageUrl'] . "'
                ></div>"
                . $renderedNodesHtml;
        }

        $breakdanceClassString = is_theme_disabled() ? "" : " class='breakdance'";

        finish($timing);

        return in_array($postType, [BREAKDANCE_HEADER_POST_TYPE, BREAKDANCE_FOOTER_POST_TYPE])
            ? ("<div
                data-breakdance-foreign-document
                data-breakdance-foreign-document-builder-url='$foreignDocumentBuilderUrl'
                data-breakdance-foreign-document-type='$postType'
                data-breakdance-foreign-document-title='" . $templateData['postTitle'] . "'
                id='post-$postId'
                " . $breakdanceClassString . "
            >
                " . $renderedNodesHtml . "
            </div>"
            )
            : $renderedNodesHtml;
    } else {
        finish($timing);

        return is_theme_disabled() ?
            $renderedNodesHtml :
            "<div class='breakdance'>" . $renderedNodesHtml . "</div>";
    }
}

/**
 * @param int $postId
 * @param int|null $repeaterItemNodeId
 * @return array{error: boolean, message: string, renderedNodes: RenderedNodes|false }
 */
function getRenderedPost($postId, $repeaterItemNodeId = null)
{
    /*
    avoid circular renders - i.e. a template that renders itself or a global block that renders itself
    if there is an error, the renderStack is returned
    IMPORTANT: this function should always call endRender before returning
     */

    $renderStack = CircularRendererTracker::getInstance()->startRender($postId);

    if ($renderStack) {
        CircularRendererTracker::getInstance()->endRender();
        return [
            'error' => true,
            'message' => getCircularRenderErrorAsHtml($renderStack),
            'renderedNodes' => false,
        ];
    }

    /** @var \WP_Post|null $post */
    $post = get_post($postId);

    if ($post !== null && $post->post_status === 'trash') {
        CircularRendererTracker::getInstance()->endRender();
        $postTitle = get_the_title($postId);
        return [
            'error' => true,
            'message' => "You can't view the \"{$postTitle}\" (#{$postId}) item because it is in the Trash. Please restore it and try again.",
            'renderedNodes' => false,
        ];
    }

    $postCache = getPostCache($postId);
    $postCssCache = $postCache['cssCache'];
    $postDependencyCache = $postCache['dependencyCache'];

    $postDependencyCache && ScriptAndStyleHolder::getInstance()->append($postDependencyCache);

    /**
     * In order to preserve correct CSS order, current document's CSS assets
     * MUST be registered before rendering any child documents.
     *
     * @see https://github.com/soflyy/breakdance/issues/4259
     */
    ScriptAndStyleHolder::getInstance()->setPostGeneratedCssFilePaths((int) $postId, $postCssCache);

    /*
    TODO: would it be smart to check if there actually is a valid cache here
    getPostCssCache and getGlobalCssCache both should always return a valid
    css cache... since they generate the cache.
    but what about not being able to write to the file system, etc?
    that has to be handled somewhere
    for now, we say the cache is valid and just assume everrything works
     */

    $validCache = true;

    $renderedNodes = getRenderedNodes($postId, $validCache, $repeaterItemNodeId);
    CircularRendererTracker::getInstance()->endRender();

    return [
        'error' => false,
        'message' => '',
        'renderedNodes' => $renderedNodes
    ];
}

/**
 * @param int $postId
 * @return array{postTitle: string, isTemplateFallback: string, templatesPageUrl: string}
 */
function getTemplatesInfoForBuilder($postId)
{
    $postTitle = get_the_title($postId);
    $templateSettings = getTemplateSettingsFromDatabase($postId);

    return [
        'postTitle' => htmlspecialchars($postTitle, ENT_QUOTES),
        'isTemplateFallback' => ($templateSettings['fallback'] ?? false) ? 'true' : 'false',
        'templatesPageUrl' => get_menu_page_url(BREAKDANCE_TEMPLATE_POST_TYPE),
    ];
}

/**
 * @param int $postId
 * @param boolean $justDoHtmlAndSkipTheCss
 * @param integer | null $repeaterItemNodeId
 * @return RenderedNodes|false
 */
function getRenderedNodes($postId, $justDoHtmlAndSkipTheCss = false, $repeaterItemNodeId = null)
{
    $tree = \Breakdance\Data\get_tree($postId);

    if ($tree === false) {
        return false;
    }

    $renderedNodes = _render($tree['root']['children'], $postId, $justDoHtmlAndSkipTheCss, [], $repeaterItemNodeId);

    // don't forget about the addJavaScriptsInSettingsToHead in Canvas.vue
    return $renderedNodes;
}

/**
 * @param CSSSelector[] $selectors
 * @return string
 */
function cssForSelectors($selectors)
{

    $breakpoints = \Breakdance\Config\Breakpoints\get_breakpoints();
    $breakpointIds = array_map(
        function ($breakpoint) {
            return $breakpoint['id'];
        },
        $breakpoints
    );

    $template = \Breakdance\ClassesSelectors\template();
    /** @var PropertiesData $globalSettings */
    $globalSettings = get_global_settings_array()['settings'] ?? [];

    return array_reduce(
        $selectors,
        /**
         * @param string $acc
         * @param CSSSelector $selector
         */
        function ($acc, $selector) use ($breakpoints, $template, $breakpointIds, $globalSettings) {

            $css = array_reduce(
                $breakpoints,
                /**
                 * @param string $acc
                 * @param Breakpoint $breakpoint
                 */
                function ($acc, $breakpoint) use ($template, $selector, $breakpointIds, $globalSettings) {

                    /** @var array */
                    $selectorProperties = $selector['properties'] ?? [];

                    /**
                     * @psalm-suppress MixedAssignment
                     */
                    $flattenedSelectorProps = getFlattenedPropertiesByBreakpoint(
                        $breakpoint['id'],
                        array_merge(
                            $selectorProperties,
                            ['globalSettings' => $globalSettings]
                        ),
                        $breakpointIds,
                        BASE_BREAKPOINT_ID,
                        []
                    );
                    $flattenedSelectorProps['isBaseBreakpoint'] = $breakpoint['id'] === BASE_BREAKPOINT_ID;

                    $twigInstance = Twig::getInstance();

                    $selectorName = $selector['type'] === 'class' ? '.breakdance ' . $selector['name'] : $selector['name'];

                    $CSS = renderTags(
                        $twigInstance->runTwig($template, $flattenedSelectorProps),
                        [
                            'selector' => $selectorName,
                        ]
                    );

                    $query = mediaQueryString($breakpoint);

                    return $acc .
                        ($query
                            ? $query . ' {' . $CSS . '}'
                            : $CSS);
                },
                ''
            );

            return $acc . $css;
        },
        ''
    );
}

/**
 * @param PropertiesData $propertiesData
 * @param string $template
 * @param string[] $propertyPathsToWhitelistInFlatProps
 * @return string
 */

function cssForGlobalStyles($propertiesData, $template, $propertyPathsToWhitelistInFlatProps)
{
    $breakpoints = \Breakdance\Config\Breakpoints\get_breakpoints();
    $breakpointIds = array_map(
        function ($breakpoint) {
            return $breakpoint['id'];
        },
        $breakpoints
    );

    return array_reduce(
        $breakpoints,
        /**
         * @param string $acc
         * @param Breakpoint $breakpoint
         */
        function ($acc, $breakpoint) use ($template, $propertiesData, $breakpointIds, $propertyPathsToWhitelistInFlatProps) {

            /**
             * @psalm-suppress MixedAssignment
             */
            $flattenedGlobalProps = getFlattenedPropertiesByBreakpoint(
                $breakpoint['id'],
                $propertiesData,
                $breakpointIds,
                BASE_BREAKPOINT_ID,
                $propertyPathsToWhitelistInFlatProps
            );

            $flattenedGlobalProps['breakpoint'] = $breakpoint['id'];
            $flattenedGlobalProps['isBaseBreakpoint'] = $breakpoint['id'] === BASE_BREAKPOINT_ID;

            $twigInstance = Twig::getInstance();
            $css = $twigInstance->runTwig($template, $flattenedGlobalProps);
            $query = mediaQueryString($breakpoint);
            $responsiveCss = $query ? "{$query} { {$css} }" : $css;

            return $acc . $responsiveCss;
        },
        ''
    );
}

/**
 * The return type is not accurate.
 *
 * This function doesn't actually return an Element. It just returns the string class name.
 * If you try to call it non-statically (i.e. $element->someThing()) it will explode. You have to call it
 * statically. It would be nice to make Psalm know that this function returns
 * a string that is a valid class name of something that inherits \Breakdance\Elements\Element
 *
 * @param string $nodeType
 * @return \Breakdance\Elements\Element (not really though)
 * @psalm-suppress MoreSpecificReturnType
 * @psalm-suppress InvalidReturnType
 * @throws \Exception
 */
function getElementFromNodeType($nodeType)
{
    if (!class_exists($nodeType)) {
        $nodeType = "EssentialElements\\MissingElement";

        if (!class_exists($nodeType)) {
            throw new \Exception('Cant render element that doesnt exist - ' . $nodeType);
        }
    }

    /**
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress InvalidStringClass
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    return $nodeType;
}

/**
 * @param \Breakdance\Elements\Element $element
 * @param PropertiesData $props
 * @param string $childHtml
 * @param array $tags
 * @param PropertiesData $ssrParentProperties
 * @param int|null $repeaterItemNodeId
 * @return string
 */
function getInnerHtml($element, $props, $childHtml, $tags, $ssrParentProperties = [], $repeaterItemNodeId = null)
{
    /**
     * @psalm-suppress TooManyArguments
     */
    do_action('breakdance_render_element_template', $element, $props);

    $TEMPLATE = $element::template();

    if (trim($TEMPLATE) === '%%CHILDREN%%') {
        return renderTags($TEMPLATE, [
            'children' => $childHtml,
        ]);
    } else if (!hasRequiredPluginsAndTheyAreAvailable($element::settings())) {
        /** @var string $elementName */
        $elementName = $element::name();
        /** @var string[] $requiredPlugins */
        $requiredPlugins = $element::settings()['requiredPlugins'] ?? [];
        return getRequiredPluginsNotActiveSsrMessage($requiredPlugins, $elementName);
    } else {
        $twigInstance = Twig::getInstance();
        $renderedTemplate = $twigInstance->runTwig($TEMPLATE, $props);

        // Replace SSR with the result of $element::ssr()
        if (strpos($TEMPLATE, '%%SSR%%') !== false) {
            $renderedTemplate = renderTags($renderedTemplate, [
                'ssr' => $element::ssr($props, $ssrParentProperties, false, $repeaterItemNodeId),
                'elementName' => $element::name()
            ]);
        }

        return renderTags($renderedTemplate, array_merge([
            'children' => $childHtml,
        ], $tags));
    }
}

/**
 * @param \Breakdance\Elements\Element $element
 * @param PropertiesData $props
 * @param array $tags
 * @return ElementDependencyWithoutConditions
 */
function getDependencies($element, $props, $tags)
{
    /**  @var ElementDependenciesAndConditions[] */
    $elementDependencies = array_merge(
        $element::dependencies() ?: [],
        \Breakdance\Elements\FilteredGets\externalDependencies() ?: []
    );

    /**  @var ElementDependenciesAndConditions[] */
    $dependenciesToInclude = [];

    /**  @var ElementDependenciesAndConditions[] */
    foreach ($elementDependencies as $elementDependency) {
        // if it's not set, or it's empty
        if (!isset($elementDependency['frontendCondition']) || trim($elementDependency['frontendCondition']) === '') {
            $dependenciesToInclude[] = $elementDependency;

            continue;
        }

        /** @psalm-suppress  MixedAssignment */
        $twigInstance = Twig::getInstance();

        /** @var string */
        $processedFrontendCondition = $twigInstance->runTwig(
            $elementDependency['frontendCondition'],
            $props
        );

        try {
            /** @psalm-suppress MixedAssignment */
            $include = eval($processedFrontendCondition);

            if ($include) {
                $dependenciesToInclude[] = $elementDependency;
            }
        } catch (Exception $e) {
            error_log("Error when evaluating frontend condition:");
            error_log($e);
        }
    }

    /** @var ElementDependenciesAndConditions[] */
    $processedDependenciesToInclude = [];

    foreach ($dependenciesToInclude as $dep) {
        /**
         * trick stupid psalm
         * @var ElementDependenciesAndConditions
         */
        $dependency = $dep;

        // process the inlineScripts with Twig
        if (isset($dependency['inlineScripts'])) {
            /**  @var string[] */
            $inlineStyles = $dependency['inlineScripts'];
            $dependency['inlineScripts']
                = processInlineDependenciesWithTwig($dependency['inlineScripts'], $props, $tags);
        }

        // process the inlineStyles with Twig
        if (isset($dependency['inlineStyles'])) {
            /**  @var string[] */
            $inlineStyles = $dependency['inlineStyles'];
            $dependency['inlineStyles']
                = processInlineDependenciesWithTwig($inlineStyles, $props, $tags);
        }

        if (isset($dependency['scripts'])) {
            $dependency['scripts'] = replaceVariableInDependencies($dependency['scripts']);
        }

        if (isset($dependency['styles'])) {
            $dependency['styles'] = replaceVariableInDependencies($dependency['styles']);
        }

        $processedDependenciesToInclude[] = $dependency;
    }

    return dependencies_array_merge_recursive_without_conditions($processedDependenciesToInclude);
}

/**
 *
 * Merges recursively dependencies thile removing the conditions
 * in order to return a clean array
 * @param ElementDependenciesAndConditions[] $dependenciesArray
 * @return ElementDependencyWithoutConditions
 */
function dependencies_array_merge_recursive_without_conditions($dependenciesArray)
{
    /** @var ElementDependencyWithoutConditions */
    $dependenciesWithoutConditions = [];

    foreach ($dependenciesArray as $dependencies) {
        // removes conditions from the array
        unset($dependencies['frontendCondition']);
        unset($dependencies['builderCondition']);

        /** @var ElementDependencyWithoutConditions */
        $dependenciesWithoutConditions[] = $dependencies;
    }

    /** @var ElementDependencyWithoutConditions */
    $mergedDependencies = array_merge_recursive([], ...$dependenciesWithoutConditions);

    return $mergedDependencies;
}

/**
 * @param string[] $dependencies
 * @param PropertiesData $props
 * @param array $tags
 * @return string[]
 */
function processInlineDependenciesWithTwig($dependencies, $props, $tags)
{
    $twigInstance = Twig::getInstance();

    return array_map(
        function ($dependency) use ($twigInstance, $props, $tags) {
            return renderTags(
                $twigInstance->runTwig($dependency, $props),
                $tags
            );
        },
        $dependencies
    );
}

/**
 * Replace %%BREAKDANCE_ELEMENTS_PLUGIN_URL%% and %%BREAKDANCE_EXTRAS_SLUG%%%, creating a valid URL with whatever setup the user has
 * @param string[] $dependencies
 * @return string[]
 * @psalm-suppress InvalidReturnType
 */
function replaceVariableInDependencies($dependencies)
{
    $urls = \Breakdance\AvailableDependencyFiles\getReusableDependenciesUrls();

    /**
     * @psalm-suppress MixedAssignment
     * @var string
     */
    $breakdanceElementsUrl = defined('BREAKDANCE_ELEMENTS_PLUGIN_URL') ? BREAKDANCE_ELEMENTS_PLUGIN_URL : '';

    /**
     * @psalm-suppress InvalidReturnStatement
     */
    return array_map(
        function ($dependencyString) use ($urls, $breakdanceElementsUrl) {
            $dependencyWithPluginsUrl = str_replace('%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%', $breakdanceElementsUrl, $dependencyString);

            return preg_replace_callback(
                "/%%BREAKDANCE_REUSABLE_(.+)%%/",
                /**
                 * @param string[] $matches
                 * @return string
                 */
                function ($matches) use ($urls) {
                    if (isset($matches[1])) {
                        $key = camel(lower($matches[1]));
                        // if the key is not found, return the original string
                        return $urls[$key] ?? $matches[1];
                    }
                    return '';
                },
                $dependencyWithPluginsUrl
            );
        },
        $dependencies
    );
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return DefaultCSS
 */
function getDefaultCss($element)
{

    return [
        'slug' => $element::slug(),
        'css' => $element::defaultCSS(),
    ];
}

/**
 * @param string $cssTemplate
 * @param PropertiesData $props
 * @param Breakpoint[] $breakpoints
 * @param array $tags
 * @param string[]|false $propertyPathsToWhitelistInFlatProps
 * @param int|null $childElementCount
 * @return string
 */
function getCss($cssTemplate, $props, $breakpoints, $tags, $propertyPathsToWhitelistInFlatProps, $childElementCount = null)
{
    $result = '';

    foreach ($breakpoints as $breakpoint) {
        $CSS = templateToCss(
            $cssTemplate,
            $props,
            $breakpoint['id'],
            $tags,
            $propertyPathsToWhitelistInFlatProps,
            $childElementCount
        );

        $query = mediaQueryString($breakpoint);

        $result .= ($query
            ? $query . " {" . $CSS . "}"
            : $CSS);
    }

    return $result;
}

/**
 * @param string $cssTemplate
 * @param PropertiesData $props
 * @param string $breakpointId
 * @param array $tags
 * @param string[]|false $propertyPathsToWhitelistInFlatProps
 * @param int|null $childElementCount
 * @return string
 */
function templateToCss($cssTemplate, $props, $breakpointId, $tags, $propertyPathsToWhitelistInFlatProps, $childElementCount = null)
{
    static $breakpoints = null;
    static $builtin_breakpoints = null;

    if ($breakpoints === null) {
        $breakpoints = \Breakdance\Config\Breakpoints\get_breakpoints();
    }
    if ($builtin_breakpoints === null) {
        $builtin_breakpoints = \Breakdance\Config\Breakpoints\get_builtin_breakpoints();
    }

    /** @var Breakpoint[] $breakpoints */
    /** @var Breakpoint[] $builtin_breakpoints */

    /**
     * @psalm-suppress MixedArgument
     */
    $breakpointIds = array_map(
        /**
         * @param Breakpoint $breakpoint
         */
        fn ($breakpoint): string => $breakpoint['id'],
        $breakpoints
    );

    /**
     * @psalm-suppress MixedAssignment
     */
    $PROPSDATA = getFlattenedPropertiesByBreakpoint(
        $breakpointId,
        $props,
        $breakpointIds,
        BASE_BREAKPOINT_ID,
        $propertyPathsToWhitelistInFlatProps
    );

    $twigInstance = Twig::getInstance();

    $PROPSDATA['isBaseBreakpoint'] = $breakpointId === BASE_BREAKPOINT_ID;
    $PROPSDATA['firstResponsiveBreakpointId'] = FIRST_RESPONSIVE_BREAKPOINT_ID;
    $PROPSDATA['breakpoint'] = $breakpointId;
    /**
     * @psalm-suppress MixedAssignment
     */
    $PROPSDATA['builtinBreakpoints'] = $builtin_breakpoints;
    $PROPSDATA['childElementCount'] = $childElementCount;

    $processedTemplate = $twigInstance->runTwig($cssTemplate, $PROPSDATA);

    return renderTags($processedTemplate, $tags);
}

/**
 * @param array{tag:string,classList:string[],childHtml:string,atts:array<string, string>, id: string} $args
 * @return string
 */
function htmlElement($args)
{

    $classString = trim(
        array_reduce(
            $args['classList'],
            function (string $acc, string $className) {
                return $acc . ' ' . $className;
            },
            ''
        )
    );

    $atts = $args['atts'] ?? [];

    if (isset($atts['class'])) {
        $classString = $classString . ' ' . $atts['class'];
        // remove it to prevent issues with overriding the 'class' attribute we're setting manually
        unset($atts['class']);
    }

    $attsString = $atts
        ?
        array_reduce(
            array_keys($atts),
            function (string $acc, string $attName) use ($atts) {
                $attValue = htmlspecialchars($atts[$attName], ENT_QUOTES);

                if (in_array($attName, BUILDER_ONLY_HTML_ATTRIBUTES)) {
                    return $acc;
                }

                return $acc . ' ' . $attName . '="' . $attValue . '"';
            },
            ''
        )
        : '';

    $id = $args['id'];
    $tag = $args['tag'];
    $childHtml = $args['childHtml'];

    $idString = $id ? " id=\"{$id}\"" : '';

    $startTag = "<{$tag}{$idString} class=\"$classString\"{$attsString}>";
    $endStag = "</{$tag}>";

    return "{$startTag}{$childHtml}{$endStag}";
}

/**
 * @param TreeNode|false $node
 * @param \Breakdance\Elements\Element $element
 * @param PropertiesData $props
 * @param string $childHtml
 * @param string[] $classNames
 * @param array $tags
 * @param PropertiesData $ssrParentProperties
 * @param int|null $repeaterItemNodeId
 * @return string
 */
function getHtml($node, $element, $props, $childHtml, $classNames, $tags, $ssrParentProperties = [], $repeaterItemNodeId = null)
{

    $tag = getHtmlTag($element, $props);

    $id = getHtmlId($props);

    $atts = getAttributes($element, $props);

    if ($node) {
        /**
         * @psalm-suppress TooManyArguments
         * @var array<string, string>
         */
        $atts = apply_filters('breakdance_frontend_element_attributes', $atts, $node, $element);
    }


    $additionalClassNames = getAdditionalClassNames($element, $props);

    $classList = array_merge($classNames, $additionalClassNames);

    /**
     * @psalm-suppress TooManyArguments
     * @var string[]
     */
    $classListAfterFilters = apply_filters('breakdance_render_element_class_list', $classList, $element);

    return htmlElement(
        [
            'tag' => $tag,
            'classList' => $classListAfterFilters,
            'childHtml' => getInnerHtml($element, $props, $childHtml, $tags, $ssrParentProperties, $repeaterItemNodeId),
            'atts' => $atts,
            'id' => $id,
        ]
    );
}

/**
 * @param \Breakdance\Elements\Element $element
 * @param PropertiesData $props
 * @return string[]
 */
function getAdditionalClassNames($element, $props)
{

    // this is the PHP equivalent of 'get additionalClasses()' in BuilderElement.vue

    /*
    i said about getAttributes:
    this is dumb as fuck from a performance standpoint
    and should be memoized
    does the same apply here?
     */

    $additionalClasses = $element::additionalClasses() ?: [];

    $twigInstance = Twig::getInstance();

    $processedClassRules = array_map(
        function ($classRule) use ($props, $twigInstance) {

            $value = $twigInstance->runTwig($classRule['template'], $props);

            // why do we need to strip the newline and trim? why would name ever have a newline?
            // i've cargoculted this over from getAttributes

            // After Twig processes it, it can add \n, which causes the if to be true
            $cleanValue = trim(str_replace("\n", '', $value));
            $cleanName = trim($classRule['name']);

            return [
                'name' => $cleanName,
                'include' => $cleanValue,
            ];
        },
        $additionalClasses
    );

    $filteredProcessedClassRules = array_filter($processedClassRules, function ($processedClassRule) {
        return !!$processedClassRule['include'];
    });

    $classNames = array_map(
        function ($filteredProcessedClassRule) {
            return $filteredProcessedClassRule['name'];
        },
        $filteredProcessedClassRules
    );


    /**
     * @var string[] $classNames
     * @psalm-suppress TooManyArguments
     */
    $classNames = apply_filters('breakdance_element_classnames_for_html_class_attribute', $classNames, $element, $props);

    return $classNames;
}

/**
 * @param ElementAttribute $attribute
 * @param PropertiesData $props
 *
 * @return string
 * @see BuilderElementComponent.renderAttributeValue() at BuilderElement.vue
 *
 */
function renderAttributeValue($attribute, $props)
{
    if (array_key_exists('template', $attribute)) {
        $twigInstance = Twig::getInstance();
        return $twigInstance->runTwig($attribute['template'], $props);
    }

    if (array_key_exists('propertyPath', $attribute)) {
        /** @var mixed $propertyValue */
        $propertyValue = readFromArrayByPath(
            $props,
            $attribute['propertyPath'],
            ''
        );

        // Don't render null values as string.
        if (!isset($propertyValue)) return '';

        /** @var string $stringPropertyValue */
        $stringPropertyValue = js_like_cast_to_string($propertyValue);
        return $stringPropertyValue;
    }

    if (array_key_exists('rawValue', $attribute)) {
        return (string) $attribute['rawValue'];
    }

    return '';
}

/**
 * Equivalent of 'get attributes()' in BuilderElement.vue
 *
 * TODO performance: is this slow? Should it be memoized?
 *
 * @param \Breakdance\Elements\Element $element
 * @param PropertiesData $props
 * @return array<string, string>
 */
function getAttributes($element, $props)
{
    /** @var ElementAttribute[] $atts */
    $atts = array_merge(
        $element::attributes() ?: [],
        \Breakdance\Elements\FilteredGets\externalAttributes() ?: []
    );

    $renderedAtts = [];

    foreach ($atts as $att) {
        $value = renderAttributeValue($att, $props);

        // After Twig processes it, it can add \n, which causes the if to be true
        $cleanValue = trim(str_replace("\n", '', $value));
        $cleanName = trim($att['name']);

        // Explicitly check it's not empty. "0" = empty but we still want to render that.
        if ($cleanValue !== '' && $cleanName !== '') {
            $renderedAtts[] = [$cleanName => $cleanValue];
        }
    }

    /**
     * @psalm-suppress MixedArrayAccess
     * @var mixed
     */
    $maybeUserAttSettings = $props['settings']['advanced']['attributes'] ?? null;

    /**
     * @var array{name:string,value:string}[]
     */
    $userAttSettings = is_array($maybeUserAttSettings) ? $maybeUserAttSettings : [];

    $userAtts = array_map(function ($att) {
        if (!isset($att) || !isset($att['name']) || !isset($att['value'])) {
            return [];
        }

        return [$att['name'] => $att['value']];
    }, $userAttSettings);

    // -----

    $atts = array_merge(...$renderedAtts, ...$userAtts);

    return array_filter($atts, function ($att) {
        // PHP considers "0" as falsy.
        return !!$att || $att === "0";
    });
}

/**
 * @param \Breakdance\Elements\Element $element
 * @param PropertiesData $props
 * @return string
 */
function getHtmlTag($element, $props)
{

    if ($element::tagControlPath()) {
        /** @var string|null $tag */
        $tag = readFromArrayByPath($props, $element::tagControlPath());
    } else {
        /**
         * @var string|null $tag
         * @psalm-suppress MixedArrayAccess
         */
        $tag = $props['settings']['advanced']['tag'] ?? null;
    }

    return is_string($tag) && $tag ? $tag : $element::tag();
}

/**
 * @param PropertiesData $props
 * @return string
 */
function getHtmlId($props)
{
    /** @var string $id */
    $id = $props['settings']['advanced']['id'] ?? '';

    return $id;
}

define('BLANK_RENDERED_NODE_DATA', [
    'html' => '',
    'cssRules' => [],
    'dependencies' => [],
    'defaultCss' => [],
]);

/**
 * @param CSSRule[] $cssRules
 * @param string $cssNamespace
 * @return array{filename:string,content: string}
 * or WP_Error?
 */
function writeCssFileFromRules($cssRules, $cssNamespace)
{
    // verify the cssNamespace can be used as a file name or return an error? - i.e. just say alphanumeric only.

    $css = "\n\n" . join("\n\n", $cssRules);

    return [
        'filename' => writeCssFile($css, $cssNamespace),
        'content' => $css
    ];
}

/**
 * @param string $css
 * @param string $cssNamespace
 * @return string // the filename
 * @throws \Exception
 */
function writeCssFile($css, $cssNamespace)
{
    $css = formatCss($css);
    $basename = $cssNamespace . '.css';

    $writeErrorOrFilename = write_file_to_bucket(Consts::BREAKDANCE_FS_BUCKET_CSS, $basename, $css);

    if (!is_fs_error($writeErrorOrFilename)) {
        /** @var string */
        return $writeErrorOrFilename;
    } else {
        /** @var \WP_Error $writeErrorOrFilename */
        throw new \Exception(generate_error_msg_from_fs_wp_error($writeErrorOrFilename));
    }
}

/**
 * @param TreeNode[] $nodes
 * @param int $postId
 * @param boolean $justDoHtmlAndSkipTheCss
 * @param array $ssrParentProperties
 * @param integer | null $repeaterItemNodeId
 * @return RenderedNodes
 */
function _render($nodes, $postId, $justDoHtmlAndSkipTheCss, $ssrParentProperties = [], $repeaterItemNodeId = null)
{
    static $breakpoints = null;
    static $global_settings = null;

    if ($breakpoints === null) {
        /** @var Breakpoint[] $breakpoints */
        $breakpoints = \Breakdance\Config\Breakpoints\get_breakpoints();
    }

    if ($global_settings === null) {
        /** @var array $global_settings */
        $global_settings = get_global_settings_array();
    }

    $acc = BLANK_RENDERED_NODE_DATA;

    foreach ($nodes as $node) {
        $timing = start("renderNode-{$node['id']}");

        $classNameForNode = \Breakdance\Elements\getClassNameForNode(
            $node['data']['type'],
            (string) $node['id'],
            $postId,
            $repeaterItemNodeId
        );

        $appliedClassNames = getAppliedClassNames($node);

        /**
         * @var string[]
         */
        $extraClassNames = [];

        if ($repeaterItemNodeId) {
            // if this is a repeater, also add the class name without repeater
            $extraClassNames[] = \Breakdance\Elements\getClassNameForNode(
                $node['data']['type'],
                (string) $node['id'],
                $postId
            );
        }


        $element = getElementFromNodeType($node['data']['type']);

        $cssTemplate = \Breakdance\Elements\FilteredGets\cssTemplate($element);

        $baseClassName = \Breakdance\Elements\getBaseClassNameForBuilderElement($element);

        $dynPaths = $element::dynamicPropertyPaths();

        /** @var PropertiesData $propsIncludingGlobalSettings */
        $propsIncludingGlobalSettings = $node['data']['properties'];

        // some elements/macros need global settings, such as Typography macro
        /** @var PropertiesData */
        $propsIncludingGlobalSettings['globalSettings'] = $global_settings['settings'] ?? [];

        $propsAndCount = renderDynamicDataInProps($propsIncludingGlobalSettings);

        $tags = [
            'id' => $node['id'],
            'selector' => ".breakdance .{$classNameForNode}",
            'uniqueSlug' => $classNameForNode,
            'postId' => $postId,
            'elementName' => $element::name()
        ];

        $settings = $element::settings() ?: [];

        $sharePropsWithSSRChildren = (bool)($settings['sharePropsWithSSRChildren'] ?? false);

        /** @var PropertiesData */
        $parentProperties = $sharePropsWithSSRChildren ? $node['data']['properties'] : $ssrParentProperties;

        /**
         * @psalm-suppress RedundantConditionGivenDocblockType
         */
        $nodeChildrenCount = isset($node['children']) && is_array($node['children']) ? count($node['children']) : 0;

        /**
         * @psalm-suppress MixedArgument
         * psalm doesn't have recursive types, so we just force
         * the mixed type through
         * @psalm-suppress RedundantConditionGivenDocblockType because somehow $node['children'] can be null, not just a blank array. dafuq? probably nuances of json decoding
         * @var array{children:TreeNode[]} $node
         */
        $renderedChildrenData = $nodeChildrenCount
            ? _render(
                $node['children'],
                $postId,
                $justDoHtmlAndSkipTheCss,
                $parentProperties,
                $repeaterItemNodeId
            )
            : BLANK_RENDERED_NODE_DATA;

        $cssToInlineAsHtml = '';

        if ($propsAndCount['dynamicPropertiesCount'] > 0) {
            /**
             * @psalm-suppress MixedArgument
             */
            $formattedCss = formatCss(
                getCss(
                    $cssTemplate,
                    $propsAndCount['propsWithDynamicDataRenderedAndNonDynamicPropsStripped'],
                    $breakpoints,
                    $tags,
                    $element::propertyPathsToWhitelistInFlatProps(),
                    $nodeChildrenCount
                )
            );

            if (!empty($formattedCss)) {
                $cssToInlineAsHtml = '<style>' . $formattedCss . '</style>';
            }
        }

        /**
         * @psalm-suppress TooManyArguments
         * @psalm-suppress MixedAssignment
         */
        $shouldShowNode = apply_filters('breakdance_render_show_node', true, $node);

        /**
         * @psalm-suppress InvalidArgument
         *
         * The condition only applies to the HTML, since we want the CSS (which will be cached) to be there
         * for the times the condition doesn't hide the element
         */
        $elementHtml = $shouldShowNode
            ? getHtml(
                $node,
                $element,
                $propsAndCount['propsWithDynamicDataRendered'],
                $renderedChildrenData['html'],
                array_merge(
                    [$classNameForNode, $baseClassName],
                    $appliedClassNames,
                    $extraClassNames
                ),
                $tags,
                $ssrParentProperties,
                $repeaterItemNodeId
            )
            : "";

        // ---------------

        /**
         * @psalm-suppress TooManyArguments
         */
        $elementHtml = (string) apply_filters('breakdance_render_element_html', $elementHtml, $node);

        $acc['html'] .= $cssToInlineAsHtml . $elementHtml;

        // Don't render the dependencies for, e.g Swiper's js, if the element isn't rendered
        if ($shouldShowNode) {
            $acc['dependencies'] = dependencies_array_merge_recursive_without_conditions([
                $acc['dependencies'],
                // pass the props with dynamic data rendered so we can use dynamic data values in conditions
                getDependencies(
                    $element,
                    $propsAndCount['propsWithDynamicDataRendered'],
                    $tags
                ),
                $renderedChildrenData['dependencies'],
            ]);
        }

        if ($justDoHtmlAndSkipTheCss === false) {
            /**
             * @psalm-suppress MixedArgument
             */
            $acc['cssRules'] = array_merge(
                $acc['cssRules'],
                /**
                 * we give it propsWithDynamicPropsStrippedOut because we dont want any dynamic data to be
                 * rendered in the cached CSS files. dynamic data should only be rendered in the CSS to inline as HTML
                 */
                [
                    getCss(
                        $cssTemplate,
                        $propsAndCount['propsWithDynamicPropsStrippedOut'],
                        $breakpoints,
                        $tags,
                        $element::propertyPathsToWhitelistInFlatProps(),
                        $nodeChildrenCount
                    ),
                ],
                $renderedChildrenData['cssRules']
            );

            $acc['defaultCss'] = array_merge(
                $acc['defaultCss'],
                [
                    getDefaultCss($element),
                ],
                $renderedChildrenData['defaultCss']
            );
        }

        finish($timing);
    }

    return $acc;
}

/**
 * @param TreeNode $node
 * @return string[]
 */
function getAppliedClassNames($node)
{

    /**
     * @psalm-suppress MixedArrayAccess
     * @var string[]|null
     */
    $maybeClasses = $node['data']['properties']['settings']['advanced']['classes'] ?? null;

    return is_array($maybeClasses) ? $maybeClasses : [];
}

/**
 * @param PropertiesData $propertiesData
 * @param string $value
 * @return boolean
 */
function propertyHasValueAtAnyBreakpoint($propertiesData, $value)
{
    $breakpoints = \Breakdance\Config\Breakpoints\get_breakpoints();

    // data should now be the array with the breakpoints, or for a property that doesnt support breakpoints, the value
    if ($propertiesData === $value) {
        return true;
    }

    if (!is_array($propertiesData)) {
        return false;
    }

    foreach ($breakpoints as $breakpoint) {
        if (array_key_exists($breakpoint['id'], $propertiesData)) {
            if ($value === $propertiesData[$breakpoint['id']]) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Replace any %%TAG_NAME%% with the specified value.
 *
 * @example
 * ```php
 * renderTags('%%TAG_NAME%% and %%FOO%%', [
 *  'tag_name' => 'tag_value',
 *  'foo' => 'bar'
 * ]);
 * ```
 *
 * @param string $template
 * @param array $tags
 * @return string
 */
function renderTags($template, $tags)
{
    // Search for %%TAG%% variables
    $search = array_map(
        function ($key) {
            return "%%" . strtoupper((string) $key) . "%%";
        },
        array_keys($tags)
    );

    // And replace them with the values
    /** @var string[] $replace */
    $replace = array_values($tags);

    return str_replace($search, $replace, $template);
}

/**
 * @param PropertiesData $props
 * @param DynamicPropertyPath[] $dynamicPropertyPaths
 * @return array{unprocessedProps:PropertiesData,propsWithDynamicPropsStrippedOut:PropertiesData,propsWithDynamicDataRendered:PropertiesData,propsWithDynamicDataRenderedAndNonDynamicPropsStripped:PropertiesData,dynamicPropertiesCount:int}
 */
function renderDynamicDataInProps($props)
{
    $dynamicPropertiesCount = 0;

    /**
     * @var PropertiesData
     */
    $propsWithDynamicDataRendered = $props;

    /**
     * @var PropertiesData
     */
    $propsWithDynamicPropsStrippedOut = $props;

    $propsWithDynamicDataRenderedAndNonDynamicPropsStripped = [];

    $propertyIterator = new \RecursiveIteratorIterator(
        new RecursivePropertyIterator((array) $props),
        \RecursiveIteratorIterator::SELF_FIRST
    );

    /** @var mixed $propertyValue */
    foreach ($propertyIterator as $propertyKey => $propertyValue) {
        if (str_ends_with((string) $propertyKey, '_dynamic_meta')) {
            continue;
        }

        if (!empty($propertyValue) && is_string($propertyValue) && str_starts_with($propertyValue, '[breakdance_dynamic')) {

            // get all parent iterator keys from the top down and separate with a . to create the path
            $dynamicPropertyPath = implode('.', array_map(static function ($depth) use ($propertyIterator) {
                return (string) $propertyIterator->getSubIterator($depth)->key();
            }, range(0, $propertyIterator->getDepth())));

            if (str_contains($dynamicPropertyPath, '.breakpoint_')) {
                [$pathBeforeResponsiveBreakpoints] = explode('.breakpoint_', $dynamicPropertyPath, 2);
                /** @var mixed $propertyValueWithAllBreakpoints */
                $propertyValueWithAllBreakpoints = readFromArrayByPath($propsWithDynamicDataRendered, $pathBeforeResponsiveBreakpoints);
                // if this property contains responsive dynamic values
                // all properties for other breakpoints must be included here to
                // avoid specificity conflicts with inlined dynamic values
                assignArrayByPath(
                    $propsWithDynamicDataRenderedAndNonDynamicPropsStripped,
                    $pathBeforeResponsiveBreakpoints,
                    $propertyValueWithAllBreakpoints
                );
            }

            $dynamicPropertiesCount = (int) $dynamicPropertiesCount + 1;
            /**
             * @var mixed $valueWithDynamicDataRendered
             */
            $valueWithDynamicDataRendered = breakdanceDoShortcode($propertyValue);

            assignArrayByPath(
                $propsWithDynamicDataRenderedAndNonDynamicPropsStripped,
                $dynamicPropertyPath,
                $valueWithDynamicDataRendered
            );

            assignArrayByPath(
                $propsWithDynamicDataRendered,
                $dynamicPropertyPath,
                $valueWithDynamicDataRendered
            );

            assignArrayByPath(
                $propsWithDynamicPropsStrippedOut,
                $dynamicPropertyPath,
                null
            );

            if (is_array($valueWithDynamicDataRendered)) {
                // if the dynamic property value is an ImageData array,
                // we often need the sibling properties to determine image
                // size, type etc, so let's include all properties at the same path
                /** @var string|null $valueType */
                $valueType = $valueWithDynamicDataRendered['type'] ?? null;
                if ($valueType === 'image') {
                    [$pathBeforeResponsiveBreakpoints] = explode('.breakpoint_', $dynamicPropertyPath, 2);
                    if (str_ends_with($pathBeforeResponsiveBreakpoints, '.image')) {
                        $parentPath = preg_replace("/\.image$/", '', $pathBeforeResponsiveBreakpoints);
                        /** @var array|null $siblingProperties */
                        $siblingProperties = readFromArrayByPath($propsWithDynamicDataRendered, $parentPath);
                        if ($siblingProperties) {
                            assignArrayByPath(
                                $propsWithDynamicDataRenderedAndNonDynamicPropsStripped,
                                $parentPath,
                                $siblingProperties
                            );
                        }
                    }
                }
            }
        }
    }

    /** @var array{unprocessedProps:PropertiesData,propsWithDynamicPropsStrippedOut:PropertiesData,propsWithDynamicDataRendered:PropertiesData,propsWithDynamicDataRenderedAndNonDynamicPropsStripped:PropertiesData,dynamicPropertiesCount:int} */
    return [
        'unprocessedProps' => $props,
        'propsWithDynamicPropsStrippedOut' => $propsWithDynamicPropsStrippedOut,
        'propsWithDynamicDataRenderedAndNonDynamicPropsStripped' => $propsWithDynamicDataRenderedAndNonDynamicPropsStripped,
        'propsWithDynamicDataRendered' => $propsWithDynamicDataRendered,
        'dynamicPropertiesCount' => $dynamicPropertiesCount,
    ];
}

/**
 * @param int $postId
 * @param int $elementId
 * @return TreeNode|null
 */
function getNodeById($postId, $elementId)
{
    $tree = \Breakdance\Data\get_tree($postId);

    if (!$tree || !array_key_exists('root', $tree)) {
        return null;
    }

    $node = null;
    $treeNodeIterator = new \RecursiveIteratorIterator(
        new RecursiveTreeNodeIterator($tree['root']['children']),
        \RecursiveIteratorIterator::SELF_FIRST
    );
    /**
     * The RecursiveTreeNodeIterator returns TreeNodes but
     * psalm doesn't recognize the TreeNode type here
     * @var array{id: integer} $treeNode
     */
    foreach ($treeNodeIterator as $treeNode) {
        if ($treeNode['id'] === $elementId) {
            /** @var TreeNode $node */
            $node = $treeNode;
            break;
        }
    }

    return $node;
}
