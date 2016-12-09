<?php

namespace ADX\DAO;

use ADX\Database;
use Zend\Db\Adapter as ZendDbAdapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use ADX\Exception;

class Api extends AbstractDAO
{
    public static function getCampaign($params)
    {
        try {
            //Init Pram
            $params = array_merge(array(
                'campaign_id' => null,
                'user_id' => null,
                'network_id' => null,
                'ads_id' => null,
                'brand_id' => null,
                'campaign_name' => null,
                'status' => null,
                'revenue_type' => null,
                'limit' => null,
                'offset' => null
            ), $params);
            //
            $result = array();
            //Get Connection Cracle
            $adapter = Database::getInstance('info_slave');
            //Create Statement
            $stmt = $adapter->createStatement('BEGIN PKG_CAMPAIGNS.GET(
                :p_USER_ID,
                :p_NETWORK_ID,
                :p_ADS_ID,
                :p_BRAND_ID,
                :p_CAMPAIGN_ID,
                :p_CAMPAIGN_NAME,
                :p_STATUS,
                :p_REVENUE_TYPE,
                :p_LIMIT,
                :p_OFFSET,
                :p_RESULT
            ); END;');
            //Create Parameter Container
            $parameterContainer = new ZendDbAdapter\ParameterContainer();
            //Bidding Data
            $parameterContainer->offsetSet('p_USER_ID', $params['user_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_NETWORK_ID', $params['network_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_ADS_ID', $params['ads_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_BRAND_ID', $params['brand_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_CAMPAIGN_ID', $params['campaign_id'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_CAMPAIGN_NAME', $params['campaign_name'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_STATUS', $params['status'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_REVENUE_TYPE', $params['revenue_type'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_LIMIT', $params['limit'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_OFFSET', $params['offset'], $parameterContainer::TYPE_STRING);
            $parameterContainer->offsetSet('p_RESULT', $result, $parameterContainer::TYPE_CURSOR);
            //Set Parameter Container
            $stmt->setParameterContainer($parameterContainer);
            //Execute
            $stmt->execute();
            //Fetch All Data
            $result = $stmt->fetchAll();
            //Total Rows
            $total = isset($result[0]['FOUND_ROWS']) ? $result[0]['FOUND_ROWS'] : 0;
            //Close Cursor
            $stmt->closeCursor();
            //Deallocate
            unset($stmt);
            //Return
            return self::_transform($result)->setTotalRecords($total);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(),self::buildLogData($parameterContainer,$stmt));
        }
    }

}