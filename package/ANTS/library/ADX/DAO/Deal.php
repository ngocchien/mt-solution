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

class Deal extends AbstractDAO
{
    public static function get($params)
    {
        try {
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null,
                'package_id' => null,
                'package_name' => null,
                'payment_model' => null,
                'is_bidding' => null,
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
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }

    public static function add($params)
    {
        try {
            $package_id = 0;
            $params = array_merge(array(
                'user_id' => null,
                'network_id' => null,
                'package_name' => null,
                'payment_model' => null,
                'is_bidding' => null,
            ), $params);
            $result = array();
            //
            $adapter = Database::getInstance('info_slave');
            //
            $stmt = $adapter->createStatement('BEGIN PKG_PACKAGES.ADD(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_PACKAGE_NAME,
                :p_PAYMENT_MODEL,
                :p_FROM_DATE,
                :p_TO_DATE,
                :p_PRICE,
                :p_PROPERTIES,
                :p_PACKAGE_TYPE,
                :p_IS_BIDDING,
                :p_DISCOUNT,
                :p_PRICE_BUY,
                :v_PACKAGE_ID
            ); END;');

            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_PACKAGE_NAME', $params['package_name'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_PAYMENT_MODEL', $params['payment_model'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_FROM_DATE', $params['from_date'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_TO_DATE', $params['to_date'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_PRICE', $params['price'], $parameterContainer::TYPE_INTEGER);
            $parameterContainer->offsetSet('p_PROPERTIES', $params['properties'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_PACKAGE_TYPE', $params['package_type'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_IS_BIDDING', $params['is_bidding'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_DISCOUNT', $params['discount'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_PRICE_BUY', $params['price_buy'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('v_PACKAGE_ID', $package_id, $parameterContainer::TYPE_INTEGER);

            $parameterContainer->offsetSetReference('v_PACKAGE_ID', 'v_PACKAGE_ID');

            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);

            //Execute
            $stmt->execute();

            $get_name_result = $parameterContainer->getNamedArray();

            //
            $package_id = isset($get_name_result['v_PACKAGE_ID']) ? $get_name_result['v_PACKAGE_ID'] : 0;

            //Close Cursor
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return $package_id;
        } catch (\Exception $e) {
            echo '<pre>';
            print_r([
                $e->getMessage(),
                $e->getCode()
            ]);
            echo '</pre>';
            die();
            throw new Exception($e->getMessage(), $e->getCode(), self::buildLogData($parameterContainer, $stmt));
        }
    }


}