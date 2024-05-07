<?php

function oxygen_vsb_current_user_can_access()
{

    // Its the super man
    if (is_multisite() && is_super_admin()) {
        return true;
    }

    $user = wp_get_current_user();

    if (!$user) {
        return false;
    }

    $user_edit_mode = oxygen_vsb_get_user_edit_mode();
    if ($user_edit_mode === "true" || $user_edit_mode == 'edit_only') {
        return true;
    }
    if ($user_edit_mode === "false") {
        return false;
    }

    if ($user && isset($user->roles) && is_array($user->roles)) {
        foreach ($user->roles as $role) {
            if ($role == 'administrator') {
                return true;
            }
            $option = get_option("oxygen_vsb_access_role_$role", false);
            if ($option && ($option == 'true' || $option == 'edit_only')) {
                return true;
            }
        }
    }

    return false;
}

function oxygen_vsb_current_user_can_full_access()
{

    // Its the super man
    if (is_multisite() && is_super_admin()) {
        return true;
    }

    $user = wp_get_current_user();

    if (!$user) {
        return false;
    }

    $user_edit_mode = oxygen_vsb_get_user_edit_mode();
    if ($user_edit_mode === "true") {
        return true;
    }
    if ($user_edit_mode === "false" || $user_edit_mode == 'edit_only') {
        return false;
    }

    if ($user && isset($user->roles) && is_array($user->roles)) {
        foreach ($user->roles as $role) {
            if ($role == 'administrator') {
                return true;
            }
            $option = get_option("oxygen_vsb_access_role_$role", false);
            if ($option && $option == 'true') {
                return true;
            }
        }
    }

    return false;
}

function oxygen_vsb_get_user_edit_mode($skip_role=false)
{

    $user_id = get_current_user_id();
    $users_access_list = get_option("oxygen_vsb_options_users_access_list", array());

    if (isset($users_access_list[$user_id]) && isset($users_access_list[$user_id][0])) {
        return $users_access_list[$user_id][0];
    }

    if ($skip_role) {
        return "";
    }

    $user = wp_get_current_user();

    if (!$user) {
        return "";
    }

    $edit_only = false;

    if ($user && isset($user->roles) && is_array($user->roles)) {
        foreach ($user->roles as $role) {
            if ($role == 'administrator') {
                return "true";
            }
            $option = get_option("oxygen_vsb_access_role_$role", false);
            if ($option && $option == 'true') {
                return "true";
            }

            if ($option && $option == 'edit_only') {
                $edit_only = true;
            }
        }
    }

    if ($edit_only) {
        return "edit_only";
    }

    return "";
}


function oxygen_is_user_access_option_set($option_name)
{
    $user_id = get_current_user_id();

    if (!$user_id) {
        return false;
    }

    $access_option = get_option($option_name, array());

    if (isset($access_option[$user_id]) && isset($access_option[$user_id][0]) && $access_option[$user_id][0] === "true") {
        return true;
    }
}


function oxygen_is_role_access_option_set($option_name)
{

    $user = wp_get_current_user();

    if (!$user) {
        return false;
    }

    if ($user && isset($user->roles) && is_array($user->roles)) {
        $option = get_option($option_name, false);
        foreach ($user->roles as $role) {
            if ($role == 'administrator') {
                return true;
            }
            if (isset($option[$role]) && isset($option[$role][0]) && $option[$role][0] === "true") {
                return true;
            }
        }
    }

    return false;
}


function oxygen_vsb_user_can_use_advanced_tab()
{

    if (oxygen_vsb_current_user_can_full_access()) {
        return true;
    }

    if (!oxygen_vsb_user_can_use_ids() && !oxygen_vsb_user_can_use_classes()) {
        return false;
    }

    if (oxygen_is_user_access_option_set("oxygen_vsb_options_users_access_advanced_tab")) {
        return true;
    }

    if (oxygen_vsb_get_user_edit_mode("skip_role")=="edit_only") {
        return false;
    }

    if (oxygen_is_role_access_option_set("oxygen_vsb_options_role_access_advanced_tab")) {
        return true;
    }

    return false;
}


function oxygen_vsb_user_can_use_design_library()
{

    if (oxygen_vsb_current_user_can_full_access()) {
        return true;
    }

    if (oxygen_is_user_access_option_set("oxygen_vsb_options_users_access_design_library")) {
        return true;
    }

    if (oxygen_vsb_get_user_edit_mode("skip_role")=="edit_only") {
        return false;
    }

    if (oxygen_is_role_access_option_set("oxygen_vsb_options_role_access_design_library")) {
        return true;
    }

    return false;
}


function oxygen_vsb_user_can_use_reusable_parts()
{

    if (oxygen_vsb_current_user_can_full_access()) {
        return true;
    }

    if (oxygen_is_user_access_option_set("oxygen_vsb_options_users_access_reusable_parts")) {
        return true;
    }

    if (oxygen_vsb_get_user_edit_mode("skip_role")=="edit_only") {
        return false;
    }

    if (oxygen_is_role_access_option_set("oxygen_vsb_options_role_access_reusable_parts")) {
        return true;
    }

    return false;
}


function oxygen_vsb_user_can_drag_n_drop()
{

    if (oxygen_vsb_current_user_can_full_access()) {
        return true;
    }

    if (oxygen_is_user_access_option_set("oxygen_vsb_options_users_access_drag_n_drop")) {
        return true;
    }

    if (oxygen_vsb_get_user_edit_mode("skip_role")=="edit_only") {
        return false;
    }

    if (oxygen_is_role_access_option_set("oxygen_vsb_options_role_access_drag_n_drop")) {
        return true;
    }

    return false;
}

function oxygen_vsb_user_can_use_classes()
{

    if (oxygen_vsb_current_user_can_full_access()) {
        return true;
    }

    if (oxygen_is_user_access_option_set("oxygen_vsb_options_users_access_disable_classes")) {
        return false;
    }

    if (oxygen_vsb_get_user_edit_mode("skip_role")=="edit_only") {
        return true;
    }

    if (oxygen_is_role_access_option_set("oxygen_vsb_options_role_access_disable_classes")) {
        return false;
    }

    return true;
}


function oxygen_vsb_user_can_use_ids()
{

    if (oxygen_vsb_current_user_can_full_access()) {
        return true;
    }

    if (oxygen_is_user_access_option_set("oxygen_vsb_options_users_access_disable_ids")) {
        return false;
    }

    if (oxygen_vsb_get_user_edit_mode("skip_role")=="edit_only") {
        return true;
    }

    if (oxygen_is_role_access_option_set("oxygen_vsb_options_role_access_disable_ids")) {
        return false;
    }

    return true;
}

function oxygen_vsb_user_has_enabled_elements()
{

    if (oxygen_vsb_user_get_enabled_elements() !== false) {
        return true;
    }
}

function oxygen_vsb_user_get_enabled_elements()
{
    // User based
    
    $user_id = get_current_user_id();
    $user_enable_elements = get_option("oxygen_vsb_options_users_access_enable_elements", array());

    if (isset($user_enable_elements[$user_id]) && isset($user_enable_elements[$user_id][0]) && $user_enable_elements[$user_id][0] === "true") {
        $user_enabled_elements = get_option("oxygen_vsb_options_users_access_enabled_elements", array());

        if (isset($user_enabled_elements[$user_id]) && is_array($user_enabled_elements[$user_id])) {
            return $user_enabled_elements[$user_id];
        }
    }

    if (oxygen_vsb_get_user_edit_mode("skip_role")=="edit_only") {
        return false;
    }

    // Role based

    $user = wp_get_current_user();
    if (!$user) {
        return false;
    }

    $role_enable_elements = get_option("oxygen_vsb_options_role_access_enable_elements", array());
    $is_elements_enabled = false;

    if ($user && isset($user->roles) && is_array($user->roles)) {
        foreach ($user->roles as $role) {
            if ($role == 'administrator') {
                $is_elements_enabled = true;
            }
            if (isset($role_enable_elements[$role]) && isset($role_enable_elements[$role][0]) && $role_enable_elements[$role][0] === "true") {
                $is_elements_enabled = true;
            }
        }
    }

    if ($is_elements_enabled) {

        $role_enabled_elements = get_option("oxygen_vsb_options_role_access_enabled_elements", array());
        $enabled_elements_list = array();

        if ($user && isset($user->roles) && is_array($user->roles)) {
            foreach ($user->roles as $role) {
                if ($role == 'administrator') {
                    $is_elements_enabled = true;
                }
                if (isset($role_enabled_elements[$role]) && is_array($role_enabled_elements[$role])) {
                    $enabled_elements_list = array_merge($enabled_elements_list, $role_enabled_elements[$role]);
                }
            }
        }
    }

    if (!empty($enabled_elements_list)) {
        return $enabled_elements_list;
    }

    return false;
}

function oxygen_hide_element_button($tag = "")
{

    if (!oxygen_vsb_current_user_can_full_access()) {
        $enabled_elements = oxygen_vsb_user_get_enabled_elements();
        if (!is_array($enabled_elements) || !in_array($tag, $enabled_elements)) {
            return true;
        }
    }

    return false;
}

function ct_init_user_enabled_elements()
{

    $user_enabled_elements = oxygen_vsb_user_get_enabled_elements();

    if (!is_array($user_enabled_elements)) {
        echo "userEnabledElements=[];";
        return;
    }

    $output = json_encode($user_enabled_elements);
    $output = htmlspecialchars($output, ENT_QUOTES);

    echo "userEnabledElements=$output;";
}