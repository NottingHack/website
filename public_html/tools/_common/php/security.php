<?php
$sText = 'letters, numbers and \'.,?!"%£&()-/';
function isText($input, $allowNull = false) {
	if ($input == "" and $allowNull == true) {
		return true;
	}
	elseif ($input == "") {
		return false;
	}
	elseif (preg_match("/^[\w\s\'\.\,\?\!\%\£\&\(\)\"\-\/\+\=\<\>]+$/", $input)) {
		return true;
	}
	else {
		return false;
	}
}

$sEmail = 'email address';
function isEmail($input) {
	if ($input == "") {
		return false;
	}
	elseif (preg_match("/^[a-zA-Z0-9\.-]+\@[a-zA-Z0-9]+\.[a-zA-Z\.]+$/i", $input)) {
		return true;
	}
	else {
		return false;
	}
}

$sNum = 'numbers, brackets and spaces';
function isNumber($input, $allowNull = false) {
	if ($input == "" and $allowNull == true) {
		return true;
	}
	elseif ($input == "") {
		return false;
	}
	elseif (preg_match("/^[\d\(\)\+ ]+$/", $input)) {
		return true;
	}
	else {
		return false;
	}
}

$sDate = 'dd/mm/yyyy';
function isDate($input, $allowNull = false) {
	if ($input == "" and $allowNull == true) {
		return true;
	}
	elseif ($input == "") {
		return false;
	}
	elseif (preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $input)) {
		return true;
	}
	else {
		return false;
	}
}

function isURL($input) {
	if ($input == "") {
		return false;
	}
	elseif (preg_match("/^http\:\/\/[a-zA-Z0-9\-\_\.\?\=\&\%\/]+$/i", $input)) {
		return true;
	}
	else {
		return false;
	}
}

?>
