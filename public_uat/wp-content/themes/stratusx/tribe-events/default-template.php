<?php
/**
 * Default Stratus Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Template -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<?php

list($key, $show_header, $page_header_float, $masonry) = themo_return_header_sidebar_settings("tribe_events");

?>
<?php include( locate_template( 'templates/page-layout.php' ) ); ?>
<div class="inner-container">
    <?php
    //-----------------------------------------------------
    // Include Header Template File
    //-----------------------------------------------------
    include( locate_template( 'templates/page-header-default.php' ) ); // Page Header Template ?>

    <?php
    //-----------------------------------------------------
    // OPEN | OUTER Container + Row
    //-----------------------------------------------------
    echo wp_kses_post($outer_container_open) . wp_kses_post($outer_row_open); // Outer Tag Open ?>

    <?php
    //-----------------------------------------------------
    // OPEN | Wrapper Class - Support for sidebar
    //-----------------------------------------------------
    echo wp_kses_post($main_class_open); ?>

    <?php
    //-----------------------------------------------------
    // OPEN | Section + INNER Container
    //----------------------------------------------------- ?>

    <section id="<?php echo sanitize_html_class($key).'_content'; ?>" <?php if(is_archive() || is_search() || is_home() ){echo "class='standard-blog'";}?>>
        <?php echo wp_kses_post($inner_container_open);?>

        <?php
        //-----------------------------------------------------
        // LOOP
        //----------------------------------------------------- ?>

        <div class="row">
            <div class="col-md-12">
                <div id="tribe-events-pg-template">
                    <?php tribe_events_before_html(); ?>
                    <?php tribe_get_view(); ?>
                    <?php tribe_events_after_html(); ?>
                </div> <!-- #tribe-events-pg-template -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->

        <?php
        //-----------------------------------------------------
        // CLOSE | Section + INNER Container
        //----------------------------------------------------- ?>
        <?php echo wp_kses_post($inner_container_close);?>
    </section>

    <?php
    //-----------------------------------------------------
    // CLOSE | Main Class
    //-----------------------------------------------------
    echo wp_kses_post($main_class_close); ?>

    <?php
    //-----------------------------------------------------
    // INCLUDE | Sidebar
    //-----------------------------------------------------
    include themo_sidebar_path(); ?>

    <?php
    //-----------------------------------------------------
    // CLOSE | OUTER Container + Row
    //-----------------------------------------------------
    echo wp_kses_post($outer_container_close) . wp_kses_post($outer_row_close); // Outer Tag Close ?>
</div><!-- /.inner-container -->