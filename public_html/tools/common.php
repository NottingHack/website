<?php
/*
 Nottinghack Tools common fil
 
*/

define('ROOT_DIR', '/home/nottinghack/public_html/tools/');
define('SECURE_DIR', '/home/nottinghack/www_secure/');
define('COMMON_DIR', ROOT_DIR . '_common/');
define('PHP_DIR', COMMON_DIR . 'php/');

define('URL', 'http://www.nottinghack.org.uk');

define('ROOT_URL', '/tools/');
define('COMMON_URL', ROOT_URL . '_common/');
define('CSS_URL', COMMON_URL . 'css/');
define('IMG_URL', COMMON_URL . 'images/');
define('JS_URL', COMMON_URL . 'js/');

require_once(PHP_DIR . 'smarty.php');
require_once(PHP_DIR . 'security.php');
require_once(PHP_DIR . 'functions.php');


$oSmarty->assign('css_url', CSS_URL);
$oSmarty->assign('img_url', IMG_URL);
$oSmarty->assign('js_url', JS_URL);

?>
