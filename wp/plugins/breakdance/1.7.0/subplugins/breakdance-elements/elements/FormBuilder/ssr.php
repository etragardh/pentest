<?php
/**
 * @var array $propertiesData
 */

$fields = $propertiesData['content']['form']['fields'];
\Breakdance\Forms\Render\renderForm($fields, $propertiesData, 'custom');
