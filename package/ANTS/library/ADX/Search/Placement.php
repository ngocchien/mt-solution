<?php
namespace ADX\Search;

class Placement extends SearchAbstract
{
    const INDEX_NAME = 'placements';

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
            $sort = !empty($params['sort']) && is_array($params['sort']) ? $params['sort'] : ['placement_id' => ['order' => 'desc']];
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

        if (!empty($params['placement_id'])) {
            array_push($must, array(
                "term" => array(
                    'placement_id' => $params['placement_id']
                )
            ));
        }

        if (!empty($params['not_placement_id'])) {
            array_push($must_not, array(
                "term" => array(
                    'placement_id' => $params['not_placement_id']
                )
            ));
        }

        if (!empty($params['in_placement_id'])) {
            array_push($must, array(
                "terms" => array(
                    'placement_id' => $params['in_placement_id']
                )
            ));
        }

        if (!empty($params['in_network_id'])) {
            array_push($must, array(
                "terms" => array(
                    'network_id' => $params['in_network_id']
                )
            ));
        }

        if (!empty($params['placement_name'])) {
            array_push($must, array(
                "term" => array(
                    'placement_name_raw' => $params['placement_name']
                )
            ));
        }

        if (!empty($params['like_placement_name'])) {
            array_push($must, array(
                "wildcard" => array(
                    'placement_name_raw' => '*' . $params['like_placement_name'] . '*'
                )
            ));
        }

        if (!empty($params['search'])) {
            $should_child['bool']['should'] = [];
            array_push($should_child['bool']['should'], array(
                "wildcard" => array(
                    'placement_name_raw' => '*' . $params['search'] . '*'
                )
            ));

            if (is_numeric($params['search'])) {
                array_push($should_child['bool']['should'], array(
                    'term' => [
                        'height' => $params['search']
                    ]
                ));

                array_push($should_child['bool']['should'], array(
                    'term' => [
                        'width' => $params['search']
                    ]
                ));
            }
            array_push($must, $should_child);
        }

        $result['must'] = $must;
        $result['must_not'] = $must_not;
        $result['should'] = $should;
        return $result;
    }
}