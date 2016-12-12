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

class Proceed implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "Connected";
        $this->clients->attach($conn);

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach ($this->clients as $client) {
            if ($from === $client) {
                $time = microtime(true) * 1000;
                $value = (float)rand(0,50);
                $data = array(
                    'x' => $time,
                    'y' => $value
                );
                $message = json_encode($data);
                $client->send($message);
            }

        }
    }

    public function onClose(ConnectionInterface $conn)
    {
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }
}