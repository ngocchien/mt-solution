<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\Model;

use ADX\Entity;
use ADX\DAO;
use ADX\Business;

class Performance extends Entity\Performance
{
    public static function getDataFilter($params)
    {
        try {
            if (!isset($params['object'])) {
                return 0; //error
            }

            if (!isset($params['filter'])) {
                return 0; //error
            }

            $object_name = $params['object'];
            $object_info = ElasticSearch::getObjectInfo($object_name);

            if (!$object_info) {
                return 0; //error
            }

            $limit = isset($params['limit']) ? ($params['limit'] == 0 ? 10000 : $params['limit']) : 10000;
            $offset = isset($params['offset']) ? $params['offset'] : 0;

            $arrCondition = array(
                'limit' => $limit,
                'offset' => $offset
            );
            switch ($object_name) {
                case 'deal':
                    foreach ($params['filter'] as $filter) {
                        switch ($filter['filter']) {
                            case 'package_name':
                                switch ($filter['operator']) {
                                    case 'contain':
                                        $arrCondition['like_package_name'] = $filter['value'];
                                        break;
                                    case 'does_not_contain':
                                        $arrCondition['not_like_package_name'] = $filter['value'];
                                        break;
                                    case 'is':
                                        $arrCondition['package_name'] = $filter['value'];
                                        break;
                                    case 'start_with':
                                        $arrCondition['start_with_package_name'] = $filter['value'];
                                        break;
                                }
                                break;
                            //
                            case 'buy_price':
                                switch ($filter['operator']) {
                                    case 'greater_than':
                                        $arrCondition['gt_buy_price'] = $filter['value'];
                                        break;
                                    case 'greater_than_equals':
                                        $arrCondition['gte_buy_price'] = $filter['value'];
                                        break;
                                    case 'less_than':
                                        $arrCondition['lt_buy_price'] = $filter['value'];
                                        break;
                                    case 'less_than_equals':
                                        $arrCondition['lte_buy_price'] = $filter['value'];
                                        break;
                                    case 'equals':
                                        $arrCondition['buy_price'] = $filter['value'];
                                        break;
                                }
                                break;
                            //
                            case 'price':
                                switch ($filter['operator']) {
                                    case 'greater_than':
                                        $arrCondition['gt_price'] = $filter['value'];
                                        break;
                                    case 'greater_than_equals':
                                        $arrCondition['gte_price'] = $filter['value'];
                                        break;
                                    case 'less_than':
                                        $arrCondition['lt_price'] = $filter['value'];
                                        break;
                                    case 'less_than_equals':
                                        $arrCondition['lte_price'] = $filter['value'];
                                        break;
                                    case 'equals':
                                        $arrCondition['price'] = $filter['value'];
                                        break;
                                }
                                break;
                            //
                            case 'discount':
                                switch ($filter['operator']) {
                                    case 'greater_than':
                                        $arrCondition['gt_discount'] = $filter['value'];
                                        break;
                                    case 'greater_than_equals':
                                        $arrCondition['gte_discount'] = $filter['value'];
                                        break;
                                    case 'less_than':
                                        $arrCondition['lt_discount'] = $filter['value'];
                                        break;
                                    case 'less_than_equals':
                                        $arrCondition['lte_discount'] = $filter['value'];
                                        break;
                                    case 'equals':
                                        $arrCondition['discount'] = $filter['value'];
                                        break;
                                }
                                break;
                            //
                            case 'from_date':
                                switch ($filter['operator']) {
                                    case 'after':
                                        $arrCondition['gt_from_date'] = $filter['value'];
                                        break;
                                    case 'before':
                                        $arrCondition['lt_from_date'] = $filter['value'];
                                        break;
                                    case 'on':
                                        $arrCondition['on_from_date'] = $filter['value'];
                                        break;
                                }
                                break;
                            //
                            case 'to_date':
                                switch ($filter['operator']) {
                                    case 'after':
                                        $arrCondition['gt_to_date'] = $filter['value'];
                                        break;
                                    case 'before':
                                        $arrCondition['lt_to_date'] = $filter['value'];
                                        break;
                                    case 'on':
                                        $arrCondition['on_to_date'] = $filter['value'];
                                        break;
                                }
                                break;
                        }
                    }

                    $instanceDeal = new \ADX\Search\Deal();
                    $result = $instanceDeal->searchData($arrCondition);
                break;
            }
            //

        return $result;
            
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public static function getDataSorting($params)
    {
        try {
            if (!isset($params['object'])) {
                return 0; //error
            }

            $object_name = $params['object'];
            $object_info = ElasticSearch::getObjectInfo($object_name);

            if (!$object_info) {
                return 0; //error
            }

            if (!isset($params['sort']) || !isset($params['az']) || !isset($params['list_user_id'])) {
                return 0; //error
            }

            $limit = isset($params['limit']) ? $params['limit'] : 10000;
            $offset = isset($params['offset']) ? $params['offset'] : 0;

            switch ($object_name) {
                case 'deal':
                    $arrCondition = array(
                        'limit' => $limit,
                        'offset' => $offset
                    );
                    //
                    if (isset($params['package_id']) && !empty($params['package_id'])) {
                        $arrCondition['in_package_id'] = $params['package_id'];
                    }
                    //
                    switch ($params['sort']) {
                        case 'package_name':
                            $arrCondition['sort'] = array('package_name_row' => ['order' => strtolower($params['az'])]);
                            break;
                        case 'price':
                            $arrCondition['sort'] = array('price' => ['order' => strtolower($params['az'])]);
                            break;
                        case 'price_buy':
                            $arrCondition['sort'] = array('price_buy' => ['order' => strtolower($params['az'])]);
                            break;
                        case 'price':
                            $arrCondition['sort'] = array('price' => ['order' => strtolower($params['az'])]);
                            break;
                    }
                    //
                    $instanceDeal = new \ADX\Search\Deal();
                    $result = $instanceDeal->searchData($arrCondition);
                    break;
            }
            //
            return $result;

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public static function getPerformanceInfo($object_name, $rows = array())
    {
        $results = array();
        $object_info = ElasticSearch::getObjectInfo($object_name);

        if (empty($object_info['index'])) {
            return false;
        }

        if (!empty($rows)) {
            foreach ($rows as $object) {

                if (!is_array($object_info['private_key'])) {
                    if (isset($object[$object_info['private_key']])) {
                        $list_object_id[] = $object[$object_info['private_key']];
                    }
                }
            }
        }

        if (!empty($list_object_id)) {
            $list_object_id = array_values(array_unique($list_object_id));

            $arrCondition = array();
            switch ($object_name) {
                case 'deal':
                    $arrCondition['array_package_id'] = $list_object_id;
                    $instanceDeal = new \ADX\Search\Deal();
                    $arr_data = $instanceDeal->searchData($arrCondition);
                    break;
            }
        }

        foreach ($arr_data['rows'] as $data){
            $results['rows'][$data[$object_info['private_key']]] = $data;
        }

        $results['total'] = $arr_data['total'];

        return $results;
    }
}