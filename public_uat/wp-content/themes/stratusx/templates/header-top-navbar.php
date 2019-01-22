<?php
// Get logo from admin options.
// Fetch ID and pull logo size.


// If this is the WooCommerce product archive page, grab the ID of the shop page.
// WOO Support
if(th_is_woocommerce_activated()) {
// Support for Woo Pages.
// Sometimes the page id isn't explicit so we have to go and look for it.
    if (is_shop()) {
        $shop_page_id = wc_get_page_id('shop');
    }
    if (is_product_category() || is_product_tag() || is_product_taxonomy()) {
        $woo_global_header_settings = true;
    }
}

if(isset($shop_page_id) && $shop_page_id > 0) {
    $postID = $shop_page_id;
}elseif(isset($woo_global_header_settings) && $woo_global_header_settings){
    $postID = "";
}elseif(isset($post->ID )&& $post->ID > ""){
	$postID = $post->ID;
}else{
	$postID = "";
}


if ( function_exists( 'get_theme_mod' ) ) {

    // Transparent Header?
    $transparent_header = 'off';
    $transparent_header = get_post_meta($postID, 'themo_transparent_header', true );


    // Dark header contents on?
    // returns dark or light
    $th_dark_header_content = 'light';
    $th_dark_header_content = get_post_meta($postID, 'themo_header_content_style', true );


    // Alt Logo enabled?
    $th_alt_logo = false;
    $th_alt_logo = get_post_meta($postID, 'themo_alt_logo', true );

    $header_dark_style_class = false;
    if($transparent_header == "on" && $th_dark_header_content == 'dark'){
        $header_dark_style_class = 'th-dark-tr';
    }

	// enable Transparent Header if it's enabled on the page.

    // check for light / dark style header.
    // Alternative logo for Transparent Header Enabled?
    $header_style = get_theme_mod( 'themo_header_style', 'dark' );

    $header_dark_style = false;
    if(isset($header_style) && $header_style == 'dark'){
        $header_dark_style = true;
    }

    // Is transparent and dark option enabled?

    if(isset($header_dark_style) && $header_dark_style ){
        $header_dark_style_class .= ' dark-header';
    }



    // Page Header?
    /*
    $page_header = 'on';
    $page_header = get_post_meta($postID, 'themo_page_header', true);
    */

    $th_hide_title = 'off';
    $th_hide_title = get_post_meta($postID, 'themo_hide_title', true);

    /*
     * Enable transparent header / nav if:
     * - It's enabled on the page settings
     * - default page header is off
     * - not archive
     */

    // TODO RL
    // Need some checks in here, make sure that the Array is set before updating.
    $th_is_product_single = false;
    if (function_exists('is_product')) {
        if(is_product()){
            $th_is_product_single = true;
        }else{
            $th_is_product_single = false;
        }
    }

    //if(!is_archive() && isset($transparent_header) && !empty( $transparent_header ) && $transparent_header == 'on' && $th_hide_title == 'on')
    if( !is_search() && !is_archive() && isset($transparent_header) && !empty( $transparent_header ) && $transparent_header == 'on' && !is_singular('post') && !$th_is_product_single)
    {
	    $transparency = true;
		$transparent_header = 'data-transparent-header="true"';
	}else{
		$transparency = false;
		$transparent_header = '';
	}

	// Alternative logo for Transparent Header Enabled?
	// Alternative logo for Transparent Header Enabled?d
	$transparent_logo_enabled = get_theme_mod( 'themo_logo_transparent_header_enable', false );

	// To support for transparent header we want to keep a copy of the main logo, and use it when user scrolls (sticky header).
	$logo_main = get_theme_mod( 'themo_logo');
    $logo_height_theme_options = get_theme_mod( 'themo_logo_height', 100 );

    add_image_size('themo-logo', 9999, $logo_height_theme_options); //  TODO - Find a better plcae for this. Perhaps a wp cusomizer hook on each refresh / control update.




	if(!$logo_main > ""){
        // If we are using the dark header, then default to white logo.
        if(isset($header_dark_style) && $header_dark_style ){
            $logo_main = get_template_directory_uri() . '/assets/images/logo_white.png';
            $logo_main_retina = get_template_directory_uri() . '/assets/images/logo_white@2x.png';
        }else{
            $logo_main = get_template_directory_uri() . '/assets/images/logo.png' ;
            $logo_main_retina = get_template_directory_uri() . '/assets/images/logo@2x.png';
        }
	}else{
		$logo_main_retina = "";
	}

	// If transparent logo is enabled and transparency enabled, then replace logo.
	if($transparency && $transparent_logo_enabled ){
		$logo = get_theme_mod( 'themo_logo_transparent_header' );
		if(!$logo > ""){
			$logo = get_template_directory_uri() . '/assets/images/logo_white.png';
			$logo_retina = get_template_directory_uri() . '/assets/images/logo_white@2x.png';
		}else{
			$logo_retina = "";
		}
	}else{
	    $logo = $logo_main;
		$logo_retina = $logo_main_retina;
	}


	/*-----------------------------------------------------
		Logo & Retina Logo
	-----------------------------------------------------*/

	$id = themo_custom_get_attachment_id( $logo );

	// If this is a WordPress Attachment then get src, height, width and retina version too.
	if($id > 0){
		$image_attributes  = wp_get_attachment_image_src( $id, 'themo-logo' ); // ADD logo image size when ready. eg.  wp_get_attachment_image_src( $id, 'image-size' );
		list($logo_retina, $logo_retina_height, $logo_retina_width) = themo_return_retina_logo($id);
	}

	if(isset($image_attributes) && !empty( $image_attributes ) )
	{
		$logo_src = esc_url($image_attributes[0]);
		$logo_height = " height='".sanitize_text_field($image_attributes[2])."'";
		$logo_width =  " width='".sanitize_text_field($image_attributes[1])."'";

		$logo_retina_src = "src='".$logo_retina."'";
		$logo_retina_height = " height='".sanitize_text_field($logo_retina_height)."'";
		$logo_retina_width =  " width='".sanitize_text_field($logo_retina_width)."'";

	}else{
		$logo_src = esc_url($logo);
		$logo_height = "";
		$logo_width =  "";

		if($logo_retina > ""){
			$logo_retina_src = "src='".esc_url($logo_retina)."'";
			$logo_retina_height = "";
			$logo_retina_width =  "";
		}
	}

	$id_main = themo_custom_get_attachment_id( $logo_main );

	if($id_main > 0){
		$image_attributes_main  = wp_get_attachment_image_src( $id_main, 'themo-logo' ); // ADD logo image size when ready. eg.  wp_get_attachment_image_src( $id, 'image-size' );
		list($logo_main_retina, $logo_main_retina_height, $logo_main_retina_width) = themo_return_retina_logo($id_main);
	}

	if(isset($image_attributes_main) && !empty( $image_attributes_main ) )
	{
		$logo_src_main = $image_attributes_main[0];
		$logo_height_main = " height='".sanitize_text_field($image_attributes_main[2])."'";
		$logo_width_main =  "width='".sanitize_text_field($image_attributes_main[1])."'";

		$logo_main_retina_src = "src='".esc_url($logo_main_retina)."'";
		$logo_main_retina_height = " height='".sanitize_text_field($logo_main_retina_height)."'";
		$logo_main_retina_width =  " width='".sanitize_text_field($logo_main_retina_width)."'";
	}else{
		$logo_src_main = $logo_main;
		$logo_height_main = "";
		$logo_width_main =  "";

		if($logo_main_retina > ""){
			$logo_main_retina_src = "src='".esc_url($logo_main_retina)."'";
			$logo_main_retina_height = "";
			$logo_main_retina_width =  "";
		}
	}

}
?>

<header class="banner navbar navbar-default navbar-static-top <?php echo sanitize_text_field($header_dark_style_class);?>" role="banner" <?php echo sanitize_text_field($transparent_header);?>>
    <?php
        if ( function_exists( 'get_theme_mod' ) ) {
        /* Top Nav Enabled? */
        $top_nav_display = get_theme_mod( 'themo_top_nav_switch',false);

            if ( ! empty( $top_nav_display ) && $top_nav_display) { ?>

                <!-- top navigation -->
                <div class="top-nav">
                    <div class="container">
                        <div class="row col-md-12">
                            <div class="top-nav-text">
                                <?php
                                if ( function_exists( 'get_theme_mod' ) ) {
                                    /* Get top nav text. */
                                    // Check for top nav text from page, if not exists, than
                                    // get the theme options setting.
                                    $page_top_nav_text = false;
                                    $page_top_nav_text = get_post_meta($postID, "themo_top_nav_text", true);
                                    if(isset($page_top_nav_text) && $page_top_nav_text > ""){
                                        $top_nav_text = $page_top_nav_text;
                                    }else{
                                    $top_nav_text = get_theme_mod( 'themo_top_nav_text');
                                    }
                                    if ( ! empty( $top_nav_text ) ) {
                                        echo '<p>'.wp_kses_post($top_nav_text).'</p>';
                                    }
                                }
                                ?>
                            </div>
                            <?php

                            if ( function_exists( 'get_theme_mod' ) ) {
                                // Get icon block array from OT
                                $icon_block = get_theme_mod( 'themo_top_nav_icon_blocks', array() );

                                if (isset($icon_block) && is_array($icon_block)  && !empty($icon_block)) {
                                    echo '<div class="top-nav-icon-blocks">';
                                    $output = false;
                                    foreach( $icon_block as $icon ) {
                                        $th_title = false;
                                        if (isset($icon["title"])){
                                            $th_title = $icon["title"];
                                        }
                                        $glyphicon_type = $substring = substr($icon["themo_top_nav_icon"], 0, strpos($icon["themo_top_nav_icon"], '-'));
                                        if (isset($icon["themo_top_nav_icon_url_target"])) {
                                            $link_target = $icon["themo_top_nav_icon_url_target"];
                                        }
                                        $link_target_att = false;
                                        if (isset($link_target) && is_array($link_target)  && !empty($link_target)) {
                                            $link_target = $icon["themo_top_nav_icon_url_target"][0];
                                            if($link_target == '_blank'){

                                            }
                                        }elseif(isset($link_target) && $link_target){
                                                $link_target_att = "target=_blank";
                                            }
                                        $ahref = false;
                                        $ahref_close = false;
                                        if(isset($icon["themo_top_nav_icon_url"]) && $icon["themo_top_nav_icon_url"] > ""){
                                            $ahref = "<a ".esc_attr($link_target_att)." href='".esc_url($icon["themo_top_nav_icon_url"])."'>";
                                            $ahref_close = "</a>";
                                        }
                                        $output .= '<div class="icon-block">';
                                        $output .= "<p>".$ahref."<i class='".esc_attr($icon["themo_top_nav_icon"])."'></i><span>".wp_kses_post($th_title)."</span>".$ahref_close."</p>";
                                        $output .= '</div>';
                                    }
                                    echo wp_kses_post( $output );
                                    echo '</div>';
                                }
                            } ?>
                        </div>
                    </div>
                </div><!-- END top navigation -->
                <?php
            } // END Top Nav Enabled
        } // End Top Navigation
    ?>
	<div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div id="logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                   	<?php if($transparency && $transparent_logo_enabled && $th_alt_logo == "on") { // If trans header on and there is a alt logo and it's enabled on page settings, show it  ?>

                        <img class="logo-trans logo-reg" src="<?php echo esc_url( $logo_src ); ?>" <?php echo wp_kses_post( $logo_height . $logo_width );?>  alt="<?php sanitize_text_field(bloginfo("name" )); ?>" />
                    <?php }elseif($transparency && (!$transparent_logo_enabled or !$th_alt_logo or $th_alt_logo == 'Off')){ // If trans header on but alt logo is turned off or it's not enabed on the page settings don't show ?>
                        <img class="logo-trans logo-reg" src="<?php echo esc_url( $logo_src_main ); ?>" <?php echo wp_kses_post( $logo_height_main ." ". $logo_width_main );?>   alt="<?php sanitize_text_field(bloginfo("name" )); ?>" />
                    <?php }?>
                    <img class="logo-main logo-reg" src="<?php echo esc_url( $logo_src_main ); ?>" <?php echo wp_kses_post( $logo_height_main ." ". $logo_width_main );?>   alt="<?php bloginfo("name" ); ?>" />
				</a>
            </div>
        </div>

        <?php
        /*
        Shopping cart icon : show / hide
        Shopping cart item count
        */
        if(th_is_woocommerce_activated()) {
            $woo_cart_header_display = true; // default
            $themo_cart_count = false;
            if (function_exists('get_theme_mod')) {
                $woo_cart_header_display = get_theme_mod('themo_woo_show_cart_icon', true);
                $woo_cart_header_icon = get_theme_mod('themo_woo_cart_icon', 'th-i-cart');
            }
            if (isset($woo_cart_header_display) && $woo_cart_header_display) {

                global $woocommerce;
                $cart_count = $woocommerce->cart->cart_contents_count;

                $cart_url = $woocommerce->cart->get_cart_url();
                $ahref = false;
                $ahref_close = false;
                if(isset($cart_url)){
                    $ahref = "<a href='".esc_url($cart_url)."'>";
                    $ahref_close = "</a>";
                }

                if ($cart_count > 0) {
                    $themo_cart_count = "<span class='themo_cart_item_count'>" . $cart_count . "</span>";
                }
                echo "<div class='themo_cart_icon'>";
                echo wp_kses_post( $ahref );
                echo "<i class='th-icon ".esc_attr($woo_cart_header_icon)."'></i>";
                echo wp_kses_post( $themo_cart_count );
                echo wp_kses_post( $ahref_close );
                echo '</div>';
            }
        }
        ?>

        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
            <?php
            if (has_nav_menu('primary_navigation')) :
                wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-nav','fallback_cb' => false));
            endif;
            ?>
        </nav>
	</div>
</header>
