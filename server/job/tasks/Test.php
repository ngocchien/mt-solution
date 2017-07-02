<?php
/**
 * Created by PhpStorm.
 * User: GiangBeo
 * Date: 11/24/16
 * Time: 9:33 AM
 */
namespace TASK;

use My\General,
    MT,
    MT\Business,
    MT\Model;

class Test
{
    public function cloneYoutube()
    {
        date_default_timezone_set('Asia/Saigon');
        $fileNameSuccess = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $fileNameError = __CLASS__ . '_' . __FUNCTION__ . '_Error';
        $arrParam = [];
        try {
            $arr_channel = Business\Post::getChannel();

            $google_config = General::$google_config;
            $client = new \Google_Client();
            $client->setDeveloperKey($google_config['key']);

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);
            $limit = 10;

            foreach ($arr_channel as $cate_id => $channels){
                foreach ($channels as $channel_id){
                    for ($page = 0; $page <= 1000; $page++) {
                        if ($page == 0) {
                            $searchResponse = $youtube->search->listSearch(
                                'snippet', array(
                                    'channelId' => $channel_id,
                                    'maxResults' => $limit
                                )
                            );
                        } else {
                            if (empty($token_page)) {
                                break;
                            }
                            $searchResponse = $youtube->search->listSearch(
                                'snippet', array(
                                    'channelId' => $channel_id,
                                    'maxResults' => $limit,
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

                            //check exist
                            $result = Business\Post::get([
                                'source_id' => $id
                            ]);

                            if(!empty($result['rows'])){
                                continue;
                            }

                            //title video
                            $title = $item->getSnippet()->getTitle();

                            if (!$title) {
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

                            $path = DOWNLOAD_FOLDER . '/' . General::getSlug($title) . '_' . $id . '.mp4';

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

                            $cmd = 'wget -O ' . $path.' "'. $avail_formats[0]['url'] . '"';
                            exec($cmd);

                            MT\Utils::runJob(
                                'info',
                                'TASK\Test',
                                'uploadYt',
                                'doHighBackgroundTask',
                                'admin_upload',
                                [
                                    'title' => $title,
                                    'cate_id' => $cate_id,
                                    'path' => $path,
                                    'action' => __FUNCTION__,
                                    'source_id' => $item->getId()->getVideoId()
                                ]
                            );
                        }
                    }
                }
            }

            MT\Utils::writeLog($fileNameSuccess, $arrParam);
        } catch (\Exception $e) {
            if(APPLICATION_ENV != 'production'){
                echo '<pre>';
                print_r([
                    $e->getCode(),
                    $e->getMessage()
                ]);
                echo '</pre>';
                die();
            }

            $arrParam['exc'] = [
                'code' => $e->getCode(),
                'messages' => $e->getMessage()
            ];
            MT\Utils::writeLog($fileNameError, $arrParam);
        }
    }

    public function uploadYt($params)
    {
        date_default_timezone_set('Asia/Saigon');
        $fileNameSuccess = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $fileNameError = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $arrParam = [
            'Data' => $params
        ];
        try {
            $redis = MT\Nosql\Redis::getInstance('caching');
            $total_daily = $redis->GET(Model\Common::KEY_TOTAL_DAILY_UPLOAD);

            if(empty($total_daily)){
                $total_daily = 0;
            }

            if($total_daily > 1000){
                $arrParam['ERROR'] = 'Limited';
                MT\Utils::writeLog($fileNameError, $arrParam);
                return true;
            }

            $title = $params['title'];
            $description  = $title . Business\Category::getDescription($params['cate_id']);
            $tags = Business\Tag::getTag($params['cate_id']);
            $path = $params['path'];
            $cate_id = $params['cate_id'];
            $source_id = $params['source_id'];

            if(empty($path) || empty($title) || !file_exists($path) || empty($source_id)){
                $arrParam['ERROR'] = 'Params input inValid';
                MT\Utils::writeLog($fileNameError, $arrParam);
                return false;
            }

            //check exist
            $result = Business\Post::get([
                'source_id' => $source_id
            ]);

            if(!empty($result['rows'])){
                $arrParam['ERROR'] = 'Files exits';
                MT\Utils::writeLog($fileNameError, $arrParam);
                return false;
            }

            $google_config = Model\Common::changeConfigApi();
            $token = self::getToken($google_config);
            $client = new \Google_Client();
            $client->setClientId($google_config['client_id']);
            $client->setClientSecret($google_config['client_secret']);
            $client->setScopes('https://www.googleapis.com/auth/youtube');

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);
            $client->setAccessToken($token);

            //snippet
            $snippet = new \Google_Service_YouTube_VideoSnippet();
            $snippet->setTitle($title);
            $snippet->setDescription($description);
            $snippet->setTags($tags);
            $snippet->setCategoryId(Business\Category::mappingCate($cate_id));

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
            $media->setFileSize(filesize($params['path']));

            $status = false;
            $handle = fopen($path, "rb");
            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }

            fclose($handle);

            // If you want to make other calls after the file upload, set setDefer back to false
            $client->setDefer(false);

            if (!empty($status['id'])) {
                $id_db = Business\Post::create([
                    'source_id' => $source_id,
                    'my_id' => $status['id'],
                    'post_title' => $title,
                    'cate_id' => $cate_id
                ]);
                $arrParam['ID_DB'] = $id_db;
                @unlink($path);
            }

            $redis->SET(Model\Common::KEY_TOTAL_DAILY_UPLOAD, ($total_daily+1));

            MT\Utils::writeLog($fileNameSuccess, $arrParam);
        } catch (\Exception $e) {
            if(APPLICATION_ENV != 'production'){
                echo '<pre>';
                print_r([
                    $e->getCode(),
                    $e->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            $arrParam = [
                'code' => $e->getCode(),
                'messages' => $e->getMessage()
            ];
            MT\Utils::writeLog($fileNameError, $arrParam);
        }
    }

    public static function getToken($google_config){
        try{
            $redis = MT\Nosql\Redis::getInstance('caching');
            $token = $redis->HGETALL(General::KEY_ACCESS_TOKEN);

            if(empty($token) ||
                empty($token['key']) ||
                $token['key'] != $token['key'] ||
                ($token['created']+$token['expires_in']-time() < 600)){
                //config gg
                $client = new \Google_Client();
                $client->setClientId($google_config['client_id']);
                $client->setClientSecret($google_config['client_secret']);
                $client->setAccessType("offline");
                $client->setApprovalPrompt("force");
                $client->refreshToken($google_config['refresh_token']);
                $token = $client->getAccessToken();
                if(!empty($token['access_token'])){
                    $token = array_merge($token,$google_config);
                    $redis->HMSET(General::KEY_ACCESS_TOKEN, $token);
                }
            }
            return $token['access_token'];

        }catch (\Exception $ex) {
            echo '<pre>';
            print_r([
                $ex->getCode(),
                $ex->getMessage()
            ]);
            echo '</pre>';
            die();
        }
    }

    public function download($params)
    {
        date_default_timezone_set('Asia/Saigon');
        $fileNameSuccess = __CLASS__ . '_' . __FUNCTION__ . '_Success';
        $fileNameError = __CLASS__ . '_' . __FUNCTION__ . '_Error';
        $arrParam = [];
        $arrParam['Data'] = $params;
        try {

            if(empty($params['cate_id'])){
                $arrParam['Error'] = 'Empty cate_id';
                MT\Utils::writeLog($fileNameError, $arrParam);
                return true;
            }
            $cate_id = $params['cate_id'];
            $arr_channel = Business\Post::getChannel($cate_id);

            if(empty($arr_channel)){
                $arrParam['Error'] = 'Empty Channel';
                MT\Utils::writeLog($fileNameError, $arrParam);
                return true;
            }

            $google_config = General::$google_config;
            $client = new \Google_Client();
            $client->setDeveloperKey($google_config['key']);

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);
            $limit = 50;
            $order = 'date';

            $redis = MT\Nosql\Redis::getInstance('caching');
            $total_daily = $redis->GET(Model\Common::KEY_TOTAL_DAILY_DOWNLOAD);

            if(empty($total_daily)){
                $total_daily = 0;
            }

            if($total_daily >= 1000){
                $arrParam['Error'] = 'Full total daily';
                MT\Utils::writeLog($fileNameSuccess, $arrParam);
                return true;
            }

            foreach ($arr_channel as $channel_id){
                $token_page = '';
                for ($page = 0; $page <= 1000; $page++) {
                    if ($page == 0) {
                        $searchResponse = $youtube->search->listSearch(
                            'snippet', array(
                                'channelId' => $channel_id,
                                'maxResults' => $limit,
                                'order' => $order
                            )
                        );
                    } else {
                        if (empty($token_page)) {
                            break;
                        }
                        $searchResponse = $youtube->search->listSearch(
                            'snippet', array(
                                'channelId' => $channel_id,
                                'maxResults' => $limit,
                                'order' => $order,
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

                        //check exist
                        $result = Business\Post::get([
                            'source_id' => $id
                        ]);

                        if(!empty($result['rows'])){
                            continue;
                        }

                        //title video
                        $title = $item->getSnippet()->getTitle();

                        if (!$title) {
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

                        $path = DOWNLOAD_FOLDER . '/' . General::getSlug($title) . '_' . $id . '.mp4';

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

                        $cmd = 'wget -O ' . $path.' "'. $avail_formats[0]['url'] . '"';
                        exec($cmd);

                        MT\Utils::runJob(
                            'info',
                            'TASK\Test',
                            'uploadYt',
                            'doHighBackgroundTask',
                            'admin_upload',
                            [
                                'title' => $title,
                                'cate_id' => $cate_id,
                                'path' => $path,
                                'action' => __FUNCTION__,
                                'source_id' => $item->getId()->getVideoId()
                            ]
                        );

                        $total_daily +=1;
                        if($total_daily >= 1000){
                            $arrParam['Error'] = 'Full total daily';
                            $arrParam['LINE'] = __LINE__;
                            MT\Utils::writeLog($fileNameSuccess, $arrParam);
                            return true;
                        }
                    }
                }
            }

            $redis->SET(Model\Common::KEY_TOTAL_DAILY_DOWNLOAD, $total_daily);

            sleep(60);
            MT\Utils::runJob(
                'info',
                'TASK\Test',
                'download',
                'doHighBackgroundTask',
                'admin_process',
                array(
                    'actor' => __FUNCTION__,
                    'cate_id' => $cate_id + 1
                )
            );

            MT\Utils::writeLog($fileNameSuccess, $arrParam);
        } catch (\Exception $e) {
            if(APPLICATION_ENV != 'production'){
                echo '<pre>';
                print_r([
                    $e->getCode(),
                    $e->getMessage()
                ]);
                echo '</pre>';
                die();
            }

            $arrParam['exc'] = [
                'code' => $e->getCode(),
                'messages' => $e->getMessage()
            ];
            MT\Utils::writeLog($fileNameError, $arrParam);
        }
    }
}