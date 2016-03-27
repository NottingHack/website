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

    private $connected;

    private $timer;

    public function __construct($name, &$loop, $slackToken) {
        global $debug;

        $this->debug = $debug;
        $this->slackName = $name;

        $this->preferences = New Preferences;

        $this->users = new Users;

        // Add the client into the loop
        $this->client = new Slack\RealTimeClient($loop);
        $this->client->setToken($slackToken);

        // setup triggers
        $this->client->on('message', array($this, 'processMessage'));

        // Connect and then setup
        $this->connect();

        $this->startTimer();
    }

    private function connect() {
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

            $this->connected = true;
        });
    }

    private function startTimer() {
        $this->timer = $loop->addPeriodicTimer(30, array($this, 'doPeriodicActions'));
    }

    public function processMessage ($data) {
        // is this message intended for me?
        if ($this->fromMe($data)) {
            return;
        }
        if ($this->toMe($data)) {
            $message = $this->stripUsername($data['text']);

            $action = strtolower(substr($message, 0, strpos($message, ' ')));

            switch ($action) {
                case "time":
                    $this->setUserTimePref($data, $message);
                    break;
                case "frequency":
                    $this->setUserFreqPref($data, $message);
                    break;
                case "action":
                    $this->processAction($data, $message);
                    break;
                default:
                    // assume this is a taskID
                    $this->processTaskId($data, $action, $message);
            }
        }
    }

    public function doPeriodicActions() {
        $users = $this->getUsersToNotify();
        // Get Trello cards
        $cards = $this->getAllCards();
        foreach ($users as $user) {
            $userCards = $this->getCardsForUser($cards, $user);
            if (count($userCards) > 0) {
                $this->notifyUser($user, $userCards);
            }
        }
    }

    private function sendMsg($msg, $channelId) {
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
            return true;
        }
        var_dump($data);
        if (strpos($data['text'], $this->slackId) !== false) {
            return true;
        }
        return false;
    }

    private function stripUsername($message) {
        $username = '<@' . $this->slackId . '>';

        if (strpos($message, $username) !== false) {
            $message = ltrim(substr($message, strpos($message, $username) + strlen($username)), ": \t\n");
        }

        return $message;    
    }

    private function setUserTimePref($data, $message) {
        if (preg_match('/(\d{2}:\d{2})/', $message, $matches) === 1) {
            $time = $matches[1];
            list($hours, $minutes) = explode(":", $time);
            if (intval($hours) >= 0 && intval($hours) < 24 && intval($minutes) >= 0 && intval($minutes) < 60) {
                if ($this->preferences->saveTimeForUser($time, $data['user'])) {
                    $this->sendMsg("OK, your notification time is set to $time.", $data['channel']);
                } else {
                    $this->sendMsg("Sorry, there was an error trying to set your notification time.", $data['channel']);
                }
            }
        }
    }

    private function getUsersToNotify() {

    }

    private function getAllCards() {

    }

    private function getCardsForUser($cards, $user) {

    }

    private function notifyUser($user, $cards) {
        
    }
}