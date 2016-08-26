<?php

require_once(__DIR__ . '/users.class.php');
require_once(__DIR__ . '/preferences.class.php');
require_once(__DIR__ . '/tasks.class.php');
require_once(__DIR__ . '/trello.class.php');

require_once(__DIR__ . '/meeting.class.php');

use Carbon\Carbon;

/**
 * The whole shebang
 * 
 * This class is far too large, but I'm not sure how to break it up
 * For now I will organise the functions into general groupings, maybe that
 * will suggest additional objects
 * 
 */
Class TrelloBot
{

    private $debug;

    private $slackName = '';
    private $slackId = '';

    private $slackRTC;

    private $trello;

    private $users;

    private $preferences;

    private $tasks;

    private $connected;

    private $timer;

    private $meeting = false;


    //  SSSSS  EEEEEEE TTTTTTT UU   UU PPPPPP
    // SS      EE        TTT   UU   UU PP   PP
    //  SSSSS  EEEEE     TTT   UU   UU PPPPPP
    //      SS EE        TTT   UU   UU PP
    //  SSSSS  EEEEEEE   TTT    UUUUU  PP

    /**
     * Builds up all the required objects and connects to Slack's Real Time API
     * 
     * Also adds in the trigger to process incoming messages
     * 
     * @param string $name Slack username of bot
     * @param object &$loop The ReactPHP loop
     * @param string $slackToken Slack token for connection
     * @param string $trelloAppKey Slack App Key for connection
     * @param string $trelloToken Trello Token for API
     * @param string $trelloBoard Board ID we are interested in
     */
    public function __construct($name, &$loop, $slackToken, $trelloAppKey, $trelloToken, $trelloBoard)
    {
        global $debug;

        $this->debug = $debug;
        $this->slackName = $name;

        $this->preferences = New Preferences;

        $this->tasks = New Tasks;

        $this->users = new Users;

        // Add the Slack real time client into the loop
        $this->slackRTC = new Slack\RealTimeClient($loop);
        $this->slackRTC->setToken($slackToken);

        // Add the Trello API client in the loop
        $this->trello = new Trello($loop);
        $this->trello->setCredentials($trelloAppKey, $trelloToken);
        $this->trello->setBoard($trelloBoard);

        $this->onHoldListName = 'On Hold / Waiting';

        // setup triggers
        $this->slackRTC->on('message', array($this, 'processMessage'));

        // Connect and then setup
        $this->connect();

        $this->startTimer($loop);
    }

    /**
     * Connects to the Slack Real Time API
     * 
     * Once connected it populates the list of users and gets their DM channelIDs
     */
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

    /**
     * Puts a periodic timer into the loop to allow us to do automatic actions
     * 
     * @param object &$loop THe ReactPHP loop
     */
    private function startTimer(&$loop)
    {
        $this->timer = $loop->addPeriodicTimer(30, array($this, 'doPeriodicActions'));
    }

    /**
     * Called every 30 seconds. Attempts to complete our automatic items.
     */
    public function doPeriodicActions()
    {
        $this->notifyUsers();
        $this->notifyGeneral();
    }

    // IIIII NN   NN IIIII TTTTTTT IIIII   AAA   LL          MM    MM  SSSSS    GGGG
    //  III  NNN  NN  III    TTT    III   AAAAA  LL          MMM  MMM SS       GG  GG
    //  III  NN N NN  III    TTT    III  AA   AA LL          MM MM MM  SSSSS  GG
    //  III  NN  NNN  III    TTT    III  AAAAAAA LL          MM    MM      SS GG   GG
    // IIIII NN   NN IIIII   TTT   IIIII AA   AA LLLLLLL     MM    MM  SSSSS   GGGGGG


    /**
     * Processes all incoming messages from Slack
     * 
     * @param array $data Data from Slack
     */
    public function processMessage ($data)
    {
        // is this message intended for me?
        if ($this->fromMe($data)) {
            return;
        }
        if ($this->toMe($data)) {
            $message = $this->stripUsername($data['text']);
            $this->echoMsg("Message: " . $message);

            if (strpos($message, ' ') !== false) {
                $action = strtolower(substr($message, 0, strpos($message, ' ')));
            } else {
                $action = strtolower(trim($message));
            }

            $this->echoMsg("Action: " . $action);

            // is this a taskID?
            $trelloId = $this->tasks->getTrelloId($action);
            if ($trelloId !== false) {
                $this->processTaskId($data, $trelloId, $message);
                return;
            }

            // commands that can happen anywhere
            $commands = array('help', 'time', 'frequency', 'action', 'start');
            if (in_array($action, $commands)) {
                switch ($action) {
                    case 'help':
                        $this->processHelp($data, $message);
                        break;
                    case 'time':
                        $this->setUserTimePref($data, $message);
                        break;
                    case 'frequency':
                        $this->setUserFreqPref($data, $message);
                        break;
                    case 'action':
                        $this->processAction($data, $message);
                        break;
                    case 'start':
                        $this->meeting = new Meeting($data['channel'], $this);
                        break;
                    default:
                        $this->sendMsg($this->getCannedMessage('general-error'), $data['channel']);
                }
            } elseif ($this->meeting !== false && $meeting->getChannel() == $data['channel']) {
                // finally, meetings
            }
        }
    }

    /**
     * Send a message to a specific channel ID or channel name
     * 
     * @param string $msg 
     * @param string|null $channelId Slack ID of channel to send to. Can be a DM
     * @param string $channelName Text name of channel to send to
     */
    private function sendMsg($msg, $channelId = null, $channelName = null)
    {
        if (is_null($channelId)) {
            $this->slackRTC->getChannelByName($channelName)->then(function (\Slack\Channel $channel) use ($msg) {
                $this->slackRTC->send($msg, $channel);
            });
        } else {
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
    }

    /**
     * Debug - send a message to StdOut
     * 
     * @param string $msg Message to send
     */
    private function echoMsg($msg)
    {
        if ($this->debug) {
            echo($msg . "\n");
        }
    }

    /**
     * Checks to see if a message is from us
     * 
     * @param string $data Message data
     * @return boolean
     */
    private function fromMe($data)
    {
        if ($data['user'] == $this->slackId) {
            return true;
        }

        return false;
    }

    /**
     * Is the message to us, either in a DM, or mentioning us?
     * 
     * @param array $data Message data
     * @return boolean
     */
    private function toMe($data)
    {
        global $users, $botSlackId;

        if ($data['channel'][0] == 'D') {
            return true;
        }
        if (strpos($data['text'], $this->slackId) !== false) {
            return true;
        }
        return false;
    }

    /**
     * Strip our username, and anything before from a message
     * 
     * @param string $message Message text
     * @return string Message text without username
     */
    private function stripUsername($message)
    {
        $username = '<@' . $this->slackId . '>';

        if (strpos($message, $username) !== false) {
            $message = ltrim(substr($message, strpos($message, $username) + strlen($username)), ": \t\n");
        }

        return $message;    
    }


    // IIIII NN   NN  CCCCC   OOOOO  MM    MM IIIII NN   NN   GGGG
    //  III  NNN  NN CC    C OO   OO MMM  MMM  III  NNN  NN  GG  GG
    //  III  NN N NN CC      OO   OO MM MM MM  III  NN N NN GG
    //  III  NN  NNN CC    C OO   OO MM    MM  III  NN  NNN GG   GG
    // IIIII NN   NN  CCCCC   OOOO0  MM    MM IIIII NN   NN  GGGGGG


    /**
     * Processes a TIME message and sets the user's time if it is valid
     * 
     * @param array $data Message data
     * @param string $message Message text
     */
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
        } else {
            $this->sendMsg("Sorry, I didn't recognise that time", $data['channel']);
        }
    }

    /**
     * Processes a FREQUENCY message and sets the user's frequency if it is valid
     * 
     * @param array $data Message data
     * @param string $message Message text
     */
    private function setUserFreqPref($data, $message)
    {
        if (preg_match('/(daily|weekly|every other day|weekend)/', strtolower($message), $matches) === 1) {
            $frequency = $matches[1];
            if ($this->preferences->saveFrequencyForUser($frequency, $data['user'])) {
                $this->sendMsg("OK, your notification frequency is set to $frequency.", $data['channel']);
            } else {
                $this->sendMsg("Sorry, there was an error trying to set your notification frequency.", $data['channel']);
            }
        } else {
            $this->sendMsg("Sorry, that frequency is not supported", $data['channel']);
        }
    }

    /**
     * Finds the appropriate help message and sends it back
     * 
     * @param array $data Message data
     * @param string $message Message text
     */
    private function processHelp($data, $message)
    {
        if (preg_match('/^\s*help (.*)$/i', $message, $matches) === 1) {
            $helpType = 'help-' . trim(strtolower($matches[1]));
            if ($msg = $this->getCannedMessage($helpType)) {
                $this->sendMsg($msg, $data['channel']);
            } else {
                $this->sendMsg($this->getCannedMessage('error-help'), $data['channel']);
            }
        } else {
            $this->sendMsg($this->getCannedMessage('error-help'), $data['channel']);
        }
    }

    private function processTaskId($data, $trelloId, $message)
    {
        $this->sendMsg("Sorry, I can't deal with tasks yet!", $data['channel']);
    }

    private function processAction($data, $message)
    {
        $this->sendMsg("Sorry, I can't deal with actions yet!", $data['channel']);
    }

    //  OOOOO  UU   UU TTTTTTT   GGGG   OOOOO  IIIII NN   NN   GGGG
    // OO   OO UU   UU   TTT    GG  GG OO   OO  III  NNN  NN  GG  GG
    // OO   OO UU   UU   TTT   GG      OO   OO  III  NN N NN GG
    // OO   OO UU   UU   TTT   GG   GG OO   OO  III  NN  NNN GG   GG
    //  OOOO0   UUUUU    TTT    GGGGGG  OOOO0  IIIII NN   NN  GGGGGG


    private function notifyGeneral()
    {
        $time =  $this->preferences->getTime();
        $lastNotified = Carbon::createFromTimestamp($this->preferences->getLastNotified());
        if ($lastNotified->isToday()) {
            return false;
        }
        $now = Carbon::now('Europe/London');
        if ($this->compareTime($time, $now)) {
            $this->trello->getAllCards()->done(function($trelloData) {
                $cards = $trelloData;
                $this->echoMsg("Got Cards (Notify General)");
                $this->trello->getAllLists()->done(function($trelloData) use ($cards) {
                    $this->echoMsg("Got Lists (Notify General)");
                    $orderedCards = $this->orderCards($cards, $trelloData);

                    $lists = $this->preferences->getLists();

                    $msg = '*DAILY TASK NOTIFICATION!*' . "\n\n";

                    foreach ($orderedCards as $trelloId => $cards) {
                        $counts = $this->getCounts($cards, $lists);

                        if ($trelloId == 'unassigned') {
                            if ($counts['total'] > 0) {
                                $msg .= 'There are ' . $counts['total'] .' unassigned tasks';

                                $msg .= '.' . "\n";
                            }
                        } else {
                            $user = $this->users->getByTrelloId($trelloId);
                            if ($user) {
                                $msg .= '@' . $user->getSlackUsername();
                            } else {
                                $msg .= 'An unknown user';
                            }

                            $msg .= ' has ' . $counts['total'] . ' outstanding tasks';
                            if ($counts['overdue'] == 1) {
                                $msg .= ', 1 of which is overdue';
                            } elseif ($counts['overdue'] > 1) {
                                $msg .= ', ' . $counts['overdue'] . ' of which are overdue';
                            }

                            if ($counts['onhold'] == 1) {
                                $msg .= ', and 1 task on hold';
                            } elseif ($counts['onhold'] > 1) {
                                $msg .= ', and ' . $counts['onhold'] . ' tasks on hold';
                            }
                            
                            $msg .= '.' . "\n";
                        }

                    }

                    $this->echoMsg($msg);

                    $this->sendMsg($msg, null, 'general');

                    $this->preferences->saveLastNotified(time());
                });
            });
        }
    }

    private function notifyUsers()
    {
        $users = $this->getUsersToNotify();
        if (count($users) > 0) {
            $this->trello->getAllCards()->done(function($trelloData) use ($users) {
                $cards = $trelloData;
                $this->echoMsg("Got Cards (Notify Users)");
                $this->trello->getAllLists()->done(function($trelloData) use ($cards, $users) {
                    $this->echoMsg("Got Lists (Notify Users)");
                    $cards = $this->orderCards($cards, $trelloData);
                    foreach ($users as $user) {
                        if (count($cards[$user->getTrelloId()]) > 0) {
                            $msg = 'Hey ' . $user->getName() . ', you have the following tasks on your list:' . "\n\n";
                            
                            $notifyLists = $this->preferences->getListsForUser($user->getSlackId());
                            
                            foreach ($notifyLists as $listName) {
                                if (isset($cards[$user->getTrelloId()][$listName])) {
                                    if ($listName == $this->onHoldListName) {
                                        $noun = 'tasks';
                                        if (count($cards[$user->getTrelloId()][$listName] == 1)) {
                                            $noun = 'task';
                                        }
                                        $msg .= 'Additionally, you have ' . count($cards[$user->getTrelloId()][$listName]) . ' ' . $noun . ' on hold:' . "\n";
                                        foreach ($cards[$user->getTrelloId()][$listName] as $card) {
                                            $msg .= $this->formatOnHoldCardMessage($card);
                                        }
                                    } else {
                                        foreach ($cards[$user->getTrelloId()][$listName] as $card) {
                                            $msg .= $this->formatNormalCardMessage($card);
                                        }
                                    }
                                }
                            }

                            $msg .= "\n" . 'I can help you manage these tasks. Type *help tasks* for more details' . "\n";
                            $msg .= 'To change the time of these notifications, type *time 13:00*, or type *help user* for more details' . "\n";

                            $this->echoMsg($msg);
                            $this->sendMsg($msg, $user->getDM());
                            
                            $this->userNotified($user);
                        }
                    }
                });
            });
        }
    }

    private function getUsersToNotify()
    {
        $users = $this->users->getAllTrelloUsers();

        $notifyUsers = [];

        foreach ($users as $user) {
            if ($this->checkNotifyUser($user)) {
                $notifyUsers[] = $user;
            }
        }

        return $notifyUsers;
    }

    private function checkNotifyUser($user)
    {
        $time =  $this->preferences->getTimeForUser($user->getSlackId());
        $timezone = $this->preferences->getTimezoneForUser($user->getSlackId());
        $frequency = $this->preferences->getFrequencyForUser($user->getSlackId());
        $lastNotified = Carbon::createFromTimestamp($this->preferences->getLastNotifiedForUser($user->getSlackId()), $timezone);

        $now = Carbon::now($timezone);

        $lastNotifiedDays = $lastNotified->diffInDays($now, false);

        if ($lastNotifiedDays == 0) {
            // last notified today, not again
            return false;
        }

        // check this day is ok according to Frequency.
        // Not notified today (as above), so daily will be ok
        switch ($frequency) {
            case 'weekly':
                if ($lastNotifiedDays < 7) {
                    return false;
                }
                break;
            case 'every other day':
                if ($lastNotifiedDays < 2) {
                    return false;
                }
                break;
            case 'weekend':
                if ($now->dayOfWeek != Carbon::SATURDAY && $now->dayOfWeek != Carbon::SUNDAY) {
                    return false;
                }
                break;
            case 'daily':
                break;
        }

        // Just need to check the time now
        if ($this->compareTime($time, $now)) {
            return true;
        } else {
            return false;
        }
    }

    private function compareTime($textTime, $carbonTime) {
        if (preg_match('/^(\d{1,2})\:(\d{2})$/', $textTime, $matches) === 1) {
            if ($carbonTime->hour == $matches[1] && $carbonTime->minute == $matches[2]) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function userNotified($user)
    {
        $this->preferences->saveLastNotifiedForUser(time(), $user->getSlackId());
    }

    private function orderCards($unsortedCards, $lists)
    {
        $listLookup = [];
        $cards = [
            'unassigned' => [],
        ];

        foreach ($lists as $list) {
            $listLookup[$list['id']] = $list['name'];
        }
        foreach ($unsortedCards as $card) {
            if (count($card['idMembers']) > 0) {
                foreach ($card['idMembers'] as $userTrelloId) {
                    if (!isset($cards[$userTrelloId])) {
                        $cards[$userTrelloId] = [];
                    }
                    if (!isset($cards[$userTrelloId][$listLookup[$card['idList']]])) {
                        $cards[$userTrelloId][$listLookup[$card['idList']]] = [];
                    }
                    
                    $cards[$userTrelloId][$listLookup[$card['idList']]][] = $this->extractCardDetails($card, $listLookup[$card['idList']], $userTrelloId);
                }
            } else {
                // unassigned card
                if (!isset($cards['unassigned'][$listLookup[$card['idList']]])) {
                    $cards['unassigned'][$listLookup[$card['idList']]] = [];
                }
                $cards['unassigned'][$listLookup[$card['idList']]][] = $this->extractCardDetails($card, $listLookup[$card['idList']]);
            }
        }

        // sort them

        // return them
        return $cards;
    }

    private function getCounts($cards, $lists)
    {
        $counts = [
            'total'     => 0,
            'overdue'   => 0,
            'onhold'    => 0,
        ];

        $now = Carbon::now();

        foreach ($cards as $listName => $listCards) {
            if (in_array($listName, $lists)) {
                if ($listName == $this->onHoldListName) {
                    $counts['onhold'] += count($listCards);
                } else {
                    $counts['total'] += count($listCards);
                    foreach ($listCards as $card) {
                        if ($card['due'] != '' && $card['due']->diffInDays($now, false) > 0) {
                            $counts['overdue'] += 1;
                        }
                    }
                }
            }
        }

        return $counts;
    }

    private function extractCardDetails($card, $listName, $userTrelloId = '')
    {
        $newCard = [
            'trello_id'     => $card['id'],
            'task_id'       => $this->tasks->getTaskId($card['id']),
            'title'         => $card['name'],
            'other_users'   => [],
            'due'           => '',
            'list_name'      => $listName,
        ];

        if ($userTrelloId != '' && $this->users->getByTrelloId($userTrelloId) !== false) {
            $user = $this->users->getByTrelloId($userTrelloId);
            $newCard['other_users'] = $this->convertUserList($card['idMembers'], $userTrelloId);
        }

        if (!is_null($card['due'])) {
            $newCard['due'] = Carbon::parse($card['due']);
            if ($userTrelloId != '' && isset($user)) {
                $newCard['due']->timezone = $this->preferences->getTimezoneForUser($user->getSlackId());
            }
        }

        return $newCard;
    }

    private function convertUserList($users, $exclude = '')
    {
        $userList = [];

        foreach ($users as $user) {
            if ($user == $exclude) {
                continue;
            } else {
                $userList[] = $this->users->getByTrelloId($user);
            }
        }

        return $userList;
    }

    private function formatNormalCardMessage($card)
    {
        $msg = '*' . $card['title'] . '*';
        if ($card['due'] == '') {
            $msg .= '. This one doesn’t have a due date, does it need one?';
        } else {
            $diff = $card['due']->diffInDays(null, false);
            if ($diff > 0) {
                $msg .= ' was due ' . $diff . ' days ago (' . $card['due']->toFormattedDateString() . ').';
            } elseif ($diff == 0) {
                $msg .= ', due *today*.';
            } elseif ($diff > -7) {
                $msg .= ', due on ' . $card['due']->format('l') . ' (' . $card['due']->toFormattedDateString() . ').';
            } else {
                $msg .= ', due on ' . $card['due']->toFormattedDateString() . '.';
            }
        }

        if (count($card['other_users']) > 0) {
            $other_users = [];
            foreach ($card['other_users'] as $user) {
                if (!$user) {
                    // this was a user that is on trello, but no longer on slack
                    $other_users[] = "unknown";
                } else {
                    $other_users[] = '@' . $user->getSlackUsername();
                }
            }
            $join = count($other_users) > 1 ? 'are' : 'is';
            $msg .= ' ' . $this->getEnglishList($other_users) . ' ' . $join . ' helping with this.';
        }

        $msg .= '  _' . $card['task_id'] . '_';

        $msg .= ' - ' . $card['list_name'];

        $msg .= "\n";

        return $msg;
    }

    private function formatOnHoldCardMessage($card)
    {
        $msg = $card['title'] . '.  _' . $card['task_id'] . '_';

        $msg .= "\n";

        return $msg;
    }

    private function getCannedMessage($msgName)
    {
        $path = __DIR__ . '/messages/' . $msgName . '.msg';

        if (file_exists($path)) {
            return file_get_contents($path);
        } else {
            return false;
        }
    }

    private function getEnglishList($list) {
        if (count($list) == 0) {
            return '';
        } elseif (count($list) == 1) {
            return $list[0];
        } elseif (count($list) == 2) {
            return $list[0] . ' and ' . $list[1];
        } else {
            $last = array_pop($list);

            return implode(', ', $list) . ', and ' . $last;
        }
    }
}