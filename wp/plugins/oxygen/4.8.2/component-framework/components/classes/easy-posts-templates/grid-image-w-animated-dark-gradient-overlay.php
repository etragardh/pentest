<div class='oxy-post'>
  
  <div class='oxy-post-padding'>
    
    <a class='oxy-post-image' href='<?php the_permalink(); ?>'>
      <div class='oxy-post-image-fixed-ratio' style='background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);'>
      </div>
      
      <div class='oxy-post-image-date-overlay'>
		<?php the_time(get_option('date_format')); ?>
	  </div>
      
	</a>
  
    <div class='oxy-post-wrap'>

      <a class='oxy-post-title' href='<?php the_permalink(); ?>'><?php the_title(); ?></a>
      
      <div class='oxy-post-content-and-link-wrap'>
        <div class='oxy-post-content'>
            <?php the_excerpt(); ?>
        </div>
      </div>
      
    </div>
    
  </div>

</div>