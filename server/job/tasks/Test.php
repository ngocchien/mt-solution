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
                'UCj6B4cj9mB9JXWW65T4Sh4w'
            ];

            $google_config = \My\General::$google_config;
            $client = new \Google_Client();
            $client->setDeveloperKey($google_config['key']);

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);
            for ($i = 0; $i <= $total; $i++) {
                $token_page = null;
                for ($page = 0; $page <= 1000; $page++) {
                    if ($page == 0) {
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
                        $status = $redis->SET($id, General::getSlug($title));

                        if (!$status) {
                            continue;
                        }

                        //get info video
                        $url = 'http://www.youtube.com/get_video_info?&video_id=' . $id . '&asv=3&el=detailpage&hl=en_US';
                        $rp = General::crawler($url);
                        $thumbnail_url = $title = $url_encoded_fmt_stream_map = $type = $url = '';
                        parse_str($rp);
                        $my_formats_array = explode(',', $url_encoded_fmt_stream_map);

                        if (empty($url_encoded_fmt_stream_map)) {
                            continue;
                        }

                        $path = '/var/ydownload/' . General::getSlug($title) . '_' . $id . '.mp4 "';

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
                            $j++;
                        }
                        echo '<pre>';
                        print_r($avail_formats);
                        die();
                        exec('wget -O ' . $path . $avail_formats[0]['url'] . '"');

                        echo '123123';
                        die();

                    }
                }
            }
            Utils::writeLog($fileNameSuccess, $arrParam);
        } catch
        (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function uploadYt($params)
    {
        date_default_timezone_set('Asia/Saigon');
        $fileNameSuccess = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $fileNameError = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $arrParam = [
            'params_input' => $params
        ];
        try {
            $google_config = General::$google_config;
            $client = new \Google_Client();
            $client->setDeveloperKey($google_config['key']);

            $videoPath = $params['video_path'];

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);
            $snippet = new \Google_Service_YouTube_VideoSnippet();
            $snippet->setTitle($params['title']);
            $snippet->setDescription($params['description']);
            $snippet->setTags(array("tag1", "tag2"));
            $snippet->setCategoryId("22");

            //status
            $status = new \Google_Service_YouTube_VideoStatus();
            $status->privacyStatus = "public";

            //videos
            $video = new \Google_Service_YouTube_Video();
            $video->setSnippet($snippet);
            $video->setStatus($status);

            $chunkSizeBytes = 1 * 1024 * 1024;
            $client->setDefer(true);

            $insertRequest = $youtube->videos->insert("status,snippet", $video);

            // Create a MediaFileUpload object for resumable uploads.
            $media = new \Google_Http_MediaFileUpload(
                $client,
                $insertRequest,
                'video/*',
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($videoPath));

            $status = false;
            $handle = fopen($videoPath, "rb");
            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }

            fclose($handle);

            // If you want to make other calls after the file upload, set setDefer back to false
            $client->setDefer(false);

            echo '<pre>';
            print_r($status);
            echo '</pre>';
            die();

            Utils::writeLog($fileNameSuccess, $arrParam);
        } catch (\Exception $e) {
            echo '<pre>';
            print_r([
                'code' => $e->getCode(),
                'messages' => $e->getMessage()
            ]);
            echo '</pre>';
            die();
            Utils::writeLog($fileNameError, $arrParam);
        }
    }
}