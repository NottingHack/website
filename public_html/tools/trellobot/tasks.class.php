<?php

require_once('datastore.class.php');

class Tasks extends DataStore
{
    public function __construct()
    {
        $this->dataFile = __DIR__ . '/data/tasks';

        parent::__construct();
    }

    public function getTaskId($trelloId)
    {
        if ($this->getKeyValue($trelloId, 'trello') !== false) {
            return $this->getKeyValue($trelloId, 'trello');
        } else {
            while (1) {
                $taskId = mt_rand(100000, 999999);
                if ($this->getKeyValue($taskId, 'tasks') === false) {
                    $this->setKeyValue($taskId, $trelloId, 'tasks');
                    $this->setKeyValue($trelloId, $taskId, 'trello');
                    $this->saveData();
                    break;
                }
            }
            return $taskId;
        }
    }

    public function getTrelloId($taskId)
    {
        if ($this->getKeyValue($taskId, 'tasks') !== false) {
            return $this->getKeyValue($taskId, 'tasks');
        } else {
            return false;
        }
    }
}