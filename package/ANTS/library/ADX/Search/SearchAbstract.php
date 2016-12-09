<?php

namespace ADX\Search;

abstract class SearchAbstract
{

    protected $_searchClient;
    protected $_searchIndex;
    protected $_searchType;
    protected $_params;
    protected $_resultSet;
    protected $_limit;
    protected $_offset;
    protected $_sort;
    protected $_source;
    protected $_paramsQuery;

    /**
     * @return the $_searchIndex
     */
    public function getSearchIndex()
    {
        return $this->_searchIndex;
    }

    /**
     * @param field_type $_searchIndex
     */
    public function setSearchIndex($_searchIndex)
    {
        //get config
        $config = \ADX\Config::get('elastic');
        $hosts = $config['elastic']['adapters']['info_slave'];
        $this->_searchClient = \ADX\Elastic::getInstances('info_slave');
        $this->_searchIndex = isset($hosts['prefix']) ? $hosts['prefix'] . '.' . $_searchIndex : $_searchIndex;
    }

    /**
     * @return the $_searchType
     */
    public function getSearchType()
    {
        return $this->_searchType;
    }

    /**
     * @param field_type $_searchType
     */
    public function setSearchType($_searchType)
    {
        $this->_searchType = $this->_searchIndex->getType($_searchType);
    }

    /**
     * @return the $_limit
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * @param field_type $_limit
     */
    public function setLimit($_limit)
    {
        $this->_limit = $_limit;
        return $this;
    }

    /**
     * @return the $_resultSet
     */
    public function getResultSet()
    {
        return $this->_resultSet;
    }

    /**
     * @param field_type $_resultSet
     */
    public function setResultSet($_resultSet)
    {
        $this->_resultSet = $_resultSet;
    }

    /**
     * @return the $_params
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @param field_type $_params
     */
    public function setParams($_params)
    {
        $this->_params = $_params;
        return $this;
    }

    public function setOffset($_offset)
    {
        $this->_offset = $_offset;
        return $this;
    }

    public function getOffset()
    {
        return $this->_offset;
    }

    public function setSort($_sort)
    {
        $this->_sort = $_sort;
        return $this;
    }

    public function getSort()
    {
        return $this->_sort;
    }

    public function setSoucre($_soucre)
    {
        $this->_source = $_soucre;
        return $this;
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function setParamsQuery($params)
    {
        $this->_paramsQuery = $params;
    }

    public function getParamsQuery()
    {
        return $this->_paramsQuery;
    }

    public function excuteQuery()
    {
        $body = [
            'index' => $this->_searchIndex,
            'type' => 'data',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $this->_paramsQuery['must'],
                        'must_not' => $this->_paramsQuery['must_not'],
                        'should' => $this->_paramsQuery['should']
                    ]
                ],
                'from' => $this->_offset,
                'size' => $this->_limit,
                'sort' => $this->_sort,
                '_source' => $this->_source
            ]
        ];

        $result = $this->_searchClient->search($body);

        $data = [
            'total' => $result['hits']['total'],
            'rows' => []
        ];

        foreach ($result['hits']['hits'] as $rows) {
            if (isset($rows['_source']) && !empty($rows['_source'])) {
                $data['rows'][] = $rows['_source'];
            }
        }
        unset($result);

        return $data;
    }
}
