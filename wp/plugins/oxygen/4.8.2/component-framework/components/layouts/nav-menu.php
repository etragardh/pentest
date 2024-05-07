<?php 

/**
 * Get WP Nav Menu instance and return rendered menu HTML
 * Editing something here also edit it in ajax.php!
 *
 * @since 2.0
 * @author Ilya K.
 */

oxygen_vsb_ajax_request_header_check();

$component_json = file_get_contents('php://input');
$component 		= json_decode( $component_json, true );
$options 		= $component['options']['original'];

?>

<div class='oxy-menu-toggle'>
	<div class='oxy-nav-menu-hamburger-wrap'>
		<div class='oxy-nav-menu-hamburger'>
			<div class='oxy-nav-menu-hamburger-line'></div>
			<div class='oxy-nav-menu-hamburger-line'></div>
			<div class='oxy-nav-menu-hamburger-line'></div>
		</div>
	</div>
</div>

<?php $menu = wp_nav_menu( array(
		"menu" 			=> isset( $options["menu_id"] ) ? $options["menu_id"] : null,
		"depth" 		=>  ( $options["dropdowns"] == "on" ) ? 0 : 1,
		"menu_class" 	=> "oxy-nav-menu-list",
		"fallback_cb" 	=> false,
		"echo" 			=> false
	) );

if ($menu!==false) :
	
	echo $menu;

else : ?>

	<div class="menu-example-menu-container"><ul id="menu-example-menu" class="oxy-nav-menu-list"><li id="menu-item-12" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-12"><a href="#">Example Menu</a></li>
		<li id="menu-item-13" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-13"><a href="#">Link One</a></li>
		<li id="menu-item-14" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-14"><a href="#">Link Two</a>
		<?php if ( $options["dropdowns"] == "on" ) : ?>
		<ul class="sub-menu">
			<li id="menu-item-15" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-15"><a href="#">Dropdown Link One</a></li>
			<li id="menu-item-17" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-17"><a href="#">Dropdown Link Two</a></li>
		</ul>
		<?php endif; ?>
		</li>
		<li id="menu-item-16" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-16"><a href="#">Link Three</a></li>
	</ul></div>

<?php endif;