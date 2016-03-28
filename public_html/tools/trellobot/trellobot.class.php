<?php

require_once(__DIR__ . '/users.class.php');
require_once(__DIR__ . '/preferences.class.php');
require_once(__DIR__ . '/trello.class.php');

Class TrelloBot
{

    private $debug;

    private $slackName = '';
    private $slackId = '';

    private $slackRTC;

    private $trello;

    private $users;

    private $preferences;

    private $connected;

    private $timer;

    public function __construct($name, &$loop, $slackToken, $trelloAppKey, $trelloToken, $trelloBoard)
    {
        global $debug;

        $this->debug = $debug;
        $this->slackName = $name;

        $this->preferences = New Preferences;

        $this->users = new Users;

        // Add the Slack real time client into the loop
        $this->slackRTC = new Slack\RealTimeClient($loop);
        $this->slackRTC->setToken($slackToken);

        // Add the Trello API client in the loop
        $this->trello = new Trello($loop);
        $this->trello->setCredentials($trelloAppKey, $trelloToken);
        $this->trello->setBoard($trelloBoard);

        // setup triggers
        $this->slackRTC->on('message', array($this, 'processMessage'));

        // Connect and then setup
        $this->connect();

        $this->startTimer($loop);
    }

    private function connect()
    {
        $this->slackRTC->connect()->then(function () {
            $this->echoMsg("Connected!");

            // Process list of users
            $this->slackRTC->getUsers()->done(function($slackData) {
                $this->users->setSlackUsers($slackData);
                $this->trello->getAllUsers()->done(function($trelloData) {
                    $this->users->setTrelloUsers($trelloData);
                });
            });

            // What DM channels are available?
            $this->slackRTC->getDMs()->done(function($dms) {
                $this->users->setDMs($dms);

                $this->slackId = $this->users->getBySlackUsername($this->slackName)->getSlackId();

                $this->echoMsg("Setup Complete");
            });

            $this->connected = true;
        });
    }

    private function startTimer(&$loop)
    {
        $this->timer = $loop->addPeriodicTimer(30, array($this, 'doPeriodicActions'));
    }

    public function processMessage ($data)
    {
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

    public function doPeriodicActions()
    {
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

    private function sendMsg($msg, $channelId)
    {
        if ($channelId[0] == 'D') {
            $this->slackRTC->getDMById($channelId)->then(function (\Slack\DirectMessageChannel $channel) use ($msg) {
                $this->slackRTC->send($msg, $channel);
            });
        }
        elseif ($channelId[0] == 'C') {
             $this->slackRTC->getChannelById($channelId)->then(function (\Slack\Channel $channel) use ($msg) {
                $this->slackRTC->send($msg, $channel);
            });
        }
    }

    private function echoMsg($msg)
    {
        if ($this->debug) {
            echo($msg . "\n");
        }
    }

    private function fromMe($data)
    {
        if ($data['user'] == $this->slackId) {
            return true;
        }

        return false;
    }

    private function toMe($data)
    {
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

    private function stripUsername($message)
    {
        $username = '<@' . $this->slackId . '>';

        if (strpos($message, $username) !== false) {
            $message = ltrim(substr($message, strpos($message, $username) + strlen($username)), ": \t\n");
        }

        return $message;    
    }

    private function setUserTimePref($data, $message)
    {
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

    private function getUsersToNotify()
    {
        $this->echoMsg("Notifying GeeksAreForLife");
        return [$this->users->getBySlackUsername("geeksareforlife")];
    }

    private function getAllCards()
    {

    }

    private function getCardsForUser($cards, $user)
    {
        return [1,2];
    }

    private function notifyUser($user, $cards)
    {
        $channelId = $user->getDM();
        $this->slackRTC->getDMById($channelId)->then(function (\Slack\DirectMessageChannel $channel) {
            $this->slackRTC->send("Daily Notification", $channel);
        });
    }
}