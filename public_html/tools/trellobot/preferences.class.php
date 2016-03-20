<?php

require_once('datastore.class.php');

class Preferences extends DataStore
{

    public function __construct() {
        $this->dataFile = __DIR__ . '/data/preferences';

        parent::__construct();
    }

    public function saveTimeForUser($time, $userId) {
        if ($this->saveKeyValue('time', $time, $userId)) {
            if ($this->saveData()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false
        }
    }

    public function getTimeForUser($userId) {
        return $this->getKeyValue('time', $userId);
    }


}