<?php
// a lookup array of trello and slack usernames
require_once('usernames.php');

/*  keys file, not in git
	Defines:

	$trelloAppKey
	$trelloToken
*/
require_once('keys.php');

// trello details
$trelloBoardId = '54745ca526448f2011c10a53';

// get all the members on the board

$url = 'https://api.trello.com/1/boards/' . $trelloBoardId . '/members?key=' . $trelloAppKey . '&token=' . $trelloToken;
$trelloMembers = json_decode(file_get_contents($url), true);

foreach ($trelloMembers as $member) {
	$id = searchArray($member['username'], 'trello', $usernames);
	if (!is_null($id)) {
		$usernames[$id]['trello_id'] = $member['id'];
	}
}

// get all cards on the board
//https://api.trello.com/1/boards/54745ca526448f2011c10a53/cards?fields=name,idList,url,due,idMembers&key=c90a2dceca7b8daf800215bd8a1584e6&token=54e9d7b257f5112013c0b2007080fb7aa50d9403597c541336e81b2de1b7c74a

//$url = 'https://api.trello.com/1/boards/' . $trelloBoardId . '/cards?fields=name,idList,url,due,idMembers&key=' . $trelloAppKey . '&token=' . $trelloToken;
$url = 'https://api.trello.com/1/boards/' . $trelloBoardId . '/lists?cards=open&card_fields=name,idList,url,due,idMembers&key=' . $trelloAppKey . '&token=' . $trelloToken;
$trelloCards = json_decode(file_get_contents($url), true);

var_dump($trelloCards);








function searchArray($searchValue, $searchKey, $haystack) {
	foreach ($haystack as $k => $val) {
		if ($val[$searchKey] == $searchValue) {
			return $k;
		}
	}
	return null;
}
?>