<?php while (have_posts()) : the_post(); ?>
<article <?php post_class(); ?>>
    <header>
		<?php if ( has_post_thumbnail() ) { ?>                        
        <div class="blog-post-image">
            <a title="<?php printf(esc_html__('Permanent Link to %s', 'stratus'), get_the_title()); ?>" href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(''); ?>
            </a>
        </div>    
        <?php } ?>    
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
	    <?php the_content(); ?>
    </div>
    <footer>
	    <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . esc_html__('Pages:', 'stratus'), 'after' => '</p></nav>')); ?>
    </footer>
</article>
<?php comments_template('/templates/comments.php'); ?>
<?php endwhile; ?>
