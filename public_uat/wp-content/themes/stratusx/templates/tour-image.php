<?php
/*
 * Portfolio Format - IMAGE
 * Supports Featured Image, title and subtext.
 * */

global $key,$image_size,$more,$automatic_post_excerpts,$orderby_menu;

// Get Project Format Options
$enable_lightbox = get_post_meta( $post->ID, 'themo_project_lightbox', false);
$project_thumb_alt_img = get_post_meta( $post->ID, 'themo_project_thumb', false);

//print_r($project_thumb_alt_img);

if (isset($project_thumb_alt_img[0]) && is_array($project_thumb_alt_img[0]) > "") {
    $img_src = themo_return_metabox_image($project_thumb_alt_img[0], null, "themo_portfolio_standard", true, $alt);
    $img_src = esc_url($img_src);
    $alt_text = esc_attr($alt);
}

$show_lightbox = false;
if(isset($enable_lightbox) && is_array($enable_lightbox)){
    $enable_lightbox = $enable_lightbox[0][0];
    if($enable_lightbox) {
        $show_lightbox = true;
    }
}

$href = "";
$href_close = "";
$href_lightbox = "";

// Pre lightbox link
$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'th_img_xl');
$elementor_global_image_lightbox = get_option('elementor_global_image_lightbox');
if (!empty($elementor_global_image_lightbox) && $elementor_global_image_lightbox == 'yes') {
    $href_lightbox = '<a href="' . esc_url($large_image_url[0]) . '" title="' . esc_attr(the_title_attribute('echo=0')) . '">';
}else{
    $href_lightbox = '<a href="' . esc_url($large_image_url[0]) . '" title="' . esc_attr(the_title_attribute('echo=0')) . '" data-toggle="lightbox" data-gallery="multiimages" >';
}


// Standard link
$href = '<a href="' . esc_url(get_permalink()) . '" title="' . the_title_attribute('echo=0') . '"  >';
$href_close = '</a>';

//-----------------------------------------------------
// Single Output
//-----------------------------------------------------
if(is_single()){ ?>
    <section class="content-editor">
        <?php while (have_posts()) : the_post(); ?>
            <?php the_content(); ?>
        <?php endwhile; ?>
    </section>
<?php } else {
//-----------------------------------------------------
// Index and Archive Output
//-----------------------------------------------------
    $more = 0;
    echo '<div class="port-wrap">';
    if(isset($img_src) &&  $img_src > ""){
        echo '<img class="img-responsive port-img" src="'.esc_url($img_src).'" alt="'.esc_attr($alt_text).'">';
    }else{
        if ( has_post_thumbnail() ) {
            $featured_img_attr = array('class'	=> "img-responsive port-img");
            echo wp_kses_post(get_the_post_thumbnail($post->ID,$image_size,$featured_img_attr));
        }
    }

    echo '<div class="port-overlay"></div>';
    echo '<div class="port-inner">';
    echo '<div class="port-center">';
    echo '<h3 class="port-title">'.get_the_title().'</h3>';
    if($automatic_post_excerpts === 'off'){
        $content = apply_filters( 'the_content', get_the_content() );
        $content = str_replace( ']]>', ']]&gt;', $content );
        if($content != ""){
            echo '<p class="port-sub">'.wp_kses_post($content).'</p>';
        }
    }else{
        $excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );
        $excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
        $excerpt = str_replace('<p', '<p class="port-sub"', $excerpt);
        if($excerpt != ""){
            echo wp_kses_post( $excerpt );
        }
    }
    echo '</div><!-- /.port-center -->';
    // See if Lightbox is enabled.
    if($show_lightbox){

        $href = $href_lightbox;
        $href = str_replace('<a', '<a class="port-link"', $href_lightbox);
        echo wp_kses_post( $href.$href_close );
    }else{
        if(isset($orderby_menu)){
            echo '<a class="port-link" href="' . esc_url_raw(add_query_arg('portorder','menu',get_the_permalink())). '"></a>';
        }else{
            echo '<a class="port-link" href="' . get_the_permalink(). '"></a>';
        }
    }
    echo '</div><!-- /.port-inner -->';
    echo '</div><!-- /.port-wrap -->';
}
