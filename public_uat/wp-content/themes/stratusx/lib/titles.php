<?php
/**
 * Page titles
 */
function roots_title() {
  if (is_home()) {
    if (get_option('page_for_posts', true)) {
      return get_the_title(get_option('page_for_posts', true));
    } else {
      return esc_html__('Latest Posts', 'stratus');
    }
  } elseif (is_archive()) {
    $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    if ($term) {
      return apply_filters('single_term_title', $term->name);
    } elseif (is_post_type_archive()) {
      return apply_filters('the_title', get_queried_object()->labels->name);
    } elseif (is_day()) {
      return sprintf(esc_html__('Daily Archives: %s', 'stratus'), get_the_date());
    } elseif (is_month()) {
      return sprintf(esc_html__('Monthly Archives: %s', 'stratus'), get_the_date('F Y'));
    } elseif (is_year()) {
      return sprintf(esc_html__('Yearly Archives: %s', 'stratus'), get_the_date('Y'));
    } elseif (is_author()) {
      $author = get_queried_object();
      return sprintf(esc_html__('Author Archives: %s', 'stratus'), $author->display_name);
    } else {
      return single_cat_title('', false);
    }
  } elseif (is_search()) {
    return sprintf(esc_html__('Search Results for %s', 'stratus'), get_search_query());
  } elseif (is_404()) {
    return esc_html__('Page Not Found', 'stratus');
  } else {
    return get_the_title();
  }
}