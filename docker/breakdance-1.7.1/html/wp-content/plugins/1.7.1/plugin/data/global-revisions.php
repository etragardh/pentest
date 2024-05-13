<?php

namespace Breakdance\Data\GlobalRevisions;

use DateInterval;
use DateTimeImmutable;

use function Breakdance\Data\delete_global_option;
use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;

const BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP = 15;

const BREAKDANCE_REVISION_TYPE_GLOBAL_SETTINGS = 'global_settings';
const BREAKDANCE_REVISION_TYPE_SELECTORS = 'selectors';


add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_retrieve_global_settings_revisions',
        '\Breakdance\Data\GlobalRevisions\ajax_retrieve_global_settings_revisions',
        'edit',
        false,
    );
    \Breakdance\AJAX\register_handler(
        'breakdance_retrieve_selectors_revisions',
        '\Breakdance\Data\GlobalRevisions\ajax_retrieve_selectors_revisions',
        'edit',
        false,
    );
});

/**
 * @return array
 */
function ajax_retrieve_global_settings_revisions()
{
    $current_revisions_list = load_revisions_list(BREAKDANCE_REVISION_TYPE_GLOBAL_SETTINGS);

    return array_map(
        '\Breakdance\Data\GlobalRevisions\format_global_settings_revision_for_ajax',
        $current_revisions_list
    );
}

/**
 * @return array
 */
function ajax_retrieve_selectors_revisions()
{
    $current_revisions_list = load_revisions_list(BREAKDANCE_REVISION_TYPE_SELECTORS);

    return array_map('\Breakdance\Data\GlobalRevisions\format_selectors_revision_for_ajax', $current_revisions_list);
}


/**
 * @param RevisionFromDb $revision
 * @return RevisionAjaxObj
 */
function format_global_settings_revision_for_ajax($revision)
{
    return array_merge(
        format_revision_for_ajax($revision), [
            'globalSettings' => get_revision_data($revision['id'], BREAKDANCE_REVISION_TYPE_GLOBAL_SETTINGS)
        ]
    );
}

/**
 * @param RevisionFromDb $revision
 * @return RevisionAjaxObj
 */
function format_selectors_revision_for_ajax($revision)
{
    return array_merge(
        format_revision_for_ajax($revision), [
            'selectors' => get_revision_data($revision['id'], BREAKDANCE_REVISION_TYPE_SELECTORS)
        ]
    );
}

/**
 * @param RevisionFromDb $revision
 * @return RevisionAjaxObj
 */
function format_revision_for_ajax($revision)
{
    return [
        'id' => $revision['id'],
        'date' => sprintf(
            '%s %s',
            (string)wp_date((string)get_option('date_format'), $revision['timestamp']),
            (string)wp_date((string)get_option('time_format'), $revision['timestamp'])
        ),
        'author' => get_the_author_meta('display_name', $revision['author_user_id']),
    ];
}

/**
 * @param string $revision_id
 * @param string $revision_type
 * @return false|RevisionData
 */
function get_revision_data($revision_id, $revision_type)
{
    /** @var string|false $option_data */
    $option_data = get_global_option(format_revision_global_option_key($revision_id, $revision_type));

    if ($option_data === false) {
        return $option_data;
    }

    /** @var RevisionData $data */
    $data = json_decode($option_data);

    return $data;
}

/**
 * @param string $revision_type
 * @return RevisionFromDb[]
 */
function load_revisions_list($revision_type)
{
    /** @var false|RevisionFromDb[] $current_revisions_list */
    $current_revisions_list = get_global_option(format_revisions_list_global_option_key($revision_type));

    if ($current_revisions_list === false) {
        return [];
    } else {
        return $current_revisions_list;
    }
}

/**
 * @param string $revision_id
 * @param string $revision_type
 * @return string
 */
function format_revision_global_option_key($revision_id, $revision_type)
{
    return sprintf('%s_revision_%s', $revision_type, $revision_id);
}

/**
 * @param string $revision_type
 * @return string
 */
function format_revisions_list_global_option_key($revision_type)
{
    return sprintf('revisions_list_%s_json_string', $revision_type);
}

/**
 * @param string $revision_data_json_string
 * @param string $revision_type
 * @return void
 */
function add_new_revision($revision_data_json_string, $revision_type)
{
    /**
     * This is vulnerable to a "lost update" problem, but using DB table locks (SELECT ... FOR UPDATE)
     * would overcomplicate this code because WP doesn't provide a toolset for dealing with global option locks.
     */
    $current_revisions_list = load_revisions_list($revision_type);

    do {
        $id = wp_generate_uuid4();
        $revision_key = format_revision_global_option_key($id, $revision_type);
    } while (false !== get_global_option($revision_key));

    set_global_option($revision_key, $revision_data_json_string);

    $new_revision = [
        'timestamp' => time(),
        'id' => $id,
        'author_user_id' => get_current_user_id()
    ];

    array_unshift($current_revisions_list, $new_revision);

    $current_revisions_list = maybe_remove_extra_revisions($current_revisions_list, $revision_type);

    set_global_option(format_revisions_list_global_option_key($revision_type), $current_revisions_list);
}

/**
 * Keeps BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP of the latest revisions plus one per each day for the past month.
 *
 * @param RevisionFromDb[] $revisions_list
 * @param string $revision_type
 * @return RevisionFromDb[]
 */
function maybe_remove_extra_revisions($revisions_list, $revision_type)
{
    $current_revisions_list_size = sizeof($revisions_list);
    if ($current_revisions_list_size > BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP) {
        /** @var RevisionFromDb[] $revisions_to_delete */
        $revisions_to_delete = array_splice(
            $revisions_list,
            BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP - $current_revisions_list_size
        );

        $now_datetime = new DateTimeImmutable();
        $one_day_interval = new DateInterval('P1D');
        $one_month_interval = new DateInterval('P30D'); // to avoid different month days number related issues
        $month_ago_datetime = $now_datetime->sub($one_month_interval);

        $prev_date_to_compare_with = new DateTimeImmutable(
            sprintf('@%s', $revisions_list[BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP - 1]['timestamp'])
        );
        foreach ($revisions_to_delete as $revision_to_delete) {
            $revision_datetime = new DateTimeImmutable(sprintf('@%s', $revision_to_delete['timestamp']));

            $prev_minus_one_day_datetime = $prev_date_to_compare_with->sub($one_day_interval);

            if ($revision_datetime > $prev_minus_one_day_datetime || $revision_datetime < $month_ago_datetime) {
                delete_global_option(
                    format_revision_global_option_key($revision_to_delete['id'], $revision_type)
                );
            } else {
                $revisions_list[] = $revision_to_delete;

                $prev_date_to_compare_with = $revision_datetime;
            }
        }
    }

    return $revisions_list;
}
