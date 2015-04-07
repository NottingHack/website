<?php
/*
Plugin Name: Google Calendar Widget (NH) 
Description: A customisable widget for displaying an agenda of upcoming events.
Author: James Hayward
Version: 0.1
Author URI: http://www.purplegecko.co.uk/
*/
/* Version History

*/

class GL_Cal extends WP_Widget {

// former google calendar API call	
//	private $feed_url = 'http://www.google.com/calendar/feeds/info%40nottinghack.org.uk/private-acbf60a032394b53e3caae31e5c725eb/';
// current (April 2015) google calendar API call
	private $feed_url = 'https://www.google.com/calendar/feeds/info%40nottinghack.org.uk/public/basic';
		
	function GL_Cal() {
		// widget actual processes
		$widget_ops = array('classname' => 'widget_gl_cal', 'description' => 'Provides agenda of upcoming events' );
		parent::__construct( false, $name = 'Google Calendar Agenda', $widget_ops );
	}
	
	function form( $instance ) {
		// outputs the options form on admin
		$title = esc_attr($instance['title']);
		$show_num = esc_attr($instance['show_num']);
		$format = esc_attr($instance['format']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_num'); ?>">Number of items to show</label>
			<input id="<?php echo $this->get_field_id('show_num'); ?>" name="<?php echo $this->get_field_name('show_num'); ?>" type="text" value="<?php echo $show_num; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('format'); ?>">Date Format:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>" type="text" value="<?php echo $format; ?>" />
		</p>
		<?php
	}
	
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['show_num'] = strip_tags( $new_instance['show_num'] );
		$instance['format'] = strip_tags( $new_instance['format'] );
		return $instance;
	}
	
	function widget( $args, $instance ) {
		// outputs the content of the widget
		global $current_user;
		extract( $args );
		
		$xml_url = $this->feed_url . 'full?max-results=' . $instance['show_num'] . '&futureevents=true&orderby=starttime&sortorder=a&singleevents=true';
		$xml = file_get_contents( $xml_url );
		
		# check that we got something!
		if ( '' != $xml ) {
			# get array of events, indexed by YYYYMMDD using a function in a different file!
			$events = glgc_get_events( $xml, "date" );
		}
		else {
			$events = array();
		}
		
		# set up date format
		if ($instance['format'] == "") {
			$format = "l, jS F Y";
		}
		else {
			$format = $instance['format']; 
		}
		
		$title = apply_filters('widget_title', $instance['title']);
		
		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		// Widget content
		if (count($events) > 0) {
			echo('<ul class="glcal-agenda">');
			foreach ($events as $date => $eventlist) {
				echo('<li class="gl-date">');
				echo(date($format, strtotime($date)));
				echo('</li>');
				echo('<ul class="gl-events">');
				foreach ($eventlist as $event) {
					echo('<li class="gl-event">');
					echo('<span>' . date("H:i", $event['start']) . ' </span>');
					echo('<a href="' . $event['url'] . '">' . $event['name'] . '</a>');
					echo('</li>');
				}
				echo('</ul>');
			}
			echo('</ul>');
		}
		
		echo $after_widget;
	}
}

add_action('widgets_init', create_function('', 'return register_widget("GL_Cal");'));
?>
