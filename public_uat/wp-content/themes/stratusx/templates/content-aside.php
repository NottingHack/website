<?php 
global $masonry_template_key;
?>
<div class="post-inner">
	<div class="entry-content">
	    <?php the_content(); ?>
    </div>
	<?php get_template_part('templates/entry-meta'.$masonry_template_key); ?>
	<?php get_template_part('templates/entry-meta-footer'.$masonry_template_key); ?>
</div>