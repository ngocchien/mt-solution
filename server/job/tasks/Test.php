<?php
/**
 * Created by PhpStorm.
 * User: GiangBeo
 * Date: 11/24/16
 * Time: 9:33 AM
 */
namespace TASK;

use MT\Exception;
use MT\Utils;
use MT\Model;
use My\General;

class Test
{

    public function cloneYoutube($params)
    {
        date_default_timezone_set('Asia/Saigon');
        $fileNameSuccess = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $fileNameError = __CLASS__ . '_' . __FUNCTION__ . '_Error';
        $arrParam = [];
        try {
            $arr_channel = [

            ];

            $google_config = \My\General::$google_config;
            $client = new \Google_Client();
            $client->setDeveloperKey($google_config['key']);

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);
            $total = count($arr_channel);
            for ($i = 0; $i <= $total; $i++) {
                $token_page = null;
                for ($page = 0; $page <= 1000; $page++) {
                    if ($i == 0) {
                        $searchResponse = $youtube->search->listSearch(
                            'snippet', array(
                                'channelId' => $arr_channel[$i],
                                'maxResults' => 50
                            )
                        );
                    } else {
                        if (empty($token_page)) {
                            break;
                        }
                        $searchResponse = $youtube->search->listSearch(
                            'snippet', array(
                                'channelId' => $arr_channel[$i],
                                'maxResults' => 50,
                                'pageToken' => $token_page
                            )
                        );
                    }

                    if (empty($searchResponse) || empty($searchResponse->getItems())) {
                        break;
                    }

                    $token_page = $searchResponse->getNextPageToken();

                    foreach ($searchResponse->getItems() as $item) {
                        if (empty($item) || empty($item->getSnippet())) {
                            continue;
                        }
                        $id = $item->getId()->getVideoId();

                        if (empty($id)) {
                            continue;
                        }

                        $redis = \MT\Nosql\Redis::getInstance('caching');

                        if ($redis->GET($id)) {
                            continue;
                        }

                        //title video
                        $title = $item->getSnippet()->getTitle();
                        if (!$title) {
                            continue;
                        }

                        //Limit request token in day: time, count
//                        $status = $redis->SET($id, General::getSlug($title));
//
//                        if (!$status) {
//                            continue;
//                        }

                        //get info video
                        $url = 'http://www.youtube.com/get_video_info?&video_id=' . $id . '&asv=3&el=detailpage&hl=en_US';
                        $rp = General::crawler($url);
                        $thumbnail_url = $title = $url_encoded_fmt_stream_map = $type = $url = '';
                        parse_str($rp);
                        $my_formats_array = explode(',', $url_encoded_fmt_stream_map);

                        if (empty($url_encoded_fmt_stream_map)) {
                            continue;
                        }

                        $avail_formats[] = '';
                        $j = 0;
                        $ipbits = $ip = $itag = $sig = $quality = '';
                        $expire = time();
                        foreach ($my_formats_array as $format) {
                            parse_str($format);
                            $avail_formats[$j]['itag'] = $itag;
                            $avail_formats[$j]['quality'] = $quality;
                            $type = explode(';', $type);
                            $avail_formats[$j]['type'] = $type[0];
                            $avail_formats[$j]['url'] = urldecode($url) . '&signature=' . $sig;
                            parse_str(urldecode($url));
                            $avail_formats[$j]['expires'] = date("G:i:s T", $expire);
                            $avail_formats[$j]['ipbits'] = $ipbits;
                            $avail_formats[$j]['ip'] = $ip;
                            $i++;
                        }

                        exec('wget -O ' . General::getSlug($title) . '_' . $id . '.mp4 ' . $avail_formats[0]['url']);
                        $info = $avail_formats[0];

                    }
                }
            }


            echo '<pre>';
            print_r($searchResponse);
            echo '</pre>';
            die();

            Utils::writeLog($fileNameSuccess, $arrParam);
        } catch
        (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function refreshAudience($params)
    {
        date_default_timezone_set('Asia/Saigon');
        $fileNameSuccess = "Worker_Admin_Refresh_Audience_Success";
        $fileNameError = "Worker_Admin_Refresh_Audience_Error";
        $arrParam = [];
        try {
            $arrParam['Param'] = array(
                'time_interval' => $params['time_interval']
            );
            $all_network = Utils::autoReconnectionProcess(
                '\ADX\DAO\Job',
                'getAllNetWork',
                array(),
                $fileNameError
            );
            if ($all_network['error'] == 0 || empty($all_network['rows'])) {
                $arrParam['DB']['Message'] = 'Can not find any network';
                $arrParam['DB']['Error'] = $all_network;
                Utils::errorMessenger(__CLASS__, __FUNCTION__, __LINE__, $arrParam, $fileNameError);
                return;
            }
            foreach ($all_network['rows'] as $item) {
                $list_audience_running = Utils::autoReconnectionProcess(
                    '\ADX\DAO\Job',
                    'getAllAudienceRunning',
                    array(
                        'network_id' => $item['NETWORK_ID']
                    )
                );
                if ($list_audience_running['error'] == 0 || empty($list_audience_running['rows'])) {
                    $arrParam['DB']['Message'] = 'Can not get audience running of network ' . $item['NETWORK_ID'];
                    $arrParam['DB']['Error'] = $all_network;
                    Utils::errorMessenger(__CLASS__, __FUNCTION__, __LINE__, $arrParam, $fileNameError);
                } else {
                    unset($arrParam['DB']);
                    $total_refreshed = 0;
                    foreach ($list_audience_running['rows'] as $audience) {
                        $conversion_id = $audience['CONVERSION_ID'];
                        $audience_id = $audience['AUDIENCE_ID'];
                        $arrParam['AUDIENCE_UPDATED'][$item['NETWORK_ID']][$conversion_id][] = $audience_id;

                        $data = array(
                            'objId' => '',
                            'actorId' => '',
                            'actorType' => 'job_adx',
                            'data' => array(
                                'conversion_id' => $conversion_id,
                                'audience_id' => array($audience_id)
                            ),
                            'fromComponent' => 'back-end'
                        );

                        $location_name = isset($audience['COUNTRY_CODE']) ? $audience['COUNTRY_CODE'] : '';
                        //
                        PubSub::publish('pubsub_audience', 'audience', 'refresh', $data, $location_name);
                        //
                        PubSub::publish('pubsub_audiencev2', 'audience', 'refresh', $data, $location_name);

                        $total_refreshed += 1;

                    }
                    $arrParam['Audience_Updated'][] = array(
                        'network_id' => $item['NETWORK_ID'],
                        'total_updated' => $total_refreshed
                    );
                }

            }
            Utils::writeLog($fileNameSuccess, $arrParam);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}