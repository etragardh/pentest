<div class="ct-template-anchors">
	
	<label>
		<input class="ct-template-anchor" type="radio" name="ct_template_type" value="archive" <?php checked( $template_type, "archive"); ?>>
		<?php _e("Archive", "component-theme"); ?>
	</label>
	<label>
		<input class="ct-template-anchor" type="radio" name="ct_template_type" value="single_post" <?php checked( $template_type, "single_post"); ?>>
		<?php _e("Single", "component-theme"); ?>
	</label>

	<label>
		<input class="ct-template-anchor" type="radio" name="ct_template_type" value="reusable_part" <?php checked( $template_type, "reusable_part"); ?>>
		<?php _e("Re-usable part", "component-theme"); ?>
	</label>

	<label>
		<input class="ct-template-anchor" type="radio" name="ct_template_type" value="other_template" <?php checked( $template_type, "other_template"); ?>>
		<?php _e("Other Template", "component-theme"); ?>
	</label>
</div>

<div id="ct_archive" class="ct-template-section">

	<b><?php _e("Post Types Archives","component-theme"); ?></b>
	<br/>

	<label>
		<input type="checkbox" name="ct_template_archive_post_types_all" value="true" 
			<?php if ( $template_archive_post_types_all ) echo 'checked="checked"'; ?>>
		<?php _e("All Post Types", "component-theme"); ?>
	</label>
	
	<select class="ct-select2" name="ct_template_archive_post_types[]" id="ct_template_archive_post_types" multiple="multiple">

		<?php 

		// get all types available for install
		$post_types 	= get_post_types( '', 'objects' ); 
		$exclude_types 	= array( "ct_template", "nav_menu_item", "revision" );

		foreach ( $post_types as $post_type ) : 

			if ( in_array ( $post_type->name, $exclude_types ) ) {
				continue;
			}

			if ( !$post_type->has_archive ) {
				continue;
			}
			?>

			<option value="<?php echo esc_attr( $post_type->name ); ?>"
				<?php if ( in_array( $post_type->name, $template_archive_post_types ) ) echo 'selected="selected"'; ?>>
				<?php echo sanitize_text_field( $post_type->label ); ?><option value="<?php echo $category->cat_ID; ?>" 
			</option>
		
		<?php endforeach; ?>
	</select>
	<script type="text/javascript">
		jQuery('#ct_template_archive_post_types').select2({
			placeholder: "Select",
		});
	</script>
	
	<br/><br/>
	<b><?php _e("Categories Archives", "component-theme"); ?></b>
	<br/>

	<label>
		<input type="checkbox" name="ct_template_categories_all" value="true" 
			<?php if ( $template_categories_all ) echo 'checked="checked"'; ?>>
		<?php _e("All Categories", "component-theme"); ?>
	</label>
	
	<select name="ct_template_categories[]" id="ct_template_categories" multiple="multiple">
		<?php 

		// get all categories available in install
		$categories = get_categories();

		foreach ( $categories as $category ) : ?>

			<option value="<?php echo esc_attr( $category->cat_ID ); ?>"
				<?php if ( in_array( $category->cat_ID, $template_categories ) ) echo 'selected="selected"'; ?>>
				<?php echo sanitize_text_field( $category->name ); ?>
			</option>
		
		<?php endforeach; ?>
	
	</select>
	<script type="text/javascript">
		jQuery('#ct_template_categories').select2({
			placeholder: "Select",
		});
	</script>


	<br/><br/>
	<b><?php _e("Tags Archives", "component-theme"); ?></b>
	<br/>

	<label>
		<input type="checkbox" name="ct_template_tags_all" value="true" 
			<?php if ( $template_tags_all ) echo 'checked="checked"'; ?>>
		<?php _e("All Tags", "component-theme"); ?>
	</label>
	
	<select name="ct_template_tags[]" id="ct_template_tags" multiple="multiple">
	
		<?php 

		// get all tags available in install
		$tags = get_tags();

		foreach ( $tags as $tag ) : ?>

			<option value="<?php echo esc_attr( $tag->term_id ); ?>" 
				<?php if ( in_array( $tag->term_id, $template_tags ) ) echo 'selected="selected"'; ?>>
				<?php echo sanitize_text_field( $tag->name ); ?>
			</option>
		
		<?php endforeach; ?>

	</select>
	<script type="text/javascript">
		jQuery('#ct_template_tags').select2({
			placeholder: "Select",
		});
	</script>

	<br/><br/>
	<b><?php _e("Custom Taxonomies", "component-theme"); ?></b>
	<br/>

	<label>
		<input type="checkbox" name="ct_template_custom_taxonomies_all" value="true" 
			<?php if ( $template_custom_taxonomies_all ) echo 'checked="checked"'; ?>>
		<?php _e("All Taxonomies", "component-theme"); ?>
	</label>

	<select name="ct_template_custom_taxonomies[]" id="ct_template_custom_taxonomies" multiple="multiple">

		<?php 

		// get custom taxonomies
		$args = array(
				"_builtin" => false
			);

		$taxonomies = get_taxonomies( $args, 'object' );

		foreach ( $taxonomies as $taxonomy ) : 

			$args = array(
				'hide_empty' 	=> 0,
				'taxonomy' 		=> $taxonomy->name,
			);

			$categories = get_categories( $args );

			if ( !isset($template_custom_taxonomies[$taxonomy->name]) || !$template_custom_taxonomies[$taxonomy->name] ) {
				$template_custom_taxonomies[$taxonomy->name] = array();
			}

			?>

			<optgroup label="<?php echo sanitize_text_field( $taxonomy->labels->name . " (" . $taxonomy->name . ")" ); ?>">
				
				<option value="<?php echo esc_attr( "all_".$taxonomy->name ); ?>"
					<?php if ( in_array( "all_".$taxonomy->name, $template_custom_taxonomies ) ) echo 'selected="selected"'; ?>>
					<?php _e( "All ", "component-theme" ); echo sanitize_text_field( $taxonomy->labels->name ); ?>
				</option>

				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" 
						<?php if ( in_array( $category->term_id, $template_custom_taxonomies ) ) echo 'selected="selected"'; ?>>
						<?php echo sanitize_text_field( $category->name ); ?>
					</option>
				<?php endforeach; ?>

			</optgroup>
		
		<?php endforeach; ?>

	</select>
	<script type="text/javascript">
		jQuery("#ct_template_custom_taxonomies").select2({
			placeholder: "Select",
		});
	</script>

	<br/><br/>
	<b><?php _e("Authors Archives", "component-theme"); ?></b>
	<br/>

	<label>
		<input type="checkbox" name="ct_template_authors_archives_all" value="true" 
			<?php if ( $template_authors_archives_all ) echo 'checked="checked"'; ?>>
		<?php _e("All Authors Archives", "component-theme"); ?>
	</label>
	
	<select name="ct_template_authors_archives[]" id="ct_template_authors_archives" multiple="multiple">
	
		<?php 
		// get all users to loop
		$authors = oxygen_get_authors();

		foreach ( $authors as $author ) : ?>

			<option value="<?php echo esc_attr( $author->ID ); ?>" 
				<?php if ( in_array( $author->ID, $template_authors_archives ) ) echo 'selected="selected"'; ?>>
				<?php echo sanitize_text_field( $author->user_login ); ?>
			</option>
		
		<?php endforeach; ?>

	</select>
	<script type="text/javascript">
		jQuery('#ct_template_authors_archives').select2({
			placeholder: "Select",
		});
	</script>

	<br/><br/>
	<b><?php _e("Other", "component-theme"); ?></b>
	<br/>

	<label>
		<input type="checkbox" name="ct_template_index" value="true" 
			<?php if ( $template_index ) echo 'checked="checked"'; ?>>
		<?php _e("Index", "component-theme"); ?>
	</label><br/>
	
	<label>
		<input type="checkbox" name="ct_template_front_page" value="true" 
			<?php if ( $template_front_page ) echo 'checked="checked"'; ?>>
		<?php _e("Front Page", "component-theme"); ?>
	</label><span class="description"><?php _e("('Front page' option must be set in Settings > Reading settings)","component-theme"); ?></span><br/>
	
	<label>
		<input type="checkbox" name="ct_template_blog_posts" value="true" 
			<?php if ( $template_blog_posts ) echo 'checked="checked"'; ?>>
		<?php _e("Blog Posts", "component-theme"); ?>
	</label><span class="description"><?php _e("('Blog Posts' option must be set in Settings > Reading settings)","component-theme"); ?></span><br/>

	<label>
		<input type="checkbox" name="ct_template_date_archive" value="true" 
			<?php if ( $template_date_archive ) echo 'checked="checked"'; ?>>
		<?php _e("Date Archive", "component-theme"); ?>
	</label><br/>

	<label>
		<input type="checkbox" name="ct_template_search_page" value="true" 
			<?php if ( $template_search_page ) echo 'checked="checked"'; ?>>
		<?php _e("Search Results Page", "component-theme"); ?>
	</label><br/>

	<label>
		<input type="checkbox" name="ct_template_404_page" value="true" 
			<?php if ( $template_404_page ) echo 'checked="checked"'; ?>>
		<?php _e("Page 404", "component-theme"); ?>
	</label><br/>
	

</div><!-- #ct_archive -->

<div id="ct_single_post" class="ct-template-section">

	<div id="ct_post_types" class="ct-template-options-section ct-section-active">

		<div class="ct-template-column-left">
		
			<?php _e("Apply to","component-theme"); ?><br/>
			<?php 

			// get all types available for install
			$post_types 	= get_post_types( '', 'objects' );
			$exclude_types 	= array( "ct_template", "nav_menu_item", "revision" );

			foreach ( $post_types as $post_type ) : 

				if ( in_array ( $post_type->name, $exclude_types ) ) {
					continue;
				} ?>
			
				<label>
					<input type="checkbox" name="ct_template_post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" 
						<?php if ( in_array( $post_type->name, $template_post_types ) ) echo 'checked="checked"'; ?>>
					<?php echo sanitize_text_field( $post_type->label ); ?>
				</label><br/>
			
			<?php endforeach; ?>
			<label>
				<input type="checkbox" name="ct_template_single_all" value="true" <?php checked( $template_single_all, "true"); ?>>
				<?php _e("Apply to all","component-theme"); ?>
			</label>
		</div><!-- .ct-template-column-left -->

		<div class="ct-template-column-left">
			<label>
				<input type="checkbox" id="ct_use_template_taxonomies" name="ct_use_template_taxonomies" value="true" <?php checked($use_taxonomies, "true"); ?>>
				<?php _e("Only apply to posts in certain taxonomies", "component-theme"); ?><br/>
			</label>

			<div class="ct-template-taxonomies">
				<?php

					// get all public taxonomies registered in WordPress install
					$args = array(
						'public' => true,
					);
					$taxonomies = get_taxonomies( $args, 'objects');
				
				?>

				<div id="ct-template-taxonomy-placeholder">
					<div class="ct-template-taxonomy">
						<?php _e("Has","component-theme"); ?>
						<select name="ct_taxonomy_names[]">
						<?php foreach ( $taxonomies as $key => $value ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>">
								<?php echo sanitize_text_field( $value->labels->singular_name ); ?>
							</option>
						<?php endforeach; ?>
						</select>
						<input type="text" name="ct_taxonomy_values[]" value="">
						<span class="ct-taxonomy-relation"><?php _e("And","component-theme"); ?></span>
						<span class="dashicons dashicons-trash ct-remove-taxonomy"></span>
						<span class="dashicons dashicons-plus ct-add-taxonomy"></span>
					</div>
				</div>
				
				<?php foreach ( $template_taxonomies['names'] as $template_taxonomy_key => $template_taxonomy_name ) : ?>
				<div class="ct-template-taxonomy">
					<?php _e("Has","component-theme"); ?>
					<select name="ct_taxonomy_names[]">
					<?php foreach ( $taxonomies as $key => $value ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php echo selected( $key, $template_taxonomy_name ); ?>>
							<?php echo sanitize_text_field( $value->labels->singular_name ); ?>
						</option>
					<?php endforeach; ?>
					</select>
					<input type="text" name="ct_taxonomy_values[]" value="<?php echo esc_attr( $template_taxonomies['values'][$template_taxonomy_key] ); ?>">
					<span class="ct-taxonomy-relation"><?php _e("And","component-theme"); ?></span>
					<span class="dashicons dashicons-trash ct-remove-taxonomy"></span>
					<span class="dashicons dashicons-plus ct-add-taxonomy"></span>
				</div>
				<?php endforeach; ?>
			</div>
		</div><!-- .ct-template-column-left -->
		<div class="ct-clearfix"></div>
	</div>

</div><!-- #ct_single_post -->