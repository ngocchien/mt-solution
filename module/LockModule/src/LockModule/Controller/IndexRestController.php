<?php

namespace LockModule\Controller;

use Admin\Controller\AbstractAdminRestController;
use Zend\View\Model\JsonModel;
use ADX\Business;
use ADX\Model;
use ADX\Utils;


class IndexRestController extends AbstractAdminRestController
{
    public function getList()
    {
        $data = array();
        return Business\Api::success(200, $data);
    }

    public function get($id)
    {
        $data = array();

        try {
            $manager_id = $this->getEvent()->getParam('manager_id', false);
            $network_id = $this->getEvent()->getParam('network_id', false);
            $user_id = $this->getEvent()->getParam('user_id', false);
            //
            $params = $this->getRequest()->getQuery()->toArray();
            $params = array_merge(array(
                'manager_id' => $manager_id,
                'user_id' => $user_id,
                'network_id' => $network_id,
                'module_name' => $id
            ), $params);
            $data = Business\LockModule::getModule($params);
            //
            return Business\Api::success(200, $data);
        } catch (\Exception $ex) {
            return Business\Api::error(101, $data);
        }
    }

    public function create($data)
    {

        try {
            $manager_id = $this->getEvent()->getParam('manager_id', false);
            $network_id = $this->getEvent()->getParam('network_id', false);
            $user_id = $this->getEvent()->getParam('user_id', false);


            $data = array_merge(array(
                'user_id' => $user_id,
                'manager_id' => $manager_id,
                'network_id' => $network_id,
                'status' => 1
            ), $data);
            $status = Business\LockModule::addLockModule($data);
            return Business\Api::success(200, $status);
        } catch (\Exception $ex) {
            return Business\Api::error(101, $status);
        }
    }

    public function update($id, $data)
    {
        $status = '0';

        try {

            $manager_id = $this->getEvent()->getParam('manager_id', false);
            $network_id = $this->getEvent()->getParam('network_id', false);
            $user_id = $this->getEvent()->getParam('user_id', false);

            $data = array_merge(array(
                'user_id' => $user_id,
                'manager_id' => $manager_id,
                'network_id' => $network_id,
                'module_name' => $id
            ), $data);

            $status = Business\LockModule::updateLockModule($data);

            return Business\Api::success(200, $status);
        } catch (\Exception $ex) {
            return Business\Api::error(101, $status);
        }
    }

    public function delete($id)
    {
        try {
            $manager_id = $this->getEvent()->getParam('manager_id', false);
            $network_id = $this->getEvent()->getParam('network_id', false);
            $user_id = $this->getEvent()->getParam('user_id', false);
            $data = array(
                'user_id' => $user_id,
                'manager_id' => $manager_id,
                'network_id' => $network_id,
                'module_name' => $id
            );
            $status = Business\LockModule::deleteLockModule($data);
            return Business\Api::success(200, $status);
        } catch (\Exception $ex) {
            return Business\Api::error(101, $status);
        }
    }
}
