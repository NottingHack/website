<?php echo get_avatar($comment, $size = '64'); ?>
<div class="media-body">
  <h4 class="media-heading"><?php echo get_comment_author_link(); ?></h4>
  <p class="post-meta"><time datetime="<?php echo comment_date('c'); ?>"><a href="<?php echo esc_url(htmlspecialchars(get_comment_link($comment->comment_ID))); ?>"><?php printf(esc_html__('%1$s', 'stratus'), get_comment_date(),  get_comment_time()); ?></a></time>
  <?php edit_comment_link(esc_html__('(Edit)', 'stratus'), '', ''); ?></p>

  <?php if ($comment->comment_approved == '0') : ?>
    <div class="comment-awaiting">
      <?php esc_html_e('Your comment is awaiting moderation.', 'stratus'); ?>
    </div>
  <?php endif; ?>

  <?php comment_text(); ?>
  
  <div class="comment-reply">
  	<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
  </div>