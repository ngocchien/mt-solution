<?php
/**
 * Created by PhpStorm.
 * User: truchq
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\DAO;

use ADX\Database;
use Zend\Db\Adapter as ZendDbAdapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use ADX\Exception;

class Age extends AbstractDAO
{
    public static function getAge($params)
    {
        $params = array_merge(array(
            'user_id' => null,
            'age_id' => array(),
            'columns' => null,
            'offset' => null,
            'limit' => null,
            'network_id'=>null
        ),$params);

        if(empty($params['columns'])){
            $params['columns'] = 'AGE_NAME, AGE_RANGE_ID';
        }

        try {
            //
            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');
            //Create Statement
            $stmt = $adapter->createStatement('BEGIN PKG_AGES.GET(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_AGE_ID,
                :p_COLUMNS,
                :p_OFFSET,
                :p_LIMIT,
                :p_TABLE
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_AGE_ID', $params['age_id'], $parameterContainer::TYPE_ARRAY_NUM);
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
            return self::_transform($result);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(),self::buildLogData($parameterContainer,$stmt));
        }
    }
}