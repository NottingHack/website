<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('common.php');
require_once(PHP_DIR . 'twitteroauth/twitteroauth.php');
require_once(SECURE_DIR . 'dm2tweet_config.php');

$sOutputFile = ROOT_DIR . 'output/dm2tweet.log';

$oTwitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $sOAuthToken, $sOAuthSecret);
$oTwitter->format = 'json';
$oTwitter->decode_json = true;

$aDMs = $oTwitter->get('direct_messages', array('count' => 200));

echo("DMs: " . count($aDMs) . "<br />\n");

$iSent = 0;
$iRejected = 0;

foreach ($aDMs as $oDM) {
	$sSender = strtolower($oDM->sender_screen_name);
	if (in_array($sSender, $aAuthorisedUsers)) {
		# get message and tweet out
		$sTweet = $oDM->text;
		$oTwitter->post('statuses/update', array('status' => $sTweet));
		
		# now delete DM to ensure not duplicated
		$oTwitter->post('direct_messages/destroy/' . $oDM->id_str);
		
		# log the message
		if (!file_exists($sOutputFile)) {
			$sOutput = '"Sender","Msg","Date","Time"' . "\n";
			file_put_contents($sOutputFile, $sOutput, LOCK_EX); 
		}
		$sOutput = '"' . $sSender . '","' . $sTweet . '","' . date("d/m/Y") . '","' . date("H:i:s") . '"' . "\n";
		file_put_contents($sOutputFile, $sOutput, FILE_APPEND | LOCK_EX); 
		
		$iSent++;
	}
	else {
		$iRejected++;
	}
}

echo("Sent: " . $iSent . "<br />\n");
echo("Rejected: " . $iRejected . "<br />\n");
?>
