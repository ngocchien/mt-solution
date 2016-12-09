<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\Business;

use ADX\Elastic;
use ADX\Exception;
use ADX\Model;
use ADX\Utils;
use ADX\Nosql;

class LockModule
{
    public static function getModule($params = array())
    {
//        $client = Elastic::getInstances('info_slave');
//
//        if (!isset($params['module_name']) || $params['module_name'] == '') {
//            return 0;
//        }
//
//        $must = array();
//        $must[] = array(
//            'term' => array(
//                'module_name_raw' => Utils::remove_accent($params['module_name'])
//            )
//        );
//
//        $body = [
//            'index' => 'lock_module',
//            'type' => 'data',
//            'body' => [
//                'from' => 0,
//                'size' => 100,
//                'query' => [
//                    'bool' => [
//                        'must' => $must
//                    ]
//                ]
//            ]
//        ];
//
//        $results = $client->search($body);
//
//        if (isset($results['hits']['hits']) && !empty($results['hits']['hits'])) {
//            foreach ($results['hits']['hits'] as $hits) {
//                $data[] = $hits['_source'];
//            }
//        }
        
        //
        if (!isset($params['module_name']) || $params['module_name'] == '') {
            return 0;
        }

        //
        $redis = Nosql\Redis::getInstance('caching');
        $key_redis = 'adx:v3:' . $params['module_name'];

        $module = $redis->HGETALL($key_redis);


        $user = Model\User::getNetworkUser($params);
        $operational_status = $user['OPERATIONAL_STATUS'];


        return array(
            'module' => $module,
            'user' => array(
                'status' => $operational_status
            ),
            'notify' => array(
                
            )
        );
    }

    public static function addLockModule($params = array())
    {
        //ES
        $client = Elastic::getInstances('info_slave');

        if (!isset($params['module_name']) || $params['module_name'] == '') {
            return 0;
        }

        //Mapping
        if (!$client->indices()->exists(array('index' => 'lock_module'))) {
            //mapping index
            Model\ElasticSearch::mapping(array(
                'object' => 'lock_module'
            ));
        }

        if (isset($params['from_date'])) {
            $params['from_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $params['from_date'])));
        }

        if (isset($params['to_date'])) {
            $params['to_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $params['to_date'])));
        }

        //
        $data = [
            'index' => 'lock_module',
            'type' => 'data',
            'id' => $params['module_name'],
            'body' => [
                'module_id' => $params['module_name'],
                'module_name' => $params['module_name'],
                'module_name_raw' => Utils::remove_accent($params['module_name']),
                'status' => 1,
                'from_date' => $params['from_date'],
                'to_date' => $params['to_date'],
            ]
        ];

        $result = $client->index($data);

        if ($result['_shards']['successful'] == 1) {
            //cache redis
            $redis = Nosql\Redis::getInstance('caching');
            $key_redis = 'adx:v3:' . $params['module_name'];

            $data_cache = array(
                'status' => 1,
                'from_date' => $params['from_date'],
                'to_date' => $params['to_date']
            );

            $redis->HMSET($key_redis, $data_cache);
        }


        return 1;
    }

    public static function deleteLockModule($params = array())
    {
        //ES
        $client = Elastic::getInstances('info_slave');

        if (!isset($params['module_name']) || $params['module_name'] == '') {
            return 0;
        }

        $data = [
            'index' => 'lock_module',
            'type' => 'data',
            'id' => Utils::remove_accent($params['module_name']),
        ];

        $result = $client->delete($data);

        if ($result['_shards']['successful'] == 1) {
            //cache redis
            $redis = Nosql\Redis::getInstance('caching');
            $key_redis = 'adx:v3:' . $params['module_name'];

            $redis->DELETE($key_redis);
        }

        return 1;
    }

    public static function updateLockModule($params = array())
    {
        //ES
        $client = Elastic::getInstances('info_slave');

        if (!isset($params['module_name']) || $params['module_name'] == '') {
            return 0;
        }

        if (isset($params['from_date'])) {
            $params['from_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $params['from_date'])));
        }

        if (isset($params['to_date'])) {
            $params['to_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $params['to_date'])));
        }

        $data['body'][] = [
            'index' => [
                '_index' => 'lock_module',
                '_type' => 'data',
                '_id' => Utils::remove_accent($params['module_name'])
            ]
        ];

        $data['body'][] = [
            'module_id' => $params['module_name'],
            'module_name' => $params['module_name'],
            'module_name_raw' => Utils::remove_accent($params['module_name']),
            'status' => $params['status'],
            'from_date' => $params['from_date'],
            'to_date' => $params['to_date']
        ];

        $result = $client->bulk($data);

        if ($result['items'][0]['index']['status'] == 200) {
            //cache redis
            $redis = Nosql\Redis::getInstance('caching');
            $key_redis = 'adx:v3:' . $params['module_name'];

            $data_cache = array(
                'status' => $params['status'],
                'from_date' => $params['from_date'],
                'to_date' => $params['to_date']
            );

            $redis->HMSET($key_redis, $data_cache);
        }

        return 1;
    }
}