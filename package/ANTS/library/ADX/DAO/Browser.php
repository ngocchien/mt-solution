<?php
/**
 * Created by PhpStorm.
 * User: tuandv
 * Date: 5/12/16
 * Time: 15:18
 */

namespace ADX\DAO;

use ADX\Database;
use Zend\Db\Adapter as ZendDbAdapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use ADX\Exception;

class Browser extends AbstractDAO
{
    public static function get($params)
    {
        try {
            //Init Pram
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null,
                'browser_id' => array(),
                'columns' => null,
                'offset' => null,
                'limit' => null

            ), $params);
            $params['browser_id'] = is_null($params['browser_id']) ? array() : (array)$params['browser_id'];

            //
            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');
            //Create Statement
            $stmt = $adapter->createStatement('BEGIN PKG_BROWSERS.GET(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_BROWSER_ID,
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
            $parameterContainer->offsetSet('p_BROWSER_ID', $params['browser_id'], $parameterContainer::TYPE_ARRAY_NUM);
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
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
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