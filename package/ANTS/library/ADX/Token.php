<?php

namespace ADX;

class Token
{
    public static function validate($params)
    {
        $token_info = array(
            'valid' => false,
            'data' => array(
                'api' => array(),
                'app' => array()
            )
        );
        //
        if (!is_array($params)) {
            return $token_info;
        }
        //
        if (!isset($params['_token']) || empty($params['_token'])) {
            return $token_info;
        }
        //
        $checked = self::checked($params['_token']);
        //
        if (!$checked['valid']) {
            if (isset($checked['token_expire']) && $checked['token_expire']) {
                $token_info['token_expire'] = true;
            }
            return $token_info;
        }
        //
        $token_info['valid'] = true;
        //
        if (isset($checked['allow_access']) && !$checked['allow_access']) {
            $token_info['allow_access'] = false;
            //
            return $token_info;
        }
        //
        $token_info['data']['api'] = array(
            'manager_id' => $checked['manager_id'],
            'network_id' => $checked['network_id']
        );
        //
        return $token_info;
    }

    public static function checked($token)
    {
        $data = array(
            'valid' => false,
            'network_id' => '',
            'manager_id' => ''
        );
        //

        $type_token = 'app';
        if (php_sapi_name() != 'cli') {
            $type_token = 'user';
            //
            /*
             * $checked_hash_ogs = Hash(HTTP_HOST+HTTP_X_FORWARDED_FOR)//HTTP_USER_AGENT
             */
        }
        //
        $redis = Nosql\Redis::getInstance('caching');
        $key_token = JOB_PREFIX_BUYER . 'token:' . md5($token) . ':' . $type_token;
        $check_token_expire = $redis->HGETALL($key_token);
        //
        if (!$check_token_expire || empty($check_token_expire)) {
            //Token expire, require user login again.
            $data['token_expire'] = true;
            return $data;
        }
        //
        /*
         * if(php_sapi_name() != 'cli' && $check_token_expire['hash_ogs'] != $checked_hash_ogs){
         * return $data;
         * }
         */
        //
        if (!isset($check_token_expire['g2fa']) || $check_token_expire['g2fa'] == 1) {
            return $data;
        }
        //
        if (!isset($check_token_expire['role'])) {
            return $data;
        }
        //
        $data['valid'] = true;
        //
        $role = explode('|', $check_token_expire['role']);
        if (!in_array(TOKEN_ROLE, $role)) {
            $data['allow_access'] = false;
            return $data;
        }
        //
        $redis->EXPIRE($key_token, TIME_EXPIRE_TOKEN_API);
        //

        $data['manager_id'] = $check_token_expire['user_id'];
        $data['network_id'] = $check_token_expire['network_id'];

        //
        return $data;
    }
}