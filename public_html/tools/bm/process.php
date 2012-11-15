<?php

$sFile = "minutes_2012-11-07.txt";

$aMinutes = file($sFile, FILE_IGNORE_NEW_LINES);

$aIgnoreNames = array("nh-holly");

foreach ($aMinutes as $sMinute) {
	if (strpos($sMinute, '[') === 0) {
		list($sTime, $sMinute) = explode("]", $sMinute);
		$sTime = trim($sTime, "[");
		if (strpos($sMinute, ":") !== FALSE) {
			// something is said
			list($sName, $sMinute) = explode(":", $sMinute, 2);
		}
		else {
			// Room notification
			$sName = "";
		}
		
		if (!in_array($sName, $aIgnoreNames)) {
			outputMinute($sTime, $sName, $sMinute);
		}
	}
}


function outputMinute($sTime, $sName, $sMinute) {
	echo($sTime . " > " . $sName . " > " . $sMinute . "<br />\n");
}

?>
