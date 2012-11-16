<?php

$sFile = "minutes_2012-09-05.txt";

$aMinutes = file($sFile, FILE_IGNORE_NEW_LINES);

$aIgnoreNames = array("nh-holly");

$aColours = array("#00C322", "#FF1300", "#3E13AF", "#FFD700", "#CD0074", "#FFAA00", "#9FEE00", "#009999", "#A64B00", "#D8005F");
$iColourID = 0;
$aUserMaps = array();

$sOutput = '{| class="wikitable"' . "\n";
$sOutput .= "!Time!!Name!!Minute\n";

foreach ($aMinutes as $sMinute) {
	if (strpos($sMinute, '[') === 0) {
		list($sTime, $sMinute) = explode("]", $sMinute);
		$sTime = trim($sTime, "[");
		if (strpos($sMinute, " ") !== 0) {
			// something is said
			list($sName, $sMinute) = explode(":", $sMinute, 2);
		}
		else {
			// Room notification
			$sName = "";
		}
		
		if (!in_array($sName, $aIgnoreNames)) {
			$sOutput .= outputMinute($sTime, $sName, $sMinute);
		}
	}
}

$sOutput .= "|}\n";

echo($sOutput);

function outputMinute($sTime, $sName, $sMinute) {
	global $aUserMaps, $aColours, $iColourID;
	
	if (!isset($aUserMaps[$sName])) {
		if ($iColourID < count($aColours)) {
			$aUserMaps[$sName] = $aColours[$iColourID];
			$iColourID++;
		}
		else {
			$aUserMaps[$sName] = "000000";
		}
	}
	
	$sOutput = "|-\n";
	$sOutput .=  '|<span style="color: #AAAAAA">' . $sTime . '</span>||<span style="color: ' . $aUserMaps[$sName] . '">' . $sName . '</span>||' . $sMinute . "\n";
	
	return $sOutput;
}

?>
