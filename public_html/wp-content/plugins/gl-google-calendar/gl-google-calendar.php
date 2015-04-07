<?php
/*
Plugin Name: Google Calendar Integration
Description: Builds calendar of events from google calendar, and replaces {gl_google_calendar} in post/page with calendar.
Author: James Hayward <james@geeksareforlife.com>
Version: 0.1
Author URI: http://www.geeksareforlife.com/
*/
/* Version History

*/

add_action( 'wp_print_styles', 'glgc_add_stylesheets' );
add_action( 'init', 'glgc_add_javascript' );
add_filter( 'the_content', 'gl_google_calendar' );

$glgc_lastsundays = array();

function glgc_add_stylesheets() {
	$styleurl = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ));
	$styledir = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ));
	
	# enqueue our style sheet - this does structural work only.
	# presentation style to be added into theme.
	if ( file_exists( $styledir . 'styles.css' ) ) {
		wp_register_style( 'glGoogleCalendar', $styleurl . 'styles.css' );
		wp_enqueue_style( 'glGoogleCalendar');
	}
	
	if ( file_exists( $styledir . 'ie6hacks.css' ) ) {
		wp_register_style( 'glGoogleCalendarIE6', $styleurl . 'ie6hacks.css' );
		$GLOBALS['wp_styles']->add_data( 'glGoogleCalendarIE6', 'conditional', 'IE 6' );
		wp_enqueue_style( 'glGoogleCalendarIE6');
	}
}

function glgc_add_javascript() {
	$jsurl = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'js/';
	$jsdir = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'js/';
	
	if (!is_admin()) {
		if ( file_exists( $jsdir . 'events.js' ) ) {
			wp_enqueue_script( 'jquery' );
			wp_register_script( 'glGoogleCalendarEvents',  $jsurl . 'events.js' );
			wp_enqueue_script( 'glGoogleCalendarEvents' );
		}
	}
}

function gl_google_calendar( $content ) {
	$search = "<p>{gl_google_calendar}</p>";
	
	# shortcut if content doesn't contain our string
	if (strpos($content, $search) === false) {
		return $content;
	}
	
	# This is where the calendar will be generated
	$calendar = '';
	
	#full?start-min=2011-02-01T00:00:00&start-max=2011-03-31T23:59:59
	// old google calendar API call
	//$feed_url = 'http://www.google.com/calendar/feeds/info%40nottinghack.org.uk/private-acbf60a032394b53e3caae31e5c725eb/';
	// April 2015 API call
	$feed_url = 'http://www.google.com/calendar/feeds/info%40nottinghack.org.uk/public/basic';
	
	# If user has clicked on next/prev month links, set the stamp appropriately
	if ( isset( $_GET['gl_show'] ) ) {
		$stamp = intval( $_GET['gl_show'] );
	}
	else {
		# use current month
		$stamp = mktime();
	}
	
	# Rip out month and year
	$thismonth = date( 'm', $stamp );
	$thisyear = date( 'Y', $stamp );
	# and next month and year
	$nextmonth = str_pad( ($thismonth + 1) % 12, 2, '0', STR_PAD_LEFT );
	$nextyear = floor( ($thismonth + 1) / 12 ) > 0 ? $thisyear + 1 : $thisyear;
	# little calendars
	$months = array(
					$nextmonth,
					str_pad( ($thismonth + 2) % 12, 2, '0', STR_PAD_LEFT ),
					str_pad( ($thismonth + 3) % 12, 2, '0', STR_PAD_LEFT ),
					str_pad( ($thismonth + 4) % 12, 2, '0', STR_PAD_LEFT ),
					);
	$years = array(
					$nextyear,
					floor( ($thismonth + 2) / 12 ) > 0 ? $thisyear + 1 : $thisyear,
					floor( ($thismonth + 3) / 12 ) > 0 ? $thisyear + 1 : $thisyear,
					floor( ($thismonth + 4) / 12 ) > 0 ? $thisyear + 1 : $thisyear,
					);
	
	# generate strings for URL
	$start = $thisyear . '-' . $thismonth . '-01T00:00:00';
	$end = $nextyear . '-' . $nextmonth . '-01T00:00:00';
	
	# Generate URL and get XML from Google
	$xml_url = $feed_url . 'full?start-min=' . $start . '&start-max=' . $end; 
	$xml = file_get_contents( $xml_url );
	#echo($xml_url);
	
	# check that we got something!
	if ( '' != $xml ) {
		# get array of events, indexed by YYYYMMDD
		$events = glgc_get_events( $xml, "date" );
		
		$calendar .= "\n\n" . '<div class="gl-main-calendar">' . "\n";
		
		$calendar .= glgc_build_calendar_html($thismonth, $thisyear, $events);
		
		$calendar .= '<div class="gl-clearer"></div>' . "\n";
		$calendar .= '</div>' . "\n\n";
		
		# little calendars
		for ($i = 0; $i < count($months)-1; $i++) {
			$start = $years[$i] . '-' . $months[$i] . '-01T00:00:00';
			$end = $years[$i + 1] . '-' . $months[$i + 1] . '-01T00:00:00';
			
			# Generate URL and get XML from Google
			$xml_url = $feed_url . 'full?start-min=' . $start . '&start-max=' . $end;
			$xml = file_get_contents( $xml_url );
			
			if ( '' != $xml ) {
				# get array of events, indexed by YYYYMMDD
				$events = glgc_get_events( $xml, "date" );
		
				$calendar .= "\n\n" . '<div class="gl-little-calendar">' . "\n";
		
				$calendar .= glgc_build_calendar_html($months[$i], $years[$i], $events);
		
				$calendar .= '<div class="gl-clearer"></div>' . "\n";
				$calendar .= '</div>' . "\n\n";
			}
		}
		
	}
	
	$content = str_ireplace($search, $calendar, $content);
	
	return $content;
}

function glgc_get_events( $xml, $mode ) {
	$dom = new DOMDocument();
	$dom->loadXML( $xml );
	$xpath = new DOMXPath( $dom );
	$xpath->registerNamespace( 'g', 'http://www.w3.org/2005/Atom' );
	$xpath->registerNamespace( 'openSearch', 'http://a9.com/-/spec/opensearchrss/1.0/' );
	$xpath->registerNamespace( 'gCal', 'http://schemas.google.com/gCal/2005' ); 
	$xpath->registerNamespace( 'gd', 'http://schemas.google.com/g/2005' );
	
	$events = array();

	$nodes = $xpath->query( '/g:feed/g:entry' );
	
	foreach ( $nodes as $node ) {
		$eventbase = array(
						   'name'	=>	$xpath->query('g:title', $node)->item(0)->nodeValue,
					   );
		$eventbase = array_merge($eventbase, glgc_parse_desc($xpath->query('g:content', $node)->item(0)->nodeValue));
		# google events with recurrence have multiple gd:when elements
		$whennodes = $xpath->query('gd:when', $node);
		foreach ($whennodes as $whennode) {
			$startstamp = glgc_convert_bst(strtotime($whennode->getAttribute('startTime')));
			$endstamp = glgc_convert_bst(strtotime($whennode->getAttribute('endTime')));
			$event = $eventbase;
			$event['start'] = $startstamp;
			$event['end'] = $endstamp;  
			
			if ($mode == "date") {
				if ( !isset( $events[ date( 'Ymd', $startstamp) ] ) ) {
					$events[ date( 'Ymd', $startstamp) ] = array();
				} 
				$events[ date( 'Ymd', $startstamp) ][] = $event;
			}
			elseif ($mode == "list") {
				$events[] = $event;
			}
		}
		
		
		/*if ( '1' == $xpath->query('ismeetup', $node)->item(0)->nodeValue ) {
			$stamp =  floor( $xpath->query('utc_time', $node)->item(0)->nodeValue / 1000 );
			$event = array(
						   'name'		=>	$xpath->query('name', $node)->item(0)->nodeValue,
						   'rsvps'		=>	$xpath->query('rsvpcount', $node)->item(0)->nodeValue,
						   'rating'		=>	$xpath->query('rating', $node)->item(0)->nodeValue,
						   'album'		=>	$xpath->query('photo_album_id', $node)->item(0)->nodeValue,
						   'photos'		=>	$xpath->query('photo_count', $node)->item(0)->nodeValue,
						   'url'		=>	$xpath->query('event_url', $node)->item(0)->nodeValue,
						   'status'		=>	$xpath->query('status', $node)->item(0)->nodeValue,
						   'attendees'	=>	$xpath->query('attendee_count', $node)->item(0)->nodeValue,
						   'date'		=>	date( 'd/m/Y', $stamp ),
						   'time'		=>	date( 'H:i', $stamp ),
						   'groupname'	=>	$xpath->query('group_name', $node)->item(0)->nodeValue,
						   );
			if ( !isset( $events[ date( 'Ymd', $stamp) ] ) ) {
				$events[ date( 'Ymd', $stamp) ] = array();
			} 
			$events[ date( 'Ymd', $stamp) ][] = $event;
		}*/
	}
	
	return $events;
}

# this checks if the date/time is BST, and outputs an updated stamp
function glgc_convert_bst($stamp) {
	global $glgc_lastsundays;
	
	# first check if this is April - Sept, cos that is definitly BST
	$isBST = false;
	$month = date( 'n', $stamp );
	$year = date( 'Y', $stamp );
	if ( $month >= 4 and $month <= 9 ) {
		$isBST = true;
	}
	elseif ( $month == 3 ) {
		# if past the last sunday in the month
		if ( !isset( $glgc_lastsundays[$year . $month] ) ) {
			$glgc_lastsundays[$year . $month] = glgc_calcsunday( $year, $month );
		}
		if ( date( 'j', $stamp ) > $glgc_lastsundays[$year . $month] ) {
			$isBST = true;
		}
	}
	elseif ( $month == 10 ) {
		# if before the last sunday in the month
		if ( !isset( $glgc_lastsundays[$year . $month] ) ) {
			$glgc_lastsundays[$year . $month] = glgc_calcsunday( $year, $month );
		}
		if ( date( 'j', $stamp ) < $glgc_lastsundays[$year . $month] ) {
			$isBST = true;
		}
	}
	
	if ( $isBST ) {
		$stamp = $stamp + 3600;
	}
	
	return $stamp;
} 

function glgc_calcsunday( $year, $month ) {
	# last day of this month
	$lastday = date( 'j', mktime( 0, 0, 0, $month+1, 0, $year ) );
	while ( $lastday > 0 ) {
		if ( date( 'w', mktime( 0, 0, 0, $month, $lastday, $year ) ) == 0 ) {
			return $lastday;
		}
		$lastday--;
	}
	return -1;
}

# This looks for fields within the description, and outputs and array.
function glgc_parse_desc($desc) {
	$return = array();
	
	$return['desc'] = '';
	
	$lines = explode("\n", $desc);
	
	foreach ($lines as $line) {
		if (preg_match('/^\{.*\}$/', $line) == 1) {
			$parts = explode(' | ', trim($line, "{}"));
			if ($parts[0] == "event_page") {
				$return['url'] = $parts[1];
			}
		}
		else {
			$return['desc'] .= $line . "\n";
		}
	}
	
	return $return;
} 

# This builds all the HTML, except the containing div.
# The containing div is where we style big or small calendar
function glgc_build_calendar_html($month, $year, $events) {
	$img_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'img/';
	
	# get some details for the main loop
	$start_stamp = mktime( 1, 0, 0, $month, 1, $year );
	
	$start_day = date( "N", $start_stamp );
	$month_length = date( "t", $start_stamp );
	$last_day = date( "N", mktime( 1, 0, 0, $month, $month_length, $year ) );
	
	$num_weeks = ceil( ( ( $start_day - 1 ) + $month_length ) / 7 );
	
	# next and previous time stamps
	$nextyear = floor( ($month + 1) / 12 ) > 0 ? $year + 1 : $year;
	$nextmonth = ($month + 1) % 12;
	$nextstamp = mktime( 0, 0, 0, $nextmonth, 1, $nextyear );
	$prevyear = ($month - 1) == 0 ? $year - 1 : $year;
	$prevmonth = ($month - 1) == 0 ? 12 : ($month - 1);
	$prevstamp = mktime( 0, 0, 0, $prevmonth, 1, $prevyear );
	
	# Header
	$calendar = "\t" . '<div class="gl-month-header">';
	$calendar .= '<a class="navlinks" href="' . $_SERVER["PHP_SELF"] . '?page_id=' . $_GET['page_id'] . '&gl_show=' . $prevstamp . '"><img src="' . $img_url . 'month_back.png" width="18" height="21" alt="back" /></a>';
	$calendar .= '<a href="' . $_SERVER["PHP_SELF"] . '?page_id=' . $_GET['page_id'] . '&gl_show=' . $start_stamp . '" class="gl-month-header">' . date("F Y", $start_stamp) . '</a>';
	$calendar .= '<a class="navlinks" href="' . $_SERVER["PHP_SELF"] . '?page_id=' . $_GET['page_id'] . '&gl_show=' . $nextstamp . '"><img src="' . $img_url . 'month_fwd.png" width="18" height="21" alt="forward" /></a>';
	$calendar .= '</div>' . "\n";
	$calendar .= "\t" . '<div class="gl-week">' . "\n";
	$calendar .= "\t\t" . '<div class="gl-header">M<span class="gl-day-text">onday</span></div>' . "\n";
	$calendar .= "\t\t" . '<div class="gl-header">T<span class="gl-day-text">uesday</span></div>' . "\n";
	$calendar .= "\t\t" . '<div class="gl-header">W<span class="gl-day-text">ednesday</span></div>' . "\n";
	$calendar .= "\t\t" . '<div class="gl-header">T<span class="gl-day-text">hursday</span></div>' . "\n";
	$calendar .= "\t\t" . '<div class="gl-header">F<span class="gl-day-text">riday</span></div>' . "\n";
	$calendar .= "\t\t" . '<div class="gl-header">S<span class="gl-day-text">aturday</span></div>' . "\n";
	$calendar .= "\t\t" . '<div class="gl-header">S<span class="gl-day-text">unday</span></div>' . "\n";
	$calendar .= "\t" . '</div>' . "\n";
	
	# MAIN LOOP - this is what actually builds the calendar
	$day_num = 1;
	for ( $week = 0; $week < $num_weeks; $week++ ) {
		$calendar .= "\t" . '<div class="gl-week">' . "\n";
		
		for ( $i = 1; $i < 8; $i++ ) {
			# what's the date?
			$now_date = $year . $month . str_pad( $day_num, 2, '0', STR_PAD_LEFT );;
			
			# Deal with those parts of the week before and after the month
			if ( $week == 0 and $i < $start_day ) {
				$calendar .= "\t\t" . '<div class="gl-day gl-empty"></div>' . "\n";
			}
			elseif ( $week == ( $num_weeks - 1 ) and $i > $last_day ) {
				$calendar .= "\t\t" . '<div class="gl-day gl-empty"></div>' . "\n";
			}
			else {
				$calendar .= "\t\t" . '<div class="gl-day';
				# extra classes
				if ( $i == 6 or $i == 7 ) {
					$calendar .= ' gl-weekend';
				}
				if ( isset( $events[$now_date] ) ) {
					$calendar .= ' gl-events';
				}
				$calendar .= '">' . "\n";
				
				# day contents
				$calendar .= "\t\t\t" . '<div class="gl-date">' . $day_num . '</div>' ."\n";
				
				if ( isset( $events[$now_date] ) ) {
					for ( $j = 0; $j < count( $events[$now_date] ); $j++ ) {
						$event = $events[$now_date][$j];
						$calendar .= "\t\t\t" . '<div class="gl-event gl-event' . $j;
						# extra classes
						if ( 'past' == $event['status'] ) {
							$calendar .= ' gl-past';
						}
						$calendar .= '">' . "\n";
						
						# Time
						$calendar .= "\t\t\t\t" . '<div class="gl-time">' . date("H:i", $event['start']) . '</div>' . "\n";
						# Name
						$calendar .= "\t\t\t\t" . '<div class="gl-name">' . $event['name'] . '</div>' . "\n";
						
						
						# Additional Info Fields
						# Desc
						$calendar .= "\t\t\t\t" . '<div class="gl-desc">' . glgc_excerpt($event['desc']) . '</div>' . "\n";
						# URL
						if (isset($event['url'])) {
							$calendar .= "\t\t\t\t" . '<div class="gl-link">' . $event['url'] . '</div>' . "\n";
						}
						
						/*# Attendees or RSVPS, depending on status
						$calendar .= "\t\t\t\t" . '<div class="gl-people">';
						if ( 'past' == $event['status'] ) {
							$calendar .= $event['attendees'];
						}
						else {
							$calendar .= $event['rsvps'];
						}
						$calendar .= '</div>' . "\n";
						
						# Rating
						$calendar .= "\t\t\t\t" . '<div class="gl-rating ' . gl_convert_rating( $event['rating'] ) . '">' . $event['rating'] . '</div>' . "\n";
						
						# Photos
						if ( 0 < $event['photos'] ) {
							$calendar .= "\t\t\t\t" . '<div class="gl-photos"><a href="http://www.meetup.com/' . $event['groupname'] . '/photos/' . $event['album'] . '/">' . $event['photos'] . '</a></div>' . "\n";
						}
						*/
						# close the event div
						$calendar .= "\t\t\t" . '</div>' . "\n";
					}
				}
				
				# close the day div
				$calendar .= "\t\t" . '</div>' . "\n";
				
				$day_num++;
			}
		}
		
		# close the week div
		$calendar .= "\t" . '</div>' . "\n";
	}
	
	return $calendar;
}

function glgc_excerpt($string, $length = 150) {
	$string = strip_tags($string, "<br><br /><br/>");
	if(strlen($string) > $width) {
		$string = wordwrap($string, $length);
		$string = substr($string, 0, strpos($string, "\n"));
	}
	
	return $string;
}
