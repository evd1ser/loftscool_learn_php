<?php

namespace Base\Models;
use Base\Context;

trait ModelGetByIds
{
    /**
     * @param $modelTypeId
     * @param array $ids
     * @return BaseModel[]
     * @throws Exception
     */
    public static function getByIds($_method, array $ids)
    {
        $db = Context::getInstance()->getDbConnection();

        $return = [];

        array_walk($ids, function(&$id) {
            $id = (int)$id;
        });
        $ids = array_unique($ids);
        $idsStr = implode(',', $ids);

        /** @var BaseModel $model */
        $model = new self();
        $table = $model::getTable();
        $select = "SELECT * FROM $table WHERE id IN($idsStr)";
        $data = $db->fetchAll($select, $_method);

        if ($data) {
            foreach ($data as $elem) {
                /** @var BaseModel $model */
                $model = new self();
                $model->initByDbData($elem);
                $return[$elem['id']] = $model;
            }
        }

        return $return;
    }
}
