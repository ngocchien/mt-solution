<?php

namespace Index\Controller;

use Admin\Controller\AbstractAdminRestController;
use MT\Search;
use Zend\View\Model\JsonModel;
use MT\Business;

class IndexController extends AbstractAdminRestController
{
    public function indexAction(){
        echo '<pre>';
        print_r('123123213221');
        echo '</pre>';
        die();
    }

    public function index(){
        echo '<pre>';
        print_r('123123213221');
        echo '</pre>';
        die();
    }

    public function getList()
    {
        echo '<pre>';
        print_r('123123');
        echo '</pre>';
        die();
        $arr_return = [];
        try {
            $params = $this->getRequest()->getQuery()->toArray();
            $params = array_merge(array(
                'user_id' => $this->getEvent()->getParam('user_id', false),
                'network_id' => $this->getEvent()->getParam('network_id', false),
            ), $params);
            $instanceSearch = new \ADX\Search\Deal();
            switch ($params['type']) {
                case 'checkName':
                    $arrConidition = [
                        'package_name' => $params['package_name'],
                        'network_id' => $params['network_id'],
                        'source' => ['package_id', 'package_name']
                    ];
                    $response = $instanceSearch->searchData($arrConidition);
                    break;
                default :
                    $response = Search\Package::searchData(
                        [
                            'network_id' => $params['network_id']
                        ]
                    );
                    break;
            }
            return Business\Api::success(200, $response);
        } catch (\Exception $ex) {
            return Business\Api::error(101, $arr_return);
        }
    }

    public function get($id)
    {
        echo '<pre>';
        print_r('123123');
        echo '</pre>';
        die();
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

    public function create($params)
    {
        $arr_return = [];
        try {
            $params = array_merge(array(
                'user_id' => $this->getEvent()->getParam('user_id', false),
                'network_id' => $this->getEvent()->getParam('network_id', false),
            ), $params);

            //validate params
            $data = Business\Deal::validateParams($params);

            if ($data['status'] == 'error') {
                return Business\Api::error($data['code']);
            }

            //create package
            $result = Business\Deal::createDeal($data);

            if (!empty($result['status']) && $result['status'] == 'error') {
                return Business\Api::error($data['code']);
            }

            return Business\Api::success(200, $result);
        } catch (\Exception $ex) {
            return Business\Api::error(101, $arr_return);
        }
    }
}
