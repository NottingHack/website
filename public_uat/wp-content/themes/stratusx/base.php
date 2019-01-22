<?php get_template_part('templates/head'); ?>

<?php
$boxed_div_open ="";
$boxed_div_close ="";
$boxed_class ="";
if ( function_exists( 'get_theme_mod' ) ) {
    $boxed_mode = get_theme_mod( 'themo_boxed_layout', false );
    if ($boxed_mode){
        $boxed_div_open = '<div id="boxed">';
        $boxed_div_close = '</div><!-- #boxed -->';
        add_filter( 'body_class', function( $classes ) {
            return array_merge( $classes, array( 'boxed-mode' ) );
        } );
    }

    $sticky_header = get_theme_mod( 'themo_sticky_header', true );
    if ($sticky_header == true){
        add_filter( 'body_class', function( $classes ) {
            return array_merge( $classes, array( 'th-sticky-header' ) );
        } );
    }

    /*$headers_typography_ludicrous = get_theme_mod( 'headers_typography_ludicrous', 'off' );
    if ($headers_typography_ludicrous == 'on'){
        add_filter( 'body_class', function( $classes ) {
            return array_merge( $classes, array( 'th-bundled-font' ) );
        } );
    }*/
}
?>

<body <?php body_class(); ?>>

<?php
// Slider preloader enabled?
if ( function_exists( 'get_theme_mod' ) ) {
    $themo_preloader = get_theme_mod( 'themo_preloader', true );
    if ($themo_preloader == true){ ?>
        <!-- Preloader Start -->
        <div id="loader-wrapper">
            <div id="loader"></div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
        <!-- Preloader End -->
    <?php
    }
}
?>

<?php

//-----------------------------------------------------
// demo options
//-----------------------------------------------------
$is_demo = false;
if($is_demo){
	wp_register_script('demo_options', get_template_directory_uri() . '/demo/js/demo_options.js', array(), 1, true);
	wp_enqueue_script('demo_options');
    include( get_template_directory() . '/demo/demo_options.php');
}
?>

<?php
// jquery Animation Variable
global $themo_animation;
?>

<?php echo wp_kses_post($boxed_div_open); // Pre sanitized ?>

  <?php
    do_action('get_header');
    // Use Bootstrap's navbar if enabled in config.php
    if (current_theme_supports('bootstrap-top-navbar')) {
      get_template_part('templates/header-top-navbar');
    } else {
      get_template_part('templates/header');
    }
  ?>
  <div class="wrap" role="document">
  
    <div class="content">

        <?php include roots_template_path(); ?>

    </div><!-- /.content -->
  </div><!-- /.wrap -->

  <?php get_template_part('templates/footer'); ?>

<?php echo wp_kses_post($boxed_div_close); ?>
</body>
</html>