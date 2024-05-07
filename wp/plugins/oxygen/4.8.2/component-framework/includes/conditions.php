<?php

Class OxygenConditions {

	public $global_conditions;
	public $condition_operators;

	function __construct() {

    	add_action('ct_builder_ng_init', 	array( $this, 'init_global_conditions' ));
		add_filter('template_include', 		array( $this, 'global_condition_eval_template'), 100 );

		$this->condition_operators = array(
			'string' => array('==','!=','contains','does not contain'),
			'int'	 => array('==', '!=', '>=', '<=', '>', '<'),
			'simple' => array('==','!=')
		);

		$this->register_conditions();

		add_action('wp_ajax_load_conditions_terms', array( $this, 'load_conditions_terms' ));
		add_action('wp_ajax_load_conditions_tags', array( $this, 'load_conditions_tags' ));
		add_action('wp_ajax_load_conditions_categories', array( $this, 'load_conditions_categories' ));
	}


	function register_conditions() {

		add_action('init', array($this, 'register_post_type_condition'), 99);
		add_action('init', array($this, 'register_user_role_condition'), 99);
		add_action('init', array($this, 'register_condition_post_type'), 99);
		if ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] ) {
			// only regiter in builder and inside shortcode validation
			add_action('init', array($this, 'register_condition_taxonomy_term'), 99);
		}
		add_action('init', array($this, 'register_category_condition'), 99);
		add_action('init', array($this, 'register_tag_condition'), 99);
		add_action('init', array($this, 'register_status_condition'), 99);
		add_action('init', array($this, 'register_user_can_condition'), 99);
	
		// Post Conditions
		$this->register_condition(
			'Post ID',
			array('options' => array(), 'custom' => true), 
			$this->condition_operators['int'], 
			'post_id_callback', 
			'Post');

		$this->register_condition(
			'Post Parent ID',
			array('options' => array(), 'custom' => true), 
			$this->condition_operators['int'], 
			'post_parent_id_callback', 
			'Post');
		
		$this->register_condition(
			'Post Title',
			array('options' => array(), 'custom' => true), 
			$this->condition_operators['string'], 
			'post_title_callback', 
			'Post');

		$this->register_condition(
			'Post Has Featured Image',
			array('options' => array(true, false), 'custom' => false), 
			$this->condition_operators['simple'], 
			'has_featured_image_callback', 
			'Post');
		
		$this->register_condition(
			'Post Comment Count',
			array('options' => array(), 'custom' => true), 
			$this->condition_operators['int'], 
			'comment_count_callback', 
			'Post');
		
		
		// User Conditions
		$this->register_condition(
			'User Logged In', 
			array('options'=>array('true', 'false')), 
			$this->condition_operators['simple'], 
			'user_logged_in_callback', 
			'User');
		
		$this->register_condition(
			'User ID', 
			array('custom'=> true), 
			$this->condition_operators['int'], 
			'user_id_callback', 
			'User');

		
		// Archive Conditions
		$this->register_condition(
			'# of posts', 
			array('custom' => true), 
			$this->condition_operators['int'], 
			'num_posts_callback', 
			'Archive');


		// Author Conditions
		$this->register_condition(
			'Author Name', 
			array('custom' => true), 
			$this->condition_operators['string'], 
			'author_name_callback', 
			'Author');


		// Other Conditions
		$this->register_condition(
			'Date', 
			array('custom' => true, 'placeholder' => 'MM/DD/YYYY'), 
			array('==', 'is after', 'is before'), 
			'date_callback', 
			'Other');
		
		$this->register_condition(
			'Time', 
			array('custom' => true, 'placeholder' => 'HH:MM:SS'), 
			array('==', 'is after', 'is before'), 
			'time_callback', 
			'Other');

		$this->register_condition(
			'Day of Week', 
			array('options' => $this->helper_weekdays(), 'custom' => true), 
			array('==', 'is not', 'is after', 'is before'), 
			'day_of_week_callback', 
			'Other');

		$this->register_condition(
			'Day of Month', 
			array('options' => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31), 'custom' => 'true'), 
			array('==', 'is after', 'is before'), 
			'day_of_month_callback', 
			'Other');

		$this->register_condition(
			'Cookie List', 
			array('custom' => true), 
			array('contains', 'does not contain'), 
			'cookie_list_callback', 
			'Other');

		$this->register_condition(
			'Session Variables', 
			array('custom' => true), 
			array('contains', 'does not contain'), 
			'session_variables_callback', 
			'Other');

		$this->register_condition(
			'Post Has Excerpt',
			array('options' => array(true, false), 'custom' => false), 
			$this->condition_operators['simple'], 
			'excerpt_callback', 
			'Post');

		$this->register_condition(
			'Post Content Empty', 
			array('options' => array(true, false), 'custom' => false), 
			$this->condition_operators['simple'], 
			'post_content_empty_callback', 
			'Post');

		$this->register_condition(
			'Username', 
			array('options' => array(), 'custom' => true), 
			$this->condition_operators['string'], 
			'username_callback', 
			'User');

		$this->register_condition(
			'ZZOXYVSBDYNAMIC', 
			array('custom' => true), 
			array('==', '>=', '<=', 'contains', 'is_blank', 'is_not_blank', '!=', '>', '<', 'does_not_contain'),
			'dynamic_data_callback', 
			'Other');
	}


	function register_condition($tag, $values=array('options'=>array()), $operators=array(), $callback = null, $category = null ) {
		
		if (empty($tag) || '' == trim($tag)) {
			$message = __( 'Invalid condition name: Empty name given.' );
			trigger_error($message);
			return;
		}

		if (!isset($this->global_conditions) || !is_array($this->global_conditions)) {
			$this->global_conditions = array();
		}

		$this->global_conditions[$tag] = array(
			'name' => $tag,
			'values' => $values,
			'operators' => $operators,
			'callback' => $callback,
			'category' => $category,
			);
	}


	function init_global_conditions() {

		// move the 'ZZOXYVSBDYNAMIC' to the end
		$vsbdynamic = false;
		$conditions = array();

		if (is_array($this->global_conditions)) {
			
			foreach ($this->global_conditions as $key => $item) {
				if ($key == 'ZZOXYVSBDYNAMIC') {
					$vsbdynamic = $item;
				}
				else {
					$conditions[$key] = $item;
				}
			}

			if ($vsbdynamic) {
				$conditions['ZZOXYVSBDYNAMIC'] = $vsbdynamic;
			}
		}


		$output = json_encode($conditions);
		$output = htmlspecialchars( $output, ENT_QUOTES );

		echo "globalConditions=$output;";

		$grouped = array();

		foreach($conditions as $condition) {
			$grouped[$condition['category']][] = $condition;
		}

		$output = json_encode($grouped);
		$output = htmlspecialchars( $output, ENT_QUOTES );	

		echo "globalConditionsGrouped=$output;";
	}


	function global_condition_eval_template( $template ) {

	    $new_template = '';

	    if( isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_eval_conditions') {
	        if ( file_exists(CT_FW_PATH . '/components/layouts/ifelse.php') ) {
	            $new_template = CT_FW_PATH . '/components/layouts/ifelse.php';
	        }
	    }

	    if ( '' != $new_template ) {
	        return $new_template ;
	    }

	    return $template;
	}


	function global_conditions_result($conditionsData) {
		
		$conditions = $conditionsData['conditions'];
		$or = (isset($conditionsData['type']) && $conditionsData['type'] == "1") ? true : false;
		$result = !$or;

		foreach ($conditions as $condition) {

			// Register only when used as it may be pretty heavy
			if ($condition['name'] == "Taxonomy Term") {
				$this->register_condition_taxonomy_term();
			}
				
			// check if condition exist
			if ( ! isset($this->global_conditions[$condition['name']]) ) {
				continue;
			}

			// internal class callback
			if ( method_exists( $this, $this->global_conditions[$condition['name']]['callback'] ) ) {
				$callback = array( $this, $this->global_conditions[$condition['name']]['callback'] ); 
			}
			// 3rd party callback
			else if ( function_exists( $this->global_conditions[$condition['name']]['callback'] ) ) {
				$callback = $this->global_conditions[$condition['name']]['callback'];
			}
			// no callback exist
			else {
				$callback = false;
			}
			
			if (is_array($condition) &&
				isset($this->global_conditions[$condition['name']]) && 
				isset($this->global_conditions[$condition['name']]['callback']) &&
				$callback !== false
			) {
				$condition['value'] = do_shortcode($condition['value']); // because the value main contain a shortcode
				
				if($condition['name']=='ZZOXYVSBDYNAMIC') {
					$got = call_user_func( $callback, $condition['value'], $this->global_conditions[$condition['name']]['operators'][$condition['operator']], $condition['oxycode']);
					$result = $or?($result || $got):($result && $got);
				}
				else {
					$got = call_user_func( $callback, $condition['value'], $this->global_conditions[$condition['name']]['operators'][$condition['operator']]);
					$result = $or?($result || $got):($result && $got);
				}
			} else {
				$got = filter_var($condition, FILTER_VALIDATE_BOOLEAN);
				$result = $or?($result || $got):($result && $got);
			}
		}
		return $result;
	}


	function post_id_callback($value, $operator) {

		$current_post_id = get_the_ID();
		$value = intval($value);

		return $this->eval_int($current_post_id, $value, $operator);
	}


	function post_parent_id_callback($value, $operator) {

		$current_post_parent_id = wp_get_post_parent_id(get_the_ID());
	    $value = (int) $value;

		return $this->eval_int($current_post_parent_id, $value, $operator);
	}

		
	function register_post_type_condition() {
		
		$post_types = array_values(get_post_types(array('public'=>true)));

		$this->register_condition(
			'Post Type',
			array('options' => $post_types, 'custom' => false), 
			$this->condition_operators['simple'], 
			'post_type_callback', 
			'Post');
	}

		
	function post_type_callback($value, $operator) {

		$current_post_type = get_post_type( get_the_ID() );
		$value = (string) $value;

		return $this->eval_string($current_post_type, $value, $operator);
	}


	function post_title_callback($value, $operator) {

		$current_post_title = get_the_title( get_the_ID() );
		$value = (string) $value;

		return $this->eval_string($current_post_title, $value, $operator);
	}


	function has_featured_image_callback($value, $operator) {

		$current_post_thumbnail = has_post_thumbnail();
		$value = (bool) $value;

		return $this->eval_string($current_post_thumbnail, $value, $operator);
	}


	function comment_count_callback($value, $operator) {

		$current_post_comments = get_comments_number();
		$value = intval($value);

		return $this->eval_int($current_post_comments, $value, $operator);
	}


	function user_logged_in_callback($value, $operator) {

		$isLoggedIn = is_user_logged_in();
		$shouldbeLoggedIn = false;

		if ($value == 'true') {
			$shouldbeLoggedIn = true;
		}

		if ($operator == '!=') {
			return ($isLoggedIn !== $shouldbeLoggedIn);
		}
		else {
			return ($isLoggedIn === $shouldbeLoggedIn);
		}
	}


	function register_user_role_condition() {
		
		global $wp_roles;

	    $roles = $wp_roles->get_names();
	    $this->register_condition(
	    	'User Role', 
	    	array('options'=>array_keys($roles)), 
	    	$this->condition_operators['simple'], 
	    	'user_role_callback', 
	    	'User');
	}


	function user_role_callback($value, $operator) {

		$user = wp_get_current_user();
		$hasRole = in_array( $value, (array) $user->roles );

		if($operator == '!=') {
			return !$hasRole;
		}
		else {
			return $hasRole;
		}
	}


	function user_id_callback($value, $operator) {

		$current_user_id = get_current_user_id();
		$value = intval($value);

		return $this->eval_int($current_user_id, $value, $operator);
	}


	function register_condition_post_type() {
		
		$postTypes = get_post_types();

		$this->register_condition(
			'Archive Post Type', 
			array('options' => array_keys($postTypes)), 
			$this->condition_operators['simple'], 
			'archive_post_type_callback', 
			'Archive');	
	}


	function archive_post_type_callback($value, $operator) {

		$postType = get_post_type();
		$isSame = $postType === strtolower(trim($value));

		if($operator == '!=') {
			return !$isSame;
		}
		else {
			return $isSame;
		}
	}


	function register_condition_taxonomy_term() {

		$taxonomies = get_taxonomies();
		$finalTerms = array();

		foreach ($taxonomies as $key => $taxonomy) {
			
			$args = array(
				'hide_empty' => false,
				'number' => 20,
				'order' => 'count',
			);

			$terms = get_terms($key, $args);

			foreach ($terms as $term) {
				$finalTerms[$term->term_id] = $term->name;
			}
		}

		$finalTerms = array_map('utf8_encode', $finalTerms);

		$this->register_condition(
			'Taxonomy Term', 
			array(
				'options' => $finalTerms, 
				'keys' => true, 
				'ajax' => true, 
				'placeholder' => __('Search term...', 'oxygen'), 
				'callback' => 'load_conditions_terms'), 
			$this->condition_operators['simple'], 
			'taxonomy_term_callback', 
			'Archive');	
	}

	function load_conditions_terms() {
	
		oxygen_vsb_ajax_request_header_check();

		$search_value = $_REQUEST['search_value'];

		$taxonomies = get_taxonomies();
		$finalTerms = array();

		foreach ($taxonomies as $key => $taxonomy) {
			
			$args = array(
				'taxonomy' => $taxonomy,
				'hide_empty' => 0,
				'number' => 20,
				'order' => 'count',
				'name__like' => $search_value
			);

			$terms = get_terms($key, $args);
			foreach ($terms as $term) {
				$finalTerms[$term->term_id] = $term->name;
			}
		}

		$finalTerms = array_map('utf8_encode', $finalTerms);

		// Echo JSON
	  	header('Content-Type: application/json');
		echo json_encode($finalTerms);
		die();
	}

	function taxonomy_term_callback($value, $operator) {
	    
	    $term_id = intval($value);
	    $is_true = false;
	    
	    if ($term_id && term_exists($term_id)) {
	        $term = get_term($term_id);
	        $taxonomy = $term->taxonomy;
	        
	        if (is_single()) {
	            // if is single post, check if the post have the term
	            if (has_term($term_id, $taxonomy)) {
	                $is_true = true;
	            }
	        } elseif (is_archive()) {
	            // if is an archive page, check if the current archive is for the correct taxonomy and term
	            if ($taxonomy == 'category') {
	                $is_true = is_category($term_id);
	            } elseif ($taxonomy == 'post_tag') {
	                $is_true = is_tag($term_id);
	            } else {
	                $is_true = is_tax($taxonomy, $term_id);
	            }
	        }
	    }
	    
	    if ($operator == '!=') {
	        $is_true = !$is_true;
	    }
	    
	    return $is_true;
	}


	function num_posts_callback($value, $operator) {

		global $wp_query;

		$value = intval($value);
		$numPosts = 0;

		if ($wp_query) {
			$numPosts = $wp_query->post_count;
		}

		return $this->eval_int($numPosts, $value, $operator);
	}


	function author_name_callback($value, $operator) {
		
		global $post;
		
		$user_data = get_userdata($post->post_author);
		return $this->eval_string($user_data->data->display_name, $value, $operator);
	}


	function date_callback($value, $operator) {

		$current_time = current_time('timestamp');
		$date = strtotime($value, $current_time);

		if ($date === false) {
			return false;
		}
		
		$now = strtotime(date("Y-m-d", $current_time));
		$diff = $now - $date;
		$diff = round($diff / (60 * 60 * 24));

		if ($operator == '==') {
			return ($diff == 0);
		}
		elseif($operator == 'is after') {
			return ($diff > 0);
		}
		else {
			return ($diff < 0);
		}
	}


	function time_callback($value, $operator) {
		
		$current_time = current_time('timestamp');
		$time = strtotime($value, $current_time);
		
		if ($time === false) {
			return false;
		}
		
		$now = strtotime('now', $current_time);
		$diff = $now - $time;
		$diff = floor($diff / 60); // round to a minute atleast

		if ($operator == '==') {
			return ($diff == 0);
		}
		elseif ($operator == 'is after') {
			return ($diff >= 0);
		}
		else {
			return ($diff < 0);
		}
	}


	function helper_weekdays() {
		
		$daysOfWeek = array();
		$toDay = date('w');
		for($i = 1; $i <= 7; $i++) {
		    $daysOfWeek[$i] = date("D", time()+(24*60*60)*($i-$toDay));
		}

		return $daysOfWeek;
	}


	function day_of_week_callback($value, $operator) {
		
		$current_time = current_time('timestamp');
		$toDay = date('w', $current_time);
		
		$day = 1; // default monday

		if (is_numeric($value)) {
			
			$day = intval($value);
			
			if($day > 7) {
				$day = 1;
			}

		} else {

			$date = date_parse($value);

			if(is_array($date) && is_array($date['relative'])) {
				$day = $date['relative']['weekday'];
			}
		}

		if ($operator == "==") {
			return ($toDay == $day);
		} 
		else if ($operator == "is not") {
			return ($toDay != $day);
		}
		else if ($operator == "is after") {
			return ($toDay > $day);
		} 
		else if ($operator == "is before") {
			return ($toDay < $day);
		}
	}


	function day_of_month_callback($value, $operator) {
		
		$current_time = current_time('timestamp');
		$toDay = date('d', $current_time);
		
		$day = intval($value);

		if ($operator == "==") {
			
			return ($toDay == $day);

		} else if ($operator == "is after") {
			return ($toDay > $day);

		} else if ($operator == "is before") {
			return ($toDay < $day);
		}
	}


	function cookie_list_callback($value, $operator) {
		
		$combined = array();

		foreach($_COOKIE as $key => $val) {
			$combined[] = $key.'='.$val;
		}

		$exists = in_array($value, $_COOKIE) || array_key_exists($value, $_COOKIE) || in_array($value, $combined);

		if ($operator == 'does not contain') {
			return !$exists;
		} else {
			return $exists;
		}
	}


	function session_variables_callback($value, $operator) {

		$combined = array();

		foreach($_SESSION as $key => $val) {
			$combined[] = $key.'='.$val;
		}

		$exists = in_array($value, $_SESSION) || array_key_exists($value, $_SESSION) || in_array($value, $combined);

		if ($operator == 'does not contain') {
			return !$exists;
		} else {
			return $exists;
		}
	}


	function register_category_condition() {

		$args = array(
			'hide_empty' => 0,
			'number' => 20,
			'order' => 'count',
		);
		$categories_raw = get_categories($args);
		$categories_clean = array();

		foreach ($categories_raw as $category) {
			array_push($categories_clean, $category->name);
		}

		$this->register_condition(
			'Post Category', 
			array('options' => $categories_clean, 'ajax' => true, 'placeholder' => __('Search category...', 'oxygen'), 'callback' => 'load_conditions_categories' ), 
			$this->condition_operators['simple'], 
			'category_callback',
			'Post');

	}

	function load_conditions_categories() {
	
		oxygen_vsb_ajax_request_header_check();

		$search_value = $_REQUEST['search_value'];
			
		$args = array(
			'hide_empty' => 0,
			'number' => 20,
			'order' => 'count',
			'name__like' => $search_value
		);

		$categories_raw = get_categories($args);
		$categories_clean = array();

		foreach ($categories_raw as $category) {
			array_push($categories_clean, $category->name);
		}

		// Echo JSON
	  	header('Content-Type: application/json');
		echo json_encode($categories_clean);
		die();
	}


	function category_callback($value, $operator) {

		if($operator == "==") {
			if(in_category($value)) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "!=") {
			if(!in_category($value)) {
				return true;
			} else {
				return false;
			}
		}
	}

		
	function excerpt_callback($value, $operator) {
		
		$has_excerpt = has_excerpt(get_the_ID());
		$value = (bool) $value;

		return $this->eval_string($has_excerpt, $value, $operator);
	}
		

	function register_tag_condition() {

		$args = array(
			'hide_empty' => 0,
			'number' => 20,
			'order' => 'count',
		);
		$tags_raw = get_tags($args);
		$tags_clean = array();

		foreach($tags_raw as $tag) {
			array_push($tags_clean, $tag->name);
		}

		$this->register_condition(
			'Post Tag', 
			array(
				'options' => $tags_clean, 
				'ajax' => true, 
				'placeholder' => __('Search tag...', 'oxygen'),
				'callback' => 'load_conditions_tags'),
			$this->condition_operators['simple'], 
			'tag_callback',
			'Post');
	}

	function load_conditions_tags() {
	
		oxygen_vsb_ajax_request_header_check();

		$search_value = $_REQUEST['search_value'];

		$args = array(
			'hide_empty' => 0,
			'number' => 20,
			'order' => 'count',
			'name__like' => $search_value
		);

		$tags_raw = get_tags($args);
		$tags_clean = array();

		foreach($tags_raw as $tag) {
			array_push($tags_clean, $tag->name);
		}

		// Echo JSON
	  	header('Content-Type: application/json');
		echo json_encode($tags_clean);
		die();
	}


	function tag_callback($value, $operator) {

		if($operator == "==") {
			if(has_tag($value)) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "!=") {
			if(!has_tag($value)) {
				return true;
			} else {
				return false;
			}
		}
	}

		
	function register_status_condition() {
		
		$stati = get_post_stati();
		$stati_clean = array();
		
		foreach($stati as $status) {
			array_push($stati_clean, $status);
		}

		$this->register_condition(
			'Post Status', 
			array('options' => $stati_clean, 'custom' => false), 
			$this->condition_operators['simple'], 
			'status_callback', 
			'Post');
	}

		
	function status_callback($value, $operator) {
		
		$status = (string) get_post_status();
		$value = (string) $value;

		return $this->eval_string($status, $value, $operator);
	}
		
		
	function post_content_empty_callback($value, $operator) {
		
		global $post;

		$content = $post->post_content;
		$value = (bool) $value;
		$is_empty = null;
		
		if( strlen($content) == 0 ) {
			$is_empty = true;
		} else {
			$is_empty = false;
		}
		
		return $this->eval_string($is_empty, $value, $operator);
	}

		
	function register_user_can_condition() {
		
		global $wp_roles;
	    $all_caps = array_keys($wp_roles->roles['administrator']['capabilities']);

		$this->register_condition(
			'User Can', 
			array('options' => $all_caps, 'custom'=>true), 
			array('--'), 
			'user_can_callback',
			'User');
	}

		
	function user_can_callback($value, $operator) {
			
		if( current_user_can($value) ) {
			return true;
		} else {
			return false;
		}
	}
		
		
	function username_callback($value, $operator) {
		
		$current_username = (string) wp_get_current_user()->user_login;
		$value = (string) $value;
		return $this->eval_string($current_username, $value, $operator);
	}


	function dynamic_data_callback($value, $operator, $shortcode) {

		// sign the shortcode before eval
		if(stripos($shortcode, '[oxygen')  !== false) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($shortcode));
		}
		else {
			return false;
		}
		
		$executed_shortcode = do_shortcode( $shortcode );

		if ($operator == "==") {
			if ($executed_shortcode == $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "!=") {
			if ($executed_shortcode != $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == ">") {
			if ($executed_shortcode > $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "<") {
			if ($executed_shortcode < $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == ">=") {
			if ($executed_shortcode >= $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "<=") {
			if ($executed_shortcode <= $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "contains") {
			if (strpos($executed_shortcode, $value) !== false) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "does_not_contain") {
			if (strpos($executed_shortcode, $value) === false) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "is_blank") {
			if (empty($executed_shortcode) ) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "is_not_blank") {
			if ( !empty($executed_shortcode) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	public static function do_shortcode($matches) {
		
		return "'".do_shortcode($matches[0])."'";
	}

	static function eval_condition($conditions) {

		$result = true;
		$logic = preg_replace_callback('/\[oxygen ([^\]]*)\]([^\"\[\s]*)/i', array( __CLASS__ , 'do_shortcode'), $conditions);

		$logic = str_replace('\n', ' ', $logic);

		ob_start();
		
		if (strlen($logic) > 0) {
			$result = eval('return ('.$logic.') !== false;');
		}
		
		ob_end_clean();

		return $result;
	}


	/**
	 * Built-in conditions & helper functions
	 *
	 * @since 2.4
	 * @author Gagan & Elijah
	 */
	// Utilities for condition writing
		
	function eval_int($comp, $value, $operator) {
		
		if ($operator == "==" || $operator == "===") {
			if ($comp === $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "!=" || $operator == "!==") {
			if ($comp !== $value) {
				return true;
			}  else {
				return false;
			}
		} else if ($operator == ">=") {
			if ($comp >= $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "<=") {
			if ($comp <= $value) {
				return  true;
			} else {
				return false;
			}
		} else if ($operator == ">") {
			if ($comp > $value) {
				return  true;
			} else {
				return false;
			}
		} else if ($operator == "<") {
			if ($comp < $value) {
				return  true;
			} else {
				return false;
			}
		}
		
	}
		
	function eval_string($comp, $value, $operator) {
		
		$comp = strtolower($comp);
		$value = strtolower($value);
		
		if ($operator == "==") {
			if ($comp == $value) {
				return true;
			} else {
				return false;
			}
		} else if ($operator == "!=") {
			if ($comp != $value) {
				return true;
			}  else {
				return false;
			}
		} else if ($operator == 'contains') {
			if (strpos($comp, $value) !== false)  {
				return true;
			} else {
				return false;
			}
		}  else if ($operator == 'does not contain') {
			if (strpos($comp, $value) === false) {
				return  true;
			} else {
				return false;
			}
		}
	}
}

function register_oxy_conditions() {

	$ajax_action_hooks = array(
		'load_conditions_terms', 
		'load_conditions_tags',
		'load_conditions_categories',
		'ct_get_post_data',
		'ct_get_template_data',
	);

	global $pagenow;

    if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' || !is_admin() || (isset($_GET['action']) && in_array($_GET['action'], $ajax_action_hooks))) {
		
		global $OxygenConditions;
		$OxygenConditions = new OxygenConditions();
		
		/**
		 * Conditions API support for Oxygen before 3.3
		 */

		global $oxy_condition_operators;
		$oxy_condition_operators = $OxygenConditions->condition_operators;

		function oxygen_vsb_register_condition($tag, $values=array('options'=>array()), $operators=array(), $callback = null, $category = null ) {
			global $OxygenConditions;
			$OxygenConditions->register_condition($tag, $values, $operators, $callback, $category);
		}
		function oxy_condition_eval_int($comp, $value, $operator) {
			global $OxygenConditions;
			return $OxygenConditions->eval_int($comp, $value, $operator);
		}

		function oxy_condition_eval_string($comp, $value, $operator) {
			global $OxygenConditions;
			return $OxygenConditions->eval_string($comp, $value, $operator);
		}

	}
	else {
		function oxygen_vsb_register_condition($tag, $values=array(), $operators=array(), $callback = null, $category = null ) {
		}
		function oxy_condition_eval_int($comp, $value, $operator) {
		}
		function oxy_condition_eval_string($comp, $value, $operator) {
		}
	}
}

add_action( 'plugins_loaded', 'register_oxy_conditions', -1 );