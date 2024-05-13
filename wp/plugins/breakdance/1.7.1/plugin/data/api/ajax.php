<?php

namespace Breakdance\Data;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_retrieve_arbitrary',
        'Breakdance\Data\ajax_retrieve',
        'edit',
        false,
        ['args' => ['key' => FILTER_SANITIZE_SPECIAL_CHARS]]
    );
    \Breakdance\AJAX\register_handler(
        'breakdance_save_arbitrary',
        'Breakdance\Data\ajax_save',
        'edit',
        false,
        ['args' => ['key' => FILTER_SANITIZE_SPECIAL_CHARS, 'data' => FILTER_UNSAFE_RAW]]
    );
});


/**
 * @param string $key
 * @return array{data:mixed}
 */
function ajax_retrieve($key)
{
    /**
     * @psalm-suppress MixedAssignment
     */
    $data = get_global_option($key);
    // Some data can be json_stringified when saved from the builder, and normal when saved from PHP directly
    $data = is_string($data) ? $data : json_encode($data);

    return [
        'data' => $data === false ? "false" : $data,
    ];
}

/**
 * @param string $key
 * @param string $data
 * @return void
 */
function ajax_save($key, $data)
{
    // data is JSON.stringifiy'd...
    // let's leave it that way, right?
    // we can JSON decode it in the JS

    set_global_option($key, $data);

}
