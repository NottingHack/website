<?php

require_once('user.class.php');
require_once('trellousers.php');

class Users
{

	private $users = [];

	private $usersByTrelloId = [];

	private $usersByTrelloName = [];

	private $usersBySlackId = [];

	private $usersBySlackName = [];

	private $usersByDM = [];


	public function __construct() {

	}

	public function setSlackUsers($users) {
		global $trellousers;

		foreach($users as $user) {
            if (!$user->isDeleted()) {
            	$id = count($this->users);

            	$this->users[$id] = new User($user->getId());
            	$this->usersBySlackId[$user->getId()] = $id;

            	$this->users[$id]->setSlackUsername($user->getUsername());
            	$this->usersBySlackName[$user->getUsername()] = $id;

            	$this->users[$id]->setFirstName($user->getFirstName());

            	if (isset($trellousers[$user->getUsername()])) {
            		$this->users[$id]->setTrelloUsername($trellousers[$user->getUsername()]);
            		$this->usersByTrelloName[$trellousers[$user->getUsername()]] = $id;
            	}
            }
        }
	}

	public function setDMs($dms) {
		foreach ($dms as $dm) {
			$dmId = $dm->getId();
			$dm->getUser()->then(function($user) use ($dmId) {
				$id = $this->usersBySlackId[$user->getId()];

				$this->users[$id]->setDM($dmId);
				$this->usersByDM[$dmId] = $id;
			});
		}
	}

	public function setTrelloUsers($users) {
		foreach ($users as $trellouser) {
			if (isset($this->usersByTrelloName[$trellouser['username']])) {
				$id = $this->usersByTrelloName[$trellouser['username']];
				
				$this->users[$id]->setTrelloId($trellouser['id']);
				$this->usersByTrelloId[$trellouser['id']] = $id;
			}
		}
	}

	public function getByTrelloUsername($username) {
		if (isset($this->users[$this->usersByTrelloName[$username]])) {
			return $this->users[$this->usersByTrelloName[$username]];
		} else {
			return false;
		}
	}

	public function getByTrelloId($id) {
		if (isset($this->users[$this->usersByTrelloId[$id]])) {
			return $this->users[$this->usersByTrelloId[$id]];
		} else {
			return false;
		}
	}

	public function getBySlackUsername($username) {
		if (isset($this->users[$this->usersBySlackName[$username]])) {
			return $this->users[$this->usersBySlackName[$username]];
		} else {
			return false;
		}
	}

	public function getBySlackId($id) {
		if (isset($this->users[$this->usersBySlackId[$id]])) {
			return $this->users[$this->usersBySlackId[$id]];
		} else {
			return false;
		}
	}
}