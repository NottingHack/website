<?php
//-----------------------------------------------------
// Meta Box Header / Subtext
//-----------------------------------------------------

$themo_page_ID = "";

if(isset($post->ID) && $post->ID > ""){
    $themo_page_ID = $post->ID; // Default Page ID
}

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

/* PAGE LAYOUT */
if(isset($woo_global_header_settings) && $woo_global_header_settings){
    $key = 'themo_default_layout';
    $page_layout = get_theme_mod( 'themo_woo_sidebar', 'full' );
}elseif(isset($shop_page_id) && $shop_page_id > 0){
    $page_layout = get_post_meta($shop_page_id, 'themo_page_layout', true ); // Returns Page layout Meta Option. Gonna be left, right or full.
}elseif((isset($key)) && ($key > "")){
	$page_layout = get_theme_mod( $key.'_sidebar', 'full' );
}elseif ( isset($themo_page_ID) &&  $themo_page_ID > "" ) {
    $page_layout = get_post_meta($themo_page_ID, 'themo_page_layout', true ); // Returns Page layout Meta Option. Gonna be left, right or full.
}else{
    $key = 'themo_default_layout';
	$page_layout = get_theme_mod( 'themo_default_layout_sidebar', 'full' );
}
$has_sidebar = themo_has_sidebar($page_layout); // true if sidebar active

/* 
Full width / Sidebar Markup
If sidebar is active, add container and row classes just below .inner-content only.
For full width add container and row to templates parts only.
*/
// Outer Tags output just after "inner-container" class (includes open and close tags)
$outer_container_open = themo_return_outer_tag("<div class='container'>",$has_sidebar);
$outer_row_open = themo_return_outer_tag("<div class='row'>",$has_sidebar);

$outer_container_close = themo_return_outer_tag("</div><!-- /.container -->",$has_sidebar);
$outer_row_close = themo_return_outer_tag("</div><!-- /.row -->",$has_sidebar);

// Inner tags output inside template parts (includes open and close tags)
$inner_container_open = themo_return_inner_tag("<div class='container'>",$has_sidebar);

$inner_row_open = themo_return_inner_tag("<div class='row'>",$has_sidebar);

$inner_container_close = themo_return_inner_tag("</div><!-- /.container -->",$has_sidebar);
$inner_row_close = themo_return_inner_tag("</div><!-- /.row -->",$has_sidebar);

// Main Class for sidebar support.
if($page_layout == 'right'){
	$sidebar_push_pull = '';
}elseif($page_layout == 'left'){
	$sidebar_push_pull = 'col-sm-push-4';
}else{
	$sidebar_push_pull = '';
}

$main_class_open = themo_return_outer_tag('<div class="main col-sm-8 '. $sidebar_push_pull .'" role="main">',$has_sidebar);
$main_class_close = themo_return_outer_tag('</div><!-- /.main -->',$has_sidebar);