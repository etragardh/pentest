<?php

namespace Breakdance\Icons;

use function Breakdance\Data\delete_global_option;
use function Breakdance\Data\get_global_option;

/**
 * @return \wpdb
 * @psalm-suppress MixedInferredReturnType
 */
function wpdb()
{
    global $wpdb;

    /**
     * @psalm-suppress MixedReturnStatement
     */
    return $wpdb;
}

function handle_icons_logic_on_plugin_install_aka_soft_reset()
{
    // Soft Reset: "Reset your ... Icon Sets to factory defaults."
    clear_icon_sets_table();
    clear_icons_table();
    install_stock_icons_to_db();
}

/**
 * @param array $options
 * @psalm-param array{search_term: string|null, icon_set_slug: string|null, offset: int|null, suggestions: string[]|null} $options
 * @return array
 */
function find_icons($options)
{
    $limit = 100;
    $offset = isset($options['offset']) ? (int)$options['offset'] : 0;

    $conditions = [];

    if (isset($options['icon_set_slug'])) {
        $icon_set_slug = trim($options['icon_set_slug']);
        if (strlen($icon_set_slug) > 0) {
            $conditions[] = wpdb()->prepare('`icon_set_slug` = %s', $icon_set_slug);
        }
    }

    if (isset($options['search_term'])) {
        $search_term = trim($options['search_term']);
        if (strlen($search_term) > 0) {
            $search_term = "%{$search_term}%";
            $conditions[] = wpdb()->prepare('(`slug` LIKE %s OR `name` LIKE %s)', $search_term, $search_term);
        }
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    if (isset($options['suggestions']) && is_array($options['suggestions'])) {
        foreach ($options['suggestions'] as $suggestion_term) {
            $suggestion_term = trim($suggestion_term);
            if (strlen($suggestion_term) > 0) {
                $suggestion_term = "%{$suggestion_term}%";
                $conditions[] = wpdb()->prepare('(`slug` LIKE %s)', $suggestion_term);
            }
        }
    }

    $conditions_sql = empty($conditions) ? '' : sprintf(' WHERE %s ', join(' AND ', $conditions));

    $raw_sql = <<<SQL
    SELECT * FROM `%s` %s ORDER BY `name` ASC LIMIT %u OFFSET %u;
    SQL;

    $sql = sprintf($raw_sql, get_icons_table_full_name(), $conditions_sql, $limit, $offset);

    /**
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedArgument
     */
    $results = wpdb()->get_results($sql, ARRAY_A);

    if ($results === null) {
        $results = [];
    }

    /**
     * @psalm-suppress PossiblyInvalidArgument
     */
    return array_map('\Breakdance\Icons\map_db_icon_to_webapp_icon', $results);
}

/**
 * @return array
 */
function get_icon_sets()
{
    /**
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedArgument
     */
    $results = wpdb()->get_results(
        sprintf('SELECT * FROM `%s` ORDER BY `name` ASC;', get_icon_sets_table_full_name()),
        ARRAY_A
    );

    if (!is_array($results)) {
        $results = [];
    }

    return $results;
}

/**
 * @param array $uploaded_icons
 * @psalm-param list<array{name: string, slug: string, svgCode: string}> $uploaded_icons
 * @param array $icon_set_to_use
 * @psalm-param array{name: string, slug: string} $icon_set_to_use
 * @return void
 */
function upload_icons($uploaded_icons, $icon_set_to_use)
{
    $generator_fn = function () use ($uploaded_icons, $icon_set_to_use): \Generator {
        foreach ($uploaded_icons as $icon) {
            yield map_webapp_icon_to_db_icon($icon, $icon_set_to_use['slug']);
        }
    };

    create_icon_set($icon_set_to_use);
    create_icons_from_iterable($generator_fn());
}

function install_stock_icons_to_db()
{
    $stock_icons = read_icons_from_stock_icons_file();

    /**
     * @var string[] $icon_set_slugs
     */
    $icon_set_slugs = [];

    $generator_fn = function () use ($stock_icons, &$icon_set_slugs): \Generator {
        foreach ($stock_icons as $icon) {
            /**
             * @psalm-suppress MixedArgument
             */
            if (!in_array($icon['icon_set_slug'], $icon_set_slugs)) {
                $icon_set_slugs[] = $icon['icon_set_slug'];
            }

            yield $icon;
        }
    };

    create_icons_from_iterable($generator_fn());

    /**
     * @psalm-suppress MixedAssignment
     */
    foreach ($icon_set_slugs as $icon_set_slug) {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         */
        create_icon_set([
            // For stock icons, slug is also a name
            'name' => $icon_set_slug,
            'slug' => $icon_set_slug,
        ]);
    }
}

/**
 * @return string
 */
function get_icons_table_full_name()
{
    return wpdb()->prefix . 'breakdance_icons';
}

/**
 * @return string
 */
function get_icon_sets_table_full_name()
{
    return wpdb()->prefix . 'breakdance_icon_sets';
}

/**
 * @param array $icon_set_definition
 * @psalm-param array{name: string, slug: string} $icon_set_definition
 *
 * @return void
 */
function create_icon_set($icon_set_definition)
{
    wpdb()->replace(
        get_icon_sets_table_full_name(),
        [
            'name' => $icon_set_definition['name'],
            'slug' => $icon_set_definition['slug'],
        ]
    );
}

/**
 * @param string $icon_set_slug
 * @return void
 */
function delete_icon_set($icon_set_slug)
{
    wpdb()->delete(get_icon_sets_table_full_name(), [
        'slug' => $icon_set_slug,
    ]);
    wpdb()->delete(get_icons_table_full_name(), [
        'icon_set_slug' => $icon_set_slug,
    ]);
}

/**
 * @param array $icons
 * @psalm-param list<array{name: string, slug: string, icon_set_slug: string, svg_code: string}> $icons
 * @return void
 */
function insert_icons_batch(array $icons)
{
    $sql_query = sprintf(
        'INSERT INTO `%s` (`name`, `slug`, `icon_set_slug`, `svg_code`)
        VALUES %s 
        ON DUPLICATE KEY UPDATE `svg_code` = VALUES(`svg_code`), `name` = VALUES(`name`)',
        get_icons_table_full_name(),
        join(
            ',',
            array_map(function ($icon) {
                return wpdb()->prepare(
                    '(%s,%s,%s,%s)',
                    trim($icon['name']),
                    trim($icon['slug']),
                    trim($icon['icon_set_slug']),
                    trim($icon['svg_code'])
                );
            }, $icons)
        )
    );

    wpdb()->query($sql_query);
}

/**
 * @psalm-param iterable<int, array{name: string, slug: string, icon_set_slug: string, svg_code: string}> $icons
 * @return void
 */
function create_icons_from_iterable(iterable $icons)
{
    /** @psalm-var list<array{name: string, slug: string, icon_set_slug: string, svg_code: string}> $buffer */
    $buffer = [];

    foreach ($icons as $icon) {
        $buffer[] = $icon;

        if (sizeof($buffer) >= 100) {
            insert_icons_batch($buffer);
            $buffer = [];
        }
    }

    if (sizeof($buffer) > 0) {
        insert_icons_batch($buffer);
    }
}

function clear_icons_table()
{
    wpdb()->query(sprintf('DELETE FROM `%s`;', get_icons_table_full_name()));
}

function clear_icon_sets_table()
{
    wpdb()->query(sprintf('DELETE FROM `%s`;', get_icon_sets_table_full_name()));
}

function migrate_icons_from_wp_option_to_db_table()
{
    /**
     * @psalm-suppress MixedAssignment
     */
    $icon_sets_to_migrate = json_decode((string) get_global_option('icons'), true);

    if (!is_array($icon_sets_to_migrate) || sizeof($icon_sets_to_migrate) === 0) {
        return;
    }

    $generator_fn = function () use ($icon_sets_to_migrate): \Generator {
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($icon_sets_to_migrate as $icon_set) {
            if (!isset($icon_set['name'], $icon_set['slug'], $icon_set['icons']) || !is_array($icon_set['icons'])) {
                continue;
            }

            /**
             * @psalm-suppress MixedArgument
             */
            create_icon_set($icon_set);

            /**
             * @psalm-suppress MixedArrayAccess
             * @psalm-suppress MixedAssignment
             */
            foreach ($icon_set['icons'] as $icon) {
                if (!isset($icon['name'], $icon['slug'], $icon['svgCode'])) {
                    continue;
                }

                /**
                 * @psalm-suppress MixedArrayAccess
                 */
                yield [
                    'name' => $icon['name'],
                    'slug' => $icon['slug'],
                    'icon_set_slug' => $icon_set['slug'],
                    'svg_code' => $icon['svgCode'],
                ];
            }
        }
    };

    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    create_icons_from_iterable($generator_fn());
}

/**
 * @param array $webapp_icon
 * @param string $icon_set_slug
 * @return array
 * @psalm-param array{name: string, slug: string, svgCode: string} $webapp_icon
 * @psalm-return array{name: string, slug: string, svg_code: string, icon_set_slug: string}
 */
function map_webapp_icon_to_db_icon($webapp_icon, $icon_set_slug)
{
    return [
        'name' => $webapp_icon['name'],
        'slug' => $webapp_icon['slug'],
        'icon_set_slug' => $icon_set_slug,
        'svg_code' => $webapp_icon['svgCode'],
    ];
}

/**
 * @param $icon_from_db
 * @return array
 * @psalm-param array{name: string, slug: string, svg_code: string, icon_set_slug: string} $icon_from_db
 * @psalm-return array{name: string, slug: string, svgCode: string, iconSetSlug: string} $webapp_icon
 */
function map_db_icon_to_webapp_icon($icon_from_db)
{
    return [
        'slug' => $icon_from_db['slug'],
        'name' => $icon_from_db['name'],
        'svgCode' => $icon_from_db['svg_code'],
        'iconSetSlug' => $icon_from_db['icon_set_slug'],
    ];
}

/**
 * @return string
 */
function get_stock_icons_filepath()
{
    return dirname(__FILE__) . '/stock-icons.csv';
}

///**
// * Uncomment and call this when you need to export current icons to stock icons file
// * @param iterable $icons
// * @return bool
// */
//function export_icons_to_stock_icons_file(iterable $icons)
//{
//    $file_handle = fopen(get_stock_icons_filepath(), 'w');
//    if ($file_handle === false) {
//        return false;
//    }
//
//    foreach ($icons as $icon) {
//        fputcsv(
//            $file_handle, [
//                $icon['slug'],
//                $icon['name'],
//                $icon['icon_set_slug'],
//                $icon['svg_code']
//            ]
//        );
//    }
//
//    fclose($file_handle);
//
//    return true;
//}

/**
 * @psalm-suppress MoreSpecificReturnType
 * @return \Generator<int, array{name: string, slug: string, svg_code: string, icon_set_slug: string}>
 */
function read_icons_from_stock_icons_file(): \Generator
{
    ini_set('auto_detect_line_endings', 'true');

    $file_handle = fopen(get_stock_icons_filepath(), 'r');

    if ($file_handle === false) {
        return;
    }

    try {
        while (($data = fgetcsv($file_handle)) !== false) {
            if (!is_array($data)) {
                continue;
            }

            // A blank line in a CSV file will be returned as an array comprising a single null
            // field, and will not be treated as an error.
            if (sizeof($data) === 1 && $data[array_key_first($data)] === null) {
                continue;
            }

            $icon = [];
            [
                $icon['slug'],
                $icon['name'],
                $icon['icon_set_slug'],
                $icon['svg_code']
            ] = $data;

            if (empty($icon['slug']) || empty($icon['name']) || empty($icon['icon_set_slug']) || empty($icon['svg_code'])) {
                continue;
            }

            yield $icon;
        }
    } finally {
        fclose($file_handle);
    }
}
