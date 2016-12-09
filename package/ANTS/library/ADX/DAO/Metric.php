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

class Metric extends AbstractDAO
{
    //
    public static function getDetailMetric($params)
    {
        $params = array_merge(array(
            'metric_id'=>null,
            'network_id'=>0,
            'type'=>0
        ),$params);
        try {
            //
            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');
            //Create Statement
            $stmt = $adapter->createStatement('BEGIN PKG_METRICS.DETAIL_METRIC(
                :p_METRIC_ID,
                :p_NETWORK_ID,
                :p_TYPE,
                :p_RESULT
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_METRIC_ID', $params['metric_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TYPE', $params['type'], $parameterContainer::TYPE_STRING);
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
            throw new Exception($e->getMessage(), $e->getCode(),self::buildLogData($parameterContainer,$stmt));
        }
    }

    public static function getMetric($params)
    {
        $params = array_merge(array(
            'by_fnc' => null,
            'order'=>null,
            'columns'=>null
        ), $params);
        try {
            //
            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');

            //Create Statement
            $stmt = $adapter->createStatement('BEGIN PKG_METRICS.GET_LISTING(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_TYPE,
                :p_BY_FNC,
                :p_COLUMNS,
                :p_ORDER,
                :p_RESULT
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TYPE', $params['type'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_BY_FNC', $params['by_fnc'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_ORDER', $params['order'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_COLUMNS', $params['columns'], $parameterContainer::TYPE_STRING);
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

            throw new Exception($e->getMessage(), $e->getCode(),self::buildLogData($parameterContainer,$stmt));
        }
    }

}