<?php
/**
 * Enqueue scripts and stylesheets
 */ 
function roots_scripts() {

	if (is_single() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
	wp_enqueue_script('jquery');

	/********************************
	Bootstrap + Vendor CSS / JS
	 ********************************/
	wp_register_script('t_vendor_footer', get_template_directory_uri() . '/assets/js/vendor/vendor_footer.js', array(), '1.1', true);
	wp_enqueue_script('t_vendor_footer');

	
	/********************************
		Main JS - Theme helpers
	********************************/  
  	wp_register_script('roots_main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.1', true);
	wp_enqueue_script('roots_main');


	/********************************
		NiceScroll
	********************************/
	if ( function_exists( 'get_theme_mod' ) ) {
		$smooth_scroll = get_theme_mod( 'themo_smooth_scroll', false );
		if ($smooth_scroll == true){
			wp_register_script('nicescroll', get_template_directory_uri() . '/assets/js/vendor/jquery.nicescroll.min.js', array(), '3.6.8', true);
  			wp_enqueue_script('nicescroll');
		}
	}
	
	/********************************
		Main Stylesheet
	********************************/  
	wp_register_style('roots_app',  get_template_directory_uri() . '/assets/css/app.css', array(), '1');
	wp_enqueue_style('roots_app');


    /********************************
    WooCommerce
     ********************************/
    // If woocommerce enabled then ensure shortcodes are respected inside our html metaboxes.
    if ( class_exists( 'woocommerce' ) ) {
        global $post;
        if(isset($post->ID) && $post->ID > 0){
            $themo_meta_data = get_post_meta($post->ID); // get all post meta data
            foreach ( $themo_meta_data as $key => $value ){ // loop
                $pos_html = strpos($key, 'themo_html_'); // Get position of 'themo_html_' in each key.
                $pos_content = strpos($key, '_content'); // Get position of '_content' in each key.
                if($pos_html == 0 && $pos_content > 0 && isset($value) && is_array($value) && isset($value[0]) && strstr( $value[0], '[product_page' )){
                    global $woocommerce;
                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                    wp_enqueue_script( 'prettyPhoto', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
                    wp_enqueue_script( 'prettyPhoto-init', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), $woocommerce->version, true );
                    wp_enqueue_style( 'woocommerce_prettyPhoto_css', $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css' );
                }
            }
        }
    }

    /********************************
    Custom Inline Styling
     ********************************/
    function th_inline_styles() {
        if ( function_exists( 'get_theme_mod' ) ) {
            $use_ludicrous_font = get_theme_mod( 'headers_typography_ludicrous', true);
            if (isset($use_ludicrous_font) && $use_ludicrous_font){
                $th_custom_css = "/* Include Ludirous Font */\n @font-face { font-family: 'ludicrous'; src:  url('".get_template_directory_uri()."/assets/fonts/ludicrous.woff') format('woff'), url('".get_template_directory_uri()."/assets/fonts/ludicrous.woff2')  format('woff2')}";
                wp_add_inline_style( 'roots_app', $th_custom_css );
            }
        }
    }
    //add_action( 'wp_enqueue_scripts', 'th_inline_styles', 101 );
		
	/********************************
		Child Theme
	********************************/
	if (is_child_theme()) {
		wp_register_style('roots_child', get_stylesheet_uri());
		wp_enqueue_style('roots_child');
	}

  
}
add_action('wp_enqueue_scripts', 'roots_scripts', 100);


