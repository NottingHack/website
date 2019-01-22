<section class="content-editor">
<?php while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
    <?php if(!is_front_page()){
        wp_link_pages(array('before' => '<nav class="pagination th-pagination">', 'after' => '</nav>'));
    }?>
<?php endwhile; ?>
</section>