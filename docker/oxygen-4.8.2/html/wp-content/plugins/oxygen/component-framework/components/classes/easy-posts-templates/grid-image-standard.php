<div class='oxy-post'>
  
    <a class='oxy-post-image' href='<?php the_permalink(); ?>'>
      <div class='oxy-post-image-fixed-ratio' style='background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);'>
      </div>
      
      <div class='oxy-post-image-date-overlay'>
		<?php the_time(get_option('date_format')); ?>
	  </div>
      
	</a>
  
	<a class='oxy-post-title' href='<?php the_permalink(); ?>'><?php the_title(); ?></a>

	<div class='oxy-post-meta'>

		<div class='oxy-post-meta-author oxy-post-meta-item'>
			<?php the_author(); ?>
		</div>

		<div class='oxy-post-meta-comments oxy-post-meta-item'>
			<a href='<?php comments_link(); ?>'><?php comments_number(); ?></a>
		</div>

	</div>

	<div class='oxy-post-content'>
		<?php the_excerpt(); ?>
	</div>

	<a href='<?php the_permalink(); ?>' class='oxy-read-more'>Read More</a>

</div>