<?php
// just in case
date_default_timezone_set('UTC');

$options = getopt('d', array("debug"));

$debug = false;
if (isset($options['d']) or isset($options['debug'])) {
    $debug = true;

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/trellobot.class.php');
// Defines: 
// $botName
// $slackToken
// $trelloAppKey
// $trelloToken
//$ trelloBoard
require_once(__DIR__ . '/../../../www_secure/trellobot_conf.php');

$loop = React\EventLoop\Factory::create();

$trellobot = new TrelloBot($botName, $loop, $slackToken, $trelloAppKey, $trelloToken, $trelloBoard);

$loop->run();
