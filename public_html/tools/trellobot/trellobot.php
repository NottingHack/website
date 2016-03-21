<?php
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
require_once(__DIR__ . '/../../../www_secure/trellobot_keys.php');

$loop = React\EventLoop\Factory::create();

$trellobot = new TrelloBot('trellobot', $loop, $slackToken);

$loop->run();
