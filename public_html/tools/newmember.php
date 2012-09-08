<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require('common.php');

$aEmails = array(
				 'sysblog'		=>	array(
				 						  'jhayward1980@gmail.com',
				 						  ),
				 'sysplanet'	=>	array(
				 						  'jhayward1980@gmail.com',
				 						  ),
				 'syswiki'		=>	array(
				 						  'jhayward1980@gmail.com',
				 						  ),
				 'sysgroup'		=>	array(
				 						  'jhayward1980@gmail.com',
				 						  #'nottinghack@gmail.com',
				 						  ),
				 );


if (isset($_POST['submit'])) {
	$aErrors = checkForm();
	
	if (count($aErrors) > 0) {
		$oSmarty->assign('errors', $aErrors);
		$oSmarty->assign('formdata', $_POST);
		$oSmarty->display('newmember.tpl');
	}
	else {
		# process
		$sHeaders = "From: New Member Request <newmember@nottinghack.org.uk>\r\n";
		
		$sMsg = 'New member request details' . "\n\n";
		$sMsg .= 'Name: ' . $_POST['yourname'] . "\n";
		$sMsg .= 'Email: ' . $_POST['youremail'] . "\n";
		$sMsg .= 'Preferred username: ' . $_POST['username'] . "\n\n";
		
		if (isset($_POST['sysblog']) and $_POST['sysblog'] == "on") {
			$sTo = implode(',', $aEmails['sysblog']);
			$sSubject = 'Request for ' . $_POST['yourname'] . ': Nottinghack Blog';
			
			mail($sTo, $sSubject, $sMsg, $sHeaders); 
		}
		if (isset($_POST['sysplanet']) and $_POST['sysplanet'] == "on") {
			$sImgURL = "";
			
			if ($_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
				$sExt = getFileExt($_FILES['avatar']['name']);
				if ($sExt == "jpg" or $sExt == "jpeg" or $sExt == "gif" or $sExt == "png") {
					$sNewName = substr(sha1(mktime()), 0, 8) . "." . $sExt;
					move_uploaded_file($_FILES['avatar']['tmp_name'], "uploads/" . $sNewName);
					$sImgURL = URL . ROOT_URL . 'uploads/' . $sNewName;
				}
			}
			$sThisMsg = $sMsg;
			$sThisMsg .= 'Blog feed: ' . $_POST['yourrss'] . "\n";
			if ($sImgURL != "") {
				$sThisMsg .= 'Avatar: ' . $sImgURL . "\n";
			}
			
			$sTo = implode(',', $aEmails['sysplanet']);
			$sSubject = 'Request for ' . $_POST['yourname'] . ': Planet Nottinghack';
			
			mail($sTo, $sSubject, $sThisMsg, $sHeaders); 
		}
		if (isset($_POST['syswiki']) and $_POST['syswiki'] == "on") {
			$sTo = implode(',', $aEmails['syswiki']);
			$sSubject = 'Request for ' . $_POST['yourname'] . ': Wiki';
			
			mail($sTo, $sSubject, $sMsg, $sHeaders); 
		}
		if (isset($_POST['sysgroup']) and $_POST['sysgroup'] == "on") {
			$sTo = implode(',', $aEmails['sysgroup']);
			$sSubject = 'Request for ' . $_POST['yourname'] . ': Members Mailing List';
			
			mail($sTo, $sSubject, $sMsg, $sHeaders); 
		}
		
		$oSmarty->display('newmember_complete.tpl');
	}
	
	
}
else {
	$oSmarty->display('newmember.tpl');
}


function checkForm() {
	$aErrors = array();
	
	if (!isText($_POST['yourname'])) {
		$aErrors['yourname'] = 1;
	}
	if (!isEmail($_POST['youremail'])) {
		$aErrors['youremail'] = 1;
	}
	if (!isText($_POST['username'])) {
		$aErrors['username'] = 1;
	}
	
	if (isset($_POST['sysplanet']) and $_POST['sysplanet'] == "on") {
		if (!isURL($_POST['yourrss'])) {
			$aErrors['yourrss'] = 1;
		}
	}
	
	
	return $aErrors;
}

?>
