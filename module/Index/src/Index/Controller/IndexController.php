<?php

namespace Index\Controller;

use MT\Search;
use MT\Controller\MyController;
use My\General;
use Zend\View\Model\JsonModel;
use MT\Business;
use MT\Model;

class IndexController extends MyController
{
    public function indexAction()
    {
        try{
            return [];
                //get info video
            $url = 'https://www.youtube.com/embed/?autoplay=0&docid=0B047lK_RsbdCU3JqZ3p1RElZTnc&partnerid=30&html5=1&controls=0&showinfo=0&rel=0&modestbranding=0&playsinline=1&enablejsapi=1&widgetid=1';

//            $id = '0B047lK_RsbdCU3JqZ3p1RElZTnc';
//            $url = 'http://www.youtube.com/get_video_info?&video_id=' . $id . '&asv=3&el=detailpage&hl=en_US';
            $rp = General::crawler($url);
            echo '<pre>';
            print_r($rp);
            echo '</pre>';
            die();
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


//            $google_config = General::$google_config_driver;
//            $client = new \Google_Client();
//            $client->setClientId($google_config['client_id']);
//            $client->setClientSecret($google_config['client_secret']);
//            $client->addScope("https://www.googleapis.com/auth/drive");
//            $client->setAccessType("offline");
//            $client->setApprovalPrompt("force");
//            $client->refreshToken($google_config['refresh_token']);
//            $new_token = $client->getAccessToken();
//            echo '<pre>';
//            print_r($new_token);
//            echo '</pre>';
//            die();


//            $client = new \Google_Client();
//            $client->setClientId($google_config['client_id']);
//            $client->setClientSecret($google_config['client_secret']);
//            $client->addScope(\Google_Service_Drive::DRIVE);
////            $client->useApplicationDefaultCredentials();
//
//            $service = new \Google_Service_Drive($client);
//
//            $fileId = '0B047lK_RsbdCU3JqZ3p1RElZTnc';
//            $content = $service->files->get($fileId, array("alt" => "media"));
//            echo '<pre>';
//            print_r($content);
//            echo '</pre>';
//            die();

//            $


//            $token = 'ya29.GluBBD7cmQE95gtnEcmJNTnzLwqJr6xUX8UoecFunoDmmdV6YihMLyqnAiSEVM8by_9y1IRpMo6hcnz4ZVceaBqXVlAtCu7rBY59SC5uitSK_BkfOzd3NgZsibEs';
//            $client->setAccessToken($token);
//            echo '<pre>';
//            print_r($client);
//            echo '</pre>';
//            die();

//            $driveService = new \Google_Service_Drive($client);

//            $response = $driveService->files->listFiles();
//            echo '<pre>';
//            print_r($response);
//            echo '</pre>';
//            die();

//            $fileId = '0B047lK_RsbdCU3JqZ3p1RElZTnc';
//            $response = $driveService->files->get($fileId);
//            echo '<pre>';
//            print_r($response);
//            echo '</pre>';
//            die();
//            echo '<pre>';
//            print_r($response->getBody()->getContents());
//            echo '</pre>';
//            die();

//            $fileId = '0B047lK_RsbdCU3JqZ3p1RElZTnc';
//            $response = $service->files->listFiles();
//            $response = $driveService->files->get($fileId, array(
//                'alt' => 'media' ));
//            $content = $response->getBody()->getContents();
//
//            echo '<pre>';
//            print_r($content);
//            echo '</pre>';
//            die();
//            $content = $response->getBody()->getContents();
//
//            echo '<pre>';
//            print_r($config);
//            echo '</pre>';
//            die();
//
//            echo '<pre>';
//            print_r($this->params()->fromRoute());
//            echo '</pre>';
//            die();
            $google_config = Model\Common::getConfigGoogle();
            $google_config = $google_config[0];
            $client = new \Google_Client();
            $client->setClientId($google_config['client_id']);
            $client->setClientSecret($google_config['client_secret']);
            $client->setAccessType("offline");
            $client->setApprovalPrompt("force");
            $client->refreshToken($google_config['refresh_token']);
            $new_token = $client->getAccessToken();


            echo '<pre>';
            print_r($new_token);
            echo '</pre>';
            die();
        }catch (\Exception $ex){
            echo '<pre>';
            print_r([
                $ex->getCode(),
                $ex->getMessage()
            ]);
            echo '</pre>';
            die();
        }
    }
}
