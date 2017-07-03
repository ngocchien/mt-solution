<?php

namespace MT;

use MT\Nosql;

class Token
{
    const PREFIX_CHARACTER_GENERAL = 'mt_solution';
    const KEY_TOKEN_REDIS = 'mt:token:';
    const KEY_TOKEN_EXPIRE = 21600;

    public static function validate($params)
    {
        $data = array(
            'status' => false,
            'messages' => '',
            'data' => []
        );

        if(empty($params['token'])){
            $data['messages'] = 'Params Input inValid';
            return $data;
        }

        $token = $params['token'];
        //
        $redis = Nosql\Redis::getInstance('caching');
        $key_token = self::buildKeyToken($token);
        $token = $redis->HGETALL($key_token);

        //
        if (empty($token)) {
            $data['messages'] = 'Token inValid or Expire';
            return $data;
        }

        //refresh expire
        $redis->EXPIRE($key_token, self::KEY_TOKEN_EXPIRE);

        $data = [
            'status' => true,
            'messages' => 'success',
            'data' => $token
        ];
        //
        return $data;
    }

    public static function create($params){
        $result = [
            'error' => true,
            'messages' => 'Params input inValid'
        ];

        if(empty($params) || !is_array($params)){
            return $result;
        }

        if(empty($params['user_id']) || empty($params['user_name']) || empty($params['full_name'])){
            return $result;
        }

        $token = md5(self::PREFIX_CHARACTER_GENERAL.':'.$params['user_id'].':'.time());
        $params = array_merge($params,[
            'token' => $token
        ]);
        $key = self::buildKeyToken($token);

        $redis = Nosql\Redis::getInstance('caching');
        $status = $redis->HMSET($key,$params);

        if(empty($status)){
            return [
                'error' => true,
                'messages' => 'Create Token Error'
            ];
        }

        return [
            'success' => true,
            'token' => $token,
            'messages' => 'success'
        ];
    }

    public static function delete($params){
        try{
            $result = [
                'error' => true,
                'messages' => 'Params input inValid'
            ];

            if(empty($params) || !is_array($params)){
                return $result;
            }

            if(empty($params['token'])){
                return $result;
            }

            $key = self::buildKeyToken($params['token']);

            $redis = Nosql\Redis::getInstance('caching');

            return $redis->DEL($key);
        }catch (Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r($exc->getMessage());
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }

    }

    public static function buildKeyToken($token){
        if(empty($token)){
            return false;
        }

        return self::KEY_TOKEN_REDIS.md5($token);
    }
}
