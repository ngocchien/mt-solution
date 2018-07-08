<?php
require 'init_autoloader.php';

//Load namespaces
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
            'WORK' => __DIR__ . '/works',
        ),
    )
));

use Thruway\ClientSession;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;

//config
$socket_url = '127.0.0.1';
$socket_port = '8888';
$channel = 'realm1';

$client = new Client($channel);
$client->addTransportProvider(new PawlTransportProvider("ws://{$socket_url}:{$socket_port}/"));