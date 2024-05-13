<?php

namespace Breakdance\Data;

/**
 * @param string $field_name
 * @return void
 */
function delete_global_option($field_name)
{
    delete_option('breakdance_' . $field_name);
}


/**
 * @param string $field_name
 * @param mixed $value
 * @return void
 */
function set_global_option($field_name, $value)
{
    update_option('breakdance_' . $field_name, encode_before_writing_to_wp($value, false), false);
    do_action("breakdance_option_updated_{$field_name}", $value);
}

/**
 * @param string $field_name
 * @param false|string $array_key - if the value stored in $field_name is an array, rather than return the whole array, specifying $key_name will return the value of a certain key
 * @return mixed|false
 */
function get_global_option($field_name, $array_key = false)
{
    /** @var mixed $value */
    $value = get_option('breakdance_' . $field_name);

    /** @var mixed $decoded_value */
    $decoded_value = decode_after_reading_from_wp($value);

    if ($array_key) {
        return pick_from_decoded_value($decoded_value, $array_key);
    }

    return $decoded_value;
}



/**
 * @return string[]
 * returns an array of all the names of options beginning with breakdance_ found in the wp_options table
 */
function get_all_option_names()
{

    global $wpdb;

    /**
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress MixedPropertyFetch
     * @psalm-suppress UndefinedConstant
     * @var array{option_name:string}[] */
    $results = $wpdb->get_results("SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE 'breakdance\_%'", ARRAY_A); /* thanks to my girlfriend @co2nie for the sql. she's basically a model. not like a financial model. an instagram model. */

    return array_map(function ($result) {
        $raw_option_name = $result['option_name'];
        $option_name = substr($raw_option_name, strlen('breakdance_')); // remove breakdance_ prefix, since Breakdance\Data API autoprefixes with Breakdance
        return (string) $option_name;
    }, $results);
}

/**
 * Returns array of global options that should be included for export
 *
 * @return array<string, string>
 */
function get_global_options_for_export()
{
    global $wpdb;

    $excluded_options = [
        'breakdance_license_key',
        'breakdance_trial_valid_license_key_was_entered_at_some_point',
        'breakdance_trial_expiration',
        'breakdance_css_cache',
        'breakdance_plugin_has_already_been_activated',
        'breakdance_global_css_cache',
        'breakdance_dependency_cache',
        'breakdance_uuid',
    ];
    // for each excluded option, we need to add '%s' to the query string so that we can use prepared statements
    $excluded_options_placeholders = implode(',', array_fill(0, count($excluded_options), '%s'));

    /**
     * @psalm-suppress MixedPropertyFetch
     */
    $retrieve_options_query = "SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE option_name LIKE 'breakdance_%' AND option_name NOT IN ({$excluded_options_placeholders})";

    /**
     * @psalm-suppress MixedMethodCall
     * @var string $retrieve_options_statement
     */
    $retrieve_options_statement = $wpdb->prepare(
        $retrieve_options_query,
        $excluded_options
    );

    /**
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress UndefinedConstant
     * @var array<string, string>[] $options_query_result_array
     */
    $options_query_result_array = $wpdb->get_results($retrieve_options_statement, ARRAY_A) ?? [];

    $options_formatted_for_export = [];
    foreach ($options_query_result_array as $option) {
        if (isset($option['option_name'])) {
            $option_name_without_breakdance_prefix = substr($option['option_name'], strlen('breakdance_')) ?: $option['option_name'];
            $options_formatted_for_export[$option_name_without_breakdance_prefix] = $option['option_value'] ?? '';
        }
    }

    return $options_formatted_for_export;
}

/**
 * Imports global options from $options array and returns true on success
 *
 * @param array<string, string>[] $options
 * @return bool
 */
function import_global_options($options)
{
    global $wpdb;

    /** @var string[] $formatted_options */
    $formatted_options = [];
    foreach ($options as $option_name => $option_value) {
        /**
         * @psalm-suppress MixedMethodCall
         * @var string $option_name_with_breakdance_prefix_prepared_for_query
         */
        // IMPORTANT: numbered arguments *must* be used here (%1\$s). https://github.com/soflyy/breakdance/pull/4601
        $option_name_with_breakdance_prefix_prepared_for_query = $wpdb->prepare("('breakdance_%1\$s', '%2\$s')", [$option_name, $option_value]);

        $formatted_options[] = $option_name_with_breakdance_prefix_prepared_for_query;
    }

    $upsert_query_values = implode(',', $formatted_options);

    /**
     * Because option_name has a unique index,
     * we can use ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)
     * to perform an "upsert" query
     *
     * @psalm-suppress MixedPropertyFetch
     */
    $upsert_options_query = "INSERT INTO {$wpdb->prefix}options (option_name, option_value) VALUES $upsert_query_values ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)";

    /**
     * @psalm-suppress MixedMethodCall
     */
    $wpdb->query($upsert_options_query);

    /**
     * @psalm-suppress MixedPropertyFetch
     */
    $queryHasError = $wpdb->last_error !== '';
    return !$queryHasError;
}

