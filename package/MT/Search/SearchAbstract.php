<?php
/*
 * creator : Chien Nguyen
 * Mail : ngocchien01@gmail.com
 * Skype : ngocchien01
 */

namespace MT\Search;

use MT;

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
    protected $_column;
    protected $_data_index;
    protected $_id;

    /**
     * @param string $id
     * @return string
     */
    protected function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @return string
     */
    protected function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $dataIndex
     * @return string
     */
    protected function setDataIndex($dataIndex)
    {
        $this->_data_index = $dataIndex;
        return $this;

    }

    /**
     * @return string
     */
    protected function getDataIndex()
    {
        return $this->_data_index;
    }

    /**
     * @param array $column
     */
    protected function setColumn($column)
    {
        $this->_column = $column;
    }

    /**
     * @return array
     */
    protected function getColumn()
    {
        return $this->_column;
    }

    /**
     * @return string $_searchIndex
     */
    protected function getSearchIndex()
    {
        return $this->_searchIndex;
    }

    /**
     * @param  string $_search_client
     * @return object
     */
    protected function setSearchClient($_search_client)
    {
        $this->_searchClient = $_search_client;
        return $this;
    }

    /**
     * @return object $__search_client
     */
    protected function getSearchClient()
    {
        return $this->_searchClient;
    }

    /**
     * @param string $index
     * @return object
     */
    protected function setSearchIndex($index)
    {
        $this->_searchIndex = $index;
        return $this;
    }

    /**
     * @return string $_searchType
     */
    protected function getSearchType()
    {
        return $this->_searchType;
    }

    /**
     * @param string $_searchType
     */
    protected function setSearchType($_searchType)
    {
        $this->_searchType = $_searchType;
    }

    /**
     * @return integer $_limit
     */
    protected function getLimit()
    {
        return $this->_limit;
    }

    /**
     * @param integer $_limit
     * @return object
     */
    protected function setLimit($_limit)
    {
        $this->_limit = $_limit;
        return $this;
    }

    /**
     * @return array $_resultSet
     */
    protected function getResultSet()
    {
        return $this->_resultSet;
    }

    /**
     * @param array $_resultSet
     */
    protected function setResultSet($_resultSet)
    {
        $this->_resultSet = $_resultSet;
    }

    /**
     * @return array $_params
     */
    protected function getParams()
    {
        return $this->_params;
    }

    /**
     * @param array $_params
     * @return object
     */
    protected function setParams($_params)
    {
        $this->_params = $_params;
        return $this;
    }

    /**
     * @param integer $_offset
     * @return object
     */
    protected function setOffset($_offset)
    {
        $this->_offset = $_offset;
        return $this;
    }

    /**
     * @return integer $_offset
     */
    protected function getOffset()
    {
        return $this->_offset;
    }

    /**
     * @param array $_sort
     * @return object
     */
    protected function setSort($_sort)
    {
        $this->_sort = $_sort;
        return $this;
    }

    /**
     * @return array $_sort
     */
    protected function getSort()
    {
        return $this->_sort;
    }

    /**
     * @param array $_source
     * @return object
     */
    protected function setSource($_source)
    {
        $this->_source = $_source;
        return $this;
    }

    /**
     * @return array $_source
     */
    protected function getSource()
    {
        return $this->_source;
    }

    /**
     * @param array $params
     */
    protected function setParamsQuery($params)
    {
        $this->_paramsQuery = $params;
    }

    /**
     * @return <array> $_paramsQuery
     */
    protected function getParamsQuery()
    {
        return $this->_paramsQuery;
    }

    /**
     * @return boolean
     */
    protected function deleteIndex()
    {
        try {
            return $this->getSearchClient()->indices()->delete([
                'index' => $this->getSearchIndex()
            ]);
        } catch (\Exception $ex) {
            if (APPLICATION_ENV !== 'production') {
                die($ex->getMessage());
            }
            return false;
        }
    }

    /**
     * @return boolean
     */
    protected function checkIndex()
    {
        try {
            return $this->getSearchClient()->indices()->exists([
                'index' => $this->getSearchIndex()
            ]);
        } catch (\Exception $ex) {
            if (APPLICATION_ENV !== 'production') {
                die($ex->getMessage());
            }
            return false;
        }
    }

    /**
     * @return boolean
     */
    protected function mapping()
    {
        try {
            //check exist
            $exist = $this->checkIndex();
            //remove index exist
            if ($exist) {
                if (!$this->deleteIndex()) {
                    return false;
                }
            }

            $params = [
                'index' => $this->getSearchIndex(),
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'max_result_window' => 100000
                    ],
                    'mappings' => [
                        $this->getSearchType() => [
                            '_source' => [
                                'enabled' => true
                            ],
                            'properties' => $this->getColumn()
                        ]
                    ]
                ]
            ];

            $result = $this->getSearchClient()->indices()->create($params);

            if (empty($result['acknowledged'])) {
                return false;
            }

            return true;

        } catch (\Exception $ex) {
            if (APPLICATION_ENV !== 'production') {
                die($ex->getMessage());
            }

            return false;
        }
    }

    /**
     * @return boolean true||false
     */
    protected function addDocument()
    {
        try {
            $data_index = [
                'index' => $this->getSearchIndex(),
                'type' => $this->getSearchType(),
                'id' => $this->getId(),
                'body' => $this->getDataIndex()
            ];
            $response = $this->getSearchClient()->index($data_index);
            if ((isset($response['created']) && $response['created'] == 1)) {
                return true;
            }
            return false;
        } catch (\Exception $ex) {
            if (APPLICATION_ENV !== 'production') {
                die($ex->getMessage());
            }
            return false;
        }
    }

    /**
     * @return boolean true||false
     */
    protected function editDocument()
    {
        try {
            $data_update = [
                'index' => $this->getSearchIndex(),
                'type' => $this->getSearchType(),
                'id' => $this->getId(),
                'body' => [
                    'doc' => $this->getDataIndex()
                ]
            ];

            $response = $this->getSearchClient()->update($data_update);
            if ((isset($response['_version']) && $response['_version'] > 1)) {
                $this->getSearchClient()->indices()->refresh();
                return true;
            }
            return false;
        } catch (\Exception $ex) {
            if (APPLICATION_ENV !== 'production') {
                die($ex->getMessage());
            }
            return false;
        }
    }

    /**
     * @return array
     */
    protected function getDocument()
    {
        try {
            $params = $this->getParamsQuery();
            $body = [
                'index' => $this->getSearchIndex(),
                'type' => $this->getSearchType(),
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $params['must'],
                            'must_not' => $params['must_not'],
                            'should' => $params['should']
                        ]
                    ],
                    'from' => $this->getOffset(),
                    'size' => $this->getLimit(),
                    'sort' => $this->getSort(),
                    '_source' => $this->getSource()
                ]
            ];
            $result = $this->getSearchClient()->search($body);

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
        } catch (\Exception $ex) {
            if (APPLICATION_ENV !== 'production') {
                die($ex->getMessage());
            }
            return [];
        }

    }
}
