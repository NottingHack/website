<?php
/**
 * Roots includes
 */
include( get_template_directory() . '/lib/init.php');            // Initial theme setup and constants
include( get_template_directory() . '/lib/wrapper.php');         // Theme wrapper class
include( get_template_directory() . '/lib/config.php');          // Configuration
include( get_template_directory() . '/lib/titles.php');          // Page titles
include( get_template_directory() . '/lib/cleanup.php');         // Cleanup
include( get_template_directory() . '/lib/nav.php');             // Custom nav modifications
include( get_template_directory() . '/lib/comments.php');        // Custom comments modifications
include( get_template_directory() . '/lib/widgets.php');         // Sidebars and widgets
include( get_template_directory() . '/lib/scripts.php');         // Scripts and stylesheets
include( get_template_directory() . '/lib/custom.php');          // Custom functions
include( get_template_directory() . '/lib/class-tgm-plugin-activation.php');    // Bundled Plugins
include( get_template_directory() . '/lib/plugin-update-checker/plugin-update-checker.php');


/**
 * Define Elementor Partner ID
 */
if ( ! defined( 'ELEMENTOR_PARTNER_ID' ) ) {
    define( 'ELEMENTOR_PARTNER_ID', 1700 );
}

/**
 * Recommend the Kirki plugin
 */
include( get_template_directory() . '/lib/include-kirki.php');          // Customizer options
/**
 * Load the Kirki Fallback class
 */
include( get_template_directory() . '/lib/stratus-kirki.php');
/**
 * Customizer additions.
 */
include( get_template_directory(). '/lib/customizer.php');


// Activate Option Tree in the theme rather than as a plugin
add_filter( 'ot_theme_mode', '__return_true' );
add_filter( 'ot_show_pages', '__return_false' );

include_once(get_template_directory() . '/option-tree/ot-loader.php');
include_once(get_template_directory() . '/option-tree/meta-boxes.php' ); // LOAD META BOXES


// Envato WP Theme Setup Wizard
// Set Envato Username - DISABLED FOR NOW
add_filter('stratus_theme_setup_wizard_username', 'stratus_set_theme_setup_wizard_username', 10);
add_filter('stratuschildtheme_theme_setup_wizard_username', 'stratus_set_theme_setup_wizard_username', 10);
if( ! function_exists('stratus_set_theme_setup_wizard_username') ){
    function stratus_set_theme_setup_wizard_username($username){
        return 'themovation';
    }
}

// Envato WP Theme Setup Wizard
// Set Envato Script URL - DISABLED FOR NOW
add_filter('stratus_theme_setup_wizard_oauth_script', 'stratus_set_theme_setup_wizard_oauth_script', 10);
add_filter('stratuschildtheme_theme_setup_wizard_oauth_script', 'stratus_set_theme_setup_wizard_oauth_script', 10);
if( ! function_exists('stratus_set_theme_setup_wizard_oauth_script') ){
    function stratus_set_theme_setup_wizard_oauth_script($oauth_url){
        return 'http://app.themovation.com/envato/api/server-script.php';
    }
}

// Envato WP Theme Setup Wizard
// Set Custom Default Content Titles and Descriptions
add_filter('stratus_theme_setup_wizard_default_content', 'stratus_theme_setup_wizard_default_content_script', 10);
add_filter('stratuschildtheme_theme_setup_wizard_default_content', 'stratus_theme_setup_wizard_default_content_script', 10);
if( ! function_exists('stratus_theme_setup_wizard_default_content_script') ){
    function stratus_theme_setup_wizard_default_content_script($default){

        // Check all by default
        $default['checked'] = 1;

        // Add user friendly titles and descriptions
        if (isset($default['title'])){
            switch($default['title']) {
                case 'Media':
                    $default['title'] = 'Media Files';
                    $default['description'] = 'Media from the demo, mostly photos and graphics.';
                    break;
                case 'Portfolio':
                    $default['title'] = 'Portfolio';
                    $default['description'] = 'Portfolio pages as seen on the demo.';
                    break;
                case 'Posts':
                    $default['title'] = 'Blog Posts';
                    $default['description'] = 'Blog Posts as seen on the demo.';
                    break;
                case 'Pages':
                    $default['description'] = 'Pages as seen on the demo.';
                    break;
                case 'My Library':
                    $default['title'] = 'Templates';
                    $default['description'] = 'Page Builder Templates for rapid page creation.';
                    break;
                case 'Widgets':
                    $default['description'] = 'Widgets as seen on the demo.';
                    break;
                case 'Forms':
                    $default['description'] = 'Formidable Forms as seen on the demo.';
                    break;
            }

        }

        return $default;
    }
}

// Envato WP Theme Setup Wizard
// Custom logo for Installer
add_filter('envato_setup_logo_image', 'envato_set_setup_logo_image', 10);
if( ! function_exists('envato_set_setup_logo_image') ){
    function envato_set_setup_logo_image($image_url){
        $logo_main = get_template_directory_uri() . '/assets/images/setup_logo.png' ;
        return $logo_main;
    }
}


// Envato WP Theme Setup Wizard
// Update Term IDs for Our Custom Post Stype saved inside _elementor_data Post Meta
/*
 * Takes page elementor widget name, page title and term slugs as an array
 * updates elementor json string to update term(s) during an import.
 */
if( ! function_exists('update_elm_widget_select_term_id') ) {
    function update_elm_widget_select_term_id($elmwidgetname, $pagetitle, $termslug = array())
    {
        // premature exit?
        if (!isset($termslug) || !isset($pagetitle) || !isset($elmwidgetname)) {
            return;
        } else {
            $pageobj = get_page_by_title($pagetitle); // get page object
            $pageid = false;
            if(isset($pageobj->ID)){
                $pageid = $pageobj->ID; // get page ID
            }

            // loop through all slugs requested and get terms ids
            foreach ($termslug as $slug) {
                $termid = term_exists($slug); // get term ID
                $termids[] = $termid; // add to array, we'll use this later.
            }

            // premature exit?
            if (!isset($termids) || !isset($pageid)) {
                return;
            } else {

                $data = get_post_meta($pageid, '_elementor_data', TRUE); // get elm json string

                /*if (!is_array($data)){
                    $data = json_decode($data, true); // decode that mofo
                }*/

                // We are looking for something very specific so let's grab it and go.
                // Does key exist? Does it match to the elm widget name passed in?

                if (isset($data[0]['elements'][0]['elements'][0]['widgetType']) && $data[0]['elements'][0]['elements'][0]['widgetType'] = $elmwidgetname) {
                    // make sure there is a term group setting.
                    if (!isset($data[0]['elements'][0]['elements'][0]['settings']['group'])) {
                        return;
                    } else {
                        $data[0]['elements'][0]['elements'][0]['settings']['group'] = $termids; //set updated term ids
                        //$newJsonString = json_encode($data); // encode the json data
                        update_post_meta($pageid, '_elementor_data',$data); // update post meta with new json string.
                    }
                }

            }

        }

    }
}

// Envato WP Theme Setup Wizard
// Hook to find / replace tour terms. Fires only during theme import profess.
if( ! function_exists('th_post_content_import_hook') ) {
    function th_post_content_import_hook()
    {
        update_elm_widget_select_term_id('themo-tour-grid', 'Home 1', array('packages'));
        update_elm_widget_select_term_id('themo-tour-grid', 'Tour Index', array('guided','packages','rafting','specials','whitewater'));
    }
}
add_action( 'th_post_content_import', 'th_post_content_import_hook', 10, 2 );


// Envato WP Theme Setup Wizard
//add_filter( 'stratus_enable_setup_wizard', '__return_true' );
//add_filter( 'stratuschildtheme_enable_setup_wizard', '__return_true' );


/*
 * Pre install check.
 * 1. Make sure we are not upgrading from Stratus Classic or at least warn of potential issues. Provide override.
 * 2. Make sure we are using PHP 5.4 +
 *
 * We use after_setup_theme vs after_switch_theme for our primary check
 * because the auto installer uses this hook and we want to make sure
 * everythig is good befor we install.
 *
*/

// do the pre check.
add_action( 'after_setup_theme', 'th_install_safety_check', 9 );
if ( ! function_exists( 'th_install_safety_check' ) ) :
    function th_install_safety_check() {

        // Check if we may be upgrading from Stratus Classic, exit and warn, provide helpful instructions.
        $th_themes_installed = wp_get_themes();
        foreach ($th_themes_installed as $th_theme) {

            if($th_theme->get( 'Name' ) > ""){
                $th_theme_name_arr = explode("-", $th_theme->get( 'Name' ), 2); // clean up child theme name
                $th_theme_name = trim(strtolower($th_theme_name_arr[0]));

                if($th_theme_name === 'stratus' && $th_theme->get( 'Version') < 3 && $th_theme->stylesheet > "" && TH_PREVENT_STRATUS_UPGRADE){

                    add_action( 'admin_notices', 'th_admin_notice_noupgrade' );
                    function th_admin_notice_noupgrade() {
                        ?>
                        <div class="update-nag">
                            <?php _e( 'Hello, we ran into a small problem, it looks like you are trying to upgrade from an earlier version of Strauts, Version 2. You can still upgrade but please be advised that these two versions are not developed under the same framework and so your existing content will not be migrated.', 'stratus'); ?> <?php _e( 'If you need help, please contact the <a href="https://themovation.ticksy.com/" target="_blank">Stratus support team here.</a> or <a href="https://themovation.ticksy.com/article/12056/" target="_blank">read the guide on updating Stratus V2.</a>', 'stratus' ); ?> <br />
                        </div>
                        <?php
                    }
                    switch_theme( $th_theme->stylesheet );
                    return false;
                }

            };
        }

        // Compare versions, just exit as after_switch_theme will do the fancy stuff.
        if ( version_compare(PHP_VERSION, TH_REQUIRED_PHP_VERSION, '<') ) : //PHP_VERSION
            return false;
        endif;

        // If it all looks good, run Envato WP Theme Setup Wizard
        include( get_template_directory() . '/plugins/envato_setup/envato_setup_init.php');     // Custom functions
        include( get_template_directory() . '/plugins/envato_setup/envato_setup.php');          // Custom functions
    }
endif;

add_action( 'after_switch_theme', 'check_theme_setup', 10, 2 );
function check_theme_setup($old_theme_name, $old_theme = false){

    // Compare versions.
    if ( version_compare(PHP_VERSION, TH_REQUIRED_PHP_VERSION, '<') ) :

        // Theme not activated info message.
        add_action( 'admin_notices', 'th_admin_notice_phpversion' );
        function th_admin_notice_phpversion() {
            ?>
            <div class="update-nag">
                <?php _e( 'Hello, we ran into a small problem, but it\'s an easy fix. Your version of <strong>PHP</strong>', 'stratus'); ?> <strong><?php echo PHP_VERSION; ?></strong> <?php _e( 'is unsupported. We recommend <strong>PHP 7+</strong>, however, the theme should work with <strong>PHP</strong>','stratus') ?> <strong><?php echo TH_REQUIRED_PHP_VERSION; ?>+</strong>. <?php _e( 'Please ask your web host to upgrade your version of PHP before activating this theme. If you need help, please contact the <a href="https://themovation.ticksy.com/" target="_blank">Stratus support team here.</a>', 'stratus' ); ?> <br />
            </div>
            <?php
        }

        // Switch back to previous theme.
        switch_theme( $old_theme->stylesheet );
        return false;

    endif;
}