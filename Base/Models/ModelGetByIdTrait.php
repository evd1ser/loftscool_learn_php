<?php

namespace Base\Models;
use Base\Context;

trait ModelGetByIdTrait
{
    public static function getById($_method, int $id)
    {
        $db = Context::getInstance()
          ->getDbConnection();

        $model = new self;
        $table = $model->getTable();
        $query = "SELECT * FROM $table WHERE id = :id";
        $data = $db->fetchOne($query, $_method, [':id' => $id]);
        if (!$data) {
            return null;
        }
        $model->initByDbData($data);
        return $model;
    }
}
