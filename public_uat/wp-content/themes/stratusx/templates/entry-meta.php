<?php
$num_comments = get_comments_number(); // get_comments_number returns only a numeric value
$write_comments = "";

if ( comments_open() ) {
	if ( $num_comments == 0 ) {
		$comments = esc_html__('No Comments', 'stratus');
	} elseif ( $num_comments > 1 ) {
		$comments = $num_comments . esc_html__(' Comments', 'stratus');
	} else {
		$comments = esc_html__('1 Comment', 'stratus');
	}
	$write_comments = '| <a href="' . esc_url(get_comments_link()) .'">'. $comments.'</a>';
}
$title = get_the_title();
$perma = get_permalink();
$link_the_date_open = false;
$link_the_date_close = false;

if(!$title > '' && $perma > ""){
    $link_the_date_open = '<a href="'.esc_url($perma).'">';
    $link_the_date_close = '</a>';
}

?>
<div class="post-meta"><span class="show-author"><?php echo esc_html__('Posted by', 'stratus'); ?> <?php echo the_author_posts_link(); ?></span> <span class="show-date"><span class="pre-date"><?php echo esc_html__('on', 'stratus'); ?></span> <time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo wp_kses_post( $link_the_date_open );?><?php echo get_the_date(); ?><?php echo wp_kses_post( $link_the_date_close );?></time></span> <span class="is-sticky">| <?php echo esc_html__('Featured', 'stratus'); ?></span> <span class="show-comments"><?php echo wp_kses_post( $write_comments ); ?></span></div>
