<?php

namespace Deal\Controller;

use Admin\Controller\AbstractAdminRestController;
use ADX\Search;
use Zend\View\Model\JsonModel;
use ADX\Business;
use ADX\Model;
use ADX\Exception;

class PerformanceRestController extends AbstractAdminRestController
{
    private $_arrParams = array();

    public function getList()
    {
        $manager_id = $this->getEvent()->getParam('manager_id', false);
        $network_id = $this->getEvent()->getParam('network_id', false);
        $user_id = $this->getEvent()->getParam('user_id', false);
        $list_user_id = $this->getEvent()->getParam('list_user_id', false);
        $log_api_id = $this->getEvent()->getParam('log_api_id', false);
        $is_support = $this->getEvent()->getParam('is_support', false);
        //
        $params = $this->params()->fromQuery();
        //
        $params['user_id'] = $user_id;
        $params['network_id'] = $network_id;
        $params['manager_id'] = $manager_id;
        $params['list_user_id'] = $list_user_id;
        $params['log_api_id'] = $log_api_id;
        $params['is_support'] = $is_support;

        $params_info = Model\Deal::getParamsInfo();

        $params = Business\Performance::validateParamsPerformance(array_merge($params_info, $params));

        $result = Business\Deal::getDealPerformance($params);

        return Business\Api::success(200, $result);
    }

    public function get($id)
    {
        return new JsonModel(array('message' => 'get'));
    }

    public function create($data)
    {
        return new JsonModel(array('message' => 'create'));
    }

    public function update($id, $data)
    {
        return new JsonModel(array('message' => 'update'));
    }
}
