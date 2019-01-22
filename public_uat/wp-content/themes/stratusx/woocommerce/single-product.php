<?php
/**
 * The Template for displaying all single products
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 */
   // list($key, $show_header, $page_header_float, $masonry) = themo_return_header_sidebar_settings(get_post_type( $post ));
?>
<?php include( locate_template( 'templates/page-layout.php' ) ); ?>
<div class="inner-container">
	<?php 	
	//-----------------------------------------------------
	// Include Header Template File
	//-----------------------------------------------------
    include( locate_template( 'templates/page-header.php' ) ); // Page Header Template ?>
    
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
    
	<section id="<?php echo 'themo_woocommerce_layout_content'; ?>" <?php if(is_archive() || is_search() || is_home() ){echo "class='standard-blog'";}?>>
	<?php echo wp_kses_post($inner_container_open);?>

	<?php
    //-----------------------------------------------------
    // LOOP
    //----------------------------------------------------- ?>
    
    <div class="row">
    	<div class="col-md-12">
			
			<?php woocommerce_content(); ?>

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