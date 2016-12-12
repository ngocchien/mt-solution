<?php

/**
 * Created by PhpStorm.
 * User: KhanhHuynh
 * Date: 17/06/2016
 * Time: 10:37 SA
 */
namespace EVENT;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Network implements MessageComponentInterface
{
    protected $clients;
    private $loop;
    public function __construct(\React\EventLoop\LoopInterface $loop) {
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "Connected";
        $this->clients->attach($conn);

    }

    public function onMessage(ConnectionInterface $conn, $msg) {
        $this->loop->nextTick(function() use ($conn) {
            $conn->send('{"command":"someString","data":"data"}');
            sleep(1);
        });
    }


    public function onClose(ConnectionInterface $conn)
    {
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }
}