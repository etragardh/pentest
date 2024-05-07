<?php

if (have_comments()) {

	?>

	<h3 class="comments-title">
		<?php
		echo Oxygen_VSB_Comments_List::util_title();
		?>
	</h3>

	<ol class="comments-list">

	    <?php

	    wp_list_comments(
	    	array(

	    		'style' => 'ol',
	    		'format' => 'html5',
	    		'avatar_size' => '100'
	    	)
	    );

	    ?>

	</ol>

	<div class="comments-navigation">
		<div class='previous-comments-link'><?php previous_comments_link(); ?></div>
		<div class='next-comments-link'><?php next_comments_link(); ?></div>
	</div>

	<?php

}

?>