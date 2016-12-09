<?php

namespace ADX\Listener;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;
use ADX\Model;
use ADX\Token;
use ADX\Nosql;

class Authentication
{
    private $is_trigger = 0;

    public function __invoke(MvcEvent $e)
    {
        if ($this->is_trigger != 0) {
            return true;
        }
        //
        $params = null;
        $method = $e->getRequest()->getMethod();
        //
        switch ($method) {
            case 'GET':
                $params = $e->getRequest()->getQuery()->toArray();
                break;
            case 'POST':
                $params = $e->getRequest()->getQuery()->toArray();
                break;
            case 'PUT':
            case 'DELETE':
                $params = $e->getRequest()->getQuery()->toArray();
                break;
        }

        //
        $check_token = Token::validate($params);

        if ($check_token['valid']) {
            //
            if (isset($check_token['allow_access']) && !$check_token['allow_access']) {
                //
                $response = $e->getResponse();
                //
                $response->setStatusCode(403);
                //
                $response->setContent(json_encode(array(
                    'error' => 1,
                    'message' => 'You are not authorized for this request'
                )));
                //
                return $response;
            }
            //
            if (!isset($params['_user_id']) || empty($params['_user_id'])) {

                $response = $e->getResponse();
                //
                $response->setStatusCode(403);
                //
                $response->setContent(json_encode(array(
                    'error' => 1,
                    'message' => 'You are not authorized for this request'
                )));
                //
                return $response;
            }

            if ($params['_user_id'] != $check_token['data']['api']['manager_id']) {
                //
                $data = Model\User::checkSupportUser(array(
                    'manager_id' => $check_token['data']['api']['manager_id'],
                    'user_id' => $params['_user_id'],
                    'network_id' => $check_token['data']['api']['network_id']
                ));

                if (empty($data)) {
                    $response = $e->getResponse();
                    //
                    $response->setStatusCode(403);
                    //
                    $response->setContent(json_encode(array(
                        'error' => 1,
                        'message' => 'You are not authorized for this request'
                    )));
                    //
                    return $response;
                }
            }

            //Check active User
            $checkActive = Model\User::checkActiveUser(array(
                'user_id' => $params['_user_id'],
                'network_id' => $check_token['data']['api']['network_id']
            ));

            if (!$checkActive) {
                $response = $e->getResponse();
                //
                $response->setStatusCode(403);
                //
                $response->setContent(json_encode(array(
                    'error' => 1,
                    'message' => 'You are not authorized for this request'
                )));
                //
                return $response;
            }

            //
            $check_token['data']['api']['user_id'] = $params['_user_id'];
            //get list user_id by _user_id
            $redis = Nosql\Redis::getInstance('caching');
            $keyBuyer = 'user:' . $params['_user_id'] . ':network:' . $check_token['data']['api']['network_id'] . ':buyer';
            $buyer_id = array_values(array_unique($redis->LRANGE($keyBuyer, 0, -1)));

            $should = array(
                '_id' => array(
                    'in' => array($params['_user_id'] . $check_token['data']['api']['network_id'])
                )
            );

            $result_search_account = Model\ElasticSearch::getAccountEs(
                array(
                    'should' => json_encode($should),
                    'columns' => 'BUYER_ROLE'
                )
            );

            if (isset($result_search_account['buyer_role'])) {
                if ($result_search_account['buyer_role'] == 1 || empty($result_search_account['buyer_role'])) {
                    $is_support = 0;
                } else {
                    $is_support = 1;
                }
            } else {
                $is_support = 0;
            }

            $check_token['data']['api']['is_support'] = $is_support;
            if (!$buyer_id) {
                $users = Model\User::getUsersSupported(array(
                    'user_id' => $params['_user_id'],
                    'network_id' => $check_token['data']['api']['network_id']
                ));

                if (!empty($users)) {

                    //remove key if exists
                    $redis->del($keyBuyer);

                    $redis->MULTI();

                    //get list user support
                    foreach ($users as $user) {
                        //
                        if ($user->level_1 != 0) {
                            $redis->LPUSH($keyBuyer, $user->user_id);
                            $buyer_id[] = $user->user_id;
                        }
                    }

                    $redis->EXEC();
                }
            } else {
                $buyer_id = array_unique($buyer_id);
            }

            $support_id = array(); //_user_id
            $check_token['data']['api']['support_id'] = $support_id;
            $check_token['data']['api']['buyer_id'] = $buyer_id;
            $check_token['data']['api']['list_user_id'] = array_merge($buyer_id, $support_id, (array)$params['_user_id']);

            try {
                $arr_respone_map_index = Model\ElasticSearch::mappingIndexApi();
                //

                if (!empty($arr_respone_map_index)) {
                    $check_token['data']['api']['log_api_id'] = Model\ElasticSearch::addApiDataProccess($arr_respone_map_index['index'], array(
                        'manager_id' => $check_token['data']['api']['manager_id'],
                        'user_id' => $check_token['data']['api']['user_id'],
                        'network_id' => $check_token['data']['api']['network_id'],
                        'params' => $params,
                        'index_name' => $arr_respone_map_index['index'],
                        'body_params' => json_decode(file_get_contents('php://input'), true)
                    ));
                    $check_token['data']['api']['index_name'] = $arr_respone_map_index['index'];
                }

            } catch (\Exception $ex) {
            }

            //
            foreach ($check_token['data']['api'] as $k => $v) {

                $e->setParam($k, $v);
            }

        } else {
            $response = $e->getResponse();

            //
            //$response->setStatusCode(isset($check_token['token_expire']) ? 401 : 403);
            $response->setStatusCode(302);
            //
            $response->setContent(json_encode(array(
                'error' => 1,
                'message' => 'You are not authorized for this request'
            )));
            //
            return $response;
        }
        //
        $this->is_trigger = 1;
    }
}