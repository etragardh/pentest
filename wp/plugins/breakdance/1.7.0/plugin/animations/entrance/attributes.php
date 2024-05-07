<?php

namespace Breakdance\Animations\Entrance;

// ADD HTML ATTRIBUTES TO ALL ELEMENTS
add_filter('breakdance_element_attributes', '\Breakdance\Animations\Entrance\addAttributes', 100, 1);

/**
 * @param  ElementAttribute[] $attributes
 *
 * @return array
 */
function addAttributes($attributes)
{
    $attributes[] = [
        "name" => "data-entrance",
        "propertyPath" => "settings.animations.entrance_animation.animation_type",
    ];

    return $attributes;
}

