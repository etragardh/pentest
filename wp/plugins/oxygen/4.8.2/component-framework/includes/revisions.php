<?php
class Oxygen_Revisions {

    static function create_revision( $post_id ) {
        $max_recent_revisions = get_option('oxygen_vsb_number_of_latest_revisions', 20);
        $max_daily_revisions = get_option('oxygen_vsb_number_of_daily_revisions', 7);

        $inserted_revision = false;

        // If the current shortcode three is from a revision, there is no need to create another revision
        if( metadata_exists( "post", $post_id, "ct_current_revision" ) ) {
            delete_metadata( 'post', $post_id, "ct_current_revision" );
            return $inserted_revision;
        }

        if( metadata_exists( "post", $post_id, "ct_builder_json" ) ){
            // First copy the current shortcode three as a new revision
            $current_three = get_post_meta( $post_id, "ct_builder_json", true);

            // Check to avoid creating an empty revision on the first save
            $revisions = self::get_post_meta_db( $post_id, "ct_builder_shortcodes_revisions" );
            if( count( $revisions ) == 0 && empty( trim( $current_three ) ) ) return $inserted_revision;
            
            $inserted_revision = add_post_meta( $post_id, "ct_builder_shortcodes_revisions", addslashes($current_three) );
            add_post_meta( $post_id, "ct_builder_shortcodes_revisions_dates", time() );

            // Delete the older revisions to only keep:
            // - The $max_recent_revisions most recent versions
            // – Then, $max_daily_revisions versions, one for each day for the preceding month, since the oldest revision
            $revisions = self::get_post_meta_db( $post_id, "ct_builder_shortcodes_revisions" );
            $dates = self::get_post_meta_db( $post_id, "ct_builder_shortcodes_revisions_dates" );
            if( is_array( $revisions ) && count( $revisions ) > $max_recent_revisions ) {
                $revisions = array_reverse( $revisions );
	            $dates = array_reverse( $dates );

	            $last_date_for_daily_revisions = $max_recent_revisions - 1;
	            $daily_revisions = 0;
	            $index =  $max_recent_revisions;

	            for( ; $index <= count( $revisions ); $index++ ){
		            if( $daily_revisions >= $max_daily_revisions ) {
			            $revision_to_delete = $revisions[ $index ]->meta_id;
			            $date_to_delete = $dates[ $index ]->meta_id;
                        delete_metadata_by_mid( 'post', $revision_to_delete );
                        delete_metadata_by_mid( 'post', $date_to_delete );
		            } else {

                        if( intval( $dates[ $last_date_for_daily_revisions ]->meta_value)  - intval( $dates[ $index ]->meta_value ) < 86400 ) {
                            $revision_to_delete = $revisions[ $index ]->meta_id;
                            $date_to_delete = $dates[ $index ]->meta_id;
                            delete_metadata_by_mid( 'post', $revision_to_delete );
                            delete_metadata_by_mid( 'post', $date_to_delete );
                        } else {
                            $last_date_for_daily_revisions = $index;
                            $daily_revisions++;
                        }
		            }
	            }

            }
        }
        return $inserted_revision;



    }

    static function restore_revision( $post_id, $revision_id ) {
        $revision_created = false;
        // First make sure there is a $revision_id that belongs to the $post_id
        $revisions = self::get_post_meta_db( $post_id, "ct_builder_shortcodes_revisions" );

        $found = false;
        if( is_array( $revisions ) ) {
            foreach ($revisions as $revision) {
                if( $revision->meta_id == $revision_id ) {
                    $found = $revision;
                    break;
                }
            }
        } else if( $revisions->meta_id == $revision_id ) $found = $revisions;

        if( FALSE === $found ) return $revision_created;

        $found->meta_value = oxygen_safe_convert_old_shortcodes_to_json($found->meta_value);

        // Create another revision of what's currently saved, if it's not saved yet
        if( !metadata_exists( "post", $post_id, "ct_current_revision" ) ) $revision_created = self::create_revision( $post_id );

        // Restore the shortcodes with what we found in the specified revision
        update_post_meta( $post_id, 'ct_builder_json', addslashes($found->meta_value) );
        update_post_meta( $post_id, 'ct_current_revision', $found->meta_id );

        // Re-generate the CSS cache for the post
	    oxygen_vsb_cache_page_css( $post_id, $found->meta_value );

        return $revision_created;
    }

    static function render_revisions_list( $post_id, $template = false ){
        if( !metadata_exists( "post", $post_id, "ct_builder_shortcodes_revisions" ) ) return;
        $revisions = self::get_post_meta_db( $post_id, "ct_builder_shortcodes_revisions" );
        $dates = self::get_post_meta_db( $post_id, "ct_builder_shortcodes_revisions_dates" );
        if( !is_array( $revisions ) ) $revisions = array( $revisions );
        $current_revision = get_post_meta( $post_id,'ct_current_revision',true );
        $preview_parameter = $template ? "oxy_preview_template_revision" : "oxy_preview_revision";
        $is_reusable = ( get_post_meta( $post_id, 'ct_template_type', true ) == "reusable_part" );

        if( $template ) {

            $template_id = $post_id;

            $catch_all = false;
	        if(!get_post_meta( $template_id, 'ct_template_all_archives', true )
	           && !get_post_meta( $template_id, 'ct_template_single_all', true )
	           && !get_post_meta( $template_id, 'ct_template_post_types', true )
	           && !get_post_meta( $template_id, 'ct_template_all_archives', true )
	           && !get_post_meta( $template_id, 'ct_template_apply_if_archive_among_taxonomies', true )
	           && !get_post_meta( $template_id, 'ct_template_apply_if_archive_among_cpt', true )
	           && !get_post_meta( $template_id, 'ct_template_apply_if_archive_among_authors', true )
	           && !get_post_meta( $template_id, 'ct_template_date_archive', true )
	           && !get_post_meta( $template_id, 'ct_template_front_page', true )
	           && !get_post_meta( $template_id, 'ct_template_blog_posts', true )
	           && !get_post_meta( $template_id, 'ct_template_search_page', true )
	           && !get_post_meta( $template_id, 'ct_template_404_page', true )
	           && !get_post_meta( $template_id, 'ct_template_inner_content', true )
	           && !get_post_meta( $template_id, 'ct_template_index', true )) {
		        $catch_all = true;
	        }

	        $data = ct_get_templates_post( $template_id, false, false, $catch_all );

	        if(is_array($data)) {

		        $template_terms = ct_get_templates_term( $template_id, false, false, $catch_all );

		        if(is_array($template_terms)) {

			        $data = array_merge( $data, $template_terms);
		        }
	        }
	        else {
		        $data = ct_get_templates_term( $template_id, false, false, $catch_all );
	        }

	        $postsList = array();

	        if( is_array( $data ) && isset( $data[ 'postsList' ] ) && is_array( $data[ 'postsList' ] ) ) {
		        $postsList = $data['postsList'];
		        foreach ( $postsList as $key => $post ) {
			        $postsList[$key][ 'permalink' ] = get_permalink( $post[ 'id' ] );
		        }
	        }

            if( $is_reusable || count( $postsList ) == 0 ) {
                $preview_parameter = "oxy_preview_sole_template_revision";
            }

	        //At this point, all scripts are registerd, enqueued and even rendered, so let's output the data directly
	        $postsList = json_encode( $postsList );
            ?> <script type="application/javascript"> var oxygenPreviewPostsList = <?php echo $postsList; ?>;</script> <?php

        }
?>
        <div>
            <span id="ct-toggle-revisions"><?php _e( "Revisions", "component-theme" ); ?></span><div class="oxy-tooltip"><div class="oxy-tooltip-text revisions"><?php _e( "Restore previous versions of this post or template's design. Restoring a revision will not affect any changes for site-wide settings like Global Styles, Fonts, or Classes.", "oxygen" ) ?></div></div>
        </div>
        <div id="ct-builder-revisions" style="display:none">
	        <?php if( $template && !$is_reusable ): ?>
                <span id="ct_preview_revision_select_label"><?php echo __("Preview template with this post: ", 'component_theme'); ?></span>
                <select id="ct_preview_revision_select">
                </select>
	        <?php endif; ?>
            <ul id="ct_builder_revisions" style="height:auto!important;">
                <?php for($i = count($revisions) -1; $i >= 0; $i--): $revision_date = get_date_from_gmt( date('Y-m-d H:i:s', intval( $dates[$i]->meta_value ) ) ); ?>
                    <li><?php echo __("Revision created automatically on ", 'component_theme') . $revision_date ?>
                        <?php if( $current_revision == $revisions[$i]->meta_id ): ?>
                            <?php echo __( ' (current)', 'component_theme' ); ?>
                        <?php else: ?>
                            <?php
                                $revision = $revisions[$i]->meta_id;
                                $permalink = get_permalink(); //only for non-templates
                                $restorelink = get_edit_post_link( $post_id, '' ) . '&ct_restore_revision=' . $revisions[$i]->meta_id . '&wp_nonce=' . wp_create_nonce( 'ct_restore_revision_' . $revisions[$i]->meta_id );
                                $deletelink = get_edit_post_link( $post_id, '' ) . '&ct_delete_revision=' . $revisions[$i]->meta_id . '&ct_delete_revision_date=' . $dates[$i]->meta_id .'&wp_nonce=' . wp_create_nonce( 'ct_delete_revision_' . $revisions[$i]->meta_id );
                                if( $is_reusable) {
                                    $permalink = get_permalink( $post_id );
                                }
                                echo "<a class='oxygen-preview-revision' href='javascript:;' target='_blank' data-revision='$revision' data-permalink='$permalink' data-restorelink='$restorelink' data-parameter='$preview_parameter' data-date='$revision_date' data-template='" . ($template ? 'true' : 'false') . "'>" . __( 'Preview', 'component_theme' ) . "</a> ";
		                        echo "<a class='oxygen-restore-revision' href='" . $restorelink . "'>" . __( 'Restore', 'component_theme' ) . "</a>";
		                        echo " <a class='oxygen-delete-revision' href='" . $deletelink . "'>" . __( 'Delete', 'component_theme' ) . "</a>";
                            ?>

                        <?php endif; ?>
                    </li>
                <?php endfor; ?>
            </ul>
            <?php 
            $delete_all_link = get_edit_post_link( $post_id, '' ) . '&ct_delete_all_revisions=' . $post_id .'&wp_nonce=' . wp_create_nonce( 'ct_delete_all_revisions' );
            echo "<a id='oxygen-delete-all-revisions' href='" . $delete_all_link . "'>" . __( 'Delete All Post Revisions', 'component_theme' ) . "</a>"; ?>
        </div>
<?php
    }

    static function restore_revision_hook() {
        if( isset( $_REQUEST['action'] ) && isset( $_GET['post'] ) && isset( $_GET['ct_restore_revision'] )  && isset( $_GET['wp_nonce'] ) ) {

            if( wp_verify_nonce( $_GET['wp_nonce'], 'ct_restore_revision_' . $_GET['ct_restore_revision'] ) ) {
                Oxygen_Revisions::restore_revision( $_GET['post'], $_GET['ct_restore_revision'] );
                wp_redirect( get_edit_post_link( $_GET['post'], '' ) );
                exit;
            }
        }
    }

    static function delete_revision_hook() {
        if( isset( $_REQUEST['action'] ) && isset( $_GET['post'] ) && isset( $_GET['ct_delete_revision'] ) && isset( $_GET['ct_delete_revision_date'] )  && isset( $_GET['wp_nonce'] ) ) {

            if( wp_verify_nonce( $_GET['wp_nonce'], 'ct_delete_revision_' . $_GET['ct_delete_revision'] ) ) {
                delete_metadata_by_mid( 'post', $_GET['ct_delete_revision'] );
                delete_metadata_by_mid( 'post', $_GET['ct_delete_revision_date'] );
            }
        }
    } 
    
    
    static function delete_all_revisions_hook() {
        if( isset( $_REQUEST['action'] ) && isset( $_GET['post'] ) && isset( $_GET['ct_delete_all_revisions'] ) && isset( $_GET['wp_nonce'] ) ) {

            if( wp_verify_nonce( $_GET['wp_nonce'], 'ct_delete_all_revisions' ) ) {
                $post_id = $_GET['ct_delete_all_revisions'];
                delete_post_meta( $post_id, "ct_builder_shortcodes_revisions" );
                delete_post_meta( $post_id, "ct_builder_shortcodes_revisions_dates" );
            }
        }
    } 

    /**
     * Alternative to get_post_meta(), to retrieve meta_ids. @see get_meta_db()
     */
    static function get_post_meta_db( $post_id, $meta_key = null, $single = false, $meta_val = null, $output = OBJECT, $mid = null ){
        return self::get_meta_db( 'post', $post_id, $meta_key, $meta_val, $single, $output, $mid );
    }

    /**
    * Alternative to get_metadata(). Differences:
    *  - returns every meta field (instead of only meta_values)
    *  - bypasses meta filters/actions
    *  - queries database, bypassing cache
    *  - returns raw meta_values (instead of unserializing arrays)
    *
    * @param string $meta_type Type of object metadata is for (e.g., comment, post, or user)
    * @param int    $object_id ID of the object metadata is for
    * @param string $meta_key  Optional. Metadata key to retrieve. By default, returns all metadata for specified object.
    * @param mixed  $meta_val  Optional. If specified, will only return rows with this meta_value.
    * @param bool   $single    Optional. If true, returns single row, else returns array of rows.
    * @param string $output    Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants. @see wpdb::get_results()
    *
    * @return array Single metadata row, array of rows, empty array if no matches, or false if there was an error.
    */
    static function get_meta_db( $meta_type, $object_id = null, $meta_key = null, $meta_val = null, $single = false, $output = OBJECT, $mid = null ){

        if( !$meta_type || !$table = _get_meta_table( $meta_type ) )
            return false;

        // Build query
        global $wpdb;
        $query = "SELECT * FROM $table";
        // Add passed conditions to query
        $where = array();
        if( $object_id = absint( $object_id ) )
            $where[] = $wpdb->prepare( sanitize_key( $meta_type.'_id' ).' = %d', $object_id );
        if( !empty($meta_key) )
            $where[] = $wpdb->prepare( 'meta_key = %s', wp_unslash( $meta_key ) );
        if( null !== $meta_val )
            $where[] = $wpdb->prepare( 'meta_value = %s', maybe_serialize(wp_unslash($meta_val)));
        if( null != $mid )
	        $where[] = $wpdb->prepare( 'meta_id = %s', maybe_serialize(wp_unslash($mid)));
        if( !empty($where) )
            $query .= ' WHERE '.implode(' AND ', $where );
            $query .= ' ORDER BY meta_id';
        if( $single )
            $query .= ' LIMIT 1';

        $rows = $wpdb->get_results( $query, $output );

        if( empty( $rows ) )
            return ( $single ? null : array() );

        return ( $single ? reset( $rows ) : $rows );
    }

    static function detect_reusable_preview() {

        if(!oxygen_vsb_current_user_can_access()) {
            return;
        }
        
        global $post;
        global $wp_scripts;
        if( is_admin() || empty( $_GET['oxy_preview_sole_template_revision'] ) ) return;
	    $shortcodes = Oxygen_Revisions::get_post_meta_db( $post->ID, null, true, null, OBJECT, $_REQUEST['oxy_preview_sole_template_revision'] )->meta_value;
	    ?>
        <!doctype html>
        <html <?php language_attributes(); ?>>
            <head>
                <meta charset="<?php bloginfo( 'charset' ); ?>">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
	            <?php wp_head(); ?>
                <?php do_action("wp_enqueue_scripts"); ?>
            </head>
            <body>
                <?php
                echo do_shortcode( $shortcodes );
                echo "<style>";
                do_action("ct_footer_styles");
                echo "</style>";
                echo "<script type='application/javascript'>";
                do_action("ct_footer_js");
                echo "</script>";
                do_action("wp_print_footer_scripts");
                do_action("wp_footer");
                ?>
            </body>
        </html>
            <?php
        exit();
    }
}

add_action( "admin_init", "Oxygen_Revisions::restore_revision_hook" );
add_action( "admin_init", "Oxygen_Revisions::delete_revision_hook" );
add_action( "admin_init", "Oxygen_Revisions::delete_all_revisions_hook" );
add_action( "wp", "Oxygen_Revisions::detect_reusable_preview" );

