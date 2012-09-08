<?php
/*
Plugin Name: Event Page Functions
Description: Adds functions to pages within a certain tree for RSVPs, ratings, etc 
Author: James Hayward
Version: 0.1
Author URI: http://www.purplegecko.co.uk/
*/
/* Version History

*/

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// Admins actions moved to admin page
add_action( 'wp_print_styles', 'pge_add_stylesheets' );
add_action( 'init', 'pge_add_javascript' );
add_filter( 'the_content', 'pg_event_pages' );

require_once( 'pge-admin-menu.php' );
require_once( 'pge-process-forms.php' );

function pge_add_stylesheets() {
	$styleurl = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ));
	$styledir = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ));
	
	# enqueue our style sheet
	if ( file_exists( $styledir . 'styles.css' ) ) {
		wp_register_style( 'pgEventPages', $styleurl . 'styles.css' );
		wp_enqueue_style( 'pgEventPages');
	}
}

function pge_add_javascript() {
	$jsurl = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'js/';
	$jsdir = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'js/';
	
	if (!is_admin()) {
		if ( file_exists( $jsdir . 'events.js' ) ) {
			wp_enqueue_script( 'jquery' );
			wp_register_script( 'pgEventPages',  $jsurl . 'events.js' );
			wp_enqueue_script( 'pgEventPages' );
		}
	}
}

function pg_event_pages( $content ) {
	global $post;
	
	$pid = 309; // Calendar
	
	// Add details to the page
	// Only act on pages within the tree
	if ( pge_is_in_tree($pid) ) {
		//delete_post_meta($post->ID, "_pe_attendee");
		//delete_post_meta($post->ID, "_pe_comment");
		
		// Process additions
		if ( isset($_POST['submit']) ) {
			$_POST = pge_strip_magic_quotes($_POST);
			
			if ( $_POST['formtype'] == "attendee" ) {
				pge_add_attendee();
			}
			elseif ( $_POST['formtype'] == "comment" ) {
				pge_add_comment();
			}
		}
		
		// Process confirmations
		if ( isset($_GET['pgeconf']) ) {
			pge_process_confirm();
		}
		
		// We're adding a div that will contain the list of attendees, comments and a link to register
		$sDiv = '<div id="pg-event">';
		
		// Attendees
		/*
		$sDiv .= '<div id="pge-attendees">';
		$sDiv .= '<h3>Attending</h3>';
		
		// Show current attendees
		$aAttendees = get_post_meta( $post->ID, "_pe_attendee" );
		if ( count( $aAttendees ) > 0 ) {
			$sDiv .= '<ul>';
			foreach ( $aAttendees as $aAttendee ) {
				$sDiv .= '<li>';
				$sDiv .= '<img src="' . $aAttendee["image"] . '" width="10" height="10" alt="' . $aAttendee['name'] . '" title="' . $aAttendee['name'] . '" />';
				$sDiv .= $aAttendee['name'];
				$sDiv .= '</li>';
			}
			$sDiv .= '</ul>';
		}
		else {
			$sDiv .= '<p>No one has registered yet.<br />Are you coming?</p>';
		}
		
		// Add new link
		$sDiv .= '<a href="#" id="pge-link-attendee">Register to attend</a>';
		
		$sDiv .= '</div>'; // pge-attendees
		*/
		
		// Comments
		$sDiv .= '<div id="pge-comments">';
		
		// Show current comments
		$aComments = get_post_meta( $post->ID, "_pe_comment" );
		$sImgURL = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'images/';
		
		$iOverallRating = 0;
		$iRatingCount = 0;
		
		if ( count( $aComments ) > 0 ) {
			$sComments = '';
			foreach ( $aComments as $aComment ) {
				$iOverallRating += $aComment['rating'];
				$iRatingCount++;
				
				$sComments .= '<div class="pge-comment">';
				$sComments .= '<img src="' . $aComment["image"] . '" width="10" height="10" alt="' . $aComment['name'] . '" title="' . $aComment['name'] . '" class="pge-profile" />';
				$sComments .= '<p class="pge-name">' . $aComment['name'] . '</p>';
				
				$sComments .= '<div class="pge-rating">';
				for ( $i = 1; $i < 6; $i++ ) {
					if ($i <= $aComment['rating']) {
						$sComments .= '<img src="' . $sImgURL . 'star1.png" class="pge-star" />';
					}
					else {
						$sComments .= '<img src="' . $sImgURL . 'star0.png" class="pge-star" />';
					}
				} 
				$sComments .= '</div>';
				
				if ($aComment['review'] != "") {
					$sComments .= '<p>' . str_replace("\n", '</p><p>', $aComment['review']) . '</p>';
				}
				
				$sComments .= '</div>';
			}
		}
		else {
			$sComments = '<p>Be the first to write a review!</p>';
		}
		
		$iOverallRating = round( $iOverallRating / $iRatingCount, 1 );
		
		$sDiv .= '<h3>Overall Rating:';
		$sDiv .= '<div id="pge-overall">';
		for ( $i = 1; $i < 6; $i++ ) {
			if ($i <= $iOverallRating) {
				$sDiv .= '<img src="' . $sImgURL . 'star1.png" class="pge-star" />';
			}
			elseif ( (($i-1)*2) < ($iOverallRating*2) ) {
				$sDiv .= '<img src="' . $sImgURL . 'star05.png" class="pge-star" />';
			}
			else {
				$sDiv .= '<img src="' . $sImgURL . 'star0.png" class="pge-star" />';
			}
		} 
		$sDiv .= '</div>';
		$sDiv .= '</h3>';
		
		$sDiv .= '<h3>Reviews</h3>';
		$sDiv .= $sComments;
		
		// Add new link
		$sDiv .= '<a href="#" id="pge-link-comment">Add a review</a>';
		
		$sDiv .= '</div>'; // pge-comments
		
		$sDiv .= '</div>'; // pg-event
		
		$content .= $sDiv;
		
		// Forms
		// Overlay
		$content .= '<div id="pge-overlay"></div>';
		
		// Add Attendee
		$content .= '<div id="pge-form-attendee" class="pge-form">';
		
		$content .= '<p>We will never share your details or profit from them.  For more details see our <a href="#" target="_blank">privacy policy</a>.</p>';
		$content .= '<p><strong>Your email must be valid as you will be sent a confirmation email.</strong></p>';
		$content .= '<p>Your social network id is only used to obtain your profile picture, and is optional.  For facebook, please copy and paste your profile URL.</p>';
		
		$content .= '<form method="POST" action="' . $_SERVER["REQUEST_URI"] . '">';
		
		$content .= '<input type="hidden" name="formtype" value="attendee" />';
		
		$content .= '<label for="yourname">Your name*</label>';
		$content .= '<input type="text" name="yourname" id="yourname" value="" />';
		
		$content .= '<label for="youremail">Your email*</label>';
		$content .= '<input type="text" name="youremail" id="youremail" value="" />';
		
		$content .= '<label for="yoursn">Social Network</label>';
		$content .= '<select name="yoursn">';
		$content .= '<option value="-"> - Select - </option>';
		$content .= '<option value="twitter">Twitter</option>';
		$content .= '<option value="facebook">Facebook</option>';
		$content .= '</select>';
		
		$content .= '<label for="snid">Network ID / URL</label>';
		$content .= '<input type="text" name="snid" value="" />';
		
		$content .= '<label for="buttons">&nbsp;</label>';
		$content .= '<input type="submit" name="submit" value="Save" class="button" />';
		$content .= '<input type="button" name="cancel" value="Cancel" id="pge-att-cancel" class="button" />';
		
		$content .= '</form>';
		$content .= '</div>';
		
		// Add Comment
		$content .= '<div id="pge-form-comment" class="pge-form">';
		
		$content .= '<p>We will not sell your details to anyone else.  For more details see our <a href="#" target="_blank">privacy policy</a>.</p>';
		$content .= '<p><strong>Your email must be valid as you will be sent a confirmation email.</strong></p>';
		$content .= '<p>Your social network id is only used to obtain your profile picture, and is optional.  For facebook, please copy and paste your profile URL.</p>';
		
		$content .= '<form method="POST" action="' . $_SERVER["REQUEST_URI"] . '">';
		
		$content .= '<input type="hidden" name="formtype" value="comment" />';
		
		$content .= '<label for="yourname">Your name*</label>';
		$content .= '<input type="text" name="yourname" id="yourname" value="" />';
		
		$content .= '<label for="youremail">Your email*</label>';
		$content .= '<input type="text" name="youremail" id="youremail" value="" />';
		
		$content .= '<label for="yoursn">Social Network</label>';
		$content .= '<select name="yoursn">';
		$content .= '<option value="-"> - Select - </option>';
		$content .= '<option value="twitter">Twitter</option>';
		$content .= '<option value="facebook">Facebook</option>';
		$content .= '</select>';
		
		$content .= '<label for="snid">Network ID / URL</label>';
		$content .= '<input type="text" name="snid" value="" />';
		
		$content .= '<label for="rating">Rate the event</label>';
		$content .= '<div class="pge-rate">';
		$content .= '<a href="#" class="pge-rate-star" id="pgerate1">&nbsp;</a>';
		$content .= '<a href="#" class="pge-rate-star" id="pgerate2">&nbsp;</a>';
		$content .= '<a href="#" class="pge-rate-star" id="pgerate3">&nbsp;</a>';
		$content .= '<a href="#" class="pge-rate-star" id="pgerate4">&nbsp;</a>';
		$content .= '<a href="#" class="pge-rate-star" id="pgerate5">&nbsp;</a>';
		$content .= '</div>';
		
		/*$content .= '<select name="rating" id="rating">';
		$content .= '<option value="-"> - Select - </option>';
		$content .= '<option value="1">1 star</option>';
		$content .= '<option value="2">2 stars</option>';
		$content .= '<option value="3">3 stars</option>';
		$content .= '<option value="4">4 stars</option>';
		$content .= '<option value="5">5 stars</option>';
		$content .= '</select>';*/
		
		$content .= '<input type="hidden" name="pge-rating" value="0" id="pge-rating" />';
		
		$content .= '<label for="review">Review</label>';
		$content .= '<textarea rows="3" name="review"></textarea>';
		
		$content .= '<label for="buttons">&nbsp;</label>';
		$content .= '<input type="submit" name="submit" value="Save" class="button" />';
		$content .= '<input type="button" name="cancel" value="Cancel" id="pge-com-cancel" class="button" />';
		
		$content .= '</form>';
		$content .= '</div>';
	}
	
	return $content;
}

function pge_is_in_tree( $pid ) {
	global $post;
	
	//testing
	//if ($post->ID == 577) {
	//	return true;
	//}
	
	
	// If we want to see the top of the tree, return true from here
	if ( is_page($pid) ) {
		return false;
	}
	
	$anc = get_post_ancestors( $post->ID );
	foreach ( $anc as $ancestor ) {
		if( is_page() && $ancestor == $pid ) {
			return true;
		}
	}
	
	return false;
}

function pge_strip_magic_quotes($arr) {
   foreach ($arr as $k => $v) {
       if (is_array($v)) {
	       $arr[$k] = strip_magic_quotes($v);
       }
       else {
	       $arr[$k] = stripslashes($v);
       }
   }
   return $arr;
}
?>
