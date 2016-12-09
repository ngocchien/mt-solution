<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\Business;

use ADX\Model;
use ADX\Utils;
use ADX\Nosql;

class Account
{
    public static function getListSupport($params)
    {
        $data = array();
        $result = array();
        $redis = Nosql\Redis::getInstance('caching');
        if (isset($params['manager']) && $params['manager']) {
            //get account tree
            $users = Model\User::getUsersSupported(array(
                'user_id' => $params['manager_id'],
                'network_id' => $params['network_id']
            ));

            //get manage info
            $user_manager = Model\ElasticSearch::getInfo('users', [$params['manager_id']]);
            if (!empty($user_manager)) {
                $full_name = isset($user_manager[0]['full_name']) ? $user_manager[0]['full_name'] : '';
            }

            if (!empty($users)) {
                foreach ($users as $user) {
                    //save user role
                    $keyUserRole = 'user:' . $user->user_id . ':network:' . $params['network_id'] . ':user_role';
                    $redis->del($keyUserRole);
                    $redis->LPUSH($keyUserRole, $user->role);

                    if (isset($user->level_1) && $user->level_1 == 0) {
                        $manager_role = $user->role;
                    }
                }
            }

            $parent = array(
                array(
                    'user_id' => $params['manager_id'],
                    'full_name' => isset($full_name) ? $full_name : '',
                    'has_child' => true,
                    'root' => true,
                    'role' => isset($manager_role) ? +$manager_role : 1,
                    'roles' => array(
                        'read' => true,
                        'write' => true
                    ),
                    'supporter_id' => ''
                )
            );

        } else {
            //get account tree
            $users = Model\User::getUsersSupported(array(
                'user_id' => $params['user_id'],
                'network_id' => $params['network_id']
            ));
        }

        $list_support = array();
        $list_support_name = array();
        $list_buyer_name = array();
        if (!empty($users)) {
            //get list user array
            foreach ($users as $user) {
                //save user role

                $keyUserRole = 'user:' . $user->user_id . ':network:' . $params['network_id'] . ':user_role';
                $redis->del($keyUserRole);
                $redis->LPUSH($keyUserRole, $user->role);

                $list_support[$user->supporter_id][] = (array)$user;
            }
            foreach ($users as $user) {
                if ($user->level_1 == 1) {

                    $has_child = false;
                    if (isset($list_support[$user->user_id])) {
                        $has_child = true;
                    }

                    $result[$user->user_id] = array(
                        'user_id' => $user->user_id,
                        'full_name' => $user->full_name,
                        'has_child' => $has_child,
                        'role' => $user->role,
                        'roles' => array(
                            'read' => true,
                            'write' => true
                        ),
                        'full_parent' => $user->full_parent,
                        'supporter_id' => isset($user->supporter_id) ? $user->supporter_id : '',
                        'conversion_id' => hash('crc32b', $user->user_id . $params['network_id'])
                    );
                    //
                    if ($user->role == 2) {
                        $list_support_name[$user->user_id] = Utils::remove_accent($user->full_name);
                    } else {
                        $list_buyer_name[$user->user_id] = Utils::remove_accent($user->full_name);
                    }
                }
            }
        }

        $list_user_sorted = array();
        asort($list_support_name);
        asort($list_buyer_name);

        //sort support
        foreach ($list_support_name as $user_id => $user_name) {
            if (isset($result[$user_id])) {
                $list_user_sorted[] = $result[$user_id];
            }
        }

        //sort buyer
        foreach ($list_buyer_name as $user_id => $user_name) {
            if (isset($result[$user_id])) {
                $list_user_sorted[] = $result[$user_id];
            }
        }

        if (isset($params['manager']) && isset($parent) && isset($parent[0])) {
            $parent[0]['children'] = $list_user_sorted;
            $list_user_sorted = $parent;
        }

        $data['account'] = $list_user_sorted;

        //get list user recent
        $data['recent'] = self::getListUserRecent(array(
            'user_id' => $params['manager_id'],
            'network_id' => $params['network_id']
        ));
        //
        return $data;
    }

    public static function getListSupported($params)
    {
        $data = array();
        $result = array();
        $users = Model\User::getUsersSupported(array(
            'user_id' => $params['user_id'],
            'network_id' => $params['network_id']
        ));
        $list_support = array();
        $list_support_name = array();
        $list_buyer_name = array();
        if (!empty($users)) {
            //get list user array

            foreach ($users as $user) {
                //save user role
                $list_support[$user->supporter_id][] = (array)$user;
            }
            foreach ($users as $user) {
                if ($user->level_1 == 1) {

                    $has_child = false;
                    if (isset($list_support[$user->user_id])) {
                        $has_child = true;
                    }
                    $full_data_supporter = Model\User::getListUserSupport(
                        array(
                            'user_id' => $user->user_id,
                            'network_id' => $params['network_id']
                        )
                    );
                    $full_parent_string = '';
                    $arr_parent_id = [];
                    foreach ($full_data_supporter as $supporter) {
                        $arr_parent_id[] = $supporter->parent_id;
                    }
                    $full_parent_string = implode(',', array_reverse($arr_parent_id));
                    $result[$user->user_id] = array(
                        'user_id' => $user->user_id,
                        'full_name' => $user->full_name,
                        'has_child' => $has_child,
                        'role' => $user->role,
                        'roles' => array(
                            'read' => true,
                            'write' => true
                        ),
                        'full_parent' => $full_parent_string,
                        'supporter_id' => isset($user->supporter_id) ? $user->supporter_id : ''
                    );
                    //
                    if ($user->role == 2) {
                        $list_support_name[$user->user_id] = Utils::remove_accent($user->full_name);
                    } else {
                        $list_buyer_name[$user->user_id] = Utils::remove_accent($user->full_name);
                    }
                }
            }
        }

        $list_user_sorted = array();
        asort($list_support_name);
        asort($list_buyer_name);

        //sort support
        foreach ($list_support_name as $user_id => $user_name) {
            if (isset($result[$user_id])) {
                $list_user_sorted[] = $result[$user_id];
            }
        }

        //sort buyer
        foreach ($list_buyer_name as $user_id => $user_name) {
            if (isset($result[$user_id])) {
                $list_user_sorted[] = $result[$user_id];
            }
        }

        $data['account'] = $list_user_sorted;
        return $data;
    }

    public static function getUserInfo($params)
    {
        $users = Model\User::getUsersSupported(array(
            'user_id' => $params['manager_id'],
            'network_id' => $params['network_id'],
            'name' => isset($params['search']) ? $params['search'] : ''
        ));


        $select_id = isset($params['select_id']) ? $params['select_id'] : '';

        $list_buyer = [];
        $all_user = [];
        foreach ($users as $user){
            $all_user[$user->user_id] = $user;
        }

        $full_parent = isset($all_user[$select_id]->full_parent) ? $all_user[$select_id]->full_parent : '';
        $full_parent = explode(',', $full_parent);

        if (!empty($select_id)) {
            foreach ($full_parent as $supporter) {
                if(!empty($supporter)){
                    $data_buyer_supporter = Model\User::getUsersSupported(array(
                        'user_id' => $supporter,
                        'network_id' => $params['network_id'],
                        'name' => isset($params['search']) ? $params['search'] : ''
                    ));
                    foreach ($data_buyer_supporter as $buyer_supporter) {
                        if ($buyer_supporter->role != 2) {
                            $list_buyer[$supporter][] = $buyer_supporter;
                        }
                    }
                }
            }

        }

        foreach ($users as $user) {
            $full_parent_user = explode(',', $user->full_parent);
            if (in_array($select_id, $full_parent_user) && $select_id != $user->user_id) {
                $list_buyer[$select_id][] = $user;
            }

        }
        return array(
            'list_account_buyer' => $list_buyer
        );
    }

    public static function getFilterAccount($params)
    {
        $data = array();
        $result = array();

        if (isset($params['search'])) {

            if (!empty($params['search'])) {

                $data = ElasticSearch::getSearchData(
                    array(
                        'all' => true,
                        'limit' => 1000,
                        'object' => 'users',
                        'search' => $params['search'],
                        'type' => 'account_filter',
                        'user_id' => $params['user_id'],
                        'network_id' => $params['network_id'],
                        'manager_id' => $params['manager_id'],
                        'list_user_id' => $params['list_user_id'],
                        'offset' => 0
                    )
                );
                $arr_user_search = [];
                foreach ($data as $data_user) {
                    $arr_user_search[$data_user['user_id']] = $data_user['user_id'];
                }
            }
        }

        $users = Model\User::getUsersSupported(array(
            'user_id' => $params['user_id'],
            'network_id' => $params['network_id'],
            //'name' => isset($params['search']) ? $params['search'] : '',
            'offset' => isset($params['page']) ? $params['page'] : 1,
            'limit' => LIMIT_ROWS
        ));

        //get manage info
        $user_manager = Model\ElasticSearch::getInfo('users', [$params['user_id']]);
        if (!empty($user_manager)) {
            $full_name = isset($user_manager[0]['full_name']) ? $user_manager[0]['full_name'] : '';
        }
        $list_support = array();
        $list_support_name = array();
        $list_buyer_name = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                //save user role
                $list_support[$user->supporter_id][] = (array)$user;
            }
            foreach ($users as $user) {
                if (!empty($arr_user_search)) {
                    if (isset($arr_user_search[$user->user_id])) {
                        if (isset($user->level_1) && $user->level_1 == 0) {
                            $manager_role = $user->role;
                        }
                        $has_child = false;
                        if (isset($list_support[$user->user_id])) {
                            $has_child = true;
                        }
                        if (isset($params['search'])) {
                            $result[$user->user_id] = array(
                                'user_id' => $user->user_id,
                                'full_name' => $user->full_name,
                                'has_child' => $has_child,
                                'role' => $user->role,
                                'roles' => array(
                                    'read' => true,
                                    'write' => true
                                ),
                                'supporter_id' => $user->supporter_id,
                                'full_parent' => $user->full_parent
                            );
                            //
                            if ($user->role == 2) {
                                $list_support_name[$user->user_id] = Utils::remove_accent($user->full_name);
                            } else {
                                $list_buyer_name[$user->user_id] = Utils::remove_accent($user->full_name);
                            }
                        } else {
                            $has_child = false;
                            if (isset($list_support[$user->user_id])) {
                                $has_child = true;
                            }
                            if ($user->level_1 == 1) {
                                $result[$user->user_id] = array(
                                    'user_id' => $user->user_id,
                                    'full_name' => $user->full_name,
                                    'has_child' => $has_child,
                                    'role' => $user->role,
                                    'roles' => array(
                                        'read' => true,
                                        'write' => true
                                    ),
                                    'full_parent' => $user->full_parent,
                                    'supporter_id' => isset($user->supporter_id) ? $user->supporter_id : ''
                                );
                                //
                                if ($user->role == 2) {
                                    $list_support_name[$user->user_id] = Utils::remove_accent($user->full_name);
                                } else {
                                    $list_buyer_name[$user->user_id] = Utils::remove_accent($user->full_name);
                                }
                            }
                        }
                    }

                } else {
                    if (isset($user->level_1) && $user->level_1 == 0) {
                        $manager_role = $user->role;
                    }
                    $has_child = false;
                    if (isset($list_support[$user->user_id])) {
                        $has_child = true;
                    }
                    if (isset($params['search'])) {
                        $result[$user->user_id] = array(
                            'user_id' => $user->user_id,
                            'full_name' => $user->full_name,
                            'has_child' => $has_child,
                            'role' => $user->role,
                            'roles' => array(
                                'read' => true,
                                'write' => true
                            ),
                            'supporter_id' => $user->supporter_id,
                            'full_parent' => $user->full_parent
                        );
                        //
                        if ($user->role == 2) {
                            $list_support_name[$user->user_id] = Utils::remove_accent($user->full_name);
                        } else {
                            $list_buyer_name[$user->user_id] = Utils::remove_accent($user->full_name);
                        }
                    } else {
                        $has_child = false;
                        if (isset($list_support[$user->user_id])) {
                            $has_child = true;
                        }
                        if ($user->level_1 == 1) {
                            $result[$user->user_id] = array(
                                'user_id' => $user->user_id,
                                'full_name' => $user->full_name,
                                'has_child' => $has_child,
                                'role' => $user->role,
                                'roles' => array(
                                    'read' => true,
                                    'write' => true
                                ),
                                'full_parent' => $user->full_parent,
                                'supporter_id' => isset($user->supporter_id) ? $user->supporter_id : ''
                            );
                            //
                            if ($user->role == 2) {
                                $list_support_name[$user->user_id] = Utils::remove_accent($user->full_name);
                            } else {
                                $list_buyer_name[$user->user_id] = Utils::remove_accent($user->full_name);
                            }
                        }
                    }
                }

            }
        }
        $parent = array(
            array(
                'user_id' => $params['user_id'],
                'full_name' => isset($full_name) ? $full_name : '',
                'has_child' => true,
                'root' => true,
                'role' => isset($manager_role) ? +$manager_role : 1,
                'roles' => array(
                    'read' => true,
                    'write' => true
                ),
                'full_parent' => ''
            )
        );

        $list_user_sorted = array();
        asort($list_support_name);
        asort($list_buyer_name);

        //sort support

        foreach ($list_support_name as $user_id => $user_name) {
            if (isset($result[$user_id])) {
                $list_user_sorted[] = $result[$user_id];
            }
        }

        //sort buyer
        foreach ($list_buyer_name as $user_id => $user_name) {
            if (isset($result[$user_id])) {
                $list_user_sorted[] = $result[$user_id];
            }
        }
        if (isset($params['manager']) && isset($parent) && isset($parent[0])) {
            $parent[0]['children'] = $list_user_sorted;
            $list_user_sorted = $parent;
        }

        if(!empty($params['search']) && empty($arr_user_search)){
            $list_user_sorted = [];
        }
        $data['account'] = $list_user_sorted;
        return $data;
    }

    public static function find_buyer_user(&$list_user, $user_account, &$account_buyer, $support_key)
    {
        foreach ($list_user as $k => $user) {
            if (isset($user->full_parent)) {
                $arr_parent = explode(',', $user->full_parent);
                if (!empty($arr_parent) && in_array($user_account->user_id, $arr_parent)) {
                    if ($user->user_id != $user_account->user_id) {
                        $account_buyer[$user_account->user_id][] = $user;
                    }
                    unset($list_user[$support_key]);
                } else {
                    self::find_buyer_user($list_user, $user, $account_buyer, $k);
                }
            }
        }
    }

    public static function getListUserRecent($params)
    {
        $redis = Nosql\Redis::getInstance('caching');
        $keyUserRecent = 'user:' . $params['user_id'] . ':network:' . $params['network_id'] . ':user_recent';
        $users_id = $redis->LRANGE($keyUserRecent, 0, -1);

        $result = array();
        if (!empty($users_id)) {
            //get data info elastic
            $rows = Model\ElasticSearch::getInfo('users', $users_id);

            $list_users = array();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $list_users[$row['user_id']] = $row;
                }
            }

            //
            foreach ($users_id as $user) {
                //get user role
                $keyUserRole = 'user:' . $user . ':network:' . $params['network_id'] . ':user_role';
                $user_role = $redis->LRANGE($keyUserRole, 0, -1);

                if (isset($list_users[$user])) {
                    $result[] = array(
                        'user_id' => $list_users[$user]['user_id'],
                        'conversion_id' => hash('crc32b', $user . $params['network_id']),
                        'full_name' => $list_users[$user]['full_name'],
                        'role' => isset($user_role[0]) ? $user_role[0] : 1
                    );
                }
            }
        }

        return $result;
    }

    public static function updateListUserRecent($params)
    {

        $redis = Nosql\Redis::getInstance('caching');
        $keyUserRecent = 'user:' . $params['manager_id'] . ':network:' . $params['network_id'] . ':user_recent';

        if ($params['manager_id'] != $params['user_id']) {
            $users_id = $redis->LRANGE($keyUserRecent, 0, -1);

            if (in_array($params['user_id'], $users_id)) {
                //remove and push
                $arr_user_id = array();

                for ($i = sizeof($users_id) - 1; $i >= 0; $i--) {
                    if ($users_id[$i] != $params['user_id']) {
                        $arr_user_id[] = $users_id[$i];
                    }
                }

                $arr_user_id[] = $params['user_id'];

                //
                if (!empty($arr_user_id)) {
                    //del key
                    $redis->del($keyUserRecent);

                    //
                    foreach ($arr_user_id as $user_id) {
                        $redis->LPUSH($keyUserRecent, $user_id);
                    }
                }

            } else {
                //
                if (!empty($users_id) && sizeof($users_id) >= 5) {
                    //remove first id
                    $redis->RPOP($keyUserRecent);
                }

                $redis->LPUSH($keyUserRecent, $params['user_id']);
            }

            //get full parent
            $users = Model\User::getUsersSupported(array(
                'user_id' => $params['manager_id'],
                'network_id' => $params['network_id']
            ));

            if (!empty($users)) {
                $full_parent = '';
                foreach ($users as $user) {
                    if ($user->user_id == $params['user_id']) {
                        $full_parent = $user->full_parent;
                    }
                }

                if ($full_parent) {
                    $list_full_parent = explode(',', $full_parent);
                    $list_parent_id = array();
                    if (!empty($list_full_parent)) {
                        foreach ($list_full_parent as $user_id) {
                            if ($user_id && $user_id != $params['user_id']) {
                                $list_parent_id[] = $user_id;
                            }
                        }
                    }

                    $full_parent_rows = Model\ElasticSearch::getInfo('users', $list_parent_id);

                    if (!empty($full_parent_rows)) {
                        foreach ($full_parent_rows as $index => $user_info) {
                            $full_parent_rows[$user_info['user_id']] = $user_info;
                            unset($full_parent_rows[$index]);
                        }
                    }

                    //
                    $result_list_parent = array();
                    if (!empty($list_parent_id)) {
                        foreach ($list_parent_id as $user_id) {
                            if (isset($full_parent_rows[$user_id])) {
                                $result_list_parent[] = array(
                                    'user_id' => $full_parent_rows[$user_id]['user_id'],
                                    'full_name' => $full_parent_rows[$user_id]['full_name']
                                );
                            }
                        }
                    }

                    $result['parent'] = $result_list_parent;
                }
            }

        }

        $result['recent'] = self::getListUserRecent(array(
            'user_id' => $params['manager_id'],
            'network_id' => $params['network_id']
        ));

        return $result;
    }

    public static function getListParent($params)
    {
        //get full parent
        $result = array();
        //user info
        $user_info = Model\ElasticSearch::getInfo('users', array($params['user_id']));
        if (!empty($user_info)) {
            $user = [];
            $user['user_id'] = $user_info[0]['user_id'];
            $user['full_name'] = $user_info[0]['full_name'];

            $redis = Nosql\Redis::getInstance('caching');
            $keyUserRole = 'user:' . $params['user_id'] . ':network:' . $params['network_id'] . ':user_role';
            $user_role = $redis->LRANGE($keyUserRole, 0, -1);
            if (!empty($user_role)) {
                $user['role'] = $user_role[0];
            }

            $result['user_info'] = $user;

        }

        $users = Model\User::getUsersSupported(array(
            'user_id' => $params['manager_id'],
            'network_id' => $params['network_id']
        ));

        if (!empty($users)) {
            $full_parent = '';
            foreach ($users as $user) {
                if ($user->user_id == $params['user_id']) {
                    $full_parent = $user->full_parent;
                }
            }

            if ($full_parent) {
                $list_full_parent = explode(',', $full_parent);
                $list_parent_id = array();
                if (!empty($list_full_parent)) {
                    foreach ($list_full_parent as $user_id) {
                        if ($user_id && $user_id != $params['user_id']) {
                            $list_parent_id[] = $user_id;
                        }
                    }
                }

                $full_parent_rows = Model\ElasticSearch::getInfo('users', $list_parent_id);

                if (!empty($full_parent_rows)) {
                    foreach ($full_parent_rows as $index => $user_info) {
                        $full_parent_rows[$user_info['user_id']] = $user_info;
                        unset($full_parent_rows[$index]);
                    }
                }

                //
                $result_list_parent = array();
                if (!empty($list_parent_id)) {
                    foreach ($list_parent_id as $user_id) {
                        if (isset($full_parent_rows[$user_id])) {
                            $result_list_parent[] = array(
                                'user_id' => +$full_parent_rows[$user_id]['user_id'],
                                'full_name' => $full_parent_rows[$user_id]['full_name']
                            );
                        }
                    }
                }

                $result['parent'] = $result_list_parent;
            }
        }

        return $result;
    }

    public static function getBalanceByUser($params = array())
    {
        $network_id = $params['network_id'];
        $user_id = $params['user_id'];
        $data = array(
            'balance' => 0,
            'spent' => 0,
            'remaining' => 0
        );
        //
        if (empty($network_id) || empty($user_id)) {
            return $data;
        }
        //
        $config = \ADX\Config::get('redis')['redis']['adapters'];
        //
        $redis_spent = new \Redis();
        $redis_balance = new \Redis();
        //
        $redis_spent->connect($config['log_budget']['host'], $config['log_budget']['port']);
        $redis_balance->connect($config['caching']['host'], $config['caching']['port']);
        //
        $balance = $redis_balance->hget('u_balance_t:' . $network_id . ':' . $user_id, 'balance');
        $spent = $redis_spent->hget('u_spent_t:' . $network_id . ':' . $user_id, 'spent');
        //
        $redis_spent->close();
        $redis_balance->close();
        //
        $balance = $balance ? $balance : 0;
        $spent = $spent ? $spent : 0;
        //
        $totalExpend = $spent;
        $totalDeposit = $balance;
        $totalRemain = $balance - $spent;
        $percent = 0;
        if ($totalDeposit != 0) {
            $percent = ceil($totalExpend / $totalDeposit * 100);
        }
        $data = array(
            'total_expend' => $totalExpend,
            'total_deposit' => $totalDeposit,
            'total_remain' => $totalRemain,
            'percent' => $percent,
        );
        return $data;
    }


}