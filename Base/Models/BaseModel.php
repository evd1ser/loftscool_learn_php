<?php

namespace Base\Models;

use Base\DbConnection;

abstract class BaseModel
{
    protected $fields      = [];
    protected $fieldsSaved = [];
    private   $data        = [];

    /**
     * @return string
     */
    public function __get($name)
    {
        if(isset($this->$name)){
            return $this->$name;
        }

        return null;
    }

    abstract public static function getTable();

    abstract public static function getInstance();

    function initByDbData(array $data)
    {
        foreach ($this->fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    public function save()
    {
        $db = DbConnection::instance();

        $data = [];

        foreach ($this->fieldsSaved as $field) {
            if (isset($this->$field)) {
                $data[$field] = $this->$field;
            }
        }

        $stringRequest = implode(', ', $this->fieldsSaved);
        $stringRequestMap = ':' . implode(', :', $this->fieldsSaved);

        $db->exec('INSERT INTO ' . $this::getTable() . ' (' . $stringRequest . ') 
                                                VALUES (' . $stringRequestMap . ');', __METHOD__, $data);

        $maxId = $db->exec('SELECT MAX(Id) FROM ' . $this::getTable(), __METHOD__);

        if(!$this->id){
            $this->id = (int) $maxId['ret']['MAX(Id)'];
        }
    }
}
