<?php

add_action( 'admin_init', 'pge_admin_init' );
add_action( 'admin_menu', 'pge_menu' );


function pge_admin_init() {
	// Error reporting just in admin mode
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	$styleurl = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ));
	$styledir = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ));
	$jsurl = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'js/';
	$jsdir = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'js/';
	
	if ( file_exists( $styledir . 'admin.css' ) ) {
		wp_register_style( 'pgEventPagesAdmin', $styleurl . 'admin.css' );
	}
	
	if ( file_exists( $jsdir . 'admin.js' ) ) {
		wp_register_script( 'pgEventPagesAdmin',  $jsurl . 'admin.js' );
	}
}

function pge_menu() {
	$sPage = add_options_page('Event Management', 'Event Management', 'manage_options', 'pge-menu', 'pge_menu_page');
	add_action( 'admin_print_styles-' . $sPage, 'pge_admin_styles' );
}

function pge_admin_styles() {
	wp_enqueue_style( 'pgEventPagesAdmin' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'pgEventPagesAdmin' );
}

function pge_menu_page() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	$sOutput = '<div class="wrap">';
	$sOutput .= '<h2>Event Management :: '; 
	
	if ( !isset( $_GET['pgeshow'] ) ) {
		// all events
		if ( isset( $_GET['pgeaction'] ) ) {
			// some action
			if ( $_GET['pgeaction'] == "archive" ) {
				add_post_meta( $_GET['pgeid'], "pe_archive", "1", true );
			}
		}
		
		
		$pid = 309;
		$aPosts = pge_get_tree( $pid );
		
		$sOutput .= 'Summary</h2>';
		
		$sOutput .= '<table class="pge">';
		$sOutput .= '<thead>';
		$sOutput .= '<tr>';
		$sOutput .= '<th rowspan="2" width="50%" class="firstcol">Event</th>';
		//$sOutput .= '<th colspan="2" width="20%">Attendees</th>';
		$sOutput .= '<th colspan="2" width="20%">Reviews</th>';
		$sOutput .= '<th rowspan="2" width="30%" class="lastcol">Actions</th>';
		$sOutput .= '</tr>';
		$sOutput .= '<tr>';
		/*$sOutput .= '<th>Confirmed</th>';
		$sOutput .= '<th>Pending</th>';*/
		$sOutput .= '<th>Published</th>';
		$sOutput .= '<th>Pending</th>';
		$sOutput .= '</tr>';
		$sOutput .= '</thead>';
		$sOutput .= '<tbody>';
	
		$iClass = 1;
		foreach ($aPosts as $oPost) {
			// ignore archived events
			if ( get_post_meta( $oPost->ID, "pe_archive", true ) == "1" ) {
				continue;
			} 
		
			$sClass = $iClass == 1 ? "even" : "odd";
		
			$aAttendees = get_post_meta( $oPost->ID, "_pe_attendee" );
			$aAttendeesPend = get_post_meta( $oPost->ID, "_pe_attendee_confirm" );
			$aComments = get_post_meta( $oPost->ID, "_pe_comment" );
			$aCommentsPend = get_post_meta( $oPost->ID, "_pe_comment_confirm" );
		
			$sOutput .= '<tr class="' . $sClass . '">';
			$sOutput .= '<td class="firstcol">' . $oPost->post_title . '</td>';
			/*$sOutput .= '<td>' . count( $aAttendees ) . '</td>';
			$sOutput .= '<td>' . count( $aAttendeesPend ) . '</td>';*/
			$sOutput .= '<td>' . count( $aComments ) . '</td>';
			$sOutput .= '<td>' . count( $aCommentsPend ) . '</td>';
			$sOutput .= '<td class="lastcol">';
			$sOutput .= '<a href="' . $_SERVER['REQUEST_URI'] . '&pgeshow=details&pgeid=' . $oPost->ID . '">Details</a> ';
			$sOutput .= '<a href="' . $_SERVER['REQUEST_URI'] . '&pgeaction=archive&pgeid=' . $oPost->ID . '">Archive</a>';
			$sOutput .= '</td>';
			$sOutput .= '</tr>';
		
			$iClass = $iClass * -1;
		}
	
		$sOutput .= '</tbody>';
		$sOutput .= '</table>';
	}
	elseif ( $_GET['pgeshow'] == "details" ) {
		// We're looking at a specific event
		$sSummaryLink = substr( $_SERVER['REQUEST_URI'], 0, strpos( $_SERVER['REQUEST_URI'], "&" ) );
		
		if ( !isset( $_GET['pgeid'] ) ) {
			$sOutput .= 'Page Error</h2>';
			$sOutput .= '<p><a href="' . $sSummaryLink . '">&lt;&lt; Back to Summary</a></p>';
		}
		else {
			$oPost = get_post( $_GET['pgeid'] );
			
			// Process actions
			if ( isset( $_GET['pgeaction'] ) ) {
				if ( $_GET['pgeaction'] == "confirmattendee" ) {
					pge_confirm($oPost->ID, "attend", $_GET['email'], $_GET['confirm']);
				}
				elseif ( $_GET['pgeaction'] == "deleteattendee" ) {
					pge_delete($oPost->ID, "attend", $_GET['email'], $_GET['confirm']);
				}
				elseif ( $_GET['pgeaction'] == "confirmreview" ) {
					pge_confirm($oPost->ID, "rate", $_GET['email'], $_GET['confirm']);
				}
				elseif ( $_GET['pgeaction'] == "deletereview" ) {
					pge_delete($oPost->ID, "rate", $_GET['email'], $_GET['confirm']);
				}
			}
			
			// Output details of event
			$aAttendees = get_post_meta( $oPost->ID, "_pe_attendee" );
			$aAttendeesPend = get_post_meta( $oPost->ID, "_pe_attendee_confirm" );
			$aComments = get_post_meta( $oPost->ID, "_pe_comment" );
			$aCommentsPend = get_post_meta( $oPost->ID, "_pe_comment_confirm" );
			
			$sOutput .= $oPost->post_title . '</h2>';
			$sOutput .= '<p><a href="' . $sSummaryLink . '">&lt;&lt; Back to Summary</a></p>';
			
			// Attendees
			/*
			$sOutput .= '<h3>Attendees</h3>';
			
			$sOutput .= '<table class="pge">';
			$sOutput .= '<thead>';
			$sOutput .= '<tr>';
			$sOutput .= '<th rowspan="2" width="25%" class="firstcol">Attendee</th>';
			$sOutput .= '<th rowspan="2" width="8%">Status</th>';
			$sOutput .= '<th rowspan="2" width="28%">Email</th>';
			$sOutput .= '<th colspan="2" width="14%">Social Network</th>';
			$sOutput .= '<th rowspan="2" width="25%" class="lastcol">Actions</th>';
			$sOutput .= '</tr>';
			$sOutput .= '<tr>';
			$sOutput .= '<th width="5%">Type</th>';
			$sOutput .= '<th width="9%">ID</th>';
			$sOutput .= '</tr>';
			$sOutput .= '</thead>';
			$sOutput .= '<tbody>';
		
			$iClass = 1;
			
			foreach ($aAttendees as $aAttendee) {
				$sClass = $iClass == 1 ? "even" : "odd";
				
				$sOutput .= pge_attendee_row( $aAttendee, "Confirmed", $sClass );
				
				$iClass = $iClass * -1;
			}
			foreach ($aAttendeesPend as $aAttendee) {
				$sClass = $iClass == 1 ? "even" : "odd";
				
				$sOutput .= pge_attendee_row( $aAttendee, "Pending", $sClass );
				
				$iClass = $iClass * -1;
			}
			
			$sOutput .= '</tbody>';
			$sOutput .= '</table><br /><br />';
			*/
			// Comments
			$sOutput .= '<h3>Ratings and Reviews</h3>';
			
			$sOutput .= '<table class="pge">';
			$sOutput .= '<thead>';
			$sOutput .= '<tr>';
			$sOutput .= '<th rowspan="2" width="25%" class="firstcol">Reviewer</th>';
			$sOutput .= '<th rowspan="2" width="8%">Status</th>';
			$sOutput .= '<th rowspan="2" width="28%">Email</th>';
			$sOutput .= '<th colspan="2" width="14%">Social Network</th>';
			$sOutput .= '<th rowspan="2" width="25%" class="lastcol">Actions</th>';
			$sOutput .= '</tr>';
			$sOutput .= '<tr>';
			$sOutput .= '<th width="5%">Type</th>';
			$sOutput .= '<th width="9%">ID</th>';
			$sOutput .= '</tr>';
			$sOutput .= '</thead>';
			$sOutput .= '<tbody>';
		
			$iClass = 1;
			
			foreach ($aComments as $aComment) {
				$sClass = $iClass == 1 ? "even" : "odd";
				
				$sOutput .= pge_comment_row( $aComment, "Confirmed", $sClass );
				
				$iClass = $iClass * -1;
			}
			foreach ($aCommentsPend as $aComment) {
				$sClass = $iClass == 1 ? "even" : "odd";
				
				$sOutput .= pge_comment_row( $aComment, "Pending", $sClass );
				
				$iClass = $iClass * -1;
			}
			
			$sOutput .= '</tbody>';
			$sOutput .= '</table>';
		}
	}
	
	$sOutput .= '</div>'; 
	
	echo $sOutput;
}

function pge_attendee_row( $aAttendee, $sStatus, $sClass ) {
	$sOutput = '<tr class="' . $sClass . '">';
	$sOutput .= '<td class="firstcol"><img src="' . $aAttendee['image'] . '"> ';
	$sOutput .= $aAttendee['name'] . '</td>';
	$sOutput .= '<td>' . $sStatus . '</td>';
	$sOutput .= '<td>' . $aAttendee['email'] . '</td>';
	$sOutput .= '<td>' . $aAttendee['sn'] . '</td>';
	$sOutput .= '<td>' . $aAttendee['snid'] . '</td>';
	$sOutput .= '<td class="lastcol pgeactions">';
	if ( $sStatus == "Pending" ) {
		$sOutput .= '<a href="' . pge_href() . '&pgeaction=confirmattendee&email=' . urlencode( $aAttendee['email'] ) . '&confirm=' . urlencode( $aAttendee['confirm'] ) . '">Confirm</a> ';
	}
	$sOutput .= '<a href="' . pge_href() . '&pgeaction=deleteattendee&email=' . urlencode( $aAttendee['email'] ) . '&confirm=' . urlencode( $aAttendee['confirm'] ) . '">Delete</a> ';
	$sOutput .= '</td>';
	$sOutput .= '</tr>';
	return $sOutput;
}

function pge_comment_row( $aComment, $sStatus, $sClass ) {
	$sImgURL = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'images/';
	
	$sOutput = '<tr class="' . $sClass . '">';
	$sOutput .= '<td class="firstcol" rowspan="2">';
	$sOutput .= '<img src="' . $aComment['image'] . '"> ';
	$sOutput .= $aComment['name'] . '</td>';
	$sOutput .= '<td>' . $sStatus . '</td>';
	$sOutput .= '<td>' . $aComment['email'] . '</td>';
	$sOutput .= '<td>' . $aComment['sn'] . '</td>';
	$sOutput .= '<td>' . $aComment['snid'] . '</td>';
	$sOutput .= '<td class="lastcol pgeactions" rowspan="2">';
	if ( $sStatus == "Pending" ) {
		$sOutput .= '<a href="' . pge_href() . '&pgeaction=confirmreview&email=' . urlencode( $aComment['email'] ) . '&confirm=' . urlencode( $aComment['confirm'] ) . '">Confirm</a> ';
	}
	$sOutput .= '<a href="' . pge_href() . '&pgeaction=deletereview&email=' . urlencode( $aComment['email'] ) . '&confirm=' . urlencode( $aComment['confirm'] ) . '">Delete</a> ';
	$sOutput .= '</td>';
	$sOutput .= '</tr>';
	
	$sOutput .= '<tr class="' . $sClass . '">';
	$sOutput .= '<td>';
	for ( $i = 1; $i < 6; $i++ ) {
		if ($i <= $aComment['rating']) {
			$sOutput .= '<img src="' . $sImgURL . 'star1.png" class="pge-star" />';
		}
		else {
			$sOutput .= '<img src="' . $sImgURL . 'star0.png" class="pge-star" />';
		}
	} 
	$sOutput .= '</td>';
	$sOutput .= '<td class="firstcol" colspan="3">';
	if ( $aComment['review'] != "" ) {
		$sOutput .= $aComment['review'];
	}
	else {
		$sOutput .= 'No Review';
	}
	$sOutput .= '</td>';
	$sOutput .= '</tr>';
	return $sOutput;
}

function pge_get_tree( $pid ) {
	$aArgs = array( 'numberposts' => 500, 'post_type' => 'page', 'post_parent' => $pid );
	$aPosts = get_posts( $aArgs );
	$aReturn = $aPosts;
	foreach ( $aPosts as $aPost ) {
		$aNewPosts = pge_get_tree( $aPost->ID );
		if ( count( $aNewPosts ) > 0 ) {
			$aReturn = array_merge( $aReturn, $aNewPosts );
		}
	}
	return $aReturn;
}

function pge_delete($iID, $sType, $sEmail, $sConfirm) {
	// get pending to test
	if ( $sType == "attend" ) {
		$aChecks = get_post_meta( $iID, "_pe_attendee" );
		$aChecks1 = get_post_meta( $iID, "_pe_attendee_confirm" );
	}
	elseif ( $sType == "rate" ) {
		$aChecks = get_post_meta( $iID, "_pe_comment" );
		$aChecks1 = get_post_meta( $iID, "_pe_comment_confirm" );
	}
	else {
		return false;
	}
	
	if ( count( $aChecks ) > 0 ) {
		foreach ($aChecks as $aCheck) {
			if ( $aCheck['email'] == $sEmail and $aCheck['confirm'] == $sConfirm ) {
				// add it!
				if ( $sType == "attend" ) {
					delete_post_meta( $iID, "_pe_attendee", $aCheck );
					return true;
				}
				elseif ( $sType == "rate" ) {
					delete_post_meta( $iID, "_pe_comment", $aCheck );
					return true;
				}
			}
		}
	}
	
	if ( count( $aChecks1 ) > 0 ) {
		foreach ($aChecks1 as $aCheck) {
			if ( $aCheck['email'] == $sEmail and $aCheck['confirm'] == $sConfirm ) {
				// add it!
				if ( $sType == "attend" ) {
					delete_post_meta( $iID, "_pe_attendee_confirm", $aCheck );
					return true;
				}
				elseif ( $sType == "rate" ) {
					delete_post_meta( $iID, "_pe_comment_confirm", $aCheck );
					return true;
				}
			}
		}
	}
}

function pge_href() {
	$sHref = $_SERVER['PHP_SELF'];
	$sHref .= '?page=' . $_GET['page'];
	if ( isset( $_GET['pgeshow'] ) ) {
		$sHref .= '&pgeshow=' . $_GET['pgeshow'];
	}
	if ( isset( $_GET['pgeid'] ) ) {
		$sHref .= '&pgeid=' . $_GET['pgeid'];
	}
	
	return $sHref;
}

/*

$aMeta = get_post_meta( 577, "_pe_attendee" );
	foreach ($aMeta as $sMeta) {
		$sOutput .= '<ul>';
		foreach ($sMeta as $sKey=>$sValue) {
			$sOutput .= '<li>' . $sKey . ': ' . $sValue . '</li>';
		}
		$sOutput .= '</ul>';
	}
	$sOutput .= '<h1>Pending</h1>'; 
	$aMeta = get_post_meta( 577, "_pe_attendee_confirm" );
	foreach ($aMeta as $sMeta) {
		$sOutput .= '<ul>';
		foreach ($sMeta as $sKey=>$sValue) {
			$sOutput .= '<li>' . $sKey . ': ' . $sValue . '</li>';
		}
		$sOutput .= '</ul>';
	}
	*/
?>
