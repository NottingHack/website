<?php

function getFileExt($sFilename) {
	$sFilename = basename($sFilename);
	$iExtPos = strrpos($sFilename, '.');
	if ($iExtPos !== false) {
		return strtolower(substr($sFilename, $iExtPos+1));
	}
	else {
		return false;
	}
}

?>
