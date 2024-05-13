<?php

namespace Breakdance\DynamicData;

/**
 * @param string | null $postType
 * @return DynamicField[]
 */
function get_dynamic_fields_for_builder($postType)
{
    return DynamicDataController::getInstance()->getFieldsForPostType($postType);
}
