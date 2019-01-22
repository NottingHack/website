<form role="search" method="get" class="search-form form-inline" action="<?php echo esc_url(home_url('/')); ?>">
  <div class="input-group">
    <input type="search" value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" class="search-field form-control" placeholder="<?php esc_html_e('Search', 'stratus'); ?> <?php bloginfo('name'); ?>">
    <label class="hide"><?php esc_html_e('Search for:', 'stratus'); ?></label>
    <span class="input-group-btn">
      <button type="submit" class="search-submit btn btn-default"><?php esc_html_e('Search', 'stratus'); ?></button>
    </span>
  </div>
</form>