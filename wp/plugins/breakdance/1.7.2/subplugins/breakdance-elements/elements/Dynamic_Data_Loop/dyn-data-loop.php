<?php

function getBlockForLoopIndex2($propertiesData, $loopIndex)
{

    if ($propertiesData['content']['repeated_block']['advanced']['static_items'] ?? false) {
        $staticItemBlock = getBlockIdForLoopIndex2($propertiesData['content']['repeated_block']['advanced']['static_items'], $loopIndex);

        if ($staticItemBlock) {
            return [
                'type' => 'static',
                'id' => $staticItemBlock,
            ];
        }
    }

    if ($propertiesData['content']['repeated_block']['advanced']['alternates'] ?? false) {
        $alternateBlock = getBlockIdForLoopIndex2($propertiesData['content']['repeated_block']['advanced']['alternates'], $loopIndex);

        if ($alternateBlock) {
            return [
                'type' => 'alternate',
                'id' => $alternateBlock,
            ];
        }
    }

    $blockId = $propertiesData['content']['repeated_block']['global_block'] ?? false;

    return [
        'type' => 'default',
        'id' => $blockId,
    ];
}

/**
 * @param array{repeat?:boolean,global_block?:int,position?:int,frequency?:int}[]
 * @param int $loopIndex
 * @return false|int
 */
function getBlockIdForLoopIndex2($blocksPropertiesData, $loopIndex)
{

    $blockId = false;

    foreach ($blocksPropertiesData as $alternate) {

        $position = $alternate['position'] ?? false;
        $global_block = $alternate['global_block'] ?? false;
        $frequency = $alternate['frequency'] ?? $position;

        if ($frequency <= 1) {
            /*
            frequency of 1 or less makes no sense, and will timeout the server when using static item position because it'll cause
            the posts loop to never finish since it'll just keep outputting static items at every position - i.e. an infinite loop
            */
            $frequency = 10000;
        }

        if ($position === $loopIndex) {
            $blockId = $global_block;
            break;
        }

        if ($alternate['repeat'] && $loopIndex > $position) {

            $distanceFromStartingPosition = $loopIndex - $position;

            if ($distanceFromStartingPosition % $frequency === 0) {
                $blockId = $global_block;
                break;
            }
        }
    }

    return $blockId ? $blockId : false;
}
