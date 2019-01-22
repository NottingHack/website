<?php
list($key, $show_header, $page_header_float, $masonry) = themo_return_header_sidebar_settings();

if ( function_exists( 'get_theme_mod' ) ) {
    $themo_blog_masonry_style = get_theme_mod('themo_blog_index_layout_masonry', false);
} ?>
<?php include( locate_template( 'templates/page-layout.php' ) ); ?>

<?php
$th_no_sidebar_class = ' th-no-sidebar';
if((isset($has_sidebar) && $has_sidebar) || (isset($themo_blog_masonry_style) && $themo_blog_masonry_style)){
    $th_no_sidebar_class = false;
}
?>
<div class="inner-container<?php echo esc_attr($th_no_sidebar_class); ?>">
	<?php
	//-----------------------------------------------------
	// Include Header Template File
	//-----------------------------------------------------
	include( locate_template( 'templates/page-header-default.php' ) ); // Page Header Template ?>

    <?php
    //-----------------------------------------------------
    // OPEN | OUTER Container + Row
    //-----------------------------------------------------
	echo wp_kses_post($outer_container_open) . wp_kses_post($outer_row_open); // Outer Tag Open ?>

    <?php
    //-----------------------------------------------------
    // OPEN | Wrapper Class - Support for sidebar
    //-----------------------------------------------------
    echo wp_kses_post($main_class_open); ?>

    <?php
    //-----------------------------------------------------
    // OPEN | Section + INNER Container
    //----------------------------------------------------- ?>

    <?php

    $mas_sizer_class = false;
    if(isset($themo_blog_masonry_style) && $themo_blog_masonry_style){
        // Set Image Size
        $image_size = 'themo_blog_masonry';

        $masonry_template_key = '-masonry';
        $masonry_section_class = 'th-masonry-blog';
        $masonry_row_class = 'mas-blog';

        $mas_sizer_class = "mas-blog-post-sizer";

        if($has_sidebar){
            $masonry_div_class = 'mas-blog-post col-md-6';
        }else{
            $masonry_div_class = 'mas-blog-post col-lg-4 col-md-4 col-sm-6';
        }

    }else{
        // Set Image Sizes
        $image_size = 'th_img_xl';

        $masonry_template_key = '';
        $masonry_section_class = 'standard-blog';
        $masonry_row_class = '';
        $masonry_div_class = 'col-md-12';

        if($has_sidebar){
            $image_size = 'th_img_lg';
        }
    }



    $automatic_post_excerpts = true;
    if ( function_exists( 'get_theme_mod' ) ) {
        $automatic_post_excerpts = get_theme_mod( 'themo_automatic_post_excerpts', true );
    }

    ?>

    <section id="<?php echo esc_attr( $key ).'_content'; ?>" class="<?php echo esc_attr( $masonry_section_class ); ?>">
        <?php echo wp_kses_post( $inner_container_open );?>

        <?php
        //-----------------------------------------------------
        // LOOP
        //----------------------------------------------------- ?>

        <div class="<?php echo esc_attr( $masonry_row_class ); ?> row">
            <?php if (!have_posts()) : ?>
                <div class="alert">
                    <?php _e('Sorry, no results were found.', 'stratus'); ?>
                </div>
                <?php get_search_form(); ?>
            <?php endif; ?>

            <?php if(isset($themo_blog_masonry_style) && $themo_blog_masonry_style){ ?>
            <div class="<?php echo esc_attr( $mas_sizer_class ) . esc_attr(str_replace("mas-blog-post", "", $masonry_div_class) );?>"></div>
            <?php } ?>

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
                            <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'stratus')); ?></li>
                            <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'stratus')); ?></li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>



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
