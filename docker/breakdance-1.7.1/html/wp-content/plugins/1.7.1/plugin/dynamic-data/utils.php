<?php

namespace Breakdance\DynamicData;

use Breakdance\Themeless\ThemelessController;
use function Breakdance\Themeless\getTemplateById;

/**
 * @param array $arr
 * @param null|string $text
 * @param null|string $value
 * @return DynamicDropdown[]
 */
function format_for_dropdown($arr, $text = '', $value = '')
{
    return array_map(
        /**
         * @param array $field
         * @return DynamicDropdown
         */
        function ($field) use ($value, $text) {
            /** @var string */
            $text = empty($text) ? $field : $field[$text];
            /** @var string */
            $value = empty($value) ? $field : $field[$value];

            return [ 'text' => $text, 'value' => $value ];
        },
        $arr
    );
}

/**
 * Get the post type for the current post or template
 * @return string|null
 */
function get_dynamic_data_post_type()
{
    $id = (int) ($_POST['id'] ?? null);

    $templates = ThemelessController::getInstance()->templates;
    $template = getTemplateById($templates, $id);

    // If we are not in a template we need to discover what is the post type from the actual post.
    if (!$template) {
        return get_post_type($id) ?: null;
    }

    return $template['settings']['type'] ?? null;
}

/**
 * @return DynamicDropdown[]
 */
function get_taxonomies_for_builder_post()
{
    $postType = get_dynamic_data_post_type();

    if (!$postType) {
        return [];
    }

    if (in_array($postType, (array) BREAKDANCE_DYNAMIC_DATA_PREVIEW_POST_TYPES)) {
        // return all taxonomies for global blocks
        $taxonomies = array_values(get_taxonomies());
    } else {
        $taxonomies = get_object_taxonomies($postType);
    }

    return format_for_dropdown($taxonomies);
}

/**
 * Get all ACF Fields
 * @param string|null $type
 * @param string[] $ignore Field type to ignore
 * @return ACFField[]
 */
function get_acf_fields($type = null, $ignore = [])
{
    $fields = [];

    if (!function_exists('acf_get_field_groups') || !function_exists('acf_get_fields')) {
        return $fields;
    }

    /** @var ACFGroup[] $groups */
    $groups = acf_get_field_groups();

    foreach($groups as $group) {
        /** @var ACFField[] $groupFields */
        $groupFields = acf_get_fields($group);
        $isOptionPage = is_acf_option_field($group);
        $isTaxonomyField = is_acf_taxonomy_field($group);

        $fields[] = get_acf_fields_from_field_group($groupFields, $type, $ignore, [
            'is_option_page' => $isOptionPage,
            'is_tax_field' => $isTaxonomyField,
            'group' => $group['title'],
            'group_id' => $group['ID'],
        ]);
    }

    return array_merge([], ...$fields);
}

/**
 * @param ACFField[] $groupFields
 * @param string|null $type
 * @param string[] $ignore
 * @param array $additionalFieldProperties properties to be merged with each field array
 * @return ACFField[]
 */
function get_acf_fields_from_field_group($groupFields, $type, $ignore, $additionalFieldProperties) {
    $fields = [];
    $subFields = [];
    foreach ($groupFields as $groupField) {
        if (in_array($groupField['type'], $ignore, true)) {
            continue;
        }

        if ($type && ($groupField['type'] ?? null) !== $type) {
            continue;
        }

        if (array_key_exists('sub_fields', $groupField)) {
            $subFields[] = get_acf_fields_from_field_group($groupField['sub_fields'], $type, $ignore, [
                'group' => $groupField['label'],
                'parent_key' => $groupField['key'],
                'parent_type' => $groupField['type'],
                'group_id' => $additionalFieldProperties['group_id'] ?? $groupField['ID'],
                'is_option_page' => $additionalFieldProperties['is_option_page'] ?? false,
                'is_tax_field' => $additionalFieldProperties['is_tax_field'] ?? false
            ]);
        }

        $fields[] = array_merge($groupField, $additionalFieldProperties);
    }
    /** @var ACFField[] $merged */
    $merged = array_merge($fields, ...$subFields);
    return $merged;
}

/**
 * @param ACFGroup $group
 * @return boolean
 */
function is_acf_option_field($group)
{
    foreach ($group['location'] as $location) {
        $hasOption = array_filter($location, function ($l) {
            return $l['param'] === 'options_page';
        });
        if ($hasOption) {
            return true;
        }
    }
    return false;
}

/**
 * @param ACFGroup $group
 * @return boolean
 */
function is_acf_taxonomy_field($group) {

    $hasTaxonomyLocation = false;
    $hasPostLocation = false;

    foreach ($group['location'] as $location) {


        // has taxonomy
        $hasTaxonomy = array_filter($location, function ($l) {
            return $l['param'] === 'taxonomy';
        });

        if (count($hasTaxonomy) > 0) {
            $hasTaxonomyLocation = true;
        }

        // has post
        $hasPost = array_filter($location, function ($l) {
            return str_starts_with($l['param'], 'post');
        });

        if (count($hasPost) > 0) {
            $hasPostLocation = true;
        }

    }

    if ($hasTaxonomyLocation && !$hasPostLocation) return true;

    return false;

}

/**
 * Get toolset fields for post type
 * @param string|null $type
 * @param string[] $ignore Field types to ignore
 * @return array
 */
function get_toolset_fields($type = null, $ignore = [])
{
    /**
     * @var array
     * @psalm-suppress UndefinedFunction
     */
    $groups = wpcf_admin_fields_get_groups();
    $fields = [];
    $subFields = [];

    /** @var array $group */
    foreach($groups as $group) {
        if (!isset($group['id'])) {
            continue;
        }

        /**
         * @var array
         * @psalm-suppress UndefinedFunction
         */
        $groupFields = wpcf_admin_fields_get_fields_by_group($group['id']);

        /**
         * @var array
         * @psalm-suppress UndefinedFunction
         */
        $groupPostTypes = wpcf_admin_get_post_types_by_group($group['id']);
        /** @var array|string $field */
        foreach ($groupFields as $field) {
            if (!is_array($field)) {
                // if the field is not an array it is a repeater field which are not currently supported
                continue;
            }
            $field['group'] = (string) ($group['name'] ?? '');
            $field['group_id'] = (string) ($group['slug'] ?? '');
            $field['post_types'] = $groupPostTypes;
            $field['is_sub_field'] = false;
            $fields[] = $field;
        }
    }
    $fields = array_merge($fields, ...$subFields);

    return array_values(
        array_filter(
            $fields,
            /**
             * @param array $field
             * @return bool
             */
            function ($field) use ($ignore, $type) {
                if ($ignore && in_array($field['type'] ?? null, $ignore)) {
                    return false;
                }

                return !$type || ($field['type'] ?? null) === $type;
            }
        )
    );
}

/**
 * @param string[] $additional_formats
 * @return array{text: string, value: string}[]
 */
function get_date_formats($additional_formats = [])
{
    $default_and_additional_formats = array_merge(
        [__('F j, Y'), 'Y-m-d', 'm/d/Y', 'd/m/Y'],
        $additional_formats
    );

    /** @var string[] $date_formats */
    $date_formats = apply_filters('date_formats', $default_and_additional_formats);

    return array_map(function ($dateFormat) {
        return ['text' => (string)wp_date($dateFormat), 'value' => (string)$dateFormat];
    }, array_unique($date_formats));
}

/**
 * @param string[] $additional_formats
 * @return array{text: string, value: string}[]
 */
function get_time_formats($additional_formats = [])
{
    $default_and_additional_formats = array_merge(
        ['g:i a', 'g:i A', 'H:i'],
        $additional_formats
    );

    /** @var string[] $time_formats */
    $time_formats = apply_filters('time_formats', $default_and_additional_formats);

    return array_map(function ($timeFormat) {
        return ['text' => (string)wp_date($timeFormat), 'value' => (string)$timeFormat];
    }, array_unique($time_formats));
}

/**
 * @param string|null $type
 * @param array $ignore
 * @return array
 */
function get_metabox_fields($type = null, $ignore = [])
{
    $fields = [];

    if (!function_exists('rwmb_get_registry')) {
        return $fields;
    }

    /** @var MetaboxRegistry $registry */
    $registry = rwmb_get_registry('meta_box');

    /**
     * @psalm-suppress MixedMethodCall
     * @var MetaboxGroup[] $metaboxes
     */
    $metaboxes = $registry->all();

    foreach ($metaboxes as $metabox) {
        if (class_exists('\MBSP\MetaBox', false) && $metabox->settings_pages !== false) {
            /**
             * @psalm-suppress UndefinedClass
             * @var MetaboxSettingsPage[] $metaboxSettingsPages
             */
            $metaboxSettingsPages = \MBSP\Factory::get($metabox->settings_pages);
            $settingsPages = $metabox->settings_pages;
            if (!is_array($metabox->settings_pages)) {
                $settingsPages = [$metabox->settings_pages];
            }
            // this field group is for one or more settings pages,
            // we need to register one instance of the field for
            // each settings page it belongs to
            /** @var string[] $settingsPages */
            foreach ($settingsPages as $page) {
                foreach ($metabox->fields as $field) {
                    if (in_array($field['type'], $ignore, true)) {
                        continue;
                    }
                    if ($type && ($field['type'] ?? null) !== $type) {
                        continue;
                    }
                    $settingsPage = $metaboxSettingsPages[$page] ?? null;
                    if (!$settingsPage) {
                        continue;
                    }
                    $fields[] = get_metabox_field(
                        $field,
                        $settingsPage->page_title,
                        $settingsPage->id,
                        true,
                        $settingsPage->option_name
                    );
                }
            }
        } else {
            foreach ($metabox->fields as $field) {
                if (in_array($field['type'], $ignore, true)) {
                    continue;
                }
                if ($type && ($field['type'] ?? null) !== $type) {
                    continue;
                }

                $fields[] = get_metabox_field($field, $metabox->title, $metabox->id);
            }
        }
    }

    return $fields;
}

/**
 * @param MetaboxField $field
 * @param string $groupTitle
 * @param string $groupId
 * @param boolean $isSettingsPage
 * @param string $settingsPage
 * @return MetaboxField
 */
function get_metabox_field($field, $groupTitle, $groupId, $isSettingsPage = false, $settingsPage = '') {
    $field['group'] = $groupTitle;
    $field['group_id'] = $groupId;
    $field['is_settings_page'] = $isSettingsPage;
    $field['settings_page'] = $settingsPage;
    $field['is_sub_field'] = false;
    return $field;
}
