<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\DAO;

use ADX\Database;
use ADX\Exception;
use Zend\Db\Adapter as ZendDbAdapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

class Column extends AbstractDAO
{
    public static function getLastedModifyColumn($params)
    {
        try {
            //Init Pram
            $params = array_merge(array(
                'user_id' => 0,
                'network_id' => 0,
                'type' => 0,
                'manager_id' => 0
            ), $params);
            //
            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');
            //Create Statement
            $stmt = $adapter->createStatement('BEGIN PKG_MODIFY_COLUMNS.GET_IS_LASTED(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_TYPE,
                :p_MANAGER_ID,
                :p_TABLE
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_INTEGER);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_INTEGER);
            $parameterContainer->offsetSet('p_TYPE', $params['type'], $parameterContainer::TYPE_INTEGER);
            $parameterContainer->offsetSet('p_MANAGER_ID', $params['manager_id'], $parameterContainer::TYPE_STRING);
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
            return self::_transform($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }

}