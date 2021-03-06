<?php
namespace MT\DAO;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use MT\Database;

abstract class AbstractDAO
{
    protected static function getInstance($instance){
        $adapter = Database::getInstance($instance);
        return $adapter;
    }

    protected static function _transform(&$result)
    {
        $rows = array();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new ResultSet;
            $resultSet->initialize($result);

            $data = $resultSet->toArray();

            if(!empty($data)){
                foreach($data as &$value){

                    $value = array_change_key_case($value, CASE_LOWER);
                }
            }

            $rows['rows'] = $data;
            $rows['total'] = count($data);

        }

        return $rows;
    }
}