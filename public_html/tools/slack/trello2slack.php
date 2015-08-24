<?php
// a lookup array of trello and slack usernames
require_once('usernames.php');

/*  keys file, not in git
	Defines:

	$trelloAppKey
	$trelloToken
*/
require_once('/home/nottinghack/www_secure/slack_keys.php');

// timezone
date_default_timezone_set('Europe/London');

// trello details
$trelloBoardId = '54745ca526448f2011c10a53';

// Date period to look up to for due dates (in days)
$dueRange = 7;

// Lists to ignore
$ignoreLists = array("Done", "To Discuss", "Templates");

// get all the members on the board

$url = 'https://api.trello.com/1/boards/' . $trelloBoardId . '/members?key=' . $trelloAppKey . '&token=' . $trelloToken;
$trelloMembers = json_decode(file_get_contents($url), true);

foreach ($trelloMembers as $member) {
	$i = searchArray($member['username'], 'trello', $usernames);
	if (!is_null($i)) {
		$usernames[$i]['trelloId'] = $member['id'];
	}
}

// get all cards on the board
$url = 'https://api.trello.com/1/boards/' . $trelloBoardId . '/lists?cards=open&card_fields=name,idList,url,due,idMembers&key=' . $trelloAppKey . '&token=' . $trelloToken;
$trelloLists = json_decode(file_get_contents($url), true);

/*  Let's go through the cards
	- send a slack message for cards with due dates that are soon, mentioning the user if assigned
	- send a slack message for assigned cards without dates
	- count up the unassigned cards without dates on each list
	- Finally send messages (one per list) of unassigned card count
*/

foreach ($trelloLists as $list) {
	if (in_array($list['name'], $ignoreLists)) {
		continue;
	}

	$unassigned = 0;
	foreach ($list['cards'] as $card) {
		$slackUsers = array();
		if (count($card['idMembers']) > 0) {
			foreach ($card['idMembers'] as $trelloId) {
				$i = searchArray($trelloId, 'trelloId', $usernames);
				$slackUsers[] = $usernames[$i]['slack'];
			}
		}

		if (!is_null($card['due'])) {
			$days = daysToDue($card['due']);

			if ($days <= $dueRange) {
				if (count($slackUsers) > 0) {
					$message = 'Hey ' . implodeSlackUsers($slackUsers) . ' your task "' . $card['name'] . '" ' . dueText($days);
				}
				else {
					$message = '@channel: The task "' . $card['name'] . '" ' . dueText($days) . ' and no one is assigned!';
				}
			}
		}
		else {
			if (count($slackUsers) > 0) {
				$message = 'Hey ' . implodeSlackUsers($slackUsers) . ' your task "' . $card['name'] . " doesn't have a due date";
			}
			else {
				$unassigned++;
			}
		}
		if (isset($message)) {
			sendSlack($message);
			unset($message);
		}
	}

	if ($unassigned > 0) {
		$message = '@channel: There ';
		if ($unassigned == 1) {
			$message .= "is 1 card that isn't";
		}
		else {
			$message .= 'are ' . $unassigned . " cards that aren't";
		}
		$message .= ' assigned to anyone in the "' . $list['name'] . '" list';

		sendSlack($message);
		unset($message);
	}
}








function searchArray($searchValue, $searchKey, $haystack) {
	foreach ($haystack as $k => $val) {
		if ($val[$searchKey] == $searchValue) {
			return $k;
		}
	}
	return null;
}

function sendSlack($message) {
	// testing
	return;


	if ($message == "") {
		return;
	}
	global $webhook;

	$payload = json_encode(array("text" => $message));

	$ch = curl_init($webhook);

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	curl_close($ch);
}

function implodeSlackUsers($slackUsers) {
	$userString = "";
	if (count($slackUsers) > 0) {
		for ($i = 0; $i < count($slackUsers); $i++) {
			$userString .= "@" . $slackUsers[$i];
			if ($i < (count($slackUsers) - 1)) {
				$userString .= ", ";
			}
		}
	}
	return $userString;
}

function daysToDue($date) {
	var_dump($date);
	list($date, $time) = explode("T", $date);
	$date = DateTime::createFromFormat('Y-m-d', $date);

	$today = new DateTime();

	$interval = $today->diff($date);

	$days = intval($interval->format('%a'));
	if ($date < $today) {
		$days = $days * -1;
	}
	var_dump($days);
	return $days;
}

function dueText($days) {
	if ($days == 0) {
		$string = "is due today! :alarm_clock:";
	}
	elseif ($days == 1) {
		$string = "is due tomorrow";
	}
	elseif ($days > 1) {
		$string = "is due in " . $days . " days";
	}
	elseif ($days == -1) {
		$string = "was due yesterday!";
	}
	elseif ($days < -1) {
		$string = "was due " . $days . " days ago";
	}

	return $string;
}

?>