<?php
/**
 * Created by PhpStorm.
 * User: tuandv
 * Date: 5/12/16
 * Time: 15:01
 */
namespace ADX\DAO;

use ADX\Database;
use ADX\Exception;
use Zend\Db\Adapter as ZendDbAdapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

class Location extends AbstractDAO
{
    public static function get($params)
    {
        try {
            //Init Pram
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null,
                'location_id' => array(),
                'columns' => null,
                'offset' => null,
                'limit' => null

            ), $params);
            $params['location_id'] = is_null($params['location_id']) ? array() : (array)$params['location_id'];

            //
            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');
            //Create Statement
            $stmt = $adapter->createStatement('BEGIN PKG_LOCATIONS.GET(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_LOCATION_ID,
                :p_COLUMNS,
                :p_OFFSET,
                :p_LIMIT,
                :p_RESULT
           ); END;');

            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_LOCATION_ID', $params['location_id'], $parameterContainer::TYPE_ARRAY_NUM);
            $parameterContainer->offsetSet('p_COLUMNS', $params['columns'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_OFFSET', $params['offset'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_LIMIT', $params['limit'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_RESULT', $result, $parameterContainer::TYPE_CURSOR);
            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);
            
            //Execute
            $stmt->execute();
            //Fetch All Data
            $result = $stmt->fetchAll();

            //Close Cursor
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return self::_transformStatic($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer,$stmt));
        }
    }

    public static function getList($params = array()){
        try {
            //Init Pram
            $params = array_merge(array(
                'network_id' => null,
                'columns' => null,
                'offset' => null,
                'limit' => null

            ), $params);

            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');
            //Create Statement
            $stmt = $adapter->createStatement('BEGIN PKG_LOCATIONS.GET_LIST(
                :p_NETWORK_ID,
                :p_COLUMNS,
                :p_OFFSET,
                :p_LIMIT,
                :p_TABLE
           ); END;');

            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_COLUMNS', $params['columns'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_OFFSET', $params['offset'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_LIMIT', $params['limit'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TABLE', $result, $parameterContainer::TYPE_CURSOR);
            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);

            //Execute
            $stmt->execute();
            //Fetch All Data
            $result = $stmt->fetchAll();

            //Close Cursor
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return self::_transformStatic($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer,$stmt));
        }
    }

    protected static function _transformStatic(&$data)
    {
        $export = array();
        $tmp = array();

        foreach ($data as $row) {
            foreach ($row as $key => $value)
                $tmp[strtolower($key)] = $value;
            $export[] = $tmp;
        }

        return $export;
    }
}
