<?php

namespace Breakdance\Framework\Database\Schema;

/**
 * @param string $table_name
 * @param string $columns_sql
 * @return void
 */
function try_to_create_or_update_table_schema($table_name, $columns_sql)
{
    /**
     * @var \wpdb $wpdb
     */
    global $wpdb;

    try {
        /**
         * @psalm-suppress MixedMethodCall
         */
        $charset_collate = (string) $wpdb->get_charset_collate();


        /**
         * @psalm-suppress MixedMethodCall
         * @psalm-suppress MixedPropertyFetch
         */
        $table_name_prefixed = ((string) $wpdb->prefix) . $table_name;

        $sql = "CREATE TABLE $table_name_prefixed (
            $columns_sql
        ) $charset_collate;";


        if (!function_exists('dbDelta')) {
            /**
             * @psalm-suppress UnresolvableInclude
             * @psalm-suppress UndefinedConstant
             */
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta($sql);
    } catch (\Exception $exception) {
        error_log((string) $exception);
    }
}
