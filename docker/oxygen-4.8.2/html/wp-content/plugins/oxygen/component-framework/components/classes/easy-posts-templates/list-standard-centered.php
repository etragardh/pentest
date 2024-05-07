<div class='oxy-post'>
    
	<a class='oxy-post-title' href='<?php the_permalink(); ?>'><?php the_title(); ?></a>

  	<div class='oxy-post-meta'>

  		<div class='oxy-post-meta-date oxy-post-meta-item'>
			<?php the_time(get_option('date_format')); ?>
		</div>

		<div class='oxy-post-meta-author oxy-post-meta-item'>
			<?php the_author(); ?>
		</div>

		<div class='oxy-post-meta-comments oxy-post-meta-item'>
			<a href='<?php comments_link(); ?>'><?php comments_number(); ?></a>
		</div>

	</div>

    <?php 
    if (has_post_thumbnail()) {
      ?>
	  <a href='<?php the_permalink(); ?>'><img src='<?php the_post_thumbnail_url(); ?>' class='oxy-post-image' /></a>
	  <?php
    }
    ?>

	<div class='oxy-post-content'>
		<?php the_excerpt(); ?>
	</div>

	<a href='<?php the_permalink(); ?>' class='oxy-read-more'>Read More</a>

</div>