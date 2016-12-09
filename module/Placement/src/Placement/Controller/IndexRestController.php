<?php

namespace Placement\Controller;

use Admin\Controller\AbstractAdminRestController;
use ADX\Search;
use Zend\View\Model\JsonModel;
use ADX\Business;
use ADX\Model;
use ADX\Exception;

class IndexRestController extends AbstractAdminRestController
{
    public function getList()
    {
        $arr_return = [];
        try {
            $params = $this->getRequest()->getQuery()->toArray();
            $params = array_merge(array(
                'user_id' => $this->getEvent()->getParam('user_id', false),
                'network_id' => $this->getEvent()->getParam('network_id', false),
            ), $params);
            $limit = empty($params['limit']) ? 20 : $params['limit'];
            $page = empty($params['page']) ? 1 : $params['page'];
            $instanceSearch = new \ADX\Search\Placement();

            switch ($params['type']) {
                default :
                    $arrCondition = [
                        'in_network_id' => [$params['network_id'], 0],
                        'search' => $params['search'],
                        'limit' => $limit,
                        'page' => $page
                    ];
                    $arr_return = $instanceSearch->searchData($arrCondition);
                    break;
            }
            return Business\Api::success(200, $arr_return);
        } catch (\Exception $ex) {
            return Business\Api::error(101, $arr_return);
        }
    }

    public function get($id)
    {
        return new JsonModel(array('message' => 'Hello Get'));
    }

    public function update($id, $data)
    {
        return new JsonModel(array('message' => 'Hello Update'));
    }

    public function delete($id)
    {
        return new JsonModel(array('message' => 'Hello Delete'));
    }

    public function create()
    {
        $arr_return = [];
        try {
            return Business\Api::success(200, []);
        } catch (\Exception $ex) {
            return Business\Api::error(101, $arr_return);
        }
    }
}
