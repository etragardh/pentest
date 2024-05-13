<?php

namespace Breakdance\DynamicData;

/**
 * @return Control[]
 */
function getAdvancedControlsForPipe()
{
    if (strpos(get_site_url(), 'breakdance.com') !== false) {
        return [
            \Breakdance\Elements\control(
                'process_value',
                'Process Value (ALPHA)',
                [
                    'placeholder' => 'return $value;',
                    'codeOptions' => ['language' => 'x-php', 'autofillOnEmpty' => 'return $value;PLACECURSORHERE'],
                    'type' => "code",
                    'layout' => 'vertical'
                ]
            )
        ];
    } else {
        return [];
    }
}

/**
 * @param string|array $v
 * @param string $code
 * @return string|array
 */
function pipeValueThroughProcessValueReturn($v, $code)
{
    try {
        /** @var string|array */
        $result = eval('$value = $v; ' . $code);
        return $result;
    } catch (\Throwable $e) {
        return $v;
    }
}
