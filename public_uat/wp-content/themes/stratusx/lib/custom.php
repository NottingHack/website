<?php
/**
 * General Custom Functions
 *
 * @author     Themovation <themovation@gmail.com>
 * @copyright  2014 Themovation
 * @license    http://themeforest.net/licenses/regular
 * @version    1.0.5
 */

# 100 - Helper Functions
# 200 - WordPress Actions & Filters
# 300 - 3rd Party Plugins - Actions & Filters
# 400 - Option Tree FunctionsOption Tree Functions, Hooks, Filters
# 500 - Core / Special Functions
# 600 - Development Functions - to be removed.



//======================================================================
// 100 - Helper Functions
//======================================================================

// Check for empty conotent.
function th_empty_content($str) {
    return trim(str_replace('&nbsp;','',strip_tags($str))) == '';
}

// Duplicate post / page

/*
 * Gets the first image in a post content.
 * Used for helping missing featured images in blog posts.
 */

function th_catch_that_image() {
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];

    if(empty($first_img)){ //Defines a default image
        //$first_img = "/images/default.jpg";
        $first_img = false;
    }
    return $first_img;
}

/*
 * Function creates post duplicate as a draft and redirects then to the edit post screen
 */
function th_duplicate_post_as_draft(){
	global $wpdb;
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
		wp_die('No post to duplicate has been supplied!');
	}

	/*
	 * get the original post id
	 */
	$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
	/*
	 * and all the original post data then
	 */
	$post = get_post( $post_id );

	/*
	 * if you don't want current user to be the new post author,
	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	 */
	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;

	/*
	 * if post data exists, create the post duplicate
	 */
	if (isset( $post ) && $post != null) {

		/*
		 * new post data array
		 */
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);

		/*
		 * insert the post by wp_insert_post() function
		 */
		$new_post_id = wp_insert_post( $args );

		/*
		 * get all current post terms ad set them to the new post draft
		 */
		$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}

		/*
		 * duplicate all post meta just in two SQL queries
		 */
		$post_meta_infos = $wpdb->get_results($wpdb->prepare(
		                        "SELECT meta_key, meta_value 
                                FROM $wpdb->postmeta
                                WHERE post_id=%d",
                                $post_id));
		if (count($post_meta_infos)!=0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes($meta_info->meta_value);
                $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			//%1$d,%2$s,%3$s
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query);
		}


		/*
		 * finally, redirect to the edit post screen for the new draft
		 */
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	} else {
		wp_die('Post creation failed, could not find original post: ' . $post_id);
	}
}
add_action( 'admin_action_rd_duplicate_post_as_draft', 'th_duplicate_post_as_draft' );

/*
 * Add the duplicate link to action list for post_row_actions
 */
function th_duplicate_post_link( $actions, $post ) {
	if (current_user_can('edit_posts')) {
		$actions['duplicate'] = '<a href="admin.php?action=rd_duplicate_post_as_draft&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
	}
	return $actions;
}

/**
 * Detect duplicate post plugin. Don't add our won duplicate option if plugin is installed and active.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// check for plugin using plugin name
if( function_exists( 'duplicate_post_plugin_actions' ) ) {
	//plugin is activated
}else{
	add_filter( 'post_row_actions', 'th_duplicate_post_link', 10, 2 );
	add_filter('page_row_actions', 'th_duplicate_post_link', 10, 2);
}

// Pagination

if ( ! function_exists( 'th_bittersweet_pagination' ) ) {
	function th_bittersweet_pagination()
	{
		global $wp_query;
		$total = $wp_query->max_num_pages;

		if (get_option('permalink_structure')) {
			$format = '?paged=%#%';
		}

		$pages = paginate_links(array(
			'base' => get_pagenum_link(1) . '%_%',
			'format' => $format,
			'current' => max(1, get_query_var('paged')),
			'total' => $total,
			'type' => 'array',
			'prev_text' => esc_html__('Newer posts &rarr;', 'stratus'),
			'next_text' => esc_html__('&larr; Older posts', 'stratus'),
		));

		if (is_array($pages)) {
			foreach ($pages as $page) {
				if (strpos($page, 'Newer posts') !== false) {
					echo "<li class='next'>".wp_kses_post($page)."</li>";
				} elseif (strpos($page, 'Older posts') !== false) {
					echo "<li class='previous'>".wp_kses_post($page)>"</li>";
				}
			}
		}
	}
}


/*
 * backward compatible with pre-4.1
 * */

if ( ! function_exists( '_wp_render_title_tag' ) ) :
	function theme_slug_render_title() {
		?>
		<title><?php wp_title('|', true, 'right'); ?></title>
	<?php
	}
	add_action( 'wp_head', 'theme_slug_render_title' );
endif;


/*
 * If WooCommerce isn’t activated, return false.
 */

if ( ! function_exists( 'th_is_woocommerce_activated' ) ) {
    function th_is_woocommerce_activated() {
        if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
    }
}

//-----------------------------------------------------
// return woo page IDs
//-----------------------------------------------------
function themo_return_woo_page_ID(){
    if(th_is_woocommerce_activated() && is_woocommerce()){
        // Get the shop page ID, so we can get the custom header and sidebar options for Categories, archieve etc.
        if(get_option( 'woocommerce_shop_page_id' )){
            $woo_shop_page_id = get_option( 'woocommerce_shop_page_id' );
        }
        if(is_product()){
            return false;
        }elseif ((is_product_tag() || is_product_category() || is_shop()) && isset($woo_shop_page_id) && $woo_shop_page_id > ""){
            return $woo_shop_page_id;
        }
    }
    return false;
}



//-----------------------------------------------------
// Check if retina version of an image exists
// Takes attachecment ID
//-----------------------------------------------------
function themo_retina_version_exists($id){
	$post_id = (int) $id;

	if ( !$post = get_post( $post_id ) )
		return false;

	if ( !is_array( $imagedata = wp_get_attachment_metadata( $post->ID ) ) )
		return false;
	$file = get_attached_file( $post->ID );

	if ( !empty($imagedata['sizes']['themo-logo']['file']) && ($thumbfile = str_replace(basename($file), $imagedata['sizes']['themo-logo']['file'], $file)) && file_exists($thumbfile) ) {

		$path_parts = pathinfo($thumbfile);
		$image_find = $path_parts['dirname'].'/'.$path_parts['filename'].'@2x.'.$path_parts['extension'];

		if (file_exists ( $image_find )){
			return true;
		}
	}
	return false;
}

//-----------------------------------------------------
// Return Retina Logo src, heigh, width
// Takes attachecment ID
//-----------------------------------------------------

function themo_return_retina_logo($id){
	if(themo_retina_version_exists($id)){ // If we have a valid retina version, continue.

		$image_attributes  = wp_get_attachment_image_src( $id, 'themo-logo' );

		if(isset($image_attributes) && !empty( $image_attributes ) )
		{
			$logo_src = $image_attributes[0];
			$logo_height = $image_attributes[2];
			$logo_width = $image_attributes[1];;

			// Split up the URL so we can create the retina version.
			$logo_src_scheme = parse_url($logo_src,PHP_URL_SCHEME);
			$logo_src_host = parse_url($logo_src,PHP_URL_HOST);
			$logo_src_path = pathinfo(parse_url($logo_src,PHP_URL_PATH),PATHINFO_DIRNAME);
			$logo_src_filename = pathinfo(parse_url($logo_src,PHP_URL_PATH),PATHINFO_FILENAME);
			$logo_src_extension = pathinfo(parse_url($logo_src,PHP_URL_PATH),PATHINFO_EXTENSION);


			$retina_file_part = '@2x';
			$logo_retina_src = $logo_src_scheme . '://' . $logo_src_host . $logo_src_path . '/' . $logo_src_filename . $retina_file_part . '.' . $logo_src_extension;
			$logo_retina_height = $logo_height * 2;
			$logo_retina_width = $logo_width * 2;

			return array($logo_retina_src, $logo_retina_height, $logo_retina_width);

		}
	}
	return false;
}

//-----------------------------------------------------
// themo_content
//-----------------------------------------------------
function themo_content($content,$return_content=false){
	$content = wp_kses_post($content);
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
	if($return_content){
		return $content;
	}else{
		echo $content; // Sanitized just above. Retain Shortocde formatting / output.
	}
}




//-----------------------------------------------------
// returns an image via attachmentID
// @attachment_id - WordPress Media Library POST ID
// @classes - any classes to be inserted into tag if using tag mode
// @image_size - specify image size already created by add_image_size()
// @return_src - if you want to return the src only vs the img tag.
//-----------------------------------------------------
function themo_return_metabox_image($attachment_id = 0, $classes = null, $image_size = 'th_img_xl', $return_src = false, &$alt=""){
	if(!$attachment_id > "" ){
		return false;
	}

	if(!is_numeric($attachment_id)){ // We might be dealing with an URL vs ID, look up URL and get ID.
		$attachment_url = $attachment_id; // put URL in a local var
		$attachment_id = themo_return_attachment_id_from_url($attachment_url); // Search DB for URL and return ID.
	}

	if(!$attachment_id > "" ){
		return false;
	}

	$attachment_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

	if( ! empty( $attachment_alt ) && is_array($attachment_alt)) {
		$alt = trim(strip_tags($attachment_alt[0]));
	}else{
        $alt = $attachment_alt;
    }

	$image_attr = array(
		'class'	=> $classes,
		'alt'   => $alt
	);
	if ($return_src){
		$image_attributes = wp_get_attachment_image_src( $attachment_id, $image_size) ;
		if( $image_attributes ) {
			return $image_attributes[0];
		}else{
			return false;
		}

	}else{
		return wp_get_attachment_image( $attachment_id, $image_size, 0, $image_attr ) ;
	}

}

//-----------------------------------------------------
// themo_return_header_sidebar_settings
// Gets header and sidebar settings based on type page
//-----------------------------------------------------

function themo_return_header_sidebar_settings($post_type = false) {
    if (th_is_woocommerce_activated() && is_woocommerce()) { // Handle all Woo stuff...
        $key = 'themo_woo';
        $show_header = get_theme_mod( $key.'_show_header', true );
        $page_header_float = get_theme_mod( $key.'_header_float', "centered" );
        return array ($key, $show_header, $page_header_float,false);
	}elseif($post_type > ""){
        $key = $post_type."_layout";
        $show_header = get_theme_mod( $key.'_show_header', true );
        $page_header_float = get_theme_mod( $key.'_header_float', "centered" );
        $masonry = get_theme_mod( $key.'_masonry', "off" );
        return array ($key, $show_header, $page_header_float,$masonry);
    }elseif (is_home()) {
		$key = 'themo_blog_index_layout';
		$show_header = get_theme_mod( $key.'_show_header', true );
		$page_header_float = get_theme_mod( $key.'_header_float', "centered" );
		$masonry = get_theme_mod( $key.'_masonry', false );
		return array ($key, $show_header, $page_header_float,$masonry);
	}elseif (is_single()) {
		$key = 'themo_single_post_layout';
		$show_header = get_theme_mod( $key.'_show_header', true );
		$page_header_float = get_theme_mod( $key.'_header_float', "centered" );
		return array ($key, $show_header, $page_header_float,false);
	} elseif (is_archive()) {
		$key = 'themo_default_layout';
		$show_header = get_theme_mod( $key.'_show_header', true );
		$page_header_float = get_theme_mod( $key.'_header_float', "centered" );
		return array ($key, $show_header, $page_header_float,false);
	} elseif (is_search()) {
		$key = 'themo_default_layout';
		$show_header = get_theme_mod( $key.'_show_header', true );
		$page_header_float = get_theme_mod( $key.'_header_float', "centered" );
		return array ($key, $show_header, $page_header_float,false);
	} elseif (is_404()) {
		$key = 'themo_default_layout';
		$show_header = get_theme_mod( $key.'_show_header', true );
		$page_header_float = get_theme_mod( $key.'_header_float', "centered" );
		return array ($key, $show_header, $page_header_float,false);
	} else {
		$key = 'themo_default_layout';
		$show_header = get_theme_mod( $key.'_show_header', true );
		$page_header_float = get_theme_mod( $key.'_header_float', "centered" );
		return array ($key, $show_header, $page_header_float,false);
	}
}


//-----------------------------------------------------
// themo_is_element_empty
// returns true / falase
//-----------------------------------------------------
function themo_is_element_empty($element) {
	$element = trim($element);
	return empty($element) ? false : true;
}


//-----------------------------------------------------
// themo_return_attachment_id_from_url
// returns an image via attachmentID
// @attachment_id - WordPress Media Library POST ID
// @classes - any classes to be inserted into tag if using tag mode
// @image_size - specify image size already created by add_image_size()
// @return_src - if you want to return the src only vs the img tag.
//-----------------------------------------------------
function themo_return_attachment_id_from_url( $attachment_url = '' ) {
    // Sanitization
    $attachment_url = esc_url($attachment_url);
    global $wpdb;
    $attachment_id = false;
    // If there is no url, return.
    if ( '' == $attachment_url )
        return;
    // Get the upload directory paths
    $upload_dir_paths = wp_upload_dir();
    // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
    if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
        // Remove the upload path base directory from the attachment URL
        $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
        // Finally, run a custom database query to get the attachment ID from the modified attachment URL
        $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
    }
    return $attachment_id;
}


//-----------------------------------------------------
// Get Attachment ID from URL
// Use the following code to get the image you want, Please note that your image
// will have to be uploaded through WordPress in order for this to work.
// Adapt code as needed:
//-----------------------------------------------------

function themo_custom_get_attachment_id( $guid ) {
	// Prepare & Sanitization
	$guid = esc_url($guid);

	global $wpdb;

	/* nothing to find return false */
	if ( ! $guid )
		return false;

	/* get the ID */
	$id = $wpdb->get_var( $wpdb->prepare("SELECT p.ID FROM $wpdb->posts p WHERE p.guid = %s AND p.post_type = %s", $guid, 'attachment'));

	/* the ID was not found, try getting it the expensive WordPress way */
	if ( $id == 0 )
		$id = url_to_postid( $guid );

	return $id;
}


//-----------------------------------------------------
// Create retina-ready images
// Referenced via retina_support_attachment_meta().
//-----------------------------------------------------

function themo_retina_support_create_images( $file, $width, $height, $crop = false ) {
	if ( $width || $height ) {
		$resized_file = wp_get_image_editor( $file );
		if ( ! is_wp_error( $resized_file ) ) {
			$filename = $resized_file->generate_filename( $width . 'x' . $height . '@2x' );

			$resized_file->resize( $width * 2, $height * 2, $crop );
			$resized_file->save( $filename );

			$info = $resized_file->get_size();

			return array(
				'file' => wp_basename( $filename ),
				'width' => $info['width'],
				'height' => $info['height'],
			);
		}
	}
	return false;
}


//-----------------------------------------------------
// themo_return_outer_tag
// Returns output if $bool is true
//-----------------------------------------------------
function themo_return_outer_tag($output,$bool){
	if($bool){
		return $output;
	}
}

//-----------------------------------------------------
// themo_return_inner_tag
// Returns output if $bool is false
//-----------------------------------------------------
function themo_return_inner_tag($output,$bool){
	if(!$bool){
		return $output;
	}
}

//-----------------------------------------------------
// themo_has_sidebar
// Returns a boolean value if the page has a sidebar
// Takes pagelayout (full, right, left)
// Returns true there is a sidebar (left or right), false if anything else.
//-----------------------------------------------------
function themo_has_sidebar($pagelayout){
	if($pagelayout == 'right' ||  $pagelayout == 'left'){
		return true;
	}else{
		return false;
	}
}





//-----------------------------------------------------
// themo_return_social_icons
// Return background styling and html markup for
// Social Media Icons
//-----------------------------------------------------

function themo_return_social_icons() {
	$output = "";
	if ( function_exists( 'get_theme_mod' ) ) {
		/* get the slider array */
		$social_icons = get_theme_mod( 'themo_social_media_accounts', array() );
		//print_r($social_icons);
		if ( ! empty( $social_icons ) ) {
			foreach( $social_icons as $social_icon ) {
				if (isset($social_icon["themo_social_url"]) && $social_icon["themo_social_url"] >""){

                    // Link Target
                    $link_target = $social_icon["themo_social_url_target"];
                    $link_target_att = false;
                    if (isset($link_target) && $link_target) {
                        $link_target_att = "target=_blank ";
                    }

				    $output .= "<a ".$link_target_att." href='".$social_icon["themo_social_url"]."'><i class='".$social_icon["themo_social_font_icon"]."'></i></a>";
                }else{
                    $output .= "<i class='".$social_icon["themo_social_font_icon"]."'></i>";
                }

			}
		}
	}
	return $output;
}

//-----------------------------------------------------
// themo_return_payments_accepted
// Return background styling and html markup for
// Payments Accepted
//-----------------------------------------------------

function themo_return_payments_accepted() {
	$output = "";
	if ( function_exists( 'get_theme_mod' ) ) {
		/* get the slider array */
		$payments_accepted = get_theme_mod( 'themo_payments_accepted', array() );
		//print_r($social_icons);
		if ( ! empty( $payments_accepted ) ) {
			foreach( $payments_accepted as $payment_info ) {

				// Image
				$payment_logo_src = false;
				$payment_logo_width = false;
				$payment_logo_height = false;
				$payment_logo = $payment_info["themo_payments_accepted_logo"];
				if(isset($payment_logo) && $payment_logo > ""){
					$img_id = $payment_logo ;// themo_custom_get_attachment_id( $payment_logo );
					if($img_id > ""){
						$image_attributes = wp_get_attachment_image_src( $img_id, 'th_img_xs');
						if( $image_attributes ) {
							$payment_logo_src = $image_attributes[0];
							$payment_logo_width = $image_attributes[1];
							$payment_logo_height = $image_attributes[2];
							if(isset($payment_logo_width) && $payment_logo_width > ""){
								$payment_logo_width = "width='".esc_attr($payment_logo_width)."'";
							}
							if(isset($payment_logo_height) && $payment_logo_height > ""){
								$payment_logo_height = "height='".esc_attr($payment_logo_height)."'";
							}
						}
					}
				}

				// Link Target
                if (isset($payment_info["themo_payment_url_target"])) {
                    $link_target = $payment_info["themo_payment_url_target"];
                }

				$link_target_att = false;
				if (isset($link_target) && is_array($link_target)  && !empty($link_target)) {
					$link_target = $link_target[0];
					if($link_target == '_blank'){
						$link_target_att = "target='_blank'";
					}
				}elseif(isset($link_target) && $link_target){
                    $link_target_att = "target=_blank";
				}

				// Link
				$href_open = false;
				$href_close = false;
				$payment_link = $payment_info["themo_payment_url"];
				if(isset($payment_link) && $payment_link > ""){
					$href_open = "<a ".$link_target_att." href='".esc_url($payment_link)."'>";
					$href_close = '</a>';
				}
				if(isset($payment_logo_src) && $payment_logo_src > ""){
					$output .= $href_open . "<img src='".esc_url($payment_logo_src)."' alt='".esc_attr($payment_info["title"])."' " .$payment_logo_width ." ". $payment_logo_height. ">" . $href_close;
				}else{
                    if (isset($payment_info["title"])) {
                        $output .= $href_open . "<span class='th-payment-no-img'>" . $payment_info["title"] . "</span>" . $href_close;
                    }
                }
			}
		}
	}
	return $output;
}



//-----------------------------------------------------
// themo_return_contact_info
// Return background styling and html markup for
// Contact Info Widget
//-----------------------------------------------------
function themo_return_contact_info(){
	$output = "";

		if ( function_exists( 'get_theme_mod' ) ) {
			// Get icon block array from OT
			$icon_block = get_theme_mod( 'themo_contact_icons', array() );

			if (isset($icon_block) && is_array($icon_block)  && !empty($icon_block)) {

				$output .= "<div class='icon-blocks'>";

				foreach( $icon_block as $icon ) {
					$glyphicon_type = $substring = substr($icon["themo_contact_icon"], 0, strpos($icon["themo_contact_icon"], '-'));
                    if (isset($icon["themo_contact_icon_url_target"])) {
                        $link_target = $icon["themo_contact_icon_url_target"];
                    }

					$link_target_att = false;
					if (isset($link_target)  && $link_target) {
                        $link_target_att = "target='_blank'";
                    }
                    // Link
                    $href_open = false;
                    $href_close = false;
                    $contact_url = $icon["themo_contact_icon_url"];
                    if(isset($contact_url) && $contact_url > ""){
                        $href_open = "<a ".$link_target_att." href='".esc_url($contact_url)."'>";
                        $href_close = '</a>';
					}

					$output .= '<div class="icon-block">';
					$output .= "<p>".$href_open."<i class='".esc_attr($icon["themo_contact_icon"])."'></i><span>".wp_kses_post($icon["title"])."</span>".$href_close."</p>";
					$output .= '</div>';
				}
				$output .= "</div>";
			}
		}
	return $output;
}


//-----------------------------------------------------
// themo_return_footer_logo
// Return background styling and html markup for
// Footer Logo
//-----------------------------------------------------

function themo_return_footer_logo() {
    $output = "";
    if ( function_exists( 'get_theme_mod' ) ) {
        /* get the slider array */

        // Image
        $payment_logo_src = false;
        $payment_logo_width = false;
        $payment_logo_height = false;
        $footer_logo = get_theme_mod( 'themo_footer_logo', false );

        if(isset($footer_logo) && $footer_logo > ""){
            $img_id = themo_custom_get_attachment_id( $footer_logo );
            if($img_id > ""){
                $image_attributes = wp_get_attachment_image_src( $img_id, 'themo_featured');
                if( $image_attributes ) {
                    $footer_logo_src = $image_attributes[0];
                    $footer_logo_width = $image_attributes[1];
                    $footer_logo_height = $image_attributes[2];
                    if(isset($footer_logo_width) && $footer_logo_width > ""){
                        $footer_logo_width = "width='".esc_attr($footer_logo_width)."'";
                    }
                    if(isset($footer_logo_height) && $footer_logo_height > ""){
                        $footer_logo_height = "height='".esc_attr($footer_logo_height)."'";
                    }
                }
            }
        }


        // Link Target
        $link_target = get_theme_mod( 'themo_footer_logo_url_target', false );
        $link_target_att = false;
        if (isset($link_target) && !empty($link_target)) {
                $link_target_att = "target=_blank";
        }


        // Link
        $href_open = false;
        $href_close = false;
        $logo_link = get_theme_mod( 'themo_footer_logo_url', false );
        if(isset($logo_link) && $logo_link > ""){
            $href_open = "<a ".$link_target_att." href='".esc_url($logo_link)."'>";
            $href_close = '</a>';
        }

        if(isset($footer_logo_src) && $footer_logo_src > ""){
            $output .= $href_open . "<img src='".esc_url($footer_logo_src)."' " .$footer_logo_width ." ". $footer_logo_height. ">" . $href_close;
        }

    }
    return $output;
}


//======================================================================
// 200 - WordPress Actions & Filters
//======================================================================

# Actions
# Filters
# Plugins Actiosn and Filters

/* Admin noice for Master Slider */

// display custom admin notice
function th_master_slider_install_notice() {

    $th_screen = get_current_screen();

    //echo $th_screen->id;

    if ($th_screen->id === 'toplevel_page_masterslider') {

        $user_id = get_current_user_id();

        if ( get_user_meta( $user_id, 'th_ms_install_dismissed', true ) !== '1' ) { ?>
            <div class="notice notice-info">
                <p><?php _e('Looking for the sliders from the live demo? <a href="http://themovation.helpscoutdocs.com/article/198-master-slider-activation" target="_blank">Check out this article for how to import them</a>.', 'stratus'); ?> <a class="th-dismiss" href="?page=masterslider&th-ms-install-dismissed">Dismiss</a></p>
            </div>
            <?php

        }
        delete_user_meta($user_id, 'th_ms_install_dismissed');

    }
}
add_action('admin_notices', 'th_master_slider_install_notice');


function th_set_ms_install_dismissed() {
    $user_id = get_current_user_id();
    if ( isset( $_GET['th-ms-install-dismissed'] ) ){
        update_user_meta( $user_id, 'th_ms_install_dismissed', '1');
    }
}
add_action( 'admin_init', 'th_set_ms_install_dismissed' );

/**
 * Loads the child theme textdomain.
 */
function themo_child_theme_setup() {
    load_child_theme_textdomain( 'stratus', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'themo_child_theme_setup' );


/**
 * Customize Adjacent Post Link Order
 */


/**
 * Check if WPML is installed, add in Menu Classes to support dropdowns.
 *
 */

function th_wpml_new_submenu_class($menu) {
    $menu = preg_replace('/ class="sub-menu submenu-languages"/','/ class="dropdown-menu sub-menu submenu-languages"/',$menu);
    $menu = preg_replace('/ class="menu-item menu-item-language menu-item-language-current menu-item-has-children"/','/ class="dropdown menu-item menu-item-language menu-item-language-current menu-item-has-children"/',$menu);
    return $menu;
}

if ( function_exists('icl_object_id') ) {
    add_filter('wp_nav_menu_items','th_wpml_new_submenu_class');
}




function themo_adjacent_post_where($sql) {

	if ( !is_main_query() || !is_singular() )
		return $sql;

	$the_post = get_post( get_the_ID() );
	$patterns = array();
	$patterns[] = '/post_date/';
	$patterns[] = '/\'[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\'/';
	$replacements = array();
	$replacements[] = 'menu_order';
	$replacements[] = $the_post->menu_order;
	return preg_replace( $patterns, $replacements, $sql );
}


function themo_adjacent_post_sort($sql) {
	if ( !is_main_query() || !is_singular() )
		return $sql;

	$pattern = '/post_date/';
	$replacement = 'menu_order';
	return preg_replace( $pattern, $replacement, $sql );
}

if ( isset($_GET['portorder']) && $_GET['portorder'] == 'menu' ) {

	add_filter( 'get_next_post_where', 'themo_adjacent_post_where' );
	add_filter( 'get_previous_post_where', 'themo_adjacent_post_where' );
	add_filter( 'get_next_post_sort', 'themo_adjacent_post_sort' );
	add_filter( 'get_previous_post_sort', 'themo_adjacent_post_sort' );
}

function themo_add_query_vars_filter( $vars ){
	$vars[] = "portorder";
	return $vars;
}
add_filter( 'query_vars', 'themo_add_query_vars_filter' );

/**
 * Adds a pretty "Continue Reading" link to post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function themo_custom_excerpt_more( $output ) {
	if ( (has_excerpt() || themo_has_more()) && ! is_attachment() && get_post_type() != 'themo_tour' && get_post_type() != 'themo_portfolio') {
		$output .= ' &hellip; <a href="' . esc_url(get_permalink()) . '">' . esc_html__('Read More', 'stratus') . '</a>';
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'themo_custom_excerpt_more' );



function themo_read_more_link() {
	if (get_post_type() != 'themo_tour' && get_post_type() != 'themo_portfolio') {
		return ' &hellip; <a href="' . esc_url(get_permalink()) . '">' . esc_html__('Read More', 'stratus') . '</a>';
	}

}

add_filter( 'the_content_more_link', 'themo_read_more_link' );



function themo_has_more()
{
	global $post;
	if ( empty( $post ) ) return;

	if ($pos=strpos($post->post_content, '<!--more-->')) {
		return true;
	} else {
		return false;
	}
}


add_action('wp_head', 'themo_load_html5shiv_respond');
function themo_load_html5shiv_respond(){
    echo '<!--[if lt IE 9]>'."\n".'<script src="'.get_template_directory_uri() .'/assets/js/vendor/html5shiv.min.js"></script>'."\n".'<script src="'.get_template_directory_uri().'/assets/js/vendor/respond.min.js"></script>'."\n".'<![endif]-->'."\n";
}


//-----------------------------------------------------
// admin_enqueue_scripts - action
// Support for Meta Boxes (show / hide)
// Whenever a page template selected value changes,
// instantly hide/show the related metaboxs.
//-----------------------------------------------------
add_action('admin_enqueue_scripts', 'themo_admin_meta_show');

function themo_admin_meta_show()
{

	// Admin Styles
	wp_register_style( 'themo_admin_css', get_template_directory_uri() . '/assets/css/admin-styles.css', false, '1' );
	wp_enqueue_style( 'themo_admin_css' );

}

//-----------------------------------------------------
// clean_url - Filter
// Defer JS
// Adapted from https://gist.github.com/toscho/1584783
//-----------------------------------------------------
if ( ! function_exists( 'themo_add_defer_to_js' ) )
{
	function themo_add_defer_to_js( $url )
	{
		if (strpos($url, '#deferload')===false)
			return $url;
		else if (is_admin())
			return str_replace('#deferload', '', $url);
		else
			return str_replace('#deferload', '', $url)."' defer='defer";
	}
	add_filter( 'clean_url', 'themo_add_defer_to_js', 11, 1 );
}


//-----------------------------------------------------
// prepend_attachment - filter
// Set default image size on the attachment pages
//-----------------------------------------------------
add_filter('prepend_attachment', 'themo_prepend_attachment');
function themo_prepend_attachment($p) {
	return wp_get_attachment_link(0, 'th_img_xl', false);
}

//-----------------------------------------------------
// delete_attachment - filter
// Delete retina-ready images
// This function is attached to the 'delete_attachment' filter hook.
//-----------------------------------------------------
add_filter( 'delete_attachment', 'themo_delete_retina_support_images' );

function themo_delete_retina_support_images( $attachment_id ) {
	$meta = wp_get_attachment_metadata( $attachment_id );
	$upload_dir = wp_upload_dir();
	if (isset($meta['file']) && $meta['file'] > ""){
		$path = pathinfo( $meta['file'] );
		foreach ( $meta as $key => $value ) {
			if ( 'sizes' === $key ) {
				foreach ( $value as $sizes => $size ) {
					$original_filename = $upload_dir['basedir'] . '/' . $path['dirname'] . '/' . $size['file'];
					$retina_filename = substr_replace( $original_filename, '@2x.', strrpos( $original_filename, '.' ), strlen( '.' ) );
					if ( file_exists( $retina_filename ) )
						unlink( $retina_filename );
				}
			}
		}
	}
}

//-----------------------------------------------------
// wp_generate_attachment_metadata - filter
// Retina Support for Logo
// This function is attached to the 'wp_generate_attachment_metadata' filter hook.
//-----------------------------------------------------

// We can only add retina support after_setup_theme, when ot_get_option is available.
// We want to check if the user has disabled retina support before adding it automatically.
function themo_add_retina_support() {

	add_filter( 'wp_generate_attachment_metadata', 'themo_retina_support_attachment_meta', 10, 2 );

}
add_action( 'after_setup_theme', 'themo_add_retina_support' );

function themo_retina_support_attachment_meta( $metadata, $attachment_id ) {

	$retina_support = false; // Default to no retina support.
	if ( function_exists( 'get_theme_mod' ) ) {
		$retina_support = get_theme_mod( 'themo_retina_support', 'off' );
	}
	foreach ( $metadata as $key => $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $image => $attr ) {
				if(is_array( $attr )){
					if ($retina_support == 'on' || $image == 'themo-logo'){ // Always use retina for logo.
						themo_retina_support_create_images( get_attached_file( $attachment_id ), $attr['width'], $attr['height'], true );
					}
				}
			}
		}
	}
	return $metadata;
}

//-----------------------------------------------------
// wp_get_attachment_link - filter
// Lightbox Support
//-----------------------------------------------------
add_filter( 'wp_get_attachment_link' , 'themo_add_lighbox_data' );

function themo_add_lighbox_data ($content) {

	$postid = get_the_ID();
	$content = str_replace('<a', '<a class="thumbnail img-thumbnail"', $content);

	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = FALSE;
    //$doc->loadHTML($content);
    $doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

	$tags = $doc->getElementsByTagName('img');

	foreach ($tags as $tag) {
		$alt = $tag->getAttribute('alt');
	}

	$a_tag = $doc->getElementsByTagName('a');

	foreach ($a_tag as $tag) {
		$href = $tag->getAttribute('href');
		$image_large_src = "";
		// We need to get the ID by href
		// Check if this ID has a th_img_xxl size, if so replace href.


		if ($href > ""){ // If href is captured
			$image_ID = themo_return_attachment_id_from_url($href); // Get the attachment ID
			if ($image_ID > 0){ // If id has been captured, check for image size.
				$image_large_attributes = wp_get_attachment_image_src( $image_ID, "th_img_xl") ;

				if( $image_large_attributes ) { //  If there is th_img_xxl size, use it.
					$image_large_src = $image_large_attributes[0];
				}else{
					$image_large_src = wp_get_attachment_url( $image_ID );
				}
			}
		}

		// If a large size has been found, replace the original size.
		if ($image_large_src > ""){
			$content = str_replace($href, $image_large_src, $content);
		}
	}

	if (false !== strpos($href,'.jpg') || false !== strpos($href,'.jpeg') || false !== strpos($href,'.png') || false !== strpos($href,'.gif')) {
		// data-footer=\"future title / caption \"

        // Disable global lightbox by default.
        $elementor_global_image_lightbox = get_option('elementor_global_image_lightbox');
        if (!empty($elementor_global_image_lightbox) && $elementor_global_image_lightbox == 'yes') {
            $content = preg_replace("/<a/","<a data-title=\"$alt\" ",$content,1);
        }else{
            $content = preg_replace("/<a/","<a data-toggle=\"lightbox\" data-gallery=\"multiimages\" data-title=\"$alt\" ",$content,1);
        }


	}

	return $content;
}


function themo_portfolio_template_options( $query ) {

	if ( is_admin() || ! $query->is_main_query() )
		return;

	//http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts

}
//add_action( 'pre_get_posts', 'themo_portfolio_template_options', 1 );




//======================================================================
// 300 - 3rd Party Plugins - Actions & Filters
//======================================================================


// display custom admin notice
if ( defined('ENVATO_HOSTED_SITE') ) {
    // this is an envato hosted site so Skip
}else {
    add_action('admin_notices', 'th_admin_envato_market_auth_notice');
}

function th_admin_envato_market_auth_notice() {

    $screen = get_current_screen();


    if ('themes' == $screen->parent_base || 'envato-market' == $screen->parent_base){



        if(function_exists('envato_market')) {

            $option = envato_market()->get_options();

            if ( !$option || empty($option['token'])) {

                delete_option('dtbwp_update_notice');

                // we show an admin notice if it hasn't been dismissed
                $dissmissed_time = get_option('dtbwp_update_notice', false );

                if ( ! $dissmissed_time || $dissmissed_time < strtotime('-7 days') ) {


                    // Added the class "notice-my-class" so jQuery pick it up and pass via AJAX,
                    // and added "data-notice" attribute in order to track multiple / different notices
                    // multiple dismissible notice states ?>
                    <div class="notice notice-warning notice-dtbwp-themeupdates is-dismissible">
                        <p><?php
                            printf( __( '<a href="%s">Please activate</a> ThemeForest updates to ensure you have the latest version of this theme.','embark' ),  esc_url(admin_url( 'admin.php?page=envato-market')) );

                            ?></p>
                        <p>
                            <?php printf( __( '<a class="button button-primary" href="%s" target="_blank">Need help?</a>','embark' ),  esc_url('http://themovation.helpscoutdocs.com/article/128-how-to-update-your-theme') ); ?>

                        </p>
                    </div>
                    <script type="text/javascript">
                        jQuery(function($) {
                            $( document ).on( 'click', '.notice-dtbwp-themeupdates .notice-dismiss', function () {
                                $.ajax( ajaxurl,
                                    {
                                        type: 'POST',
                                        data: {
                                            action: 'dtbwp_update_notice_handler',
                                            security: '<?php echo wp_create_nonce( "dtnwp-ajax-nonce" ); ?>'
                                        }
                                    } );
                            } );
                        });
                    </script>
                <?php }

            }
        }

    }

}

// Prevent automatic wizard redriect
function filter_woocommerce_prevent_automatic_wizard_redirect( ) {
    // make filter magic happen here...
    return true;
};

// add the filter
add_filter( 'woocommerce_prevent_automatic_wizard_redirect', 'filter_woocommerce_prevent_automatic_wizard_redirect', 10, 1 );

// Plugin Activation hook for Booked.

function th_booked_del_redirect() {
    //set_transient( '_booked_welcome_screen_activation_redirect', false, 30 );
    delete_transient( '_booked_welcome_screen_activation_redirect' );
}
/*
(WP_PLUGIN_DIR.'/booked/booked.php', 'th_booked_activate');
*/

add_action('admin_init', 'th_booked_del_redirect', 8);

// Check for plugin updates

function th_masterslider_update_check() {

    // Master Slider
    $th_plugin_dir = WP_PLUGIN_DIR . '/masterslider';
    $th_plugin_slug = 'masterslider';

    if ( is_dir( $th_plugin_dir ) ) {
        // plugin directory found!
        $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
            'https://import.themovation.com/live-plugin-updater/masterslider.json',
            $th_plugin_dir.'/'.$th_plugin_slug.'.php',
            $th_plugin_slug
        );
    }

    // Widget Pack Plugin
    $th_plugin_dir = WP_PLUGIN_DIR . '/th-widget-pack';
    $th_plugin_slug = 'th-widget-pack';

    if ( is_dir( $th_plugin_dir ) ) {
        // plugin directory found!
        $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
            'https://import.themovation.com/live-plugin-updater/th-widget-pack.json',
            $th_plugin_dir.'/'.$th_plugin_slug.'.php',
            $th_plugin_slug
        );
    }

    // Kirki
    $th_plugin_dir = WP_PLUGIN_DIR . '/kirki';
    $th_plugin_slug = 'kirki';

    if ( is_dir( $th_plugin_dir ) ) {

        if ( is_dir( $th_plugin_dir ) ) {
            // plugin directory found!
            $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                'https://import.themovation.com/live-plugin-updater/kirki.json',
                $th_plugin_dir.'/'.$th_plugin_slug.'.php',
                $th_plugin_slug
            );

        }

    }

    // Envato Market
    $th_plugin_dir = WP_PLUGIN_DIR . '/envato-market';
    $th_plugin_slug = 'envato-market';

    if ( is_dir( $th_plugin_dir ) ) {
        // plugin directory found!
        $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
            'https://import.themovation.com/live-plugin-updater/envato-market.json',
            $th_plugin_dir.'/'.$th_plugin_slug.'.php',
            $th_plugin_slug
        );
    }

}
add_action( 'admin_init', 'th_masterslider_update_check' );

// Check for plugin updates

function th_elementor_update_check() {

    /**
     * Detect plugin. For use in Admin area only.
     */
    //if ( !is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
    if( ! function_exists( 'elementor_pro_load_plugin' ) ) {
        // PRO IS NOT LOADED, Continue with update check

        // Elementor
        $th_plugin_dir = WP_PLUGIN_DIR . '/elementor';
        $th_plugin_slug = 'elementor';

        if ( is_dir( $th_plugin_dir ) ) {
            // plugin directory found!
            $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                'https://import.themovation.com/live-plugin-updater/elementor.json',
                $th_plugin_dir.'/'.$th_plugin_slug.'.php',
                $th_plugin_slug
            );

        }

    }


}

add_action( 'admin_init', 'th_elementor_update_check' );

//-----------------------------------------------------
// BOOKED
//-----------------------------------------------------


/*
 * Unload booked translation, load users .mo first, then ours, then Booked Original.
 *
 */

if (!function_exists('th_load_booked_translations')) {


    function th_load_booked_translations(){

    $text_domain = 'booked';
    $locale = apply_filters('plugin_locale', get_locale(), $text_domain);

    $original_language_file = WP_LANG_DIR . DIRECTORY_SEPARATOR . $text_domain . DIRECTORY_SEPARATOR  . $text_domain.'-'.$locale.'.mo';
    $override_language_file = get_template_directory() . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $text_domain.'-'.$locale. '.override.mo';

    // Unload the translation for the text domain of the plugin
    unload_textdomain($text_domain);

    // First load the users override translation file for Booked
    load_textdomain($text_domain, $original_language_file );

    // NExt load our override file for Booked
    load_textdomain($text_domain, $override_language_file );

    // Then load the original file that ships with Booked
    load_plugin_textdomain($text_domain, FALSE, plugin_dir_path($text_domain).'/languages/');

    }

   add_action('after_setup_theme', 'th_load_booked_translations', 15);

}



//-----------------------------------------------------
// Elementor
//-----------------------------------------------------





// REMOVE OPTION TREE Theme Options Links

function th_remove_admin_bar_links() {
    global $wp_admin_bar, $current_user;

    $wp_admin_bar->remove_menu('ot-theme-options');          // Remove the updates link

    if ($current_user->ID != 1) {

    }
}
add_action( 'wp_before_admin_bar_render', 'th_remove_admin_bar_links' );

function th_remove_ot_menu () {
    remove_submenu_page( 'themes.php', 'ot-theme-options' );

}
add_action( 'admin_init', 'th_remove_ot_menu' );

// WooCommerce Actions
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 11 );

// Hide Shop Title
function th_filter_woocommerce_show_page_title( $bool )
{
    // make filter magic happen here...
    return false;
};

// add the filter
add_filter( 'woocommerce_show_page_title', 'th_filter_woocommerce_show_page_title', 10, 1 );

//Exclude AddThis widgets from anything other than posts
add_filter('addthis_post_exclude', 'themo_addthis_post_exclude');
function themo_addthis_post_exclude($display) {
	return false;
	echo 'HELLO';
	if ( !is_singular( 'post' ) )
		$display = false;
	return $display;
}


//-----------------------------------------------------
// themo_search_meta - filter
// Enhance Search to include Meta Boxes
//-----------------------------------------------------
add_filter('posts_search', 'themo_search_function', 10, 2);
function themo_search_function($search, $query) {
	global $wpdb, $pagenow;
    if(!$query->is_main_query() || !$query->is_search || $pagenow=='post.php'){
        return($search); //determine if we are modifying the right query
    }


	$search_term = $query->get('s'); // Get Search Terms
	$search = ' AND (';

	// Query Content
	$search .=  $wpdb->prepare("($wpdb->posts.post_content LIKE '%%%s%%')",$wpdb->esc_like( $search_term ));

	// add an OR between search conditions
	$search .= " OR ";

	// Query Title
	$search .=  $wpdb->prepare("($wpdb->posts.post_title LIKE '%%%s%%')",$wpdb->esc_like( $search_term ));

	// add an OR between search conditions
	$search .= " OR ";

	// Sub Query Custom Meta Boxes
	$search .=  $wpdb->prepare("( $wpdb->posts.ID IN (SELECT DISTINCT $wpdb->postmeta.post_id FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key like 'themo_%%' AND $wpdb->postmeta.meta_value LIKE '%%%s%%'))",$wpdb->esc_like( $search_term ));

	// add the filter to join tables if needed.
	// add_filter('posts_join', 'join_tables');
	return $search . ') ';
}

//-----------------------------------------------------
// themo_ajax_loader - filter
// Replace the Contact Form 7 Ajax Loading Image with our Own
//-----------------------------------------------------
if ( function_exists( 'wpcf7_ajax_loader' ) ) {
	add_filter( 'wpcf7_ajax_loader', 'themo_wap8_wpcf7_ajax_loader' );

	function themo_wap8_wpcf7_ajax_loader() {
		$url = "asdfa"; //get_template_directory_uri() . '/images/ajax-loader.gif';
		return $url;
	}
}

//-----------------------------------------------------
// activate_formidable/formidable.php - Filter
// When the formidable plugin is active set an option to
// print an admin message
//-----------------------------------------------------

add_action('activate_formidable/formidable.php', 'themo_formidable_set_notice');
function themo_formidable_set_notice() {
	add_option('formidable_do_activation_message', true);
}


/*
 * Change Meta Box visibility according to Page Template
 *
 * Observation: this example swaps the Featured Image meta box visibility
 *
 * Usage:
 * - adjust $('#postimagediv') to your meta box
 * - change 'page-portfolio.php' to your template's filename
 * - remove the console.log outputs
 */

add_action('admin_head', 'themo_wpse_50092_script_enqueuer');

function themo_wpse_50092_script_enqueuer() {
	global $current_screen;
	if(isset($current_screen->id) && 'page' != $current_screen->id) return;

    $iswooshoppage = 0;
    // Find out the shop page id for woo and hide the meta box builder.
    if(th_is_woocommerce_activated()){
        $post_ID = get_the_ID();
        $shop_page_id = wc_get_page_id( 'shop' );


        if(isset($post_ID) && isset($shop_page_id) && $post_ID == $shop_page_id){
			$iswooshoppage = 1;
        }
    }

	echo <<<HTML
        <script type="text/javascript">
        jQuery(document).ready( function($) {
		"use strict";
        var excludeTemplates = [ "templates/portfolio-standard.php","templates/blog-masonry.php","templates/blog-masonry-wide.php","templates/blog-standard.php","templates/blog-category-masonry.php"];
        var currentTemplate = $('#page_template').val();
        var excludeFound = $.inArray(currentTemplate, excludeTemplates);
            /**
             * Adjust visibility of the meta box at startup
            */
            if($iswooshoppage) {
                $('#themo_meta_box_builder_meta_box').hide();
            }
            if( excludeFound !== -1 && !excludeFound > -1) {
                // hide your meta box
                $('#themo_meta_box_builder_meta_box').hide();
                $('#themo_blog_category_meta_box').show();
            } else {
                // show the meta box
                $('#themo_meta_box_builder_meta_box').show();
                $('#themo_tour_options_meta_box').hide();
                $('#themo_portfolio_options_meta_box').hide();
                $('#themo_blog_category_meta_box').hide();
            }
            if( currentTemplate ==  "templates/portfolio-standard.php") {
            	$('#themo_tour_options_meta_box').show();
            	$('#themo_portfolio_options_meta_box').show();
            }else{
            	$('#themo_tour_options_meta_box').hide();
            	$('#themo_portfolio_options_meta_box').hide();
            }



            // Debug only
            // - outputs the template filename
            // - checking for console existance to avoid js errors in non-compliant browsers
            /*
            if (typeof console == "object")
                console.log ('default value = ' + $('#page_template').val());
                */

            /**
             * Live adjustment of the meta box visibility
            */
            $('#page_template').live('change', function(){
                var currentTemplate = $(this).val();
                var excludeFound = $.inArray(currentTemplate, excludeTemplates);

                if( excludeFound !== -1 && !excludeFound > -1) {
                     // hide your meta box
                    $('#themo_meta_box_builder_meta_box').hide();
					$('#themo_blog_category_meta_box').show();
					//$('#themo_tour_options_meta_box').show();
                } else {
                    // show the meta box
                    $('#themo_meta_box_builder_meta_box').show();
                    //$('#themo_tour_options_meta_box').hide();
					$('#themo_blog_category_meta_box').hide();
                }

                if( currentTemplate ==  "templates/portfolio-standard.php") {
					$('#themo_tour_options_meta_box').show();
					$('#themo_portfolio_options_meta_box').show();
					$('#themo_blog_category_meta_box').hide();
				}else{
					$('#themo_tour_options_meta_box').hide();
					$('#themo_portfolio_options_meta_box').hide();
				}

                // Debug only
               /* if (typeof console == "object")
                    console.log ('live change value = ' + $(this).val()); */
            });
        });
        </script>
HTML;
}



//======================================================================
// Metabox Plugin Functions
//======================================================================
// Remove BR tag from checkbox list output.
add_filter('rwmb_themo_meta_box_builder_meta_boxes_html','themo_test');
function themo_test($html){
	return strip_tags($html,'<label><input>');
}

//======================================================================
// 400 - Option Tree Functions, Hooks, Filters
//======================================================================

//-----------------------------------------------------
// ot_override_forced_textarea_simple - filter
// Allows TinyMCE or Textarea metaboxes
//-----------------------------------------------------
add_filter( 'ot_override_forced_textarea_simple', '__return_true' );

//-----------------------------------------------------
// themo_ot_meta_box_post_format_quote - filter
// Slight Changes to the quote meta box
//-----------------------------------------------------
add_filter( 'ot_meta_box_post_format_quote', 'themo_ot_meta_box_post_format_quote',10,2 );

function themo_ot_meta_box_post_format_quote($array,$pages) {
	//$pages[] = 'themo_portfolio';
	//$array['pages'] = $pages;
	$array['fields'] = array(
		array(
			'id'      => '_format_quote_copy',
			'label'   => '',
			'desc'    => esc_html__( 'Quote', 'option-tree' ),
			'std'     => '',
			'type'        => 'text',
			'rows'        => '4',
		),
		array(
			'id'      => '_format_quote_source_name',
			'label'   => '',
			'desc'    => esc_html__( 'Source Name (ex. author, singer, actor)', 'stratus' ),
			'std'     => '',
			'type'    => 'text'
		),
		array(
			'id'      => '_format_quote_source_title',
			'label'   => '',
			'desc'    => esc_html__( 'Source Title (ex. book, song, movie)', 'stratus' ),
			'std'     => '',
			'type'    => 'text'
		));
	return $array;
}

//-----------------------------------------------------
// themo_ot_meta_box_post_format_audio - filter
// Slight Changes to the audio meta box
//-----------------------------------------------------
add_filter( 'ot_meta_box_post_format_audio', 'themo_ot_meta_box_post_format_audio',10,2 );

function themo_ot_meta_box_post_format_audio($array,$pages) {

	//$pages[] = 'themo_tour';
	//$array['pages'] = $pages;

	$array['fields'] = array(
		array(
			'id'      => '_format_audio_shortcode',
			'label'   => 'Upload and Embed Audio to your website',
			'desc'    => esc_html__( 'Use the built-in <code>[audio]</code> shortcode here.', 'stratus' ),
			'std'     => '',
			'type'    => 'textarea'
		)
	);
	return $array;
}

//-----------------------------------------------------
// themo_ot_meta_box_post_format_link - filter
// Slight Changes to the audio meta box
//-----------------------------------------------------
add_filter( 'ot_meta_box_post_format_link', 'themo_ot_meta_box_post_format_link',10,2 );

function themo_ot_meta_box_post_format_link($array,$pages) {

    $pages[] = 'themo_portfolio';
    $pages[] = 'themo_tour';

	$array['pages'] = $pages;

	$array['fields'] = array(

		array(
			'id'      => '_format_link_url',
			'label'   => '',
			'desc'    => esc_html__( 'Link URL (ex. http://google.com)', 'stratus' ),
			'std'     => '',
			'type'    => 'text'
		),
		array(
			'id'      => '_format_link_title',
			'label'   => '',
			'desc'    => esc_html__( 'Link Title (ex. Check out Google)', 'stratus' ),
			'std'     => '',
			'type'    => 'text'
		),

		array(
			'id'          => '_format_link_target',
			'label'       => esc_html__( 'Link Target', 'stratus' ),
			'type'        => 'checkbox',
			'choices'     => array(
				array(
					'value'       => '_blank',
					'label'       => 'Open link in a new window / tab',
				)
			)
		),
		/*array(
			'id'          => '_format_skip_single_link',
			'label'       => esc_html__( 'Link behaviour on the portfolio homepage', 'stratus' ),
			'desc' => 'By default the portfolio homepage thumbnail goes to the project single page. You can make this link go directly to your the URL above by using this checkbox.',
			'type'        => 'checkbox',
			'choices'     => array(
				array(
					'value'       => true,
					'label'       => 'Take user directly to URL above &amp; skip project single.',
				)
			)
		)*/
	);
	return $array;
}

//-----------------------------------------------------
// themo_ot_meta_box_post_format_video - filter
// Slight Changes to the video meta box
//-----------------------------------------------------
add_filter( 'ot_meta_box_post_format_video', 'themo_ot_meta_box_post_format_video',10,2 );

function themo_ot_meta_box_post_format_video($array,$pages) {

	//$pages[] = 'themo_tour';
	//$array['pages'] = $pages;

	$array['fields'] = array(
		array(
			'id'      => '_format_video_embed',
			'label'   => 'Insert from URL (Vimeo and Youtube)',
			'desc'    => sprintf( wp_kses_post( __( '(ex. http://vimeo.com/link-to-video). You can find a list of supported oEmbed sites in the %1$s.', 'stratus' )), '<a href="http://codex.wordpress.org/Embeds" target="_blank">' . esc_html__( 'Wordpress Codex', 'stratus' ) .'</a>' ),
			'std'     => '',
			'type'    => 'text'
		),
		array(
			'id'      => '_format_video_shortcode',
			'label'   => 'Upload your own self hosted video',
			'desc'    => wp_kses_post(__( 'Use the built-in <code>[video]</code> shortcode here.', 'stratus' )),
			'std'     => '',
			'type'    => 'textarea'
		)
	);
	return $array;
}

//-----------------------------------------------------
// themo_ot_meta_box_post_format_gallery - filter
// Enable Post Format gallery to on custom post type
//-----------------------------------------------------
add_filter('ot_meta_box_post_format_gallery', 'themo_ot_meta_box_post_format_gallery',10,2);

function themo_ot_meta_box_post_format_gallery($array,$pages) {

	//$pages[] = 'themo_tour';
	//$array['pages'] = $pages;
	return $array;
}

//-----------------------------------------------------
// themo_ot_post_formats - filter
// Enable Post Format Types via OT
//-----------------------------------------------------
add_filter( 'ot_post_formats', 'themo_ot_post_formats');

function themo_ot_post_formats( ) {
	return true;
}


//-----------------------------------------------------
// FILTER for modifying field id passed in from OT.
// Need to make a wildcard match on the field ids.
//-----------------------------------------------------
add_filter( 'ot_field_ID_match', 'themo_filter_field_ID_match', 10, 1 );

function themo_filter_field_ID_match( $content) {
	return trim(str_replace(range(0,9),'',$content)); // Strip out numbers and pass it back.
}



//-----------------------------------------------------
// print_google_font_link from OT settings.
// Print Google Font link tag for inline styling.
//-----------------------------------------------------
function themo_print_google_font_link(){

	// check for custom google fonts, add them.
	if ( function_exists( 'get_theme_mod' ) ) {

		/* get the slider array */
		$google_fonts = get_theme_mod( 'themo_google_fonts', array() );

		if ( ! empty( $google_fonts ) ) {
			foreach( $google_fonts as $google_font ) {
				//$google_font_family = $google_font["themo_google_font_family"];
				if($google_font["themo_google_font_url"] > ""){
					?>
					<!-- GOOGLE FONTS -->
					<link href='<?php echo esc_url($google_font["themo_google_font_url"]); ?>' rel='stylesheet' type='text/css'>
				<?php
				}
			}
		}
	}
}


//======================================================================
// 500 - Core / Special Functions
//======================================================================


add_action( 'tgmpa_register', 'th_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function th_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// This is an example of how to include a plugin pre-packaged with a theme.

        array(
			'name'      => 'Elementor Page Builder', // The plugin name.
            'slug'      => 'elementor', // The plugin slug (typically the folder name).
            'source'    => 'https://www.dropbox.com/s/d8ym48zzp365u9k/elementor.zip?dl=1', //get_template_directory() . '/plugins/th-widget-pack.zip', // The plugin source.
            'required'  => true,
        ),
        array(
            'name'      => 'Kirki',
            'slug'      => 'kirki',
            'source'    => 'https://www.dropbox.com/s/62cikx5ctanx412/kirki.zip?dl=1', //get_template_directory() . '/plugins/th-widget-pack.zip', // The plugin source.
            'required'  => true,
        ),
		array(
			'name'      => 'Page Builder Widget Pack', // The plugin name.
            'slug'      => 'th-widget-pack', // The plugin slug (typically the folder name).
            'source'    => 'https://www.dropbox.com/s/hk8f8md37m11m0q/th-widget-pack.zip?dl=1', //get_template_directory() . '/plugins/th-widget-pack.zip', // The plugin source.
            'required'  => true,
        ),
		array(
			'name'      => 'Master Slider Pro', // The plugin name.
            'slug'      => 'masterslider', // The plugin slug (typically the folder name).
            'source'    => 'https://www.dropbox.com/s/mhpqd8atrshfgu5/master-slider.zip?dl=1', //get_template_directory() . '/plugins/th-widget-pack.zip', // The plugin source.
            'recommended' => true,
        ),

        array(
            'name' => 'Envato Market',
            'slug' => 'envato-market',
            'source' => 'https://www.dropbox.com/s/o4lpuqqr46gft34/envato-market.zip?dl=1',
            'required' => true,
        ),

		// This is an example of how to include a plugin from the WordPress Plugin Repository.
		array(
			'name'      => 'Formidable Forms',
			'slug'      => 'formidable',
			'required'  => false,
		),
		array(
			'name'      => 'Widget Logic',
			'slug'      => 'widget-logic',
			'required'  => false,
		),
        array(
            'name'      => 'Simple Page Ordering',
            'slug'      => 'simple-page-ordering',
            'required'  => false,
        ),


	);
	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'stratus',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.

		/*
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'stratus' ),
			'menu_title'                      => __( 'Install Plugins', 'stratus' ),
			/* translators: %s: plugin name. * /
			'installing'                      => __( 'Installing Plugin: %s', 'stratus' ),
			/* translators: %s: plugin name. * /
			'updating'                        => __( 'Updating Plugin: %s', 'stratus' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', 'stratus' ),
			'notice_can_install_required'     => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'stratus'
			),
			'notice_can_install_recommended'  => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'stratus'
			),
			'notice_ask_to_update'            => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'stratus'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				/* translators: 1: plugin name(s). * /
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'stratus'
			),
			'notice_can_activate_required'    => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'stratus'
			),
			'notice_can_activate_recommended' => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'stratus'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'stratus'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'stratus'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'stratus'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'stratus' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'stratus' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'stratus' ),
			/* translators: 1: plugin name. * /
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'stratus' ),
			/* translators: 1: plugin name. * /
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'stratus' ),
			/* translators: 1: dashboard link. * /
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'stratus' ),
			'dismiss'                         => __( 'Dismiss this notice', 'stratus' ),
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'stratus' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'stratus' ),

			'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),
		*/
	);

	tgmpa( $plugins, $config );
}






//======================================================================
// CATEGORY LARGE FONT
//======================================================================

//-----------------------------------------------------
// Sub-Category Smaller Font
//-----------------------------------------------------

/* Title Here Notice the First Letters are Capitalized note from from WIN */

# Option 1
# Option 2
# Option 3

/*
 * This is a detailed explanation
 * of something that should require
 * several paragraphs of information.
 */

// This is a single line quote.
