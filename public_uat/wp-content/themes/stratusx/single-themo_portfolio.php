<?php global $post;  ?>
<?php include( locate_template( 'templates/page-layout.php' ) ); ?>

<div class="inner-container">
    <?php include( locate_template( 'templates/page-header.php' ) ); // Page Header Template ?>

    <?php echo wp_kses_post($outer_container_open) . wp_kses_post($outer_row_open); // Outer Tag Open ?>

    <?php /* OPEN MAIN CLASS */
    echo wp_kses_post($main_class_open); // support for sidebar ?>

    <?php
    $format = get_post_format();
    if ( false === $format ) {
        $format = 'standard';
    }
    ?>

    <?php get_template_part('templates/portfolio', $format); ?>


    <?php // check if sidebar and remove container, else leave it. ?>
    <!-- Comment form for pages -->
    <?php echo wp_kses_post($inner_container_open); ?>
    <div class="row">
        <div class="col-md-12">
            <?php comments_template('/templates/comments.php'); ?>
        </div>
    </div>
    <?php echo wp_kses_post($inner_container_close); ?>
    <!-- End Comment form for pages -->

    <?php
    /* CLOSE MAIN CLASS */
    echo wp_kses_post($main_class_close); ?>

    <?php
    /* SIDEBAR */
    include themo_sidebar_path(); ?>

    <?php
    echo wp_kses_post($outer_container_close) . wp_kses_post($outer_row_close); // Outer Tag Close ?>
</div><!-- /.inner-container -->