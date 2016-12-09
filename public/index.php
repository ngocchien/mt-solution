<?php
ini_set("display_errors", 1);

//Root path
define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Define path to config directory
defined('CONFIG_PATH')
|| define('CONFIG_PATH', ROOT_PATH . '/config');

defined('LANGUAGE_DEFAULT')
|| define('LANGUAGE_DEFAULT', 'vi_VN');

//Host Name
define('HOST_NAME', $_SERVER['HTTP_HOST']);

//
require_once CONFIG_PATH . '/common/defined.php';
//
require_once CONFIG_PATH . '/common/constant.php';

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

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
//Zend\Loader\AutoloaderFactory::factory(array(
//    'Zend\Loader\StandardAutoloader' => array(
//        'namespaces' => array(
//            'ADX' => __DIR__ . '/../package/ANTS/library/ADX',
//        ),
//    )
//));


// Run the application!
Zend\Mvc\Application:: init(require 'config/application.config.php')->run();

//init function debug
if(isset($_GET['d_pro_p'])) {
    $domain = $_SERVER['SERVER_NAME'];
    if($_GET['d_pro_p'] == 'true'){
        setcookie('d_pro_p', 'd_pro_p', time() + 3600, '/', $domain);
    }else{
        setcookie('d_pro_p', '', time() - 3600, '/', $domain);
    }
}
function pr($data, $die = true)
{
    $pr = false;
    if(APPLICATION_ENV == 'development'){
        $pr = true;
    }else{
        if(isset($_GET['d_pro']) && $_GET['d_pro'] == true){
            $pr = true;
        }
        $cookie_debug = 'd_pro_p';
        $my_ck = isset($_COOKIE['d_pro_p']) ? $_COOKIE['d_pro_p'] : '';
        if($my_ck == $cookie_debug){
            $pr = true;
        }
    }
    if($pr == true){
        $trace = debug_backtrace();
        $caller = array_shift($trace);
        echo '<pre>';
        echo "called by [" . $caller['file'] . "] line: " . $caller['line'] . "\n";
        print_r($data);
        if ($die) {
            exit;
        }
    }
}
