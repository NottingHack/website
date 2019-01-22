<?php
global $masonry, $masonry_template_key,$image_size,$more,$automatic_post_excerpts;

if(!is_single()){
	$more = 0;
}

if ( has_post_thumbnail() ) {
	$featured_img_attr = array('class'	=> "img-responsive",); ?>
    <?php
	if (is_single()){ ?>
    	<?php the_post_thumbnail($image_size,$featured_img_attr); ?>
	<?php }else{ ?>
		<a href="<?php esc_url(the_permalink()); ?>">
			<?php the_post_thumbnail($image_size,$featured_img_attr); ?>
    	</a>
    <?php } ?>
<?php } ?>
<div class="post-inner">

    <?php
	if (!is_single()){
	    $th_post_title = the_title('','',false);
	    if(!$th_post_title > ""){
            $th_post_title = esc_html__( '(no title)', 'stratus' );
        }
	    ?>
    <h3 class="post-title"><a href="<?php esc_url(the_permalink()); ?>"><?php echo wp_kses_post( $th_post_title ); ?></a></h3>
    <?php }?>
	<?php get_template_part('templates/entry-meta'.$masonry_template_key); ?>
	<?php
	if (is_single() || (!is_single() && $automatic_post_excerpts === 'off') ){
			$content = apply_filters( 'the_content', get_the_content() );
			$content = str_replace( ']]>', ']]&gt;', $content );
			if($content != ""){ ?>
            	<div class="entry-content">
					<?php echo $content; ?>
                </div>
			<?php }
	}else{
		$excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );
		$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
			if($excerpt != ""){ ?>
            	<div class="entry-content post-excerpt">
					<?php echo wp_kses_post( $excerpt ); ?>
                </div>
			<?php }
    } ?>
	<?php get_template_part('templates/entry-meta-footer'.$masonry_template_key); ?>
</div>
