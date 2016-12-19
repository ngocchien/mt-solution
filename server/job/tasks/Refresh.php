<?php
/**
 * Created by PhpStorm.
 * User: GiangBeo
 * Date: 11/24/16
 * Time: 9:33 AM
 */
namespace TASK;

use MT\Model,
    My\General;

class Refresh
{
    public function tokenYoutube($params)
    {
        date_default_timezone_set('Asia/Saigon');
        $fileNameSuccess = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $fileNameError = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $arrParam = [
            'params_input' => $params
        ];
        try {
            //config app
            $configApi = \My\General::$google_config;

            //token youtube
            $redis = \MT\Nosql\Redis::getInstance('caching');
            $refresh_token = $redis->HGET('token:youtube', 'refresh_token');

            $url = 'https://www.googleapis.com/oauth2/v4/token';

//            client_id=8819981768.apps.googleusercontent.com&
//            client_secret=your_client_secret&
//            refresh_token=1/6BMfW9j53gdGImsiyUH5kU5RsR4zwI9lUVX-tqf8JXQ&
//            grant_type=refresh_token

            $arr_data = [
                'client_id' => $configApi['client_id'],
                'client_secret' => $configApi['client_secret'],
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            ];
            echo '<pre>';
            print_r($arr_data);
            echo '</pre>';
            die();
            $response = General::crawler($url, '', [], $arr_data);
            if (empty($response)) {

            }

            $response = json_decode($response, true);

            //set lại xuống redis
            $redis->HMSET('token:youtube', 'access_token', $response['access_token']);

            \MT\Utils::writeLog($fileNameSuccess, $arrParam);

        } catch (\Exception $e) {
            $arrParam['exc'] = [
                'code' => $e->getCode(),
                'messages' => $e->getMessage()
            ];
            \MT\Utils::writeLog($fileNameError, $arrParam);
        }
    }
}