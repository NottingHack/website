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
?>
<div class="post-meta">
	<span class="show-author"><?php echo esc_html__('Posted by', 'stratus'); ?> <?php the_author_posts_link(); ?></span> <span class="show-comments"><?php echo wp_kses_post( $write_comments ); ?></span>
</div>
