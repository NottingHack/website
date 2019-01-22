<?php
/*
Template Name: Blog - Standard
*/

$use_th_bittersweet_pagination = false;
if(is_front_page()) {
    $use_th_bittersweet_pagination=true;
}
?>

<?php global $post;  ?>
<?php include( locate_template( 'templates/page-layout.php' ) ); ?>
<?php
$th_no_sidebar_class = ' th-no-sidebar';
if(isset($has_sidebar) && $has_sidebar){
    $th_no_sidebar_class = false;
}
?>
<div class="inner-container<?php echo esc_attr($th_no_sidebar_class); ?>">
	<?php include( locate_template( 'templates/page-header.php' ) ); // Page Header Template ?>

    <?php
    if (!isset($key)){
        $key = "standard-blog";
    }
    $th_empty_class = false;
    if (th_empty_content($post->post_content)) {
        $th_empty_class = " th-editor-empty";
    } ?>
    <?php if ( have_posts() ) : ?>
        <section class="content-editor<?php echo esc_attr($th_empty_class); ?>">
            <?php while (have_posts()) : the_post(); ?>
                <?php the_content(); ?>
                <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
            <?php endwhile; ?>
        </section>
    <?php endif; ?>
    <?php 
	//-----------------------------------------------------
	// OPEN | OUTER Container + Row
	//-----------------------------------------------------
	echo wp_kses_post($outer_container_open) . wp_kses_post($outer_row_open); // Outer Tag Open ?>
    
    <?php 
	//-----------------------------------------------------
	// OPEN | Wrapper Class - Support for sidebar
	//-----------------------------------------------------
    echo wp_kses_post($main_class_open);  ?>
    
    <?php
	//-----------------------------------------------------
	// OPEN | Section + INNER Container
	//----------------------------------------------------- ?>
    
    <?php
	
	$masonry_template_key = '';
	$masonry_section_class = 'standard-blog';
	$masonry_row_class = '';
	$masonry_div_class = 'col-md-12';
	
	// Set Image Sizes
	$image_size = 'th_img_lg';
	if($has_sidebar){
		$image_size = 'th_img_lg';
	}
	$automatic_post_excerpts = true;
	if ( function_exists( 'get_theme_mod' ) ) {
		$automatic_post_excerpts = get_theme_mod( 'themo_automatic_post_excerpts', true );
	}
		
	?>

    <section id="<?php echo sanitize_html_class($key).'_content'; ?>" class="<?php echo sanitize_text_field($masonry_section_class); ?>">
	<?php echo wp_kses_post($inner_container_open);?>

	<?php
    //-----------------------------------------------------
    // LOOP
    //----------------------------------------------------- ?>

    <?php

    if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
    elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
    else { $paged = 1; }

    // Metabox options to filter by category.
    $themo_blog_cat_array = get_post_meta($post->ID, 'themo_category_checkbox', true );

    $themo_cat_arg = false;

    // Check if array is returned, if so implode, if not continue.
    if(isset($themo_blog_cat_array)){
        if(is_array($themo_blog_cat_array)) {
            $themo_blog_categories = implode(',', $themo_blog_cat_array);
        }else{
            $themo_blog_categories = $themo_blog_cat_array;
        }

        //are there any category ID's present? Continue, else do nothing.
        if($themo_blog_categories > ""){
            $themo_cat_arg = "cat=".$themo_blog_categories."&";
        }
    }

    query_posts($themo_cat_arg.'post_type=post&post_status=publish&paged='. $paged); ?>
    
    <div class="<?php echo sanitize_text_field($masonry_row_class); ?> row">
		<?php if (!have_posts()) : ?>
            <div class="alert">
            <?php esc_html_e('Sorry, no results were found.', 'stratus'); ?>
            </div>
            <?php get_search_form(); ?>
        <?php endif; ?>
            
		<?php while (have_posts()) : the_post(); ?>
		<?php
        $format = get_post_format();
        if ( false === $format ) {
        $format = 'standard';
        }
        ?>
            <div <?php post_class($masonry_div_class); ?> >
				<?php get_template_part('templates/content', $format); ?>
			</div><!-- /.col-md --> 
        <?php endwhile; ?>	
    </div><!-- /.row -->

    <div class="row">
		<?php if ($wp_query->max_num_pages > 1) : ?>
            <nav class="post-nav">
                <ul class="pager">
                    <?php if($use_th_bittersweet_pagination){
                        th_bittersweet_pagination();
                    }else{ ?>
                    <li class="previous"><?php next_posts_link(esc_html__('&larr; Older posts', 'stratus')); ?></li>
                    <li class="next"><?php previous_posts_link(esc_html__('Newer posts &rarr;', 'stratus')); ?></li>
                    <?php } ?>
                </ul>
            </nav>
        <?php endif; ?>
	</div>
    
    <?php wp_reset_postdata(); ?>
    
	<?php
	//-----------------------------------------------------
	// CLOSE | Section + INNER Container
	//----------------------------------------------------- ?>
	<?php echo wp_kses_post($inner_container_close);?>
	</section>

	<?php 
    //-----------------------------------------------------
	// CLOSE | Main Class
	//-----------------------------------------------------
    echo wp_kses_post($main_class_close); ?>
    
    <?php
    //-----------------------------------------------------
	// INCLUDE | Sidebar
	//-----------------------------------------------------
    include themo_sidebar_path(); ?>              
    
    <?php
	//-----------------------------------------------------
	// CLOSE | OUTER Container + Row
	//----------------------------------------------------- 
    echo wp_kses_post($outer_container_close) . wp_kses_post($outer_row_close); // Outer Tag Close ?>
</div><!-- /.inner-container -->