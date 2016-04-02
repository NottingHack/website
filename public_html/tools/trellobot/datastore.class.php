<?php

Class DataStore
{

    protected $dataFile = '';

    private $data;

    public function __construct()
    {
        $this->readData();
    }

    private function readData()
    {
        if (file_exists($this->dataFile)) {
            $data = file_get_contents($this->dataFile);
            if ($data === false) {
                return false;
            } elseif ($this->checkDataFormat($data)) {
                $this->data = unserialize($data);
                return true;
            } else {
                return false;
            }
        } else {
            $this->data = array();
        }
    }

    protected function saveData()
    {
        if (file_put_contents($this->dataFile, serialize($this->data)) === false) {
            return false;
        } else {
            return true;
        }
    }

    private function checkDataFormat($data)
    {
        // actually need to check at some point in the future!
        return true;
    }

    private function createCategory($category)
    {
        if (!$this->categoryExists($category)) {
            $this->data[$category] = array();
        }
        return true;
    }

    private function categoryExists($category)
    {
        if (!isset($this->data[$category])) {
            return false;
        } else {
            return true;
        }
    }

    protected function setKeyValue($key, $value, $category = 'default', $overwrite = true)
    {
        $this->createCategory($category);

        if (isset($this->data[$category][$key]) && $overwrite === false) {
            return false;
        } else {
            $this->data[$category][$key] = $value;
            return true;
        }
    }

    protected function getKeyValue($key, $category = 'default')
    {
        if (isset($this->data[$category][$key])) {
            return $this->data[$category][$key];
        } else {
            return false;
        }
    }

}