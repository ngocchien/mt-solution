<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\DAO;

use ADX\Database;
use Zend\Db\Adapter as ZendDbAdapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use ADX\Exception;

class Account extends AbstractDAO
{
    public static function getListSupport($params)
    {
        try {
            $result = array();
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_USERS.GET(
                :p_MANAGER_ID,
                :p_NETWORK_ID,
                :p_COLUMNS,
                :p_TABLE
            ); END;');

            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_MANAGER_ID', $params['manager_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_COLUMNS', $params['columns'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TABLE', $result, $parameterContainer::TYPE_CURSOR);

            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);

            //Execute
            $stmt->execute();

            //Fetch Data
            $result = $stmt->fetchAll();
            //
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return self::_transform($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer,$stmt));
        }
    }

    public static function getUsersSupported($params)
    {
        try {
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null,
                'name' => null
            ), $params);
            $result = array();
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_USERS.GET_USERS_SUPPORTED_BY(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_NAME,
                :p_TABLE
            ); END;');

            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NAME', $params['name'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TABLE', $result, $parameterContainer::TYPE_CURSOR);
            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);

            //Execute
            $stmt->execute();

            //Fetch Data
            $result = $stmt->fetchAll();

            //
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return self::_transform($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer,$stmt));
        }
    }

    public static function getListUserSupport($params)
    {
        try {
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null
            ), $params);

            //
            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');
            //Create Statement
            //p_NETWORK_ID, p_USER_ID, p_TYPE, p_COLUMNS
            $stmt = $adapter->createStatement('BEGIN PKG_USERS.GET_LIST_USER_SUPPORT (
                 :p_USER_ID,
                :p_NETWORK_ID,
                :p_RESULT
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
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
            return self::_transform($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer,$stmt));
        }
    }
    public static function checkUserSupported($params)
    {
        try {
            $params = array_merge(array(
                'user_id' => null,
                'manager_id'=>null,
                'network_id' => null
            ), $params);
            $result = array();
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_USERS.CHECK_USER_SUPPORTED(
                :p_MANAGER_ID,
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_TABLE
            ); END;');

            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_MANAGER_ID', $params['manager_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TABLE', $result, $parameterContainer::TYPE_CURSOR);

            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);

            //Execute
            $stmt->execute();

            //Fetch Data
            $result = $stmt->fetchAll();
            //
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return self::_transform($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer,$stmt));
        }
    }
}