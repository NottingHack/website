<?php
	list($key, $show_header, $page_header_float, $masonry) = themo_return_header_sidebar_settings();
?>
<?php include( locate_template( 'templates/page-layout.php' ) ); ?>
<?php
$th_no_sidebar_class  = false;
if(is_archive() || is_home()){
    $th_no_sidebar_class = ' th-no-sidebar';
    if(isset($has_sidebar) && $has_sidebar){
        $th_no_sidebar_class = false;
    }
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
    
	<section id="<?php echo sanitize_html_class($key).'_content'; ?>" <?php if(is_archive() || is_search() || is_home() ){echo "class='standard-blog'";}?>>
	<?php echo wp_kses_post($inner_container_open);?>

	<?php
    //-----------------------------------------------------
    // LOOP
    //----------------------------------------------------- ?>
    
    <div class="row">
    	<div class="col-md-12">
			<?php if (!have_posts()) : ?>
            	<div class="error-404">
                    <p><?php esc_html_e('Sorry, the page you are looking for has not been found.', 'stratus'); ?></p>
                    <br />
                    <?php get_search_form(); ?>
            	</div>
        	<?php endif; ?>
            
			<?php while (have_posts()) : the_post(); ?>
			<?php
        	$format = get_post_format();
        	if ( false === $format ) {
        		$format = 'standard';
        	}
        	?>
            <div <?php post_class();?>>
				<?php get_template_part('templates/content', $format); ?>
			</div>
        	<?php endwhile; ?>	

			<?php if ($wp_query->max_num_pages > 1) : ?>
            <nav class="post-nav">
                <ul class="pager">
                    <li class="previous"><?php next_posts_link(esc_html__('&larr; Older posts', 'stratus')); ?></li>
                    <li class="next"><?php previous_posts_link(esc_html__('Newer posts &rarr;', 'stratus')); ?></li>
                </ul>
            </nav>
            <?php endif; ?>

		</div><!-- /.col-md-12 -->                  	
    </div><!-- /.row -->

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