<?php

namespace Breakdance\Themeless\Rules;

define('TEMPLATE_PRIORITY_CATCH_ALL', 1);
define('TEMPLATE_PRIORITY_ALL_ARCHIVE_OR_ALL_SINGLE', 10);
define('TEMPLATE_PRIORITY_SPECIFIC_SINGLE', 20);
define('TEMPLATE_PRIORITY_SPECIFIC_ARCHIVE', 20);

// All possible operands for the rules
// NOTE: also present in conditions.ts
define('OPERAND_IS', 'is');
define('OPERAND_IS_NOT', 'is not');
define('OPERAND_ONE_OF', 'is one of');
define('OPERAND_ALL_OF', 'is all of');
define('OPERAND_NONE_OF', 'is none of');
define('OPERAND_BEFORE', 'is before');
define('OPERAND_AFTER', 'is after');
define('OPERAND_GREATER_THAN', 'is greater than');
define('OPERAND_LESS_THAN', 'is less than');
define('OPERAND_CONTAINS', 'contains');
define('OPERAND_NOT_CONTAIN', 'does not contain');
define('OPERAND_IS_EMPTY', 'is empty');
define('OPERAND_IS_NOT_EMPTY', 'is not empty');

define('TEMPLATE_POSTS_LIMIT', 10);
