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

class User extends AbstractDAO
{
    public static function checkLogin($params)
    {
        try {
            $result = array();
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_USER_REGISTERS.CHECK_LOGIN(
                :p_EMAIL,
                :p_NETWORK_ID,
                :p_PASSWORDS,
                :p_TABLE
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_EMAIL', $params['username'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_PASSWORDS', $params['password'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TABLE', $result, $parameterContainer::TYPE_CURSOR);
            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);
            //Execute
            $stmt->execute();
            //Fetch Data
            $result = $stmt->fetch();
            //
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }

    public static function checkSupportUser($params = array())
    {
        try {
            $result = 0;
            $params = array_merge(array(
                'manager_id' => null,
                'user_id' => null,
                'network_id' => null
            ), $params);
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_USERS.CHECK_USER_SUPPORTED(
                :p_MANAGER_ID,
                :p_USER_ID,
                :p_NETWORK_ID,
                :v_RESULT
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_MANAGER_ID', $params['manager_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('v_RESULT', $result, $parameterContainer::TYPE_INTEGER);
            $parameterContainer->offsetSetReference('v_RESULT', 'v_RESULT');

            $stmt->setParameterContainer($parameterContainer);
            //Execute
            $stmt->execute();
            //$get_name_result = $parameterContainer->getNamedArray();
            $get_name_result = $parameterContainer->getNamedArray();
            //
            $result = isset($get_name_result['v_RESULT']) ? $get_name_result['v_RESULT'] : 0;

            //Close Cursor
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }

    public static function getUser($params = array())
    {
        try {
            $result = array();
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null,
                'columns' => null
            ), $params);
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_USERS.GET(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_COLUMNS,
                :p_TABLE
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_COLUMNS', $params['columns'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TABLE', $result, $parameterContainer::TYPE_CURSOR);

            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);
            //Execute
            $stmt->execute();
            //Fetch Data
            $result = $stmt->fetch();

            //
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }

    public static function checkActiveUser($params = array())
    {
        try {
            $result = 0;
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null
            ), $params);
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_NETWORK_USERS.CHECK_ACTIVE(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_RESULT
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_RESULT', $result, $parameterContainer::TYPE_INTEGER);
            $parameterContainer->offsetSetReference('p_RESULT', 'p_RESULT');

            $stmt->setParameterContainer($parameterContainer);

            //Execute
            $stmt->execute();

            $get_name_result = $parameterContainer->getNamedArray();
            //
            $result = isset($get_name_result['p_RESULT']) ? $get_name_result['p_RESULT'] : 0;
            //Close Cursor
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }

    public static function getUserRegiter($params = array())
    {
        try {
            $result = array();
            $params = array_merge(array(
                'user_id' => null,
            ), $params);
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_USER_REGISTERS.GET_DETAIL(
                :p_USER_ID,
                :p_TABLE
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TABLE', $result, $parameterContainer::TYPE_CURSOR);

            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);
            //Execute
            $stmt->execute();
            //Fetch Data
            $result = $stmt->fetch();

            //
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }

    public static function getInfoNetworkUser($params = array())
    {
        try {
            $result = array();
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null
            ), $params);
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_NETWORK_USERS.GET_DETAIL_NETWORK_USERS(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_TABLE
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TABLE', $result, $parameterContainer::TYPE_CURSOR);
            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);
            //Execute
            $stmt->execute();
            //Fetch Data
            $result = $stmt->fetch();

            //
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }
}