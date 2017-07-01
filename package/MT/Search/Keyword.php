<?php
namespace MT\Search;
use MT;

class Keyword extends SearchAbstract
{
    private $_index_name = 'mt_keyword';
    private $_index_type = 'data';

    private $column_mapping = [
        'key_id' => [
            'type' => 'long',
            'index' => 'not_analyzed'
        ],
        'key_name' => [
            'type' => 'string',
            'index' => 'not_analyzed'
        ],
        'key_slug' => [
            'type' => 'string',
            'index' => 'not_analyzed'
        ],
        'is_crawler' => [
            'type' => 'integer',
            'index' => 'not_analyzed'
        ],
        'created_date' => [
            'type' => 'long',
            'index' => 'not_analyzed'
        ],
        'updated_date' => [
            'type' => 'long',
            'index' => 'not_analyzed'
        ],
        'key_description' => [
            'type' => 'string',
            'index' => 'analyzed'
        ]
    ];

    public function __construct()
    {
        $config = MT\Config::get('elastic');
        $hosts = $config['elastic']['adapters']['info_slave'];
        $index = isset($hosts['prefix']) ? $hosts['prefix'] . $this->_index_name : $this->_index_name;
        $this->setSearchIndex($index);
        $this->setSearchType($this->_index_type);
        $this->setSearchClient(MT\Elastic::getInstances('info_slave'));
    }

    public function createIndex()
    {
        $this->setColumn($this->column_mapping);
        return $this->mapping();
    }

    public function add($params)
    {
        $this->setId($params['key_id']);
        $this->setDataIndex($params);
        return $this->addDocument();
    }

    public function update($params)
    {
        $this->setId($params['key_id']);
        $this->setDataIndex($params);
        return $this->editDocument();
    }

    public function searchData($params = [])
    {
        //build params
        $limit = empty($params['limit']) ? 20 : (int)$params['limit'];
        $page = empty($params['page']) ? 1 : (int)$params['page'];
        $offset = $limit * ($page - 1);
        $sort = !empty($params['sort']) && is_array($params['sort']) ? $params['sort'] : ['key_id' => ['order' => 'desc']];
        $source = !empty($params['source']) && is_array($params['source']) ? $params['source'] : [];

        //build query
        $query = $this->__buildQuery($params);

        $this->setLimit($limit)->setOffset($offset)->setSort($sort)->setSource($source);
        $this->setParamsQuery($query);

        //execute
        return $this->getDocument();
    }

    private function __buildQuery($params)
    {
        $must = $must_not = $should = [];

        if (isset($params['key_id'])) {
            array_push($must, [
                "term" => [
                    'key_id' => $params['key_id']
                ]
            ]);
        }

        if (!empty($params['in_key_id'])) {
            array_push($must, [
                "terms" => [
                    'key_id' => $params['in_key_id']
                ]
            ]);
        }

        if (!empty($params['key_slug'])) {
            array_push($must, [
                "term" => [
                    'key_slug' => $params['key_slug']
                ]
            ]);
        }

        if (!empty($params['match_key_name'])) {
            array_push($must, [
                "match" => [
                    'key_name' => $params['match_key_name']
                ]
            ]);
        }

        if (!empty($params['like_key_name'])) {
            array_push($must, [
                'query_string' => [
                    'default_field' => 'key_name',
                    'query' => $params['like_key_name']
                ]
            ]);
        }

        $result = [
            'must' => $must,
            'must_not' => $must_not,
            'should' => $should
        ];

        return $result;
    }
}