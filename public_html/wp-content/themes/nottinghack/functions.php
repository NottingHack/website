<?php
/**
 * Nottinghack functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 * 
 * @package WordPress
 * @subpackage Nottinghack
 * @since Nottinghack 1.0
 */

/* Carousel data
   This will be pulled onto the front page to create the carousel. */
$nh_c_time_move = 5000;
$nh_c_time_anim = 1000;
$nh_carousel = array(
					array(
						 'image'	=>	'thumbs.jpg',
						 'title'	=>	'What Can I Do?',
						 'paras'	=>	array(
						 					  "Absolutely anything!",
						 					  "(As long as it's legal!)",
						 					  "Join us on a Wednesday Open Hack Night from 6:30pm to find out more."
						 					  ),
						 'href'		=>	'http://nottinghack.org.uk/?page_id=213',
						 'type'		=>	'internal',
						 'link'		=>	'more details',
						 ),
					/*array(
						 'image'	=>	'barcamp.jpg',
						 'title'	=>	'Bar Camp!',
						 'paras'	=>	array(
						 					  "Bar Camp will take place at Nottingham Hackspace on 23rd and 24th July.",
											  "Click below to find out more and get your tickets.",
						 					  ),
						 'href'		=>	'http://bcnott.co.uk/',
						 'type'		=>	'external',
						 'link'		=>	'more details',
						 ),
					array(
						 'image'	=>	'crafty.jpg',
						 'title'	=>	'Sew Much Fun',
						 'paras'	=>	array(
						 					  "The first and third Monday of the month is Sew Much Fun - a new craft night.",
						 					  ),
						 'href'		=>	'http://nottinghack.org.uk/?page_id=724',
						 'type'		=>	'internal',
						 'link'		=>	'more details',
						 ),
					array(
						 'image'	=>	'openday.jpg',
						 'title'	=>	'Big Open Day Event',
						 'paras'	=>	array(
						 					  "On the 29th May, over a hundred people visited Nottingham Hakspace to celebrate our Big Open Day Event.  With soldering, lock picking, chain mail, 3D printing and much, much more the day was a massive success.",
						 					  "Click on the link below to see some pictures from the event.",
						 					  ),
						 'href'		=>	'http://www.flickr.com/photos/nottinghack/sets/72157626706422487/',
						 'type'		=>	'external',
						 'link'		=>	'more details',
						 ),*/
					array(
						 'image'	=>	'matrix.jpg',
						 'title'	=>	'Become a Member',
						 'paras'	=>	array(
						 					  "Become a member and start enjoying all the member benefits!",
						 					  "Visit us on an Open Night and find out all about joining us.",
						 					  ),
						 'href'		=>	'http://nottinghack.org.uk/?page_id=276',
						 'type'		=>	'internal',
						 'link'		=>	'more details',
						 ),
					array(
						 'image'	=>	'space.jpg',
						 'title'	=>	'Host your Event with Us',
						 'paras'	=>	array(
						 					  "With large open plan space and very reasonable costs, Nottingham Hackspace is perfect for a wide range of events.",
											  "If you are interested in hosting you event with us, please contact us using the link below."
						 					  ),
						 'href'		=>	'mailto:nottinghack@gmail.com?subject=Event%20Hosting',
						 'type'		=>	'internal',
						 'link'		=>	'contact us',
						 ),
					);

function nh_init_method() {
	if (!is_admin()) {
    	wp_deregister_script( 'jquery' );
	    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js');
    	wp_enqueue_script( 'jquery' );
    }
}    
 
add_action('init', 'nh_init_method');
add_filter( 'show_admin_bar', '__return_false' );


/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run twentyten_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'nottinghack_setup' );

if ( ! function_exists( 'nottinghack_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyten_setup() in a child theme, add your own twentyten_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 *
 * @since Nottinghack 1.0
 */
function nottinghack_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'nottinghack', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'nottinghack' ),
	) );
	
	// Enable feature image
	add_theme_support( 'post-thumbnails' );
}
endif;







/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * @since Nottinghack 1.0
 * @uses register_sidebar
 */
function nottinghack_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'nottinghack' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'nottinghack' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'nottinghack' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'nottinghack' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'nottinghack' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'nottinghack' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Area 7, located on the front page only. Empty by default.
	register_sidebar( array(
		'name' => __( 'Top Side Front Page Widget Area', 'nottinghack' ),
		'id' => 'front-top-side-widget-area',
		'description' => __( 'Top side front page widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Area 8, located on the front page only. Empty by default.
	register_sidebar( array(
		'name' => __( 'Bottom Side Front Page Widget Area', 'nottinghack' ),
		'id' => 'front-bottom-side-widget-area',
		'description' => __( 'Bottom side front page widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Area 9, located on the front page only. Empty by default.
	register_sidebar( array(
		'name' => __( 'Bottom Front Page Widget Area', 'nottinghack' ),
		'id' => 'front-bottom-widget-area',
		'description' => __( 'Bottom front page widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Area 7, located on the front page only. Empty by default.
	register_sidebar( array(
		'name' => __( 'New Front Page Widget Area', 'nottinghack' ),
		'id' => 'front-widget-area',
		'description' => __( 'Front page widget area', 'nottinghack' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running nottinghack_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'nottinghack_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * @since Nottinghack 1.0
 */
function nottinghack_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'nottinghack_remove_recent_comments_style' );

if ( ! function_exists( 'nottinghack_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Nottinghack 1.0
 */
function nottinghack_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'nottinghack_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Nottinghack 1.0
 */
function nottinghack_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'twentyten' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;


if ( ! function_exists( 'nottinghack_carousel_images' ) ) :
/**
 * Prints HTML for carousel images
 *

 * @since Nottinghack 1.0
 */
function nottinghack_carousel_images() {
	global $nh_carousel;
	
	foreach ($nh_carousel as $image) {
		echo('<img src="' . get_bloginfo('template_url') . '/images/carousel/' . $image['image'] . '" width="620" height="250" alt="' . $image['title'] . '" />');
	}
}
endif;

if ( ! function_exists( 'nottinghack_carousel_js' ) ) :
/**
 * Prints JS for carousel
 *
 * @since Nottinghack 1.0
 */
function nottinghack_carousel_js() {
	global $nh_carousel, $nh_c_time_move, $nh_c_time_anim;
	
	echo('var iNumImgs = ' . count($nh_carousel) . ';' . "\n");
	echo('var iTimeMove = ' . $nh_c_time_move . ';' . "\n");
	echo('var iTimeAnim = ' . $nh_c_time_anim . ';' . "\n\n");
	
	echo('var aTexts = new Array();' . "\n");
	for ($i = 0; $i < count($nh_carousel); $i++) {
		echo('aTexts[' . $i . '] = new Object;' . "\n");
		echo('aTexts[' . $i . '].title = "' . $nh_carousel[$i]['title'] . '";' . "\n");
		echo('aTexts[' . $i . '].paras = new Array(' . "\n");
		echo('"' . implode('",' . "\n" . '"', $nh_carousel[$i]['paras']) . '"' . "\n");
		echo(');' . "\n");
		echo('aTexts[' . $i . '].href = "' . $nh_carousel[$i]['href'] . '";' . "\n");
		echo('aTexts[' . $i . '].type = "' . $nh_carousel[$i]['type'] . '";' . "\n");
		echo('aTexts[' . $i . '].link = "' . $nh_carousel[$i]['link'] . '";' . "\n");
	}
	
	
}
endif;

if ( ! function_exists( 'nottinghack_carousel_ind' ) ) :
/**
 * Prints HTML for carousel indicators
 *
 * @since Nottinghack 1.0
 */
function nottinghack_carousel_ind() {
	global $nh_carousel;
	
	echo('<ul>' . "\n");
	for ($i = 0; $i < count($nh_carousel); $i++) {
		echo('<li id="nh-ind-' . $i . '"');
		if ($i == 0) {
			echo(' class="on"');
		}
		echo('><a href="#">O</a></li>' . "\n");
	}
	echo('</ul>' . "\n");
}
endif;

?>
