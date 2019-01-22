<?php
/**
 * Initial setup and constants
 *
 * @author     @retlehs
 * @link 	   http://roots.io
 * @editor     Themovation <themovation@gmail.com>
 * @version    1.0
 */

//-----------------------------------------------------
// after_setup_theme
// Perform basic setup, registration, and init actions
// for this theme.
//-----------------------------------------------------


add_action('after_setup_theme', 'themo_setup');
 
function themo_setup() {

    // Make theme available for translation
    // Get the locale
    $locale = apply_filters('theme_locale', get_locale(), 'stratus');
    // Try and load the user generated .mo outside of the theme directory.
    // It's name convention is stratus-en_US.mo
    load_textdomain('stratus', WP_LANG_DIR.'/stratus/'.'stratus'.'-'.$locale.'.mo');
    // Last, load our default (if we have one). Name convetion is just en_US.mo (not including theme name).
    load_theme_textdomain('stratus', get_template_directory() . '/languages');


	// Register wp_nav_menu() menus (http://codex.wordpress.org/Function_Reference/register_nav_menus)
	register_nav_menus(array(
	'primary_navigation' => esc_html__('Primary Navigation', 'stratus'),
	));

	// title tag support
	add_theme_support( 'title-tag' );

	// Add post thumbnails (http://codex.wordpress.org/Post_Thumbnails)
	add_theme_support('post-thumbnails');
	// set_post_thumbnail_size(150, 150, false);

	if ( function_exists( 'add_image_size' ) ) { 
		// Set Image Size for Logo
		if ( function_exists( 'get_theme_mod' ) ) {
			$logo_height = get_theme_mod( 'themo_logo_height', 100 );
			add_image_size('themo-logo', 9999, $logo_height); //  (unlimited width, user set height)	
		}else{
			add_image_size('themo-logo', 9999, 100); // (unlimited width, 100px high)	
		}

        // NEW Sizes
        add_image_size('th_img_xs', 0, 80); // 80 high
        add_image_size('th_img_sm_landscape', 394, 303, array( 'center', 'center' )); // 394 w / 303 h
        add_image_size('th_img_sm_portrait', 394, 512, array( 'center', 'center' )); // 394 w / 512 h
        add_image_size('th_img_sm_square', 394, 394, array( 'center', 'center' )); // 394 w / 394 h
        add_image_size('th_img_sm_standard', 394, 303); // 394 w / 303 h

        add_image_size('th_img_md_landscape', 605, 465, array( 'center', 'center' )); // 605 w / 465 h
        add_image_size('th_img_md_portrait', 605, 806, array( 'center', 'center' )); // 394 w / 806 h
        add_image_size('th_img_md_square', 605, 605, array( 'center', 'center' )); // 605 w / 605 h

        add_image_size('th_img_lg', 915, 700); // 915 w / 700 h
        add_image_size('th_img_xl', 1240, 950); // 1240 w / 700 h
        add_image_size('th_img_xxl', 1920, 1080); // 915 w / 700 h
		
	}

	
  
	// Add post formats (http://codex.wordpress.org/Post_Formats)
	add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio'));

}


