<?php
namespace ADX\Search;

use ADX\Utils;

class Deal extends SearchAbstract
{
    const INDEX_NAME = 'packages';

    public function __construct()
    {
        $this->setSearchIndex(self::INDEX_NAME);
    }

    public function searchData($params = [])
    {
        try {
            //build params
            $limit = empty($params['limit']) ? 20 : (int)$params['limit'];
            $page = empty($params['page']) ? 1 : (int)$params['page'];
            $offset = $limit * ($page - 1);
            $sort = !empty($params['sort']) && is_array($params['sort']) ? $params['sort'] : ['package_id' => ['order' => 'desc']];
            $source = !empty($params['source']) && is_array($params['source']) ? $params['source'] : [];

            //build query
            $query = $this->__buildQuery($params);

            $this->setLimit($limit)->setOffset($offset)->setSort($sort)->setSoucre($source);
            $this->setParamsQuery($query);

            //excute
            return $this->excuteQuery();
        } catch (\Exception $exc) {
            throw new Exception($exc->getMessage() . $exc->getCode());
        }
    }

    public function __buildQuery($params)
    {
        $must = [];
        $must_not = [];
        $should = [];
        $result = [];
        if (empty($params)) {
            return $result;
        }

        if (!empty($params['package_id'])) {
            array_push($must, array(
                "term" => array(
                    'package_id' => $params['package_id']
                )
            ));
        }

        if (!empty($params['not_package_id'])) {
            array_push($must_not, array(
                "term" => array(
                    'package_id' => $params['not_package_id']
                )
            ));
        }

        if (!empty($params['in_package_id'])) {
            array_push($must, array(
                "terms" => array(
                    'package_id' => $params['in_package_id']
                )
            ));
        }

        if (!empty($params['network_id'])) {
            array_push($must, array(
                "term" => array(
                    'network_id' => $params['network_id']
                )
            ));
        }

        if (!empty($params['package_name'])) {
            array_push($must, array(
                "term" => array(
                    'package_name_raw' => $params['package_name']
                )
            ));
        }

        if (!empty($params['like_package_name'])) {
            array_push($must, array(
                "wildcard" => array(
                    'package_name_raw' => '*' . $params['like_package_name'] . '*'
                )
            ));
        }

        if (!empty($params['user_id'])) {
            array_push($must, array(
                "term" => array(
                    'user_id' => $params['user_id']
                )
            ));
        }

        if (!empty($params['not_like_package_name'])) {
            array_push($must_not, array(
                "wildcard" => array(
                    'package_name_raw' => '*' . $params['not_like_package_name'] . '*'
                )
            ));
        }

        if (!empty($params['start_with_package_name'])) {
            array_push($must, array(
                "prefix" => array(
                    'package_name_raw' => Utils::remove_accent($params['start_with_package_name'])
                )
            ));
        }

        if (isset($params['gt_buy_price'])) {
            array_push($must, array(
                'range' => array(
                    'price_buy' => array(
                        'gt' => $params['gt_buy_price'],
                    )
                )
            ));
        }

        if (isset($params['gte_buy_price'])) {
            array_push($must, array(
                'range' => array(
                    'price_buy' => array(
                        'gte' => $params['gte_buy_price']
                    )
                )
            ));
        }

        if (isset($params['lt_buy_price'])) {
            array_push($must, array(
                'range' => array(
                    'price_buy' => array(
                        'lt' => $params['lt_buy_price']
                    )
                )
            ));
        }
        if (isset($params['lte_buy_price'])) {
            array_push($must, array(
                'range' => array(
                    'price_buy' => array(
                        'lte' => $params['lte_buy_price']
                    )
                )
            ));
        }

        if (!empty($params['buy_price'])) {
            array_push($must, array(
                "term" => array(
                    'price_buy' => $params['buy_price']
                )
            ));
        }

        if (isset($params['gt_price'])) {
            array_push($must, array(
                'range' => array(
                    'price' => array(
                        'gt' => $params['gt_price']
                    )
                )
            ));
        }

        if (isset($params['gte_price'])) {
            array_push($must, array(
                'range' => array(
                    'price' => array(
                        'gte' => $params['gte_price']
                    )
                )
            ));
        }

        if (isset($params['lt_price'])) {
            array_push($must, array(
                'range' => array(
                    'price' => array(
                        'lt' => $params['lt_price']
                    )
                )
            ));
        }
        if (isset($params['lte_price'])) {
            array_push($must, array(
                'range' => array(
                    'price' => array(
                        'lte' => $params['lte_price']
                    )
                )
            ));
        }

        if (!empty($params['price'])) {
            array_push($must, array(
                "term" => array(
                    'price' => $params['price']
                )
            ));
        }

        if (isset($params['gt_discount'])) {
            array_push($must, array(
                'range' => array(
                    'discount' => array(
                        'gt' => $params['gt_discount']
                    )
                )
            ));
        }

        if (isset($params['gte_discount'])) {
            array_push($must, array(
                'range' => array(
                    'discount' => array(
                        'gte' => $params['gte_discount']
                    )
                )
            ));
        }

        if (isset($params['lt_discount'])) {
            array_push($must, array(
                'range' => array(
                    'discount' => array(
                        'lt' => $params['lt_discount']
                    )
                )
            ));
        }
        if (isset($params['lte_discount'])) {
            array_push($must, array(
                'range' => array(
                    'discount' => array(
                        'lte' => $params['lte_discount']
                    )
                )
            ));
        }

        if (!empty($params['discount'])) {
            array_push($must, array(
                "term" => array(
                    'discount' => $params['discount']
                )
            ));
        }

        if (isset($params['gt_from_date'])) {
            array_push($must, array(
                'range' => array(
                    'from_date' => array(
                        'gt' => Utils::getDate(Utils::formatDate($params['gt_from_date']), 0, 0, 1)
                    )
                )
            ));
        }
        if (isset($params['lt_from_date'])) {
            array_push($must, array(
                'range' => array(
                    'from_date' => array(
                        'lt' => Utils::getDate(Utils::formatDate($params['lt_from_date']), 0, 0, 1)
                    )
                )
            ));
        }

        if (isset($params['on_from_date'])) {
            array_push($must, array(
                'range' => array(
                    'from_date' => array(
                        'lte' => Utils::getDate(Utils::formatDate($params['lte_from_date']), 0, 0, 1)
                    )
                )
            ));
            array_push($must, array(
                'range' => array(
                    'from_date' => array(
                        'gte' => Utils::getDate(Utils::formatDate($params['gte_from_date']), 0, 0, 1)
                    )
                )
            ));
        }

        if (isset($params['gt_to_date'])) {
            array_push($must, array(
                'range' => array(
                    'to_date' => array(
                        'gt' => Utils::getDate(Utils::formatDate($params['gt_to_date']), 0, 0, 1)
                    )
                )
            ));
        }
        if (isset($params['lt_to_date'])) {
            array_push($must, array(
                'range' => array(
                    'to_date' => array(
                        'lt' => Utils::getDate(Utils::formatDate($params['lt_to_date']), 0, 0, 1)
                    )
                )
            ));
        }

        if (isset($params['on_to_date'])) {
            array_push($must, array(
                'range' => array(
                    'to_date' => array(
                        'lte' => Utils::getDate(Utils::formatDate($params['lte_to_date']), 0, 0, 1)
                    )
                )
            ));
            array_push($must, array(
                'range' => array(
                    'to_date' => array(
                        'gte' => Utils::getDate(Utils::formatDate($params['gte_to_date']), 0, 0, 1)
                    )
                )
            ));
        }

        if(!empty($params['array_package_id'])) {
            $arr_package_id = array_chunk($params['array_package_id'], 1000, true);
            //
            foreach ($arr_package_id as $package_id) {
                array_push($should, array(
                    "terms" => array(
                        'package_id' => $package_id
                    )
                ));
            }
        }



        $result['must'] = $must;
        $result['must_not'] = $must_not;
        $result['should'] = $should;
        return $result;
    }
}