<?php
//======================================================================
// Register sidebars and widgets
//======================================================================

//-----------------------------------------------------
// roots_widgets_init
//-----------------------------------------------------
function roots_widgets_init() {
	// Sidebars
	register_sidebar(array(
		'name'          => esc_html__('Primary', 'stratus'),
		'id'            => 'sidebar-primary',
		'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));
	//$i = $themo_footer_columns;
	/* Themovation Theme Options */
	if ( function_exists( 'get_theme_mod' ) ) {
		/* Footer  Columns */

		$themo_footer_show = get_theme_mod( 'themo_footer_widget_switch', 'off' );

		if($themo_footer_show == true){
			$themo_footer_columns = get_theme_mod( 'themo_footer_columns', 2 );

			for ($i = 1; $i <= $themo_footer_columns; $i++) {
				register_sidebar(array(
					'name'          => sprintf(esc_html__('Footer Column %1$s', 'stratus'),$i),
					'id'            => "sidebar-footer-$i",
					'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
					'after_widget'  => '</div></section>',
					'before_title'  => '<h3 class="widget-title">',
					'after_title'   => '</h3>',
				));
			}
		}
	}

    // Widgets
    register_widget('WP_Widget_Themo_Social_Icons');
	register_widget('WP_Widget_Themo_Payments_Accepted');
	register_widget('WP_Widget_Themo_Contact_Info');
    register_widget('WP_Widget_Themo_Logo');
}
add_action('widgets_init', 'roots_widgets_init');



//-----------------------------------------------------
// Social Media Icon Widget
//-----------------------------------------------------
class WP_Widget_Themo_Social_Icons extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_themo_social_icons', 'description' => esc_html__( "Social Icons", 'stratus') );
		parent::__construct('themo-social-icons', esc_html__('Social Icons', 'stratus'), $widget_ops);
		$this->alt_option_name = 'widget_themo_social_icons';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_themo_social_icons', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = sanitize_html_class($this->id);

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo esc_html( $cache[ $args['widget_id'] ] );
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base); ?>

        <?php // GET SOCIAL ICONS ?>
		<section class="widget widget-social">
    		<div class="widget-inner">
        		<?php if ( $title ) {?>
                <h3 class="widget-title"><?php echo esc_attr($title); ?></h3>
                <?php } ?>
        			<div class="soc-widget">
        			<?php echo themo_return_social_icons(); ?>
           			</div>
    			</div>
		</section>

		<?php
		//$cache[$args['widget_id']] = ob_get_flush();
		//wp_cache_set('widget_themo_social_icons', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_themo_social_icons']) )
			delete_option('widget_themo_social_icons');
		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_themo_social_icons', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
?>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'stratus'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
	}
}


//-----------------------------------------------------
// Payments Accepted Widget
//-----------------------------------------------------
class WP_Widget_Themo_Payments_Accepted extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_themo_payments_accepted', 'description' => esc_html__( "Payments Accepted", 'stratus') );
		parent::__construct('themo-payments-accepted', esc_html__('Payments Accepted', 'stratus'), $widget_ops);
		$this->alt_option_name = 'widget_themo_payments_accepted';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_themo_payments_accepted', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = sanitize_html_class($this->id);

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo esc_html( $cache[ $args['widget_id'] ] );
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base); ?>

		<?php // GET PAYMENTS ACCEPTED ?>
		<section class="widget widget-th-payments">
			<div class="widget-inner">
				<?php if ( $title ) {?>
					<h3 class="widget-title"><?php echo esc_attr($title); ?></h3>
				<?php } ?>
				<div class="th-payments-widget">
					<?php echo themo_return_payments_accepted(); ?>
				</div>
			</div>
		</section>

		<?php
		//$cache[$args['widget_id']] = ob_get_flush();
		//wp_cache_set('widget_themo_payments_accepted', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_themo_payments_accepted']) )
			delete_option('widget_themo_payments_accepted');
		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_themo_payments_accepted', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		?>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'stratus'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
	<?php
	}
}

//-----------------------------------------------------
// Contact Info Widget
//-----------------------------------------------------
class WP_Widget_Themo_Contact_Info extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_themo_contact_info', 'description' => esc_html__( "Contact Info", 'stratus') );
		parent::__construct('themo-contact-info', esc_html__('Contact Info', 'stratus'), $widget_ops);
		$this->alt_option_name = 'widget_themo_contact_info';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_themo_contact_info', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = sanitize_html_class($this->id);

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo esc_html( $cache[ $args['widget_id'] ] );
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base); ?>

		<?php // GET PAYMENTS ACCEPTED ?>
		<section class="widget widget-th-contact-info">
			<div class="widget-inner">
				<?php if ( $title ) {?>
					<h3 class="widget-title"><?php echo esc_attr($title); ?></h3>
				<?php } ?>
				<div class="th-contact-info-widget">
					<?php echo themo_return_contact_info(); ?>
				</div>
			</div>
		</section>

		<?php
		//$cache[$args['widget_id']] = ob_get_flush();
		//wp_cache_set('widget_themo_contact_info', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_themo_contact_info']) )
			delete_option('widget_themo_contact_info');
		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_themo_contact_info', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		?>
		<p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'stratus'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
	<?php
	}
}


//-----------------------------------------------------
// Logo Widget
//-----------------------------------------------------
class WP_Widget_Themo_Logo extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'widget_themo_logo', 'description' => esc_html__( "Footer Logo", 'stratus') );
        parent::__construct('themo-logo', esc_html__('Footer Logo', 'stratus'), $widget_ops);
        $this->alt_option_name = 'widget_themo_logo';

        add_action( 'save_post', array(&$this, 'flush_widget_cache') );
        add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
        add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
    }

    function widget($args, $instance) {
        $cache = wp_cache_get('widget_themo_logo', 'widget');

        if ( !is_array($cache) )
            $cache = array();

        if ( ! isset( $args['widget_id'] ) )
            $args['widget_id'] = sanitize_html_class($this->id);

        if ( isset( $cache[ $args['widget_id'] ] ) ) {
            echo esc_html( $cache[ $args['widget_id'] ] );
            return;
        }

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base); ?>

        <?php // GET PAYMENTS ACCEPTED ?>
        <section class="widget widget-th-logo">
            <div class="widget-inner">
                <?php if ( $title ) {?>
                    <h3 class="widget-title"><?php echo esc_attr($title); ?></h3>
                <?php } ?>
                <div class="th-logo-widget">
                    <?php echo themo_return_footer_logo(); ?>
                </div>
            </div>
        </section>

        <?php
        //$cache[$args['widget_id']] = ob_get_flush();
        //wp_cache_set('widget_themo_logo', $cache, 'widget');
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['widget_themo_logo']) )
            delete_option('widget_themo_logo');
        return $instance;
    }

    function flush_widget_cache() {
        wp_cache_delete('widget_themo_logo', 'widget');
    }

    function form( $instance ) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'stratus'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
    <?php
    }
}
