<?php

/**
 * Created by PhpStorm.
 * User: KhanhHuynh
 * Date: 17/06/2016
 * Time: 10:37 SA
 */
namespace EVENT;

use ADX\Utils;
use Ratchet\ConnectionInterface as Conn;
use Ratchet\MessageComponentInterface;
use Ratchet\Wamp\WampServerInterface;

class Monitor implements WampServerInterface
{
    protected static $subscribedTopics = array();

    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
    {

    }

    public function onCall(Conn $conn, $id, $topic, array $params)
    {
        $conn->callError($id, $topic, 'RPC not supported on this demo');
    }

    public function onUnSubscribe(Conn $conn, $topic)
    {

    }

    public function onOpen(Conn $conn)
    {

    }

    public function onClose(Conn $conn)
    {

    }

    public function onError(Conn $conn, \Exception $e)
    {
    }

    public function onSubscribe(Conn $conn, $topic)
    {

        echo 'done:' . $topic->getId() . "\n";
        self::$subscribedTopics[$topic->getId()] = $topic;
    }


    public function query($entry)
    {
        echo "\n";
        print_r($entry);
        echo "\n";
        
        //call job index ES.
        Utils::runJob(
            'info_buyer',
            'TASK\ElasticHelper',
            'indexMonitorQuery',
            'doHighBackgroundTask',
            'elastic_helper',
            json_decode($entry, true)
        );

        if (!array_key_exists('monitor_query', self::$subscribedTopics)) {
            return;
        }

        $event = json_encode($entry);
        $topic = self::$subscribedTopics['monitor_query'];
        $topic->broadcast($event);
    }

    public function worker($entry)
    {
        echo "\n";
        print_r($entry);
        echo "\n";

        if (!array_key_exists('monitor_worker', self::$subscribedTopics)) {
            return;
        }

        $event = json_encode($entry);
        $topic = self::$subscribedTopics['monitor_worker'];
        $topic->broadcast($event);
    }

}