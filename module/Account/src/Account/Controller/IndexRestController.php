<?php

namespace Account\Controller;

use Admin\Controller\AbstractAdminRestController;
use Zend\View\Model\JsonModel;
use ADX\Business;
use ADX\Model;
use ADX\Exception;

class IndexRestController extends AbstractAdminRestController
{
    public function getList()
    {

        $data = array();

        try {
            $manager_id = $this->getEvent()->getParam('manager_id', false);
            $network_id = $this->getEvent()->getParam('network_id', false);
            $user_id = $this->getEvent()->getParam('user_id', false);
            $list_user_id = $this->getEvent()->getParam('list_user_id', false);
            //
            $params = $this->getRequest()->getQuery()->toArray();
            $params = array_merge(array(
                'manager_id' => $manager_id,
                'user_id' => $user_id,
                'network_id' => $network_id,
                'list_user_id'=>$list_user_id
            ), $params);

            //
            $data = array();
            switch ($params['type']) {
                case 'support':
                    $data = Business\Account::getListSupport($params);
                    break;
                case 'recent':
                    //update list revent
                    $data = Business\Account::updateListUserRecent($params);
                    break;
                case 'parent':
                    $data = Business\Account::getListParent($params);
                    break;
                case 'filter':
                    $data = Business\Account::getFilterAccount($params);
                    break;
                case 'select-user':{
                    $data = Business\Account::getUserInfo($params);
                    break;
                }
                case 'get-supported':
                    $data = Business\Account::getListSupported($params);
                    break;
                default:
                    $data = Business\Account::getListSupport($params);
                    break;
            }

            //
            return Business\Api::success(200, $data);
        } catch (\Exception $ex) {

            Business\Api::error(101, $data);
        }
    }

    public function get($id)
    {
        $manager_id = $this->getEvent()->getParam('manager_id', false);
        $network_id = $this->getEvent()->getParam('network_id', false);
        $user_id = $this->getEvent()->getParam('user_id', false);
        $params = $this->getRequest()->getQuery()->toArray();
        $data = [];
        $params['user_id'] = $user_id;
        $params['network_id'] = $network_id;
        switch ($params['type']) {
            case 'balance':
                $data = Business\Account::getBalanceByUser($params);
                break;
            default:
                $data = Business\Account::getBalanceByUser($params);
                break;
        }

        return Business\Api::success(200, $data);
    }

    public function create($data)
    {
        try {
            $manager_id = $this->getEvent()->getParam('manager_id', false);
            $network_id = $this->getEvent()->getParam('network_id', false);
            $user_id = $this->getEvent()->getParam('user_id', false);

            $metric_name = $data['custom_metric_name'];
            $metric_desc = $data['custom_metric_description'];
            $formula = $data['formula'];
            $customMetricType = $data['type'];
            $parentMetric = $data['parentMetric'];
            $customFormula = $data['custom_formula'];
            $paramSP = array(
                'user_id' => $user_id,
                'network_id' => $network_id,
                'metric_name' => $metric_name,
                'metric_level' => 2,
                'formula' => $customFormula,
                'parent_id' => $parentMetric,
                'status' => 1,
                'type' => $customMetricType,
                'description' => $metric_desc,
                'metric_properties' => json_encode($formula),
                'data_type' => $data['data_type']
            );
            $checkMetricName = Model\User::getCustomMetricByName($paramSP);
            $isExist = 0;
            if (isset($checkMetricName[0]->metric_id) && $checkMetricName[0]->metric_id != 0) {
                $isExist = 1;
                $result = [];
            } else {
                $result = Model\User::addCustomMetric($paramSP);
            }
            $data['isExist'] = $isExist;
            return Business\Api::success(200, $data);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function update($id, $data)
    {
        try {
            $manager_id = $this->getEvent()->getParam('manager_id', false);
            $network_id = $this->getEvent()->getParam('network_id', false);
            $user_id = $this->getEvent()->getParam('user_id', false);

            $paramSP = array(
                'user_id' => $user_id,
                'network_id' => $network_id,
                'metric_id' => $id,
                'metric_name' => $data['custom_metric_name'],
                'formula' => $data['formula'],
                'description' => $data['custom_metric_description'],
                'metric_properties' => json_encode($data['metric_properties']),
                'data_type' => $data['data_type']
            );
            switch ($data['metric_type']) {
                case 'custom': {
                    $result = Model\User::updCustomMetric($paramSP);
                    echo $result;
                    die;
                    break;
                }
            }
            return new JsonModel(array('message' => 'Hello Update', 'manager_id' => $manager_id, 'network_id' => $network_id));

        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function delete($id)
    {
        try {
            $manager_id = $this->getEvent()->getParam('manager_id', false);
            $network_id = $this->getEvent()->getParam('network_id', false);
            $user_id = $this->getEvent()->getParam('user_id', false);
            $params = $this->getRequest()->getQuery()->toArray();
            switch ($params['type']) {
                case 'custom': {
                    $paramSP = array(
                        'user_id' => $user_id,
                        'network_id' => $network_id,
                        'metric_id' => $id
                    );
                    $result = Model\User::deleteCustomMetric($paramSP);
                    break;
                }
            }

            return new JsonModel(array('message' => 'Hello Delte', 'manager_id' => $manager_id, 'network_id' => $network_id));
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
