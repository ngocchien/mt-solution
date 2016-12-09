<?php
namespace ADX\Search;

class Inmarket extends SearchAbstract
{
    const INDEX_NAME = 'inmarkets';

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
            $sort = !empty($params['sort']) && is_array($params['sort']) ? $params['sort'] : ['inmarket_id' => ['order' => 'desc']];
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
        $result = [];
        if (empty($params)) {
            return $result;
        }

        if (isset($params['inmarket_id'])) {
            array_push($must, array(
                "term" => array(
                    'inmarket_id' => $params['inmarket_id']
                )
            ));
        }

        if (!empty($params['in_inmarket_id'])) {
            array_push($must, array(
                "terms" => array(
                    'inmarket_id' => $params['in_inmarket_id']
                )
            ));
        }

        $result['must'] = $must;
        $result['must_not'] = $must_not;
        return $result;
    }
}