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

	public function getByTrelloUserame($username) {
		return $this->users[$this->usersByTrelloName[$username]];
	}

	public function getBySlackUsername($username) {
		return $this->users[$this->usersBySlackName[$username]];
	}

	public function getBySlackId($id) {
		return $this->users[$this->usersBySlackId[$id]];
	}
}