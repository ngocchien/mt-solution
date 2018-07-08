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

defined('SERVER_PATH')
|| define('SERVER_PATH', ROOT_PATH . '/server');

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

// Setup autoloading
require 'init_autoloader.php';
//Load namespaces
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
            'WORK' => __DIR__ . '/works',
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

ini_set('default_socket_timeout', -1);

//config
$socket_url = '127.0.0.1';
$socket_port = '8888';
$channel = 'insight:fe';

use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;
use WORK as WORK_PLACE;
use MT\Config;

//init instance Redis
$config = Config::get('redis')['redis']['adapters']['caching'];

$redis = new Redis();
$redis->pconnect($config['host'], $config['port'], 0);
global $redis;

$router = new Router();

$router->registerModule(new RatchetTransportProvider($socket_url, $socket_port));

$router->addInternalClient(new WORK_PLACE\InternalClientChart($channel));
$router->addInternalClient(new WORK_PLACE\InternalClientSummary($channel));
$router->addInternalClient(new WORK_PLACE\InternalClientTopDimension($channel));

$router->start();