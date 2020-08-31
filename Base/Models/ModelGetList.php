<?php

namespace Base\Models;
use Base\Context;

trait ModelGetList
{
    /**
     * @param $modelTypeId
     * @param $_method
     * @param int $limit
     * @param array $filter
     * @param string $order
     * @return BaseModel[]
     * @throws Exception
     */
    public static function getList($_method, int $limit, array $filters = [], string $order = '')
    {
        $db = Context::getInstance()->getDbConnection();

        $return = [];

        /** @var BaseModel $model */
        $model = new self();
        $table = $model::getTable();

        $filterStr = '';

        if ($filters) {
            $filterStr = 'WHERE';


            foreach ($filters as $filter){


                list($field, $operand, $value) = $filter;

                $filterStr .= " $field $operand $value";
            }
        }

        $orderStr = '';
        if ($order) {
            $orderStr = "ORDER BY $order DESC";
        }
        $select = "SELECT * FROM $table {$filterStr} {$orderStr} LIMIT $limit";

        $data = $db->fetchAll($select, $_method);

        if ($data) {
            foreach ($data as $elem) {
                $model = new self();
                $model->initByDbData($elem);
                $return[$elem['id']] = $model;
            }
        }

        return $return;
    }
}
