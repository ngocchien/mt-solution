<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 03/07/2017
 * Time: 11:07
 */

namespace MT\Business;

use MT\Nosql;

class Common
{
    public static function checkToken($params){
        if(empty($params) || !is_array($params)){
            return false;
        }

        if(empty($params['token'])){
            return false;
        }

        $token = $params['token'];

        $redis = Nosql\Redis::getInstance('caching');
        $status = $redis->
        $total_daily = $redis->GET(self::KEY_TOTAL_DAILY_UPLOAD);

        if(empty($total_daily)){
            $total_daily = 0;
        }

        $key_config = floor($total_daily/100);

        return self::getConfigGoogle()[$key_config];
    }
}