<?php
namespace ADX\DAO;

abstract class AbstractDAO
{
    protected static function _transform(&$data)
    {
        //d
        $className = str_replace('DAO', "Model", get_called_class());
        $segments = explode('_', $className);
        $object = end($segments);
        //
        array_walk($data, function (&$row) use ($className, $object) {
            $row = $className::getInstance(@$row[strtoupper($object) . '_ID'], $row);
        });
        //
        $collectionName = str_replace('Model', "Collection", $object);
        //
        return new $collectionName($data);
    }

    protected static function _clearInstance($id)
    {
        $className = str_replace('DAO', "Model", get_called_class());
        $className::clearInstance($id);
        return 1;
    }

    public static function buildLogData(&$parameterContainer, &$stmt)
    {
        $parameter = array();
        $data_type = array();
        if(!is_null($parameterContainer)){
            $parameter = $parameterContainer->getNamedArray();
            $data_type = $parameterContainer->getErrataIterator();
        }
        return array(
            'data' => $parameter,
            'raw_sql' => $stmt->getSql(),
            'data_type' => $data_type
        );
    }
} 