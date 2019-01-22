<?php 
global $masonry, $masonry_template_key;
$link_url = get_post_meta( get_the_ID(), '_format_link_url', true);
$link_title = get_post_meta( get_the_ID(), '_format_link_title', true);
$link_target = get_post_meta( get_the_ID(), '_format_link_target', true);

// Link Target
if(isset($link_target) && is_array($link_target)){
    if($link_target[0] > "")
        $link_target_markup = "target='".$link_target[0]."'";
}else {
    $link_target_markup = "";
}
?> 

<div class="post-inner">
    <?php get_template_part('templates/entry-meta'.$masonry_template_key); ?>
    <?php 
	if (!is_single()){ ?>
    <h3 class="link-title"><?php the_title(); ?></h3>
    <?php }?>
    <div class="link-text">
        <i class="link-icon float-left accent fa fa-link fa-flip-horizontal"></i>
		<?php
		    $href = "";
		    $href_close = "";
            if ($link_url > ""){
                $href = "<a href='". $link_url . "' ". $link_target_markup .">";
                $href_close = "</a>";
            }
		    if($link_title > ""){
                echo wp_kses_post($href) . esc_attr($link_title) . $href_close ;
            }

            $content = apply_filters( 'the_content', get_the_content() );
            $content = str_replace( ']]>', ']]&gt;', $content );
            if($content != ""){
                echo '<div class="entry-content">'.$content.'</div>';
            }
        ?>
    </div>
	<?php get_template_part('templates/entry-meta-footer'.$masonry_template_key); ?>
</div>
