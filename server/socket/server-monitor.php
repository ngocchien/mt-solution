<?php
/**
 * Created by PhpStorm.
 * User: KhanhHuynh
 * Date: 05/06/2016
 * Time: 11:13 CH
 */
// Define root path
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../..'));

// Define path to config directory
defined('CONFIG_PATH')
|| define('CONFIG_PATH', ROOT_PATH . '/config');

defined('LANGUAGE_DEFAULT')
|| define('LANGUAGE_DEFAULT', 'vi_VN');

//
require_once CONFIG_PATH . '/common/defined.php';
//
require_once CONFIG_PATH . '/common/constant.php';


/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__) . '/..');

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

if (!defined('ZEND_FRAMEWORK_PATH')) {
    $fw_path = null;
    $dir = explode(PATH_SEPARATOR, get_include_path());
    //
    foreach ($dir as $path) {
        if (file_exists($path . '/vendor/autoload.php')) {
            $fw_path = realpath($path);
            break;
        }
    }
    //
    define('ZEND_FRAMEWORK_PATH', $fw_path);
    unset($fw_path, $dir, $path);
}

// Setup autoloading
require 'init_autoloader.php';

//Load namespaces
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
            'ADX' => ROOT_PATH . '/package/ANTS/library/ADX',
            'EVENT' => __DIR__ . '/events',
        ),
    )
));

//Check console params options
$opts = new Zend\Console\Getopt(array(
    'env-s' => 'environment',
    'v-i' => 'verbose option'
));

//Get info console
$env = $opts->getOption('env');
$verbose = $opts->getOption('v');

if (empty($env) || !in_array($env, array('development', 'sandbox', 'production'))) {
    echo 'Error Environment server-name.php --env [development, sandbox, production]';
    exit();
}

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', $env);

//
require_once CONFIG_PATH . '/autoload/' . APPLICATION_ENV . '/global.php';

//Print information environment
if ($verbose) {
    echo "SITE_URL_SOCKET : " . SITE_URL_SOCKET . "\n";
    echo "WEBSOCKET_PORT : " . WEBSOCKET_PORT . "\n";
    echo "ROOT_PATH : " . ROOT_PATH . "\n";
    echo "ENVIRONMENT : " . APPLICATION_ENV . "\n";
}

ini_set('default_socket_timeout', -1);

use EVENT\Monitor;

use Clue\React\Redis\Client;
use Clue\React\Redis\Factory;
use Clue\Redis\Protocol\Model\StatusReply;

//
$loop = React\EventLoop\Factory::create();
//
$app = new Ratchet\App(SITE_URL_SOCKET, WEBSOCKET_PORT, WEBSOCKET_IP, $loop, 8787);
//
$app->route('/monitor', new Monitor, array('*'));
//
$factory = new Factory($loop);
//
//
$channel_monitor_query = 'adx_v3:query:monitor';
$channel_monitor_worker = 'adx_v3:worker:monitor';

//get config
$redis = ADX\Config::get('redis');
$monitor = $redis['redis']['adapters']['real_time'];

//
$factory->createClient($monitor['host'] . ':' . $monitor['port'])->then(function (Client $client) use ($channel_monitor_query) {

    $client->subscribe($channel_monitor_query)->then(function ($channel_monitor_query) {
        echo 'Now subscribed to channel ' . $channel_monitor_query[1] . PHP_EOL;
    });

    $client->on('message', function ($channel_monitor_query, $message) {
        call_user_func_array(array('EVENT\Monitor', 'query'), array($message));
    });

});

$factory->createClient($monitor['host'] . ':' . $monitor['port'])->then(function (Client $client) use ($channel_monitor_worker) {

    $client->subscribe($channel_monitor_worker)->then(function ($channel_monitor_worker) {
        echo 'Now subscribed to channel ' . $channel_monitor_worker[1] . PHP_EOL;
    });

    $client->on('message', function ($channel_monitor_worker, $message) {
        call_user_func_array(array('EVENT\Monitor', 'worker'), array($message));
    });

});
//
$app->run();