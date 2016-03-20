<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once('../../../www_secure/trellobot_keys.php');
require_once('users.class.php');
require_once('preferences.class.php');

$botSlackName = 'trellobot';
$botSlackId = "";

$options = getopt('d', array("debug"));

$debug = false;
if (isset($options['d']) or isset($options['debug'])) {
    $debug = true;
}

$users = new Users;

$loop = React\EventLoop\Factory::create();

$client = new Slack\RealTimeClient($loop);
$client->setToken($slackToken);

// process incoming messages
$client->on('message', 'processMessage');

$client->connect()->then(function () use ($client) {
    echoMsg("Connected!\n");
    $client->getUsers()->done(function($slackData) {
        global $users;
        $users->setSlackUsers($slackData);
    });
    $client->getDMs()->done(function($dms) {
        global $users, $botSlackName, $botSlackId;
        $users->setDMs($dms);

        $botSlackId = $users->getBySlackUsername($botSlackName)->getSlackId();

        echoMsg("Setup Complete\n");
    });
});

/*$setupTimer = $loop->addPeriodicTimer(5, function () use ($client) {
    global $setupTimer;
    echo("timeout\n");

    $setupTimer->cancel();
});*/

$loop->run();

function echoMsg($msg) {
    global $debug;

    if ($debug) {
        echo($msg);
    }
}


function processMessage ($data) {
    global $client;

    // is this message intended for me?
    if (fromMe($data)) {
        return;
    }
    if (toMe($data)) {
        sendMsg("Is it me you're looking for?", $data['channel']);
    }
}

function fromMe($data) {
    global $users, $botSlackId;

    if ($data['user'] == $botSlackId) {
        return true;
    }

    return false;
}

function toMe($data) {
    global $users, $botSlackId;

    if ($data['channel'][0] == 'D') {
        echoMsg($data['text'] . "\n");
        return true;
    }
    var_dump($data);
    if (strpos($data['text'], $botSlackId) !== false) {
        return true;
    }
    return false;
}

function sendMsg($msg, $channelId) {
    global $client;

    if ($channelId[0] == 'D') {
        $client->getDMById($channelId)->then(function (\Slack\DirectMessageChannel $channel) use ($client, $msg) {
            $client->send($msg, $channel);
        });
    }
    elseif ($channelId[0] == 'C') {
         $client->getChannelById($channelId)->then(function (\Slack\Channel $channel) use ($client, $msg) {
            $client->send($msg, $channel);
        });
    }
}

?>