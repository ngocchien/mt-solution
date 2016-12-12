<?php
namespace MT\DAO;

use MT\Database,
    Zend\Db\Adapter as ZendDbAdapter,
    Zend\Db\Sql\Sql;

abstract class AbstractDAO
{
    protected $_limit;
    protected $_offset;
    protected $_page;
    protected $_table;
    protected $_sort;

    protected function _clearInstance($id)
    {
        $className = str_replace('DAO', "Model", get_called_class());
        $className::clearInstance($id);
        return 1;
    }

    public function setTable($_table)
    {
        $this->_table = $_table;
        return $this;
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function setLimit($_limit)
    {
        $this->_limit = $_limit;
        return $this;
    }

    public function getLimit()
    {
        return $this->_limit ? $this->_limit : 20;
    }

    public function setPage($_page)
    {
        $this->_page = $_page;
        return $this;
    }

    public function getPage()
    {
        return $this->_page ? $this->_page : 1;
    }

    public function getOffset()
    {
        return $this->getLimit() * ($this->getPage() - 1);
    }

    public function setSort($sort = ['cate_sort ASC'])
    {
        $this->_sort = $sort;
        return $this;
    }

    public function getSort()
    {
        return $this->_sort ? $this->_sort : ['cate_sort ASC'];
    }

    public function executeQuery($strWhere = '')
    {
        try {
            $adapter = Database::getInstance('info_slave');
            $sql = new Sql($adapter);
            $select = $sql->Select($this->getTable())
                ->where('1=1' . $strWhere)
                ->order($this->getSort())
                ->offset($this->getOffset())
                ->limit($this->getLimit());
            $query = $sql->getSqlStringForSqlObject($select);
            return $adapter->query($query, $adapter::QUERY_MODE_EXECUTE)->toArray();
        } catch (\Exception $e) {
            echo '<pre>';
            print_r($e->getMessage());
            echo '</pre>';
            die();
        }
    }
} 