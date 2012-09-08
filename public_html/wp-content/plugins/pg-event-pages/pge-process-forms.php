<?php

function pge_add_attendee() {
	global $post;
	
	// Only name and email required, if not given, just error out.
	$sName = $_POST['yourname'];
	$sEmail = $_POST['youremail'];
	
	if ( !pge_is_name( $sName ) ) {
		return false;
	}
	if ( !pge_is_email( $sEmail ) ) {
		return false;
	}
	
	// Get picture and social network
	if ( $_POST['yoursn'] == "-" ) {
		$sPicture = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'images/notfound.png';
		$sSN = "-";
		$sSNID = "";
	}
	elseif ( $_POST['yoursn'] == "twitter" ) {
		$sPicture = pge_get_twitter_pic( $_POST['snid'] );
		$sSN = "twitter";
		$sSNID = pge_valid_twitter( $_POST['snid'] );
	}
	elseif ( $_POST['yoursn'] == "facebook" ) {
		$sPicture = pge_get_facebook_pic( $_POST['snid'] );
		$sSN = "facebook";
		$sSNID = pge_valid_facebook( $_POST['snid'] );
	}
	
	$aAttendee = array(
					   "name"		=>	pge_escape_html($sName),
					   "email"		=>	$sEmail,
					   "image"		=>	$sPicture,
					   "sn"			=>	$sSN,
					   "snid"		=>	$sSNID,
					   "confirm"	=>	pge_generate_confirm(),
					   );
	
	// Check we haven't added it before, if so, error out.
	// Check non-confirmed first
	$aAttendees = get_post_meta( $post->ID, "_pe_attendee_confirm" );
	if ( count( $aAttendees ) > 0 ) {
		foreach ($aAttendees as $aCheck) {
			if (pge_compare( $aAttendee, $aCheck )) {
				return false;
			}
		}
	}
	// now check confirmed
	$aAttendees = get_post_meta( $post->ID, "_pe_attendee" );
	if ( count( $aAttendees ) > 0 ) {
		foreach ($aAttendees as $aCheck) {
			if (pge_compare( $aAttendee, $aCheck )) {
				return false;
			}
		}
	}
	// finally, we got here, send email confirmation
	pge_send_confirm( $aAttendee, "attend" );
	add_post_meta( $post->ID, "_pe_attendee_confirm", $aAttendee );
	return true;
}

function pge_add_comment() {
	global $post;
	
	// Only name, email rating required, if not given, just error out.
	$sName = $_POST['yourname'];
	$sEmail = $_POST['youremail'];
	$sRating = $_POST['pge-rating'];
	
	if ( !pge_is_name( $sName ) ) {
		return false;
	}
	if ( !pge_is_email( $sEmail ) ) {
		return false;
	}
	if ( $sRating == "-" ) {
		return false;
	}
	else {
		$iRating = intval($sRating);
		if ($iRating < 0 or $iRating > 5) {
			return false;
		}
	}
	
	// Get picture and social network
	if ( $_POST['yoursn'] == "-" ) {
		$sPicture = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'images/notfound.png';
		$sSN = "-";
		$sSNID = "";
	}
	elseif ( $_POST['yoursn'] == "twitter" ) {
		$sPicture = pge_get_twitter_pic( $_POST['snid'] );
		$sSN = "twitter";
		$sSNID = pge_valid_twitter( $_POST['snid'] );
	}
	elseif ( $_POST['yoursn'] == "facebook" ) {
		$sPicture = pge_get_facebook_pic( $_POST['snid'] );
		$sSN = "facebook";
		$sSNID = pge_valid_facebook( $_POST['snid'] );
	}
	
	if ( pge_is_text($_POST['review'], true) ) {
		$sReview = pge_escape_html( $_POST['review'] );
	}
	else {
		$sReview = "";
	}
	
	$aRating = array(
					 "name"		=>	pge_escape_html($sName),
					 "email"	=>	$sEmail,
					 "image"	=>	$sPicture,
					 "sn"		=>	$sSN,
					 "snid"		=>	$sSNID,
					 "rating"	=>	$iRating,
					 "review"	=>	$sReview,
					 "confirm"	=>	pge_generate_confirm(),
					 );
	
	// Check we haven't added it before, if so, error out.
	// check non-confirmed first
	$aComments = get_post_meta( $post->ID, "_pe_comment_confirm" );
	if ( count( $aComments ) > 0 ) {
		foreach ($aComments as $aCheck) {
			if (pge_compare( $aRating, $aCheck )) {
				return false;
			}
		}
	}
	// now check confirmed
	$aComments = get_post_meta( $post->ID, "_pe_comment" );
	if ( count( $aComments ) > 0 ) {
		foreach ($aComments as $aCheck) {
			if (pge_compare( $aRating, $aCheck )) {
				return false;
			}
		}
	}
	// finally, we got here, send email confirmation
	pge_send_confirm( $aRating, "rate" );
	add_post_meta( $post->ID, "_pe_comment_confirm", $aRating );
}


function pge_is_name( $sText ) {
	if ($sText == "") {
		return false;
	}
	elseif ( preg_match( "/^[a-zA-Z0-9 \.'-]+$/", $sText ) > 0 ) {
		return true;
	}
	else {
		var_dump($sText);
		return false;
	}
}

function pge_is_text( $sText, $bAllowNull = false ) {
	if ($sText == "" and $bAllowNull == true) {
		return true;
	}
	elseif ($sText == "") {
		return false;
	}
	elseif (preg_match("/^[\w\s\.\,\?\!\%\£\&\(\)\"\-\/\+\=\<\>\']+$/", $sText)) {
		return true;
	}
	else {
		return false;
	}
}

function pge_is_email( $sEmail ) {
	return is_email( $sEmail );
	/*if ( preg_match( "/^[a-zA-Z0-9\.-]+\@[a-zA-Z0-9]+\.[a-zA-Z\.]+$/i", $sEmail ) > 0 ) {
		return true;
	}
	else {
		return false;
	}*/
}

function pge_escape_html( $sText ) {
	return htmlspecialchars( $sText );
}

function pge_get_twitter_pic( $sID ) {
	$sURL = 'http://api.twitter.com/1/users/profile_image/' . str_replace( "@", "", $sID ) . '.json';
	
	$oCH = curl_init();
	curl_setopt($oCH, CURLOPT_URL, $sURL);
	curl_setopt($oCH, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($oCH, CURLOPT_HEADER, true); 
	curl_setopt($oCH, CURLOPT_FOLLOWLOCATION, true);
	curl_exec($oCH);
	if ( curl_getinfo($oCH, CURLINFO_HTTP_CODE) == 404 ) {
		$sPicture = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'images/twitter.png';
	}
	else {
		$sPicture = curl_getinfo($oCH, CURLINFO_EFFECTIVE_URL);
	}
	curl_close($oCH);
	
	return $sPicture;
}

function pge_get_facebook_pic( $sID ) {
	$sID = str_replace( "https://", "", $sID );
	$sID = str_replace( "http://", "", $sID );
	$sID = str_replace( "www.facebook.com/", "", $sID );
	
	if ( strstr( $sID, "=" ) !== FALSE ) {
		if ( preg_match( "/id\=(\d+)/", $sID, $aMatches ) > 0 ) {
			$sID = $aMatches[1];
		}
	}
	
	$sURL = 'https://graph.facebook.com/' . $sID . '/picture';
	
	$oCH = curl_init();
	curl_setopt($oCH, CURLOPT_URL, $sURL);
	curl_setopt($oCH, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($oCH, CURLOPT_HEADER, true); 
	curl_setopt($oCH, CURLOPT_FOLLOWLOCATION, true);
	curl_exec($oCH);
	if ( strstr( curl_getinfo($oCH, CURLINFO_CONTENT_TYPE), "image" ) === FALSE ) {
		$sPicture = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'images/facebook.png';
	}
	else {
		$sPicture = curl_getinfo($oCH, CURLINFO_EFFECTIVE_URL);
	}
	curl_close($oCH);
	
	return $sPicture;
}

function pge_valid_twitter( $sID ) {
	$sURL = 'http://api.twitter.com/1/users/show.json?screen_name=' . str_replace( "@", "", $sID );
	
	$oCH = curl_init();
	curl_setopt($oCH, CURLOPT_URL, $sURL);
	curl_setopt($oCH, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($oCH, CURLOPT_HEADER, true); 
	curl_setopt($oCH, CURLOPT_FOLLOWLOCATION, true);
	curl_exec($oCH);
	if ( curl_getinfo($oCH, CURLINFO_HTTP_CODE) == 404 ) {
		$sReturn = "invalid id: " . $sID;
	}
	else {
		$sReturn = $sID;
	}
	curl_close($oCH);
	return $sReturn;
}

function pge_valid_facebook( $sID ) {
	$sID = str_replace( "https://", "", $sID );
	$sID = str_replace( "http://", "", $sID );
	$sID = str_replace( "www.facebook.com/", "", $sID );
	
	if ( strstr( $sID, "=" ) !== FALSE ) {
		if ( preg_match( "/id\=(\d+)/", $sID, $aMatches ) > 0 ) {
			$sID = $aMatches[1];
		}
	}
	
	$sURL = 'https://graph.facebook.com/' . $sID;
	
	$aResult = json_decode( file_get_contents( $sURL ), true );
	
	if ( isset( $aResult['error'] ) ) {
		$sReturn = "invalid id: " . $sID;
	}
	else {
		$sReturn = $sID;
	}
	return $sReturn;
}

function pge_compare( $aA, $aB ) {
	foreach ($aA as $sKey => $mValue) {
		if ( $sKey == "confirm" ) {
			continue;
		}
		elseif ( !isset($aB[$sKey]) ) {
			return false;
		}
		elseif ( $mValue != $aB[$sKey] ) {
			return false;
		}
	}
	return true;
}

function pge_generate_confirm() {
	$sConfirm = substr( sha1( microtime() * mt_rand() ), 5, 10 );
	return $sConfirm;
}

function pge_send_confirm( $aDetails, $sType) {
	global $post;
	
	$sSubject = "Nottingham Hackspace Event Confirmation";
	$sHeaders = "From: Nottingham Hackspace <nottinghack@googlemail.com>\r\n";
	
	$sMsg = "Hi\n\n";
	$sMsg .= "Your email address was recently used to ";
	if ( $sType == "attend" ) {
		$sMsg .= "register to attend";
	}
	elseif ( $sType == "rate" ) {
		$sMsg .= "rate or review";
	}
	$sMsg .= " a Nottingham Hackspace event - " . $post->post_title . "\n\n";
	$sMsg .= "If this was actually you, please click on the link below to confirm your email address.  Until you do this, your request will not be processed.\n\n";
	
	$sMsg .= "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "&pgetype=" . urlencode($sType) . "&pgeconf=" . urlencode($aDetails['confirm']) . "&pgeemail=" . urlencode($aDetails['email']) . "\n\n";
	
	$sMsg .= "If not, you don't need to take any further action.  Your email address will not be used by Nottingham Hackspace in any way.\n\n";
	$sMsg .= "Thankyou\nNottingham Hackspace";
	
	
	mail($aDetails['email'], $sSubject, $sMsg, $sHeaders);
}

function pge_process_confirm() {
	global $post;
	
	// get details from GET
	$sType = $_GET['pgetype'];
	$sEmail = $_GET['pgeemail'];
	$sConfirm = $_GET['pgeconf'];
	
	pge_confirm($post->ID, $sType, $sEmail, $sConfirm); 
}

function pge_confirm($iID, $sType, $sEmail, $sConfirm) {
	// get pending to test
	if ( $sType == "attend" ) {
		$aChecks = get_post_meta( $iID, "_pe_attendee_confirm" );
	}
	elseif ( $sType == "rate" ) {
		$aChecks = get_post_meta( $iID, "_pe_comment_confirm" );
	}
	else {
		return false;
	}
	
	if ( count( $aChecks ) > 0 ) {
		foreach ($aChecks as $aCheck) {
			if ( $aCheck['email'] == $sEmail and $aCheck['confirm'] == $sConfirm ) {
				// add it!
				if ( $sType == "attend" ) {
					add_post_meta( $iID, "_pe_attendee", $aCheck );
					delete_post_meta( $iID, "_pe_attendee_confirm", $aCheck );
				}
				elseif ( $sType == "rate" ) {
					add_post_meta( $iID, "_pe_comment", $aCheck );
					delete_post_meta( $iID, "_pe_comment_confirm", $aCheck );
				}
			}
		}
	}
}

?>
