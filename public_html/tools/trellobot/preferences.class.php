<?php

require_once('datastore.class.php');

class Preferences extends DataStore
{

    private $defaults = [
        'time'      =>  '13:00',
        'timezone'  =>  'Europe/London',
        'frequency' =>  'daily',
        'lists'     =>  ['In Progress', 'Next', 'Incoming Tasks', 'On Hold / Waiting'],
        ];

    public function __construct()
    {
        $this->dataFile = __DIR__ . '/data/preferences';

        parent::__construct();
    }

    public function saveTimeForUser($time, $userId)
    {
        return $this->saveValue('time', $time, $userId);
    }

    public function saveTimezoneForUser($time, $userId)
    {
        return $this->saveValue('time', $time, $userId);
    }

    public function saveFrequencyForUser($time, $userId)
    {
        return $this->saveValue('time', $time, $userId);
    }

    public function saveListsForUser($time, $userId)
    {
        return $this->saveValue('time', $time, $userId);
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
            return $this->defaults[$key];
        } else {
            return $this->getKeyValue($key, $userId);
        }
    }



}