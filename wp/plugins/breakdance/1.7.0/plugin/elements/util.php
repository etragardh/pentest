<?php

namespace Breakdance\Elements;

/**
 * @param Control $control
 * @return Control
 */
function removeHoverFromControlAndChildren($control) {
    $control['enableHover'] = false;

    $control['children'] = array_map(
        /**
         * @param Control $control
         */
        function($control) {
            return removeHoverFromControlAndChildren($control);
        },
        $control['children']
    );

    return $control;
}

/**
 * @param Control[] $sections
 * @return Control[]
 */
function makeSectionsPopouts($sections) {
    return array_map(
        /**
         * @param Control $section
        */
        function($section) {
            /** @psalm-suppress MixedArrayAssignment */
            $section['options']['sectionOptions']['type'] = 'popout';
            return $section;
        },
        $sections
    );
}

/**
 * @param string $message
 * @return string
 */
function getSsrErrorMessage($message){
    return <<<HTML
<div class="breakdance-empty-ssr-message breakdance-empty-ssr-message-error" style="background: #fef2f2; padding: 12px; margin: 12px 0;">
    {$message}
</div>
HTML;
}

/**
 * Mirrored in BuilderElement.vue
 *
 * @param array|false $settings
 * @return boolean
 */
function hasRequiredPluginsAndTheyAreAvailable($settings){
    /** @var string[]|false $requiredPlugins */
    $requiredPlugins = $settings['requiredPlugins'] ?? false;

    if (!$requiredPlugins){
        return true;
    }


    $availablePlugins = \Breakdance\Data\getAvailablePlugins();

    $requiredPluginsAreActiveCount = count(
        array_filter(
            $requiredPlugins,
            fn($requiredPlugin) => in_array($requiredPlugin, $availablePlugins, true)
        )
    );

    return $requiredPluginsAreActiveCount === count($requiredPlugins);
}

/**
 * @param string[] $requirePlugins
 * @param string $elementName
 * @return string
 */
function getRequiredPluginsNotActiveSsrMessage($requirePlugins, $elementName){
    $joinedRequirePlugins = implode(', ', $requirePlugins);
    $pluginOrPlugins = $requirePlugins > 1 ? "plugins" : "plugin";

    return getSsrErrorMessage(
        "The \"{$elementName}\" element requires the {$pluginOrPlugins} \"{$joinedRequirePlugins}\" in order to work. They're either inactive or not installed."
    );
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return bool
 */
function isElementASection($element)
{
    /** @psalm-suppress InvalidStringClass */
    return $element::slug() === 'EssentialElements\\Section';
}
