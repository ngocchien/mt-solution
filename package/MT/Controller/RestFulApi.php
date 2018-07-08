<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 03/07/2017
 * Time: 10:53
 */

namespace MT\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;
use MT\Token;

class RestFulApi extends AbstractRestfulController
{
    public function onDispatch(MvcEvent $e)
    {
        try {
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
            $check_token = Token::validate($params);

            if(empty($check_token['status'])){
                $response = $e->getResponse();
                //
                $response->setStatusCode(302);

                //
                $response->setContent(json_encode(array(
                    'error' => 1,
                    'message' => 'You are not authorized for this request'
                )));

                return $response;
            }

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

                //Check isset params _user_id
                if (!isset($params['_user_id']) || empty($params['_user_id']) || !is_numeric($params['_user_id'])) {

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

                //Alias _user_id to user_id
                $check_token['data']['api']['user_id'] = $params['_user_id'];

                //Check isset params _account_id
                if (isset($params['_account_id'])) {
                    if (!empty($params['_account_id']) && is_numeric($params['_account_id'])) {
                        //Check access account
                        $access_level = Model\Account::ACCESS_LEVEL_ADMIN;
                        if ($check_token['data']['api']['manager_id'] != $params['_account_id']) {
                            $user_access = Model\User::checkUserAccess(array(
                                'network_id' => $check_token['data']['api']['network_id'],
                                'user_id' => $check_token['data']['api']['manager_id'],
                                'account_id' => $params['_account_id']
                            ));

                            if ($user_access !== null) {
                                //Access level
                                $access_level = $user_access;
                            } else {
                                $response = $e->getResponse();

                                $response->setStatusCode(403);

                                $response->setContent(json_encode(array(
                                    'error' => 1,
                                    'message' => 'You are not authorized for this request'
                                )));

                                return $response;
                            }

                            //Check privilege
                            $module_controller = $e->getRouteMatch()->getParam('controller', '');
                            $arr_module_controller = explode("\\", $module_controller);
                            $module = isset($arr_module_controller[0]) ? $arr_module_controller[0] : '';
                            $controller = isset($arr_module_controller[2]) ? str_replace('Rest', '', $arr_module_controller[2]) : '';
                            $module_privilege = false;

                            if ($module && $controller) {
                                $module_privilege = Model\Common::getModulePrivilege($module, $controller);
                            }

                            if (!$module_privilege && $method != 'GET' && $access_level == Model\Account::ACCESS_LEVEL_READ_ONLY) {
                                //
                                $response = $e->getResponse();

                                //
                                $response->setContent(json_encode(array(
                                    'error' => 521,
                                    'message' => 'You are not privilege for this request'
                                )));

                                //
                                return $response;
                            }
                        }

                        //
                        $check_token['data']['api']['login_id'] = $check_token['data']['api']['manager_id'];
                        $check_token['data']['api']['manager_id'] = $params['_account_id'];
                    } else {
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

                //Check support
                if ($check_token['data']['api']['user_id'] != $check_token['data']['api']['manager_id']) {
                    $supported = Model\User::checkSupportUser(array(
                        'manager_id' => $check_token['data']['api']['manager_id'],
                        'user_id' => $check_token['data']['api']['user_id'],
                        'network_id' => $check_token['data']['api']['network_id']
                    ));

                    if (empty($supported)) {
                        if(isset($check_token['data']['api']['login_id']) && isset($check_token['data']['api']['user_id'])){
                            //Check access
                            $user_access = Model\User::checkUserAccess(array(
                                'network_id' => $check_token['data']['api']['network_id'],
                                'user_id' => $check_token['data']['api']['login_id'],
                                'account_id' => $check_token['data']['api']['user_id']
                            ));

                            if ((string)$user_access != (string)Model\Account::ACCESS_LEVEL_READ_ONLY && (string)$user_access != (string)Model\Account::ACCESS_LEVEL_ADMIN) {
                                //Kiểm tra những user access nào được support user_id
                                $shared_user = Model\User::getSharedUsers(array(
                                    'network_id' => $check_token['data']['api']['network_id'],
                                    'user_id' => $check_token['data']['api']['login_id']
                                ));

                                if($shared_user){
                                    $arr_shared_user = array();
                                    $arr_user_id = array();
                                    foreach ($shared_user as $user){
                                        if(isset($user->user_id) && $user->user_id){
                                            $arr_user_id[] = $user->user_id;
                                            $arr_shared_user[$user->user_id] = array(
                                                'user_id' => $user->user_id,
                                                'full_name' => $user->full_name,
                                                'email' => $user->email,
                                                'access_level' => $user->access_level
                                            );
                                        }
                                    }

                                    if(!empty($arr_user_id)){
                                        $user_supported = Model\User::getUserSupported(array(
                                            'network_id' => $check_token['data']['api']['network_id'],
                                            'user_id' => $check_token['data']['api']['user_id'],
                                            'manager_id' => $arr_user_id
                                        ));

                                        if(!empty($user_supported && isset($user_supported[0]['manager_id']))){
                                            //Set manager first access
                                            $check_token['data']['api']['manager_id'] = $user_supported[0]['manager_id'];

                                            $arr_user_supported = array();
                                            foreach ($user_supported as $user){
                                                if(isset($user['manager_id']) && $user['manager_id'] && isset($arr_shared_user[$user['manager_id']])){
                                                    $redis = Nosql\Redis::getInstance('caching');
                                                    $keyUserRole = 'user:' . $user['manager_id'] . ':network:' . $check_token['data']['api']['network_id'] . ':user_role';
                                                    $user_role = $redis->LRANGE($keyUserRole, 0, -1);

                                                    $arr_user_supported[] = array(
                                                        'user_id' => $user['manager_id'],
                                                        'full_name' => isset($arr_shared_user[$user['manager_id']]['full_name']) ? $arr_shared_user[$user['manager_id']]['full_name'] : '',
                                                        'email' => isset($arr_shared_user[$user['manager_id']]['email']) ? $arr_shared_user[$user['manager_id']]['email'] : '',
                                                        'access_level' => isset($arr_shared_user[$user['manager_id']]['access_level']) ? $arr_shared_user[$user['manager_id']]['access_level'] : '',
                                                        'role' => !empty($user_role) ? $user_role[0] : 1
                                                    );
                                                }
                                            }

                                            $check_token['data']['api']['user_access'] = $arr_user_supported;
                                        }else{
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
                                    }else{
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
                                }else{
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
                        }else{
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
                }

                //Check active User
                $checkActive = Model\User::checkActiveUser(array(
                    'user_id' => $check_token['data']['api']['user_id'],
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

                //get list user_id by _user_id
                $redis = Nosql\Redis::getInstance('caching');
                $keyBuyer = 'user:' . $check_token['data']['api']['user_id'] . ':network:' . $check_token['data']['api']['network_id'] . ':buyer';
                $buyer_id = array_values(array_unique($redis->LRANGE($keyBuyer, 0, -1)));

                if (!$buyer_id) {
                    $users = Model\User::getUsersSupported(array(
                        'user_id' => $check_token['data']['api']['user_id'],
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
                        $redis->expire($keyBuyer, 1800);
                    }
                } else {
                    $buyer_id = array_unique($buyer_id);
                }

                $support_id = array(); //_user_id
                $check_token['data']['api']['support_id'] = array_unique($support_id);
                $check_token['data']['api']['buyer_id'] = array_unique($buyer_id);
                $check_token['data']['api']['list_user_id'] = array_merge(array_unique($buyer_id), array_unique($support_id), (array)$check_token['data']['api']['user_id']);

                $key = crc32(microtime() . '+' . uniqid());
                $check_token['data']['api']['log_api_id'] = $key;
                $check_token['data']['api']['start_time'] = Utils::startTimer();

                //
                foreach ($check_token['data']['api'] as $k => $v) {
                    $e->setParam($k, $v);
                }

            }

        } catch (\Exception $exc) {
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r($exc->getMessage());
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }
}