<?php

namespace Index\Controller;

use Facebook\Facebook;
use MT\Controller\MyController;
use My\General;
use MT\Model;
use Zend\Config\Reader\Xml;
use MT\Business;
use Zend\View\Renderer\PhpRenderer;

class IndexController extends MyController
{
    function locheader($page)
    {
        $temp = explode("\r\n", $page);
        foreach ($temp as $item) {
            $temp2 = explode(": ", $item);
            $infoheader[$temp2[0]] = $temp2[1];
        }
        $location = $infoheader['Location'];
        return $location;
    }

    public function indexAction()
    {
        try {
            echo '<pre>';
            print_r(date("M d, Y H:i:s", '1522144786'));
            echo '</pre>';
            die();
            echo '<pre>';
            print_r(date('Y-m-d H:i:s', 1522144786));
            echo '</pre>';
            die();

            echo '<pre>';
            print_r('abc');
            echo '</pre>';
            die();

            $row['hour'] = '2018-02-23 18';
            list($day, $hour) = explode(' ', $row['hour']);
            list($year, $month, $day) = explode('-',$day);
            $time = mktime($hour, 0, 0 , $month, $day, $year);

//            if (!is_numeric($row['hour'])) {
//                $row['hour'] = strtotime($row['hour']);
//            }
//            echo '<pre>';
//            print_r($row);
//            echo '</pre>';
//            die();
            $row['hour'] = date("g:i A M d, Y", $time);
            echo '<pre>';
            print_r($row);
            echo '</pre>';
            die();

            $year = 2018;
            $week = 9 - 1;

            $date = new \DateTime();
            $date->setISODate($year,$week); //year , week num , day
            echo $date->format('d-m-Y');
            die();

            $time = strtotime("1 January $year", time());
            $day = date('w', $time);
            $time += ((7 * $week) + 1 - $day) * 24 * 3600;
            echo '<pre>';
            print_r(date('Y-m-d', $time));
            echo '</pre>';
            die();
            echo '<pre>';
            print_r($time);
            echo '</pre>';
            die();
            $return[0] = date('Y-n-j', $time);
            $time += 6 * 24 * 3600;
            echo '<pre>';
            print_r($time);
            echo '</pre>';
            die();
            $return[1] = date('Y-n-j', $time);
            echo '<pre>';
            print_r($return);
            echo '</pre>';
            die();
            return $return;


            echo strtotime('2018-02-08 11:13:20') * 1000;
            echo "\n";
            echo strtotime('2018-02-08 11:13:25') * 1000;
            die();

            $unix_from_date = strtotime('2018-01-21 00:00:00');
            $unix_to_date = strtotime('2018-01-21 23:59:59');

            echo '<pre>';
            print_r([
                date('d/m/Y H', $unix_from_date),
                date('d/m/Y', $unix_to_date)
            ]);
            echo '</pre>';
            die();

//            2018-01-12 14
            $value = '2018-01-12 14';
            list($date, $hour) = explode(' ', $value);

            $time = strtotime($date) + ($hour * 60 * 60);
            echo '<pre>';
            print_r([
                $time,
                date('Y-m-d H:i:s', $time)
            ]);
            echo '</pre>';
            die();
//            strtotime($date)
            echo '<pre>';
            print_r([
                $date, $hour
            ]);
            echo '</pre>';
            die();
            echo '<pre>';
            print_r(strlen('Q4 2017'));
            echo '</pre>';
            die();

            $a_1 = ['chien'];
            $a_2 = [1, 'chien'];
            $e = array_intersect($a_1, $a_2);
            echo '<pre>';
            print_r($e);
            echo '</pre>';
            die();

//            $a = [];
//            $b = [2];
//            $a = array_merge($a, $b);
//            echo '<pre>';
//            print_r($a);
//            echo '</pre>';
//            die();
//            echo '<pre>';
////            print_r(array_merge()$b);
//            echo '</pre>';
//            die();

            $arr_creative_id = [];
            foreach ($arr_json as $json) {
                $data = json_decode($json, true);

//                if()
                $arr_creative_id = array_merge($arr_creative_id, array_values(array_unique($data['arr_object_id'])));
            }
            echo '<pre>';
            print_r(implode(',', $arr_creative_id));
            echo '</pre>';
            die();
            echo '<pre>';
            print_r($arr_creative_id);
            echo '</pre>';
            die();


            $email = 'not set';
            $domain = strstr($email, 's');
            echo '<pre>';
            print_r($domain);
            echo '</pre>';
            die();
            $this->renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRnderer');
            $this->renderer->headTitle('chiennnnnn');
//            $this->renderer->headTitle('chiennnnnn');
//            $this->renderer->headLink(array('rel' => 'image_src', 'href' => 'http://chiennn.me'));
            return;

            $doc = new \DOMDocument();
            $doc->loadHTMLFile('https://www.thegioididong.com/');

            $xpath = new \DOMXpath($doc);
//            $elements = $xpath->query("//*[@id]");
//            $doc->loadHTMLFile($url)

            $elements = $xpath->query("*/body[@id='back-top']");

            if (!is_null($elements)) {
                foreach ($elements as $element) {
                    echo "<br/>[" . $element->nodeName . "]";

                    $nodes = $element->childNodes;
                    foreach ($nodes as $node) {
                        echo $node->nodeValue . "\n";
                    }
                }
            }

            exit();

            echo '<pre>';
            print_r(date('d-m-Y H:i:s', 1524502800));
            echo '</pre>';
            die();
            echo '<pre>';
            print_r(date('d-m-Y', 1514826000));
            echo '</pre>';
            die();
//            return [];
//            echo '<pre>';
//            print_r(opcache_get_configuration());
//            echo '</pre>';
//            die();
            echo '<pre>';
            print_r(opcache_get_status());
            echo '</pre>';
            die();

            $t = '9';

//            $t = (float) $t;
//
//                echo '<pre>';
//                print_r($t);
//                echo '</pre>';
//                die();
//            echo '<pre>';
//            print_r(is_numeric($t));
//            echo '</pre>';
//            die();

//            echo '<pre>';
//            print_r(date('d-m-Y', 1506790800));
//            echo '</pre>';
//            die();
//            $str = 'Oct 2017';
//
//            $str = strtotime($str);
//            echo '<pre>';
//            print_r($str);
//            echo '</pre>';
//            die();

            $str = 'Q4 2017';
            $str = str_replace('Q', null, $str);
            list($quarter, $year) = explode(' ', $str);
            $month = 1;

            switch ($quarter) {
                case 1:
                    $month = 1;
                    break;
                case 2:
                    $month = 4;
                    break;
                case 3:
                    $month = 7;
                    break;
                case 4:
                    $month = 10;
                    break;
            }

            $str = $year . '-' . $month . '-01';
            $str = strtotime($str);

            echo '<pre>';
            print_r(date('d-m-Y', $str));
            echo '</pre>';
            die();
            echo '<pre>';
            print_r($str);
            echo '</pre>';
            die();

            echo '<pre>';
            print_r([
                $quarter,
                $year
            ]);
            echo '</pre>';
            die();
            $arr_fid_id = [
                '', 2, 2, 1, 4
            ];
            echo '<pre>';
            print_r(array_filter($arr_fid_id));
            echo '</pre>';
            die();
            $params = array_merge($this->params()->fromQuery(), $this->params()->fromRoute());

            $result = Business\Debug::metricAction($params);
            return $result;

            echo '<pre>';
            print_r([
                date('d-m-Y H:i:s', 1512925200),
                date('d-m-Y H:i:s', 1513011599)

            ]);
            echo '</pre>';
            die();


            $result = '{"status":"success","message":"Query success","result":[{"date":1508173200,"total_impression":427.0,"impression":305.0,"click":50.0,"total_click":78.0,"campaign_id":516342768},{"date":1508173200,"total_impression":427.0,"impression":48.0,"click":12.0,"total_click":78.0,"campaign_id":516342816},{"date":1508173200,"total_impression":427.0,"impression":31.0,"click":4.0,"total_click":78.0,"campaign_id":516336448},{"date":1508432400,"total_impression":427.0,"impression":4.0,"click":4.0,"total_click":78.0,"campaign_id":516341823},{"date":1508691600,"total_impression":427.0,"impression":2.0,"click":3.0,"total_click":78.0,"campaign_id":516341823},{"date":1508778000,"total_impression":427.0,"impression":2.0,"click":2.0,"total_click":78.0,"campaign_id":516341823},{"date":1509382800,"total_impression":427.0,"impression":0.0,"click":2.0,"total_click":78.0,"campaign_id":516341823},{"date":1508950800,"total_impression":427.0,"impression":6.0,"click":1.0,"total_click":78.0,"campaign_id":516341823},{"date":1508259600,"total_impression":427.0,"impression":11.0,"click":0.0,"total_click":78.0,"campaign_id":516342768},{"date":1508173200,"total_impression":427.0,"impression":8.0,"click":0.0,"total_click":78.0,"campaign_id":516341823},{"date":1508173200,"total_impression":427.0,"impression":2.0,"click":0.0,"total_click":78.0,"campaign_id":516337548},{"date":1508173200,"total_impression":427.0,"impression":0.0,"click":0.0,"total_click":78.0,"campaign_id":516341549},{"date":1508259600,"total_impression":427.0,"impression":2.0,"click":0.0,"total_click":78.0,"campaign_id":516342816},{"date":1508173200,"total_impression":427.0,"impression":2.0,"click":0.0,"total_click":78.0,"campaign_id":516332452},{"date":1508173200,"total_impression":427.0,"impression":3.0,"click":0.0,"total_click":78.0,"campaign_id":516341311},{"date":1508259600,"total_impression":427.0,"impression":1.0,"click":0.0,"total_click":78.0,"campaign_id":516336448},{"date":1508173200,"total_impression":427.0,"impression":0.0,"click":0.0,"total_click":78.0,"campaign_id":516341764}],"total_row":17}';
            $result = json_decode($result, true);

            $data_format = [];
            foreach ($result['result'] as $item) {
                $data_format[$item['date']][$item['campaign_id']] = $item;
            }

            $object = array_keys($data_format);

            $key_max_count = 0;
            foreach ($data_format as $time => $value) {
                if (count($value) > $key_max_count) {
                    $key_max_count = $time;
                }
            }

            $arr_segment_id = [];
            foreach ($data_format[$key_max_count] as $item) {
                $arr_segment_id[] = $item['campaign_id'];
            }
            $result = [];
            //data format =

            $arr_data = [];
            foreach ($data_format as $item) {
                foreach ($arr_segment_id as $segment_id) {
                    if (isset($item[$segment_id])) {
                        $arr_data[$segment_id]['click'][] = $item[$segment_id]['click'];
                        $arr_data[$segment_id]['impression'][] = $item[$segment_id]['impression'];
                    } else {
                        $arr_data[$segment_id]['click'][] = 0;
                        $arr_data[$segment_id]['impression'][] = 0;
                    }
                }
            }

            $value = [];
            foreach ($arr_data as $segment => $data) {
                foreach ($data as $metric => $data_metric) {
                    $value[] = [
                        'name' => $metric,
                        'header' => $segment,
                        'data' => $data_metric,
                        'unit' => $metric
                    ];
                }
            }

            $result = [
                'object' => $object,
                'value' => $value
            ];

//            foreach ()

            echo '<pre>';
            print_r($result);
            echo '</pre>';
            die();

            echo '<pre>';
            print_r(current($data_format));
            echo '</pre>';
            die();
            $object = [
                'data' => array_keys($data_format)
            ];

            $value = [];

            echo '<pre>';
            print_r($data_format);
            echo '</pre>';
            die();

            echo '<pre>';
            print_r(date('l d-m-Y', 1508691600));
            echo '</pre>';
            die();

            $reader = new Xml();
            $path = STATIC_PATH . '/temp.xml';
            $data = $reader->fromFile($path);
//            echo '<pre>';
//            print_r($data['channel']['item']);
//            echo '</pre>';
//            die();

            $t = $data['channel']['item'][0]['description'];
//            echo '<pre>';
//            print_r(explode(': ', $t));
//            echo '</pre>';
//            die();

//            echo preg_replace('/^[(.*?)]$/', '', $t);
            preg_match_all('/\[(.*?)\]/', $t, $matches);
            echo '<pre>';
            print_r($matches);
            echo '</pre>';
            die();
            echo '<pre>';
            print_r();
            echo '</pre>';
            exit;
            echo $t = preg_replace('/\[(.*?)\]/', '----', $t);

            echo '<pre>';
            print_r(explode('----', $t));
            echo '</pre>';
            die();
//            echo preg_replace('/(.*?):/', '', $t);
            exit();

            $partern = '/^\s\S\:$/';

            echo preg_replace($partern, '', $t);
            exit();
            $x = preg_replace($partern, '', $t);
            echo '<pre>';
            print_r($x);
            echo '</pre>';
            die();
            echo '<pre>';
            print_r(explode(': ', $t));
            echo '</pre>';
            die();

            $id = '0B047lK_RsbdCU3JqZ3p1RElZTnc';
//            $html = str_get_html($page);
//            $link = urldecode(trim($html->find('a[id=uc-download-link]',0)->href));
            $link = urldecode('/uc?export=download&confirm=lx0Y&id=0B047lK_RsbdCU3JqZ3p1RElZTnc');
            $tmp = explode("confirm=", $link);
            $tmp2 = explode("&", $tmp[1]);
            $confirm = $tmp2[0];
            $linkdowngoc = "https://drive.google.com/uc?export=download&id=$id&confirm=$confirm";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $linkdowngoc);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . "/google.mp3");
            curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . "/google.mp3");

            $page = curl_exec($ch);
//            echo '<pre>';
//            print_r($page);
//            echo '</pre>';
//            die();
            $get = $this->locheader($page);
            echo '<pre>';
            print_r($get);
            echo '</pre>';
            die();

            $frames = json_decode($json, true);

//            $arr_id = [];
//            foreach ($frames as $frame){
//                foreach ($frame['items'] as $item){
//                    if(!in_array($item['id'], $arr_id)){
//                        $arr_id[] = $item['id'];
//                    }
//                }
//            }
//            echo '<pre>';
//            print_r($arr_id);
//            echo '</pre>';
//            die();

//            $data_rp = [];
            $data = [];
            foreach ($frames as $frame) {
                foreach ($frame['items'] as $item) {
                    $data[$item['id']]['items'][] = [
                        $frame['date_time'], $item['metrics']['pageviews']
                    ];
                    $data[$item['id']]['id'] = $item['id'];
                }
            }

            echo '<pre>';
            print_r(count(array_keys($data)));
            echo '</pre>';
            die();

            echo '<pre>';
            print_r($data);
            echo '</pre>';
            die();

//            foreach (){
//
//            }

            $time = time();
            $data_rp = [];
            for ($j = 10; $j > 0; $j--) {
                $data = [
                    'id' => $j,
                    'name' => 'facebook.com',
                ];
                for ($i = 60; $i >= 1; $i--) {
                    $items = [
                        $time - ($i * 5),
                        rand($j * 10, ($j + 1) * 10),

                    ];
                    $data['data'][] = $items;
                }
                $data_rp[] = $data;
            }
            echo '<pre>';
            print_r(json_encode([
                'code' => 200,
                'status' => 'success',
                'data' => $data_rp
            ]));
            echo '</pre>';
            die();

            return [
                'code' => 200,
                'status' => 'success',
                'data' => $data_mapping
            ];

            echo '<pre>';
            print_r(json_decode($data_rp, true));
            echo '</pre>';
            die();
            return $data_rp;


            $time = time();

            $data_rp = [];
            for ($i = 60; $i >= 1; $i--) {
                $data = [
                    'time' => $time - ($i * 5),
                    'title' => date('d/m/Y', $time - ($i * 5)) . ' - Visitor (UTM Source)',
                    'items' => [
                        [
                            'id' => 1,
                            'name' => 'facebook.com',
                            'value' => rand(10, 20)
                        ],
                        [
                            'id' => 2,
                            'name' => 'google.com',
                            'value' => rand(1, 9)
                        ]
                    ]
                ];
                $data_rp[] = $data;
            }
            echo '<pre>';
            print_r(json_encode($data_rp));
            echo '</pre>';
            die();


//
//            $str = 'https%3A%2F%2Fr5---sn-i3beln7z.c.mail.google.com%2Fvideoplayback%3Fid%3D947e2da453ef37b0%26itag%3D35%26source%3Dwebdrive%26requiressl%3Dyes%26mm%3D30%26mn%3Dsn-i3beln7z%26ms%3Dnxu%26mv%3Dm%26pl%3D24%26ttl%3Dtransient%26ei%3DU9bIWYGcOcmRuwKRqqWwCw%26driveid%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%26app%3Dexplorer%26mime%3Dvideo%2Fx-flv%26gir%3Dyes%26clen%3D1362425%26dur%3D13%26lmt%3D1478861693212037%26mt%3D1506334212%26ip%3D113.161.38.181%26ipbits%3D0%26expire%3D1506337891%26cp%3DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%26sparams%3Dip%2Cipbits%2Cexpire%2Cid%2Citag%2Csource%2Crequiressl%2Cmm%2Cmn%2Cms%2Cmv%2Cpl%2Cttl%2Cei%2Cdriveid%2Capp%2Cmime%2Cgir%2Cclen%2Cdur%2Clmt%2Ccp%26signature%3DB54515EA912DF21B9773523DF094F2B6F5F5A973.25C3D94084B2A844D6A39E6AB2A7846C3C044961%26key%3Dck2';
//            echo '<pre>';
//            print_r(urldecode($str));
//            echo '</pre>';
//            die();
//
//            $str = 'status=ok&hl=vi&allow_embed=0&ps=docs&partnerid=30&autoplay=0&docid=0ByaRd0R0Qyatcmw2dVhQS0NDU0U&abd=0&public=true&el=embed&title=ea354bff8db4126eeaf3f8dff2567705&iurl=https%3A%2F%2Fdocs.google.com%2Fvt%3Fauthuser%3D0%26id%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%26s%3DAMedNnoAAAAAWcjyc2-RrBzqrNPsYtxAODgWqQakACRN&cc3_module=https%3A%2F%2Fs.ytimg.com%2Fyt%2Fswfbin%2Fsubtitles3_module.swf&ttsurl=https%3A%2F%2Fdocs.google.com%2Ftimedtext%3Fauthuser%3D0%26id%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%26vid%3D947e2da453ef37b0&reportabuseurl=https%3A%2F%2Fdocs.google.com%2Fabuse%3Fauthuser%3D0%26id%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U&token=1&plid=V0QU55MOVZVMKQ&fmt_stream_map=18%7Chttps%3A%2F%2Fr5---sn-i3beln7z.c.mail.google.com%2Fvideoplayback%3Fid%3D947e2da453ef37b0%26itag%3D18%26source%3Dwebdrive%26requiressl%3Dyes%26mm%3D30%26mn%3Dsn-i3beln7z%26ms%3Dnxu%26mv%3Dm%26pl%3D24%26ttl%3Dtransient%26ei%3DU9bIWYGcOcmRuwKRqqWwCw%26driveid%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%26app%3Dexplorer%26mime%3Dvideo%2Fmp4%26lmt%3D1478861408556243%26mt%3D1506334212%26ip%3D113.161.38.181%26ipbits%3D0%26expire%3D1506337891%26cp%3DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%26sparams%3Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Clmt%252Ccp%26signature%3D86526822049B539A90E7304847D68A662110D58B.32F6652397D8E2FB08A73558E2C636A9B66D56EF%26key%3Dck2%2C22%7Chttps%3A%2F%2Fr5---sn-i3beln7z.c.mail.google.com%2Fvideoplayback%3Fid%3D947e2da453ef37b0%26itag%3D22%26source%3Dwebdrive%26requiressl%3Dyes%26mm%3D30%26mn%3Dsn-i3beln7z%26ms%3Dnxu%26mv%3Dm%26pl%3D24%26ttl%3Dtransient%26ei%3DU9bIWYGcOcmRuwKRqqWwCw%26driveid%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%26app%3Dexplorer%26mime%3Dvideo%2Fmp4%26lmt%3D1478861692910874%26mt%3D1506334212%26ip%3D113.161.38.181%26ipbits%3D0%26expire%3D1506337891%26cp%3DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%26sparams%3Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Clmt%252Ccp%26signature%3DB37BE6E8CBDDE245AF206C9CDC3D73DE7D3594CF.55B50234A2EFA1F7722CBBF1A6A1E6CBC9A53503%26key%3Dck2%2C34%7Chttps%3A%2F%2Fr5---sn-i3beln7z.c.mail.google.com%2Fvideoplayback%3Fid%3D947e2da453ef37b0%26itag%3D34%26source%3Dwebdrive%26requiressl%3Dyes%26mm%3D30%26mn%3Dsn-i3beln7z%26ms%3Dnxu%26mv%3Dm%26pl%3D24%26ttl%3Dtransient%26ei%3DU9bIWYGcOcmRuwKRqqWwCw%26driveid%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%26app%3Dexplorer%26mime%3Dvideo%2Fx-flv%26gir%3Dyes%26clen%3D974217%26dur%3D13%26lmt%3D1478861687508478%26mt%3D1506334212%26ip%3D113.161.38.181%26ipbits%3D0%26expire%3D1506337891%26cp%3DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%26sparams%3Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Cgir%252Cclen%252Cdur%252Clmt%252Ccp%26signature%3D3F6BB70621BD1E3DD17BA99E46783D18C365D1D9.8D64D178C72B2EC9BF873801DCDF108D91A9700A%26key%3Dck2%2C35%7Chttps%3A%2F%2Fr5---sn-i3beln7z.c.mail.google.com%2Fvideoplayback%3Fid%3D947e2da453ef37b0%26itag%3D35%26source%3Dwebdrive%26requiressl%3Dyes%26mm%3D30%26mn%3Dsn-i3beln7z%26ms%3Dnxu%26mv%3Dm%26pl%3D24%26ttl%3Dtransient%26ei%3DU9bIWYGcOcmRuwKRqqWwCw%26driveid%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%26app%3Dexplorer%26mime%3Dvideo%2Fx-flv%26gir%3Dyes%26clen%3D1362425%26dur%3D13%26lmt%3D1478861693212037%26mt%3D1506334212%26ip%3D113.161.38.181%26ipbits%3D0%26expire%3D1506337891%26cp%3DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%26sparams%3Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Cgir%252Cclen%252Cdur%252Clmt%252Ccp%26signature%3DB54515EA912DF21B9773523DF094F2B6F5F5A973.25C3D94084B2A844D6A39E6AB2A7846C3C044961%26key%3Dck2%2C59%7Chttps%3A%2F%2Fr5---sn-i3beln7z.c.mail.google.com%2Fvideoplayback%3Fid%3D947e2da453ef37b0%26itag%3D59%26source%3Dwebdrive%26requiressl%3Dyes%26mm%3D30%26mn%3Dsn-i3beln7z%26ms%3Dnxu%26mv%3Dm%26pl%3D24%26ttl%3Dtransient%26ei%3DU9bIWYGcOcmRuwKRqqWwCw%26driveid%3D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%26app%3Dexplorer%26mime%3Dvideo%2Fmp4%26lmt%3D1478861689517828%26mt%3D1506334212%26ip%3D113.161.38.181%26ipbits%3D0%26expire%3D1506337891%26cp%3DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%26sparams%3Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Clmt%252Ccp%26signature%3D1C1770A63DA39D5F06CEBC80A11619C29937B183.B95A718ECEDDF5902D15B5ED8D4ACCD895E64B58%26key%3Dck2&fmt_list=22%2F1280x720%2F9%2F0%2F115%2C35%2F854x480%2F9%2F0%2F115%2C59%2F854x480%2F9%2F0%2F115%2C18%2F640x360%2F9%2F0%2F115%2C34%2F640x360%2F9%2F0%2F115&url_encoded_fmt_stream_map=itag%3D18%26url%3Dhttps%253A%252F%252Fr5---sn-i3beln7z.c.mail.google.com%252Fvideoplayback%253Fid%253D947e2da453ef37b0%2526itag%253D18%2526source%253Dwebdrive%2526requiressl%253Dyes%2526mm%253D30%2526mn%253Dsn-i3beln7z%2526ms%253Dnxu%2526mv%253Dm%2526pl%253D24%2526ttl%253Dtransient%2526ei%253DU9bIWYGcOcmRuwKRqqWwCw%2526driveid%253D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%2526app%253Dexplorer%2526mime%253Dvideo%252Fmp4%2526lmt%253D1478861408556243%2526mt%253D1506334212%2526ip%253D113.161.38.181%2526ipbits%253D0%2526expire%253D1506337891%2526cp%253DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%2526sparams%253Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Clmt%252Ccp%2526signature%253D86526822049B539A90E7304847D68A662110D58B.32F6652397D8E2FB08A73558E2C636A9B66D56EF%2526key%253Dck2%26type%3Dvideo%252Fmp4%253B%2Bcodecs%253D%2522avc1.42001E%252C%2Bmp4a.40.2%2522%26quality%3Dmedium%2Citag%3D22%26url%3Dhttps%253A%252F%252Fr5---sn-i3beln7z.c.mail.google.com%252Fvideoplayback%253Fid%253D947e2da453ef37b0%2526itag%253D22%2526source%253Dwebdrive%2526requiressl%253Dyes%2526mm%253D30%2526mn%253Dsn-i3beln7z%2526ms%253Dnxu%2526mv%253Dm%2526pl%253D24%2526ttl%253Dtransient%2526ei%253DU9bIWYGcOcmRuwKRqqWwCw%2526driveid%253D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%2526app%253Dexplorer%2526mime%253Dvideo%252Fmp4%2526lmt%253D1478861692910874%2526mt%253D1506334212%2526ip%253D113.161.38.181%2526ipbits%253D0%2526expire%253D1506337891%2526cp%253DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%2526sparams%253Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Clmt%252Ccp%2526signature%253DB37BE6E8CBDDE245AF206C9CDC3D73DE7D3594CF.55B50234A2EFA1F7722CBBF1A6A1E6CBC9A53503%2526key%253Dck2%26type%3Dvideo%252Fmp4%253B%2Bcodecs%253D%2522avc1.42001E%252C%2Bmp4a.40.2%2522%26quality%3Dhd720%2Citag%3D34%26url%3Dhttps%253A%252F%252Fr5---sn-i3beln7z.c.mail.google.com%252Fvideoplayback%253Fid%253D947e2da453ef37b0%2526itag%253D34%2526source%253Dwebdrive%2526requiressl%253Dyes%2526mm%253D30%2526mn%253Dsn-i3beln7z%2526ms%253Dnxu%2526mv%253Dm%2526pl%253D24%2526ttl%253Dtransient%2526ei%253DU9bIWYGcOcmRuwKRqqWwCw%2526driveid%253D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%2526app%253Dexplorer%2526mime%253Dvideo%252Fx-flv%2526gir%253Dyes%2526clen%253D974217%2526dur%253D13%2526lmt%253D1478861687508478%2526mt%253D1506334212%2526ip%253D113.161.38.181%2526ipbits%253D0%2526expire%253D1506337891%2526cp%253DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%2526sparams%253Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Cgir%252Cclen%252Cdur%252Clmt%252Ccp%2526signature%253D3F6BB70621BD1E3DD17BA99E46783D18C365D1D9.8D64D178C72B2EC9BF873801DCDF108D91A9700A%2526key%253Dck2%26type%3Dvideo%252Fx-flv%26quality%3Dmedium%2Citag%3D35%26url%3Dhttps%253A%252F%252Fr5---sn-i3beln7z.c.mail.google.com%252Fvideoplayback%253Fid%253D947e2da453ef37b0%2526itag%253D35%2526source%253Dwebdrive%2526requiressl%253Dyes%2526mm%253D30%2526mn%253Dsn-i3beln7z%2526ms%253Dnxu%2526mv%253Dm%2526pl%253D24%2526ttl%253Dtransient%2526ei%253DU9bIWYGcOcmRuwKRqqWwCw%2526driveid%253D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%2526app%253Dexplorer%2526mime%253Dvideo%252Fx-flv%2526gir%253Dyes%2526clen%253D1362425%2526dur%253D13%2526lmt%253D1478861693212037%2526mt%253D1506334212%2526ip%253D113.161.38.181%2526ipbits%253D0%2526expire%253D1506337891%2526cp%253DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%2526sparams%253Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Cgir%252Cclen%252Cdur%252Clmt%252Ccp%2526signature%253DB54515EA912DF21B9773523DF094F2B6F5F5A973.25C3D94084B2A844D6A39E6AB2A7846C3C044961%2526key%253Dck2%26type%3Dvideo%252Fx-flv%26quality%3Dlarge%2Citag%3D59%26url%3Dhttps%253A%252F%252Fr5---sn-i3beln7z.c.mail.google.com%252Fvideoplayback%253Fid%253D947e2da453ef37b0%2526itag%253D59%2526source%253Dwebdrive%2526requiressl%253Dyes%2526mm%253D30%2526mn%253Dsn-i3beln7z%2526ms%253Dnxu%2526mv%253Dm%2526pl%253D24%2526ttl%253Dtransient%2526ei%253DU9bIWYGcOcmRuwKRqqWwCw%2526driveid%253D0ByaRd0R0Qyatcmw2dVhQS0NDU0U%2526app%253Dexplorer%2526mime%253Dvideo%252Fmp4%2526lmt%253D1478861689517828%2526mt%253D1506334212%2526ip%253D113.161.38.181%2526ipbits%253D0%2526expire%253D1506337891%2526cp%253DQVNFWERfVlhXQlhOOkRPZVNJWVFKZGFl%2526sparams%253Dip%252Cipbits%252Cexpire%252Cid%252Citag%252Csource%252Crequiressl%252Cmm%252Cmn%252Cms%252Cmv%252Cpl%252Cttl%252Cei%252Cdriveid%252Capp%252Cmime%252Clmt%252Ccp%2526signature%253D1C1770A63DA39D5F06CEBC80A11619C29937B183.B95A718ECEDDF5902D15B5ED8D4ACCD895E64B58%2526key%253Dck2%26type%3Dvideo%252Fmp4%253B%2Bcodecs%253D%2522avc1.42001E%252C%2Bmp4a.40.2%2522%26quality%3Dlarge&timestamp=1506334291946&length_seconds=13';
//            $str = urldecode($str);
//            $arr = explode('&', $str);
//            echo '<pre>';
//            print_r($arr);
//            echo '</pre>';
//            die();


            return [];

            $fb_config = General::$config_fb;

//            $fb = new \Facebook\Facebook([
//                'app_id' => '{app-id}',
//                'app_secret' => '{app-secret}',
//                'default_graph_version' => 'v2.10',
//            ]);

            $fb = new Facebook([
                'app_id' => $fb_config['app_id'],
                'app_secret' => $fb_config['app_secret'],
                'default_graph_version' => 'v2.10'
            ]);

            try {
                //383923618322771

                // Returns a `Facebook\FacebookResponse` object
//                $response = $fb->get('/305140869535098/videos?fields=id,description,content_category,picture,source', $fb_config['access_token']);
                $response = $fb->get('/383923618322771/post?fields=id,description,content_category,picture,source', $fb_config['access_token']);
                echo '<pre>';
                print_r($response);
                echo '</pre>';
                die();
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            echo '<pre>';
            print_r($response);
            echo '</pre>';
            die();

            return [];

//            $row = [
//                'from_hour' => 16,
//                'to_hour' => 17,
//                'from_min' => 30,
//                'to_min' => 30,
//                'price' => 300000
//            ];

            $result = [
                'rows' => [
                    [
                        'from_hour' => 16,
                        'to_hour' => 17,
                        'from_min' => 30,
                        'to_min' => 30,
                        'price' => 300000
                    ],
                    [
                        'from_hour' => 17,
                        'to_hour' => 18,
                        'from_min' => 30,
                        'to_min' => 30,
                        'price' => 400000
                    ]
                ]
            ];


//            $from_hour = 17;
//            $from_min = 0;
//            $to_hour = 18;
//            $to_min = 0;

            $from_hour = 16;
            $from_min = 0;
            $to_hour = 18;
            $to_min = 0;

//            $from_hour = 17;
//            $from_min = 0;
//            $to_hour = 17;
//            $to_min = 30;

            $unit_min_from = $from_hour * 60 + $from_min;
            $unit_min_to = $to_hour * 60 + $to_min;
            $total_min = $unit_min_to - $unit_min_from;
            $total_price = $total_min_valid = 0;
            foreach ($result['rows'] as $i => $row) {
                $unit_min_db_from = $row['from_hour'] * 60 + $row['from_min'];
                $unit_min_db_to = $row['to_hour'] * 60 + $row['to_min'];
                $unit_price_by_min = $row['price'] / ($unit_min_db_to - $unit_min_db_from);
                $min_valid_in_frame = 0;

                if ($unit_min_db_from >= $unit_min_from && $unit_min_db_to <= $unit_min_to) {
                    $min_valid_in_frame = $unit_min_db_to - $unit_min_db_from;
                } elseif ($unit_min_db_from <= $unit_min_from && $unit_min_db_to >= $unit_min_to) {
                    $min_valid_in_frame = $unit_min_to - $unit_min_from;
                } elseif ($unit_min_from <= $unit_min_db_from &&
                    ($unit_min_to <= $unit_min_db_to && $unit_min_to >= $unit_min_db_from)
                ) {
                    $min_valid_in_frame = $unit_min_to - $unit_min_db_from;
                } elseif ($unit_min_to >= $unit_min_db_to &&
                    ($unit_min_from >= $unit_min_db_from && $unit_min_from <= $unit_min_db_to)
                ) {
                    $min_valid_in_frame = $unit_min_db_to - $unit_min_from;
                }

                $total_price += ($min_valid_in_frame * $unit_price_by_min);
                $total_min_valid += $min_valid_in_frame;

                if ($total_min_valid == $total_min) {
                    break;
                }
            }


            echo '<pre>';
            print_r([
                'total_min_valid' => $total_min_valid,
                'total_min' => $total_min,
                'total_price' => $total_price
            ]);
            echo '</pre>';
            die();

            echo '<pre>';
            print_r([
                $unit_min_from,
                $unit_min_to
            ]);
            echo '</pre>';
            die();


            $t = 135762.4285714286;
            $avg_duration_session = '';
            $hour = floor($t / 60 / 60 / 1000);
            $avg_duration_session .= sprintf('%02d', $hour);
            $min = floor(($t - ($hour * 60 * 60 * 1000)) / 60 / 1000);
            $avg_duration_session .= ':' . sprintf('%02d', $min);
            $second = floor(($t - (($hour * 60 * 60 * 1000) + (60 * 1000 * $min))) / 1000);
            $avg_duration_session .= ':' . sprintf('%02d', $second);

            echo '<pre>';
            print_r($avg_duration_session);
            echo '</pre>';
            die();

            echo '<pre>';
            print_r($s);
            echo '</pre>';
            die();

            echo '<pre>';
            print_r($s);
            echo '</pre>';
            die();

//            $h = $t / 60 /60 /;
//            $p = 135762.4285714286 -

            return [

            ];
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

//            if (empty($url_encoded_fmt_stream_map)) {
//                continue;
//            }

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

            $cmd = 'wget -O ' . $path . ' "' . $avail_formats[0]['url'] . '"';


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
        } catch (\Exception $ex) {
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
