<?php

require_once('datastore.class.php');

class Preferences extends DataStore
{

    private $userDefaults = [
        'time'      =>  '13:00',
        'timezone'  =>  'Europe/London',
        'frequency' =>  'daily',
        'lists'     =>  ['In Progress', 'Next', 'Incoming Tasks', 'On Hold / Waiting'],
        ];

    private $sysDefaults = [
        'time'      =>  '21:46',
        'lists'     =>  ['In Progress', 'Next', 'Incoming Tasks', 'On Hold / Waiting'],
        ];

    private $system = 'system';

    public function __construct()
    {
        $this->dataFile = __DIR__ . '/data/preferences';

        parent::__construct();
    }

    public function saveTime($time) {
        return $this->saveValue('time', $time, $this->system);
    }

    public function saveLastNotified($timestamp) {
        return $this->saveValue('last_notified', $timestamp, $this->system);
    }

    public function saveTimeForUser($time, $userId)
    {
        return $this->saveValue('time', $time, $userId);
    }

    public function saveTimezoneForUser($timezone, $userId)
    {
        return $this->saveValue('timezone', $timezone, $userId);
    }

    public function saveFrequencyForUser($frequency, $userId)
    {
        return $this->saveValue('frequency', $frequency, $userId);
    }

    public function saveListsForUser($lists, $userId)
    {
        return $this->saveValue('lists', $lists, $userId);
    }

    public function saveLastNotifiedForUser($timestamp, $userId)
    {
        return $this->saveValue('last_notified', $timestamp, $userId);
    }

    public function getTime()
    {
        return $this->getValueOrDefault('time', $this->system);
    }

    public function getLists()
    {
        return $this->getValueOrDefault('lists', $this->system);
    }

    public function getLastNotified()
    {
        if ($this->getKeyValue('last_notified', $this->system) === false) {
            return 1;
        } else {
            return $this->getKeyValue('last_notified', $this->system);
        }
    }

    public function getTimeForUser($userId)
    {
        return $this->getValueOrDefault('time', $userId);
    }

    public function getTimezoneForUser($userId)
    {
        return $this->getValueOrDefault('timezone', $userId);
    }

    public function getFrequencyForUser($userId)
    {
        return $this->getValueOrDefault('frequency', $userId);
    }

    public function getListsForUser($userId)
    {
        return $this->getValueOrDefault('lists', $userId);
    }

    public function getLastNotifiedForUser($userId)
    {
        if ($this->getKeyValue('last_notified', $userId) === false) {
            return 1;
        } else {
            return $this->getKeyValue('last_notified', $userId);
        }
    }
    

    private function saveValue($key, $value, $userId)
    {
        if ($this->setKeyValue($key, $value, $userId, true)) {
            if ($this->saveData()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function getValueOrDefault($key, $userId)
    {
        if ($this->getKeyValue($key, $userId) === false) {
            if ($userId == 'system') {
                return $this->sysDefaults[$key];
            } else {
                return $this->userDefaults[$key];
            }
        } else {
            return $this->getKeyValue($key, $userId);
        }
    }



}