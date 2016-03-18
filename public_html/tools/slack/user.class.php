<?php

class User {

    private $slackId;

    private $slackUsername;

    private $dmId;

    private $trelloId;

    private $trelloUsername;

    private $firstName;

    public function __construct($slackId) {
        $this->slackId = $slackId;
    }

    public function setSlackUsername($username) {
        $this->slackUsername = $username;
    }

    public function setDM($dmId) {
        $this->dmId = $dmId;
    }

    public function setTrelloUsername($username) {
        $this->trelloUsername = $username;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function getSlackId() {
        return $this->slackId;
    }
}