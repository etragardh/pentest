<a class='oxy-post' href='<?php the_permalink(); ?>'>
  <div class='oxy-post-padding'>
    <div class='oxy-post-image'>
      <div class='oxy-post-image-fixed-ratio' style='background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);'>
      </div>            
	</div>
  
    <div class='oxy-post-overlay'>
      <h2 class='oxy-post-title'><?php the_title(); ?></h2>
    </div>
  </div>
</a>