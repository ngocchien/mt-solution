<?php
namespace ADX\Search;

class User extends SearchAbstract
{
    const INDEX_NAME = 'users';

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
            $sort = !empty($params['sort']) && is_array($params['sort']) ? $params['sort'] : ['user_id' => ['order' => 'desc']];
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

        if (isset($params['user_id'])) {
            array_push($must, array(
                "term" => array(
                    'user_id' => $params['user_id']
                )
            ));
        }
        
        if (!empty($params['in_user_id'])) {
            array_push($must, array(
                "terms" => array(
                    'user_id' => $params['in_user_id']
                )
            ));
        }

        $result['must'] = $must;
        $result['must_not'] = $must_not;
        return $result;
    }
}