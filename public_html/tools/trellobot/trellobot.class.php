<?php

require_once(__DIR__ . '/users.class.php');
require_once(__DIR__ . '/preferences.class.php');

Class TrelloBot
{

    private $debug;

    private $slackName = '';
    private $slackId = '';

    private $client;

    private $users;

    private $preferences;

    public function __construct($name, &$loop, $slackToken) {
        global $debug;

        $this->debug = $debug;

        $this->slackName = $name;

        $this->preferences = New Preferences;

        $this->users = new Users;

        // Add the client into the loop
        $this->client = new Slack\RealTimeClient($loop);
        $this->client->setToken($slackToken);

        // process incoming messages
        $this->client->on('message', array($this, 'processMessage'));

        // Connect and then setup
        $this->client->connect()->then(function () {
            $this->echoMsg("Connected!\n");

            // Process list of users
            $this->client->getUsers()->done(function($slackData) {
                $this->users->setSlackUsers($slackData);
            });

            // What DM channels are available?
            $this->client->getDMs()->done(function($dms) {
                $this->users->setDMs($dms);

                $this->slackId = $this->users->getBySlackUsername($this->slackName)->getSlackId();

                $this->echoMsg("Setup Complete\n");
            });
        });
    }

    public function processMessage ($data) {
        // is this message intended for me?
        if ($this->fromMe($data)) {
            return;
        }
        if ($this->toMe($data)) {
            $this->sendMsg("Is it me you're looking for?", $data['channel']);
        }
    }

    function sendMsg($msg, $channelId) {
        if ($channelId[0] == 'D') {
            $this->client->getDMById($channelId)->then(function (\Slack\DirectMessageChannel $channel) use ($msg) {
                $this->client->send($msg, $channel);
            });
        }
        elseif ($channelId[0] == 'C') {
             $this->client->getChannelById($channelId)->then(function (\Slack\Channel $channel) use ($msg) {
                $this->client->send($msg, $channel);
            });
        }
    }

    private function echoMsg($msg) {
        if ($this->debug) {
            echo($msg);
        }
    }

    private function fromMe($data) {
        if ($data['user'] == $this->slackId) {
            return true;
        }

        return false;
    }

    private function toMe($data) {
        global $users, $botSlackId;

        if ($data['channel'][0] == 'D') {
            $this->echoMsg($data['text'] . "\n");
            return true;
        }
        var_dump($data);
        if (strpos($data['text'], $this->slackId) !== false) {
            return true;
        }
        return false;
    }

}