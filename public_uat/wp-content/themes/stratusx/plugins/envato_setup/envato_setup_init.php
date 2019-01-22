<?php

// This is the setup wizard init file.
// This file changes for each one of dtbaker's themes
// This is where I extend the default 'Envato_Theme_Setup_Wizard' class and can do things like remove steps from the setup process.

// This particular init file has a custom "Update" step that is triggered on a theme update. If the setup wizard finds some old shortcodes after a theme update then it will go through the content and replace them. Probably remove this from your end product.

if ( ! defined( 'ABSPATH' ) ) exit;


//add_filter('envato_setup_logo_image','dtbwp_envato_setup_logo_image');
//function dtbwp_envato_setup_logo_image($old_image_url){
//	return get_template_directory_uri().'/images/logo.png';
//}

if ( ! function_exists( 'envato_theme_setup_wizard' ) ) :

    // THEMOVATION - Added missing Function.
    if ( ! function_exists( 'array_unshift_assoc' ) ) :
        function array_unshift_assoc( &$arr, $key, $val ) {
            $arr = array_reverse( $arr, true );
            $arr[$key] = $val;
            return array_reverse( $arr, true );
        }
    endif;


    function envato_theme_setup_wizard() {

        if(class_exists('Envato_Theme_Setup_Wizard')) {
            class dtbwp_Envato_Theme_Setup_Wizard extends Envato_Theme_Setup_Wizard {


                public function init_globals() {
                    $this->theme         = wp_get_theme();
                    $this->theme_name      = strtolower( preg_replace( '#[^a-zA-Z]#', '', $this->theme->get( 'Name' ) ) );
                    $this->envato_username = apply_filters( $this->theme_name . '_theme_setup_wizard_username', 'themovation' );
                    $this->oauth_script    = apply_filters( $this->theme_name . '_theme_setup_wizard_oauth_script', 'http://themovation.net/files/envato/wptoken/server-script.php' );
                    $this->page_slug       = apply_filters( $this->theme_name . '_theme_setup_wizard_page_slug', $this->theme_name . '-setup' );
                    $this->parent_slug     = apply_filters( $this->theme_name . '_theme_setup_wizard_parent_slug', '' );

                    // create an images/styleX/ folder for each style here.
                    $this->site_styles = array(
                        'style1' => 'Stratus',
                        'style2' => '',
                        'style3' => '',
                    );

                    //If we have parent slug - set correct url
                    if ( $this->parent_slug !== '' ) {
                        $this->page_url = 'admin.php?page=' . $this->page_slug;
                    } else {
                        $this->page_url = 'themes.php?page=' . $this->page_slug;
                    }
                    $this->page_url = apply_filters( $this->theme_name . '_theme_setup_wizard_page_url', $this->page_url );

                    //set relative plugin path url
                    $this->plugin_path = trailingslashit( $this->cleanFilePath( dirname( __FILE__ ) ) );
                    $relative_url      = str_replace( $this->cleanFilePath( get_template_directory() ), '', $this->plugin_path );
                    $this->plugin_url  = trailingslashit( get_template_directory_uri() . $relative_url );
                }


                public function get_header_logo_width() {
                    return '204px';
                }


                /**
                 * Holds the current instance of the theme manager
                 *
                 * @since 1.1.3
                 * @var Envato_Theme_Setup_Wizard
                 */
                private static $instance = null;

                /**
                 * @since 1.1.3
                 *
                 * @return Envato_Theme_Setup_Wizard
                 */
                public static function get_instance() {
                    if ( ! self::$instance ) {
                        self::$instance = new self;
                    }

                    return self::$instance;
                }

                public function init_actions(){
                    if ( apply_filters( $this->theme_name . '_enable_setup_wizard', true ) && current_user_can( 'manage_options' )  ) {
                        add_filter( $this->theme_name . '_theme_setup_wizard_content', array(
                            $this,
                            'theme_setup_wizard_content'
                        ) );
                        add_filter( $this->theme_name . '_theme_setup_wizard_steps', array(
                            $this,
                            'theme_setup_wizard_steps'
                        ) );
                    }
                    parent::init_actions();
                }

                // THEMOVATION - Steps to skip
                public function theme_setup_wizard_steps($steps){
                    //unset($steps['design']); // this removes the "logo" step
//                    unset($steps['style']); // this removes the "logo" step
                    unset($steps['updates']); // this removes the "logo" step
                    unset($steps['design']); // this removes the "logo" step
                    return $steps;
                }
                public function theme_setup_wizard_content($content){
                    if($this->is_possible_upgrade()){
                        array_unshift_assoc($content,'upgrade',array(
                            'title' => __( 'Upgrade', 'stratus' ),
                            'description' => __( 'Upgrade Content and Settings.', 'stratus' ),
                            'pending' => __( 'Pending.', 'stratus' ),
                            'installing' => __( 'Installing Updates.', 'stratus' ),
                            'success' => __( 'Success.', 'stratus' ),
                            'install_callback' => array( $this,'_content_install_updates' ),
                            'checked' => 1
                        ));
                    }
                    return $content;
                }

                public function is_possible_upgrade(){
                    $widget = get_option('widget_text');
                    if(is_array($widget)) {
                        foreach($widget as $item){
                            if(isset($item['dtbwp_widget_bg'])){
                                return true;
                            }
                        }
                    }
                    // check if shop page is already installed?
                    $shoppage = get_page_by_title( 'Shop' );
                    if ( $shoppage || get_option( 'page_on_front', false ) ) {
                        return true;
                    }

                    return false;
                }

                public function _content_install_updates(){

                    // THEOVATION - If there is a menu called 'Main Menu' set it was the priamry

                    $locations = get_theme_mod( 'nav_menu_locations' );

                    if(!empty($locations))
                    {
                        foreach($locations as $locationId => $menuValue)
                        {
                            switch($locationId)
                            {
                                case 'primary-navigation':
                                    $menu = get_term_by('name', 'Main Menu', 'nav_menu');
                                    break;

                            }

                            if(isset($menu))
                            {
                                $locations[$locationId] = $menu->term_id;
                            }
                        }

                        set_theme_mod('nav_menu_locations', $locations);
                    }


                    return true;

                }

                // THEMOVATION - Custom Text & Action Hook
                public function envato_setup_customizeX() {
                    ?>

                    <?php do_action( 'th_post_content_import'); ?>

                    <h1>Theme Customization</h1>
                    <p>
                        Most changes to the website can be made through the Appearance > Customize menu from the WordPress
                        dashboard. These include:
                    </p>
                    <ul>
                        <li>Logo: Upload a new logo and adjust its size.</li>
                        <li>Menu & Header: Customize the site header and menus. </li>
                        <li>Colors: Choose a primary and accent colors. </li>
                        <li>Typography: Font sizes, styles, and colors.</li>
                        <li>Blog: Headings, styles and layout.</li>
                        <li>Cart / WooCommerce: Headings, sidebar and settings.</li>
                        <li>Slider: Settings and configurations.</li>
                        <li>Misc: Settings and configurations.</li>
                        <li>Footer: Widgets and layout settings.</li>
                    </ul>

                    <p>
                        <em>Advanced Users: If you are going to make changes to the theme source code please use a <a href="https://codex.wordpress.org/Child_Themes" target="_blank">Child Theme</a> rather than
                            modifying the main theme HTML/CSS/PHP code. This allows the parent theme to receive updates without
                            overwriting your source code changes. <br/> See <code>child-theme.zip</code> in the main theme zip for
                            a sample.</em>
                    </p>

                    <p class="envato-setup-actions step">
                        <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                           class="button button-primary button-large button-next"><?php esc_html_e( 'Continue', 'stratus' ); ?></a>
                    </p>

                    <?php
                }

                public function envato_setup_help_supportX() {
                    ?>
                    <h1>Help and Support</h1>
                    <p>This theme comes with 6 months item support from purchase date (with the option to extend this period).
                        This license allows you to use this theme on a single website. Please purchase an additional license to
                        use this theme on another website.</p>
                    <p>Item Support can be accessed from <a href="https://themovation.ticksy.com/" target="_blank">https://themovation.ticksy.com</a>
                        and includes:</p>
                    <ul>
                        <li>Availability of the author to answer questions</li>
                        <li>Answering technical questions about item features</li>
                        <li>Assistance with reported bugs and issues</li>
                        <li>Help with bundled 3rd party plugins</li>
                    </ul>

                    <p>Item Support <strong>does not</strong> Include:</p>
                    <ul>
                        <li>Customization services (this is available through <a
                                    href="https://studio.envato.com/explore/wordpress-customization"
                                    target="_blank">Envato Studio</a>)
                        </li>
                        <li>Installation services (this is available through <a
                                    href="https://studio.envato.com/explore/wordpress-installation"
                                    target="_blank">Envato Studio</a>)
                        </li>
                        <li>Help and Support for non-bundled 3rd party plugins (i.e. plugins you install yourself later on)</li>
                    </ul>
                    <p>More details about item support can be found in the ThemeForest <a
                                href="http://themeforest.net/page/item_support_policy" target="_blank">Item Support Polity</a>. </p>
                    <p class="envato-setup-actions step">
                        <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                           class="button button-primary button-large button-next"><?php esc_html_e( 'Agree and Continue', 'stratus' ); ?></a>
                        <?php wp_nonce_field( 'envato-setup' ); ?>
                    </p>
                    <?php
                }


                /**
                 * Final step
                 */
                public function envato_setup_readyX() {

                    update_option( 'envato_setup_complete', time() );
                    update_option( 'dtbwp_update_notice', strtotime('-4 days') );
                    ?>

                    <h1><?php esc_html_e( 'Your Website is Ready!', 'stratus' ); ?></h1>

                    <p>Congratulations! The theme has been activated and your website is ready. Login to your WordPress
                        dashboard to make changes and modify any of the default content to suit your needs.</p>

                    <div class="envato-setup-next-steps">
                        <div class="envato-setup-next-steps-first">
                            <h2><?php esc_html_e( 'Next Steps', 'stratus' ); ?></h2>
                            <ul>
                                <li class="setup-product"><a class="button button-primary button-large"
                                                             href="https://twitter.com/themovation"
                                                             target="_blank"><?php esc_html_e( 'Follow @themovation on Twitter', 'stratus' ); ?></a>
                                </li>
                                <li class="setup-product"><a class="button button-next button-large"
                                                             href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'View your new website!', 'stratus' ); ?></a>
                                </li>
                            </ul>
                        </div>
                        <div class="envato-setup-next-steps-last">
                            <h2><?php esc_html_e( 'More Resources', 'stratus' ); ?></h2>
                            <ul>
                                <li class="documentation"><a href="http://themovation.helpscoutdocs.com/"
                                                             target="_blank"><?php esc_html_e( 'Read the Theme Documentation', 'stratus' ); ?></a>
                                </li>
                                <li class="howto"><a href="https://wordpress.org/support/"
                                                     target="_blank"><?php esc_html_e( 'Learn how to use WordPress', 'stratus' ); ?></a>
                                </li>
                                <li class="rating"><a href="http://themeforest.net/downloads"
                                                      target="_blank"><?php esc_html_e( 'Leave an Item Rating', 'stratus' ); ?></a></li>
                                <li class="support"><a href="https://themovation.ticksy.com/"
                                                       target="_blank"><?php esc_html_e( 'Get Help and Support', 'stratus' ); ?></a></li>
                            </ul>
                        </div>
                    </div>
                    <?php
                }

            }

            dtbwp_Envato_Theme_Setup_Wizard::get_instance();
        }else{
            // log error?
        }
    }
endif;