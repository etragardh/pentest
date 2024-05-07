<?php

namespace Breakdance\Integrations;

/**
 * @return string[]
 */
function getElementSlugsThatCanHaveFiltering()
{
    return [
        'EssentialElements\\Postslist',
        'EssentialElements\\PostsLoop',
        "EssentialElements\\Wooproductslist",
        "EssentialElements\\Wooshoppage"
    ];
}


/**
 * @param Control $controls
 * @param array $condition
 * @param int $sectionIndex
 * @return Control
 */
function addConditionToControlSection($controls, $condition, $sectionIndex){
    if (!isset($controls[$sectionIndex])) return $controls;

    /**
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     */
    if (
        isset($controls[$sectionIndex]['options']['condition']) &&
        is_array($controls[$sectionIndex]['options']['condition']) &&
        count($controls[$sectionIndex]['options']['condition']) > 0
    ) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedArrayAssignment
         */
        $controls[$sectionIndex]['options']['condition']['0'][] = $condition;
    } else {
        /**
         * @psalm-suppress MixedArgument
         * @psalm-suppress PossiblyInvalidArrayOffset
         * @psalm-suppress MixedArrayAssignment
         * @psalm-suppress MixedArrayAccess
         */
        $controls[$sectionIndex]['options'] = array_merge(
            $controls[$sectionIndex]['options'] ?? [],
            ['condition' => [[$condition]]]
        );
    }

    /** @var Control $controls */
    $controls = $controls;

    return $controls;
}
