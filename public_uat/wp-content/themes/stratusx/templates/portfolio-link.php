<?php
/*
 * Portfolio Format - IMAGE
 * Supports Featured Image, title and subtext.
 * */

global $key,$image_size,$more,$automatic_post_excerpts,$orderby_menu;

//-----------------------------------------------------
// Get Project Options
//-----------------------------------------------------

$link_url = get_post_meta( $post->ID, '_format_link_url', true);
$link_title = get_post_meta( $post->ID, '_format_link_title', true);
$link_target = get_post_meta( $post->ID, '_format_link_target');
$link_direct = get_post_meta( $post->ID, '_format_skip_single_link', false);

// Link to single
if(isset($link_direct) && is_array($link_direct)){
    $link_direct = $link_direct[0];
}

// Link Target
if(isset($link_target) && is_array($link_target)){
    if($link_target[0] > "")
        $link_target_markup = "target='".$link_target[0]."'";
}else {
    $link_target_markup = "";
}
// Custom Title
if(!$link_title > "") {
    $link_title=get_the_title();
}
// href mark up
$href = "";
$href_close = "";

if ($link_url > ""){
    $href = "<a class='port-single-link' href='". $link_url . "'".$link_target_markup .">";
    $href_close = "</a>";
}

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
    if ( has_post_thumbnail() ) {
        $featured_img_attr = array('class'	=> "img-responsive port-img");
    }
    echo '<div class="port-wrap">';
        echo wp_kses_post(get_the_post_thumbnail($post->ID,$image_size,$featured_img_attr));
        echo '<div class="port-overlay"></div>';
        echo '<div class="port-inner">';
            echo '<div class="port-center">';
                echo '<h3 class="port-title">'.esc_html($link_title).'</h3>';
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
            if(!$link_direct){
                if(isset($orderby_menu)){
                    echo '<a class="port-link" href="' . esc_url_raw(add_query_arg('portorder','menu',get_the_permalink())). '"></a>';
                }else{
                    echo '<a class="port-link" href="' . get_the_permalink(). '"></a>';
                }
            }else{
                echo '<a class="port-link" href="'. esc_url($link_url) . '" ' .wp_kses_post($link_target_markup) . '></a>';
            }
    echo '</div><!-- /.port-inner -->';
    echo '</div><!-- /.port-wrap -->';
}
