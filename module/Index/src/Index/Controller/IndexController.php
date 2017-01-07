<?php

namespace Index\Controller;

use Admin\Controller\AbstractAdminRestController;
use MT\Search;
use Zend\View\Model\JsonModel;
use MT\Business;

class IndexController extends AbstractAdminRestController
{
    public function indexAction()
    {
        //client_secret=yuNS6kJUsU69NX7rPXRIrU4C&grant_type=refresh_token&refresh_token=1%2FyeOm41z-ONX4kdpghOUqprx_t3dCOGY9bNIiuG_HipLOvis2gCBMiGdKa1FHkWzL&client_id=305277173466-i7u7cmv0a7gqco2rj86a9p99jbokp9lq.apps.googleusercontent.com
        try {
            ECHO '<CENTER>HELLO MT-SOLUTION</CENTER>';
            die();
//            $google_config = \My\General::$google_config;
//            $client = new \Google_Client();
//            $client->setClientId($google_config['client_id']);
//            $client->setClientSecret($google_config['client_secret']);
//            $client->setAccessType("offline");
//            $client->setApprovalPrompt("force");
//            $client->refreshToken($google_config['refresh_token']);
//            $new_token = $client->getAccessToken();

            //set lại xuống redis
            //$redis = \MT\Nosql\Redis::getInstance('caching');
//            $redis->HMSET('token:youtube', 'access_token', $new_token['access_token']);

            echo '<pre>';
            print_r($redis);
            echo '</pre>';
            die();
        } catch (\Exception $ex) {
            echo '<pre>';
            print_r($ex->getMessage());
            echo '</pre>';
            die();
        }
        die('1111');
        try {
            $google_config = \My\General::$google_config;
            $client = new \Google_Client();
//            $client->setClientId($google_config['client_id']);
//            $client->setClientSecret($google_config['client_secret']);
            $client->setClientId('305277173466-i7u7cmv0a7gqco2rj86a9p99jbokp9lq.apps.googleusercontent.com');
            $client->setClientSecret('yuNS6kJUsU69NX7rPXRIrU4C');
            $client->setAccessType("offline");
            $client->setApprovalPrompt("force");

//            $url = 'https://www.googleapis.com/oauth2/v4/token?client_id=305277173466-i7u7cmv0a7gqco2rj86a9p99jbokp9lq.apps.googleusercontent.com&client_secret=yuNS6kJUsU69NX7rPXRIrU4C&refresh_token=1/5_0h3Lq9dvLz-OJzRUQgr1EKl-PqVghpAWmyGC6mi-bcJiCSHVXYxRy87EGyw1Wt&grant_type=refresh_token';
//            $rp = \My\General::crawler($url, '', [], [], 1);
//            echo '<pre>';
//            print_r($rp);
//            echo '</pre>';
//            die();

//            client_id=8819981768.apps.googleusercontent.com&
//            client_secret=your_client_secret&
//            refresh_token=1/6BMfW9j53gdGImsiyUH5kU5RsR4zwI9lUVX-tqf8JXQ&
//            grant_type=refresh_token

            //$config_token
            $config_token = \My\General::$youtube_tooken;
            $client->refreshToken('1/5_0h3Lq9dvLz-OJzRUQgr1EKl-PqVghpAWmyGC6mi-bcJiCSHVXYxRy87EGyw1Wt');
            $new_token = $client->getAccessToken();
            echo '<pre>';
            print_r($new_token);
            echo '</pre>';
            die();

            $configApi = \My\General::$google_config;

//            ya29.Ci-5A5UpmkCcpV6lI03FW518ctUk1bMAwS1NW8EifsUzI-T5Bmx8pma51u0kIJJHXg

            //token youtube
            $redis = \MT\Nosql\Redis::getInstance('caching');
            $redis->HMSET('token:youtube', 'access_token', 'ya29.Ci-5A5UpmkCcpV6lI03FW518ctUk1bMAwS1NW8EifsUzI-T5Bmx8pma51u0kIJJHXg');
//            echo '<pre>';
//            print_r($redis->HGET('token:youtube', 'access_token'));
//            echo '</pre>';
//            die();
            echo '<pre>';
            print_r($redis->HGET('token:youtube', 'access_token'));
            echo '</pre>';
            die();
            $refresh_token = $redis->HGET('token:youtube', 'refresh_token');

            $url = 'https://www.googleapis.com/oauth2/v4/token/';

//            client_id=8819981768.apps.googleusercontent.com&
//            client_secret=your_client_secret&
//            refresh_token=1/6BMfW9j53gdGImsiyUH5kU5RsR4zwI9lUVX-tqf8JXQ&
//            grant_type=refresh_token

//            $arr_data = [
//                'client_id' => $configApi['client_id'],
//                'client_secret' => $configApi['client_secret'],
//                'refresh_token' => $refresh_token,
//                'grant_type' => 'refresh_token'
//            ];

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

            $build_data_url = http_build_query($arr_data);

            $url .= '?' . $build_data_url;

            $headers[] = 'Content-Type: application/x-www-form-urlencoded';

            $response = \My\General::crawler($url, '', $headers, [], 1);
            echo '<pre>';
            print_r($response);
            echo '</pre>';
            die();
            if (empty($response)) {

            }

            $response = json_decode($response, true);

            //set lại xuống redis
            $redis->HMSET('token:youtube', 'access_token', $response['access_token']);

            \MT\Utils::writeLog($fileNameSuccess, $arrParam);


            $redis = \MT\Nosql\Redis::getInstance('caching');
//            $arrData = [
//                'access_token' => 'ya29.Ci-5A5gLX2anrCENLNye_DQhxikPm9gkN1bFreni6PZnXX5dGXhoT0Q-By07ZMWHKg',
//                'refresh_token' => '1/Cud1-EVnGQ4Jaj9LKoLd8Lzlvk42JU1TLP7s_t3AfYEVUXODO6nzUW0YIDtn8zMX'
//            ];
            $key = 'token:youtube';
            //$status = $redis->HMSET($key, $arrData);
            echo '<pre>';
            print_r($redis->HGET($key, 'access_token'));
            echo '</pre>';
            die();
        } catch (\Exception $exc) {
            echo '<pre>';
            print_r([
                'code' => $exc->getCode(),
                'messages' => $exc->getMessage()
            ]);
            echo '</pre>';
            die();
        }

    }

    public function index()
    {
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
