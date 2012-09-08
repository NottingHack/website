<?php
require('smarty/Smarty.class.php');

$oSmarty = new Smarty;

$oSmarty->template_dir = COMMON_DIR . 'templates/';
$oSmarty->compile_dir  = COMMON_DIR . 'templates_c/';
$oSmarty->config_dir   = COMMON_DIR . 'configs/';
$oSmarty->cache_dir    = COMMON_DIR . 'cache/';

?>