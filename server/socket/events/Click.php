<?php

/**
 * Created by PhpStorm.
 * User: KhanhHuynh
 * Date: 17/06/2016
 * Time: 10:37 SA
 */
namespace EVENT;

use Ratchet\ConnectionInterface as Conn;
use Ratchet\MessageComponentInterface;
use ADX\Nosql\Redis;
use Ratchet\Wamp\WampServerInterface;

class Click implements WampServerInterface
{
    protected static $subscribedTopics = array();

    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
    {
//        while (true) {
//            $redis = Redis::getInstance('real_time');
//            $network = 1057;
//            $key = date('Y-m-d-h-i-s', time()) . ':' . $network;
//            $time = microtime(true) * 1000;
//            $click = $redis->hmGet($key, array('click'));
//            $data = array(
//                'x' => $time,
//                'y' => (float)$click['click']
//            );
//            $event = json_encode($data);
//            echo 'Broadcase Message: ' . $event;
//            $topic->broadcast($event);
//            sleep(1);
//        }
    }

    public function onCall(Conn $conn, $id, $topic, array $params)
    {
        $conn->callError($id, $topic, 'RPC not supported on this demo');
    }

    // No need to anything, since WampServer adds and removes subscribers to Topics automatically

    public function onUnSubscribe(Conn $conn, $topic)
    {

    }

    public function onOpen(Conn $conn)
    {
        $network = 1057;
        $redis = Redis::getInstance('real_time');
        $arr_data_second = array();
        $arr_data_minitue = array();
        for ($i = 28; $i >= 0; $i--) {
            $key_second = date('Y-m-d-h-i-s', (time() - $i)) . ':' . $network;
            $key_minitue = date('Y-m-d-h-i', (time() - $i * 60)) . ':' . $network;
            $data_second = $redis->HMGET($key_second, array('click'));
            $data_minitue = $redis->HMGET($key_minitue, array('click'));
            $arr_data_second[] = array(
                'x' => (int)((microtime(true) - $i) * 1000),
                'y' => $data_second['click']
            );
            $arr_data_minitue[] = array(
                'x' => (int)((microtime(true) - $i * 60) * 1000),
                'y' => $data_minitue['click']
            );
        }
        $arrMessage = array(
            'second' => $arr_data_second,
            'minitue' => $arr_data_minitue
        );
        $conn->send(json_encode($arrMessage));
    }

    public function onClose(Conn $conn)
    {

    }

    public function onError(Conn $conn, \Exception $e)
    {
    }

    public function onSubscribe(Conn $conn, $topic)
    {
        self::$subscribedTopics[$topic->getId()] = $topic;
    }


    public function click($entry)
    {
        if (!array_key_exists('click_chart', self::$subscribedTopics)) {
            return;
        }

        $redis = Redis::getInstance('real_time');
        $network = 1057;
        $key = date('Y-m-d-h-i-s', time()) . ':' . $network;
        $time = microtime(true) * 1000;
        $click = $redis->hmGet($key, array('click'));
        $data = array(
            'x' => $time,
            'y' => (float)$click['click']
        );
        $event = json_encode($data);
        $topic = self::$subscribedTopics['click_chart'];
        $topic->broadcast($event);
    }


//    public function onOpen(ConnectionInterface $conn)
//    {
//        echo 'connected\n';
//        $this->clients->attach($conn);
//        //Get data for second and minitue
//        $network = 1057;
//        $redis = Redis::getInstance('real_time');
//        $arr_data_second = array();
//        $arr_data_minitue = array();
//        for ($i = 28; $i >= 0; $i--) {
//            $key_second = date('Y-m-d-h-i-s', (time() - $i)) . ':' . $network;
//            $key_minitue = date('Y-m-d-h-i', (time() - $i * 60)) . ':' . $network;
//            $data_second = $redis->HMGET($key_second, array('click'));
//            $data_minitue = $redis->HMGET($key_minitue, array('click'));
//            $arr_data_second[] = array(
//                'x' => (int)((microtime(true) - $i) * 1000),
//                'y' => $data_second['click']
//            );
//            $arr_data_minitue[] = array(
//                'x' => (int)((microtime(true) - $i * 60) * 1000),
//                'y' => $data_minitue['click']
//            );
//        }
//        $arrMessage = array(
//            'second' => $arr_data_second,
//            'minitue' => $arr_data_minitue
//        );
//        $conn->send(json_encode($arrMessage));
//    }
//
//    public function onMessage(ConnectionInterface $from, $msg)
//    {
//        foreach ($this->clients as $client) {
//            if ($from === $client) {
//                $redis = Redis::getInstance('real_time');
//                $network = 1057;
//                $key = date('Y-m-d-h-i-s', time()) . ':' . $network;
//                $time = microtime(true) * 1000;
//                $click = $redis->hmGet($key, array('click'));
//                $data = array(
//                    'x' => $time,
//                    'y' => (float)$click['click']
//                );
//                $message = json_encode($data);
//                $client->send($message);
//            }
//
//        }
//    }
//
//    public function onClose(ConnectionInterface $conn)
//    {
//    }
//
//    public function onError(ConnectionInterface $conn, \Exception $e)
//    {
//    }
}