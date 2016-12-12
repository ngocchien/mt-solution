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

//Print information environment
if ($verbose) {
    echo "ROOT_PATH : " . ROOT_PATH . "\n";
    echo "ENVIRONMENT : " . APPLICATION_ENV . "\n";
}

//
require_once CONFIG_PATH . '/autoload/' . APPLICATION_ENV . '/global.php';

//SETDATA 1 day
// Get instance redis.
$redis = ADX\Nosql\Redis::getInstance('real_time');
$key = date('Y-m-d');

$network_id = 1057;
for ($hour = 0; $hour < 24; $hour++) {
    for ($minute = 0; $minute < 60; $minute++) {
        $total_click = 0;
        $total_impression = 0;
        $total_proceed = 0;
        for ($second = 0; $second < 60; $second++) {
            $key_redis = $key . '-' . str_pad($hour, 2, '0', STR_PAD_LEFT) . '-' . str_pad($minute, 2, '0', STR_PAD_LEFT) . '-' . str_pad($second, 2, '0', STR_PAD_LEFT) . ':' . $network_id;
//            echo "<pre>";
//            print_r($key_redis);
//            echo "</pre>";
//            exit();
            $click = rand(2, 50);
            $impression = rand(2, 50);
            $proceed = rand(2, 50);
            $redis->delete($key_redis);
            $data_set = array(
                'click' => $click,
                'impression' => $impression,
                'proceed' => $proceed
            );
            $redis->hMSet($key_redis, $data_set);
            $total_click += $click;
            $total_impression += $impression;
            $total_proceed += $proceed;
        }
        $data_set_minitue = array(
            'click' => $total_click,
            'impression' => $total_impression,
            'proceed' => $total_proceed
        );
        $key_minitue = $key . '-' . str_pad($hour, 2, '0', STR_PAD_LEFT) . '-' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':' . $network_id;
        $redis->hMSet($key_minitue, $data_set_minitue);

    }
}
ADX\Nosql\Redis::closeConnection('real_time');
exit("123123");




