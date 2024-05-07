<?php

namespace Breakdance\Animations\Scrolling;

// ADD HTML ATTRIBUTES TO ALL ELEMENTS
add_filter("breakdance_element_attributes", "\Breakdance\Animations\Scrolling\addAttributes", 100, 1);

/**
 * @param  ElementAttribute[] $attributes
 *
 * @return array
 */
function addAttributes($attributes)
{
    $attributes[] = [
        "name" => "data-parallax",
        "template" => "{{ settings.animations.scrolling_animation.enabled ? 'true' }}",
    ];

    return $attributes;
}
