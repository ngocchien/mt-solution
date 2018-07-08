<?php
$json = '{\"api_key\":\"AIzaSyDW0szgrecdX94Q5s1jAYhYE8LpdCFvjnc\",\"client_id\":\"47246308384-09dceuo2a8usraklmtl6mgbfi0q5ljp8.apps.googleusercontent.com\",\"client_secret\":\"d3NNefkt3ETgbGXdap0hIKLv\",\"redirect_uri\":\"http:\\/\\/api.chiennn.me\\/api-callback\\/youtube\",\"scope\":[\"email\",\"profile\",\"https:\\/\\/www.googleapis.com\\/auth\\/userinfo.profile\",\"https:\\/\\/www.googleapis.com\\/auth\\/youtube\",\"https:\\/\\/www.googleapis.com\\/auth\\/youtube.force-ssl\",\"https:\\/\\/www.googleapis.com\\/auth\\/youtube.readonly\",\"https:\\/\\/www.googleapis.com\\/auth\\/youtube.upload\",\"https:\\/\\/www.googleapis.com\\/auth\\/youtubepartner\",\"https:\\/\\/www.googleapis.com\\/auth\\/youtubepartner-channel-audit\"],\"type\":\"youtube\",\"refresh_token\":\"1\\/aoMKVtO0Ug3e_4w3pBf48xuFqmM2yAkSV2-qGinM0QlexoicHkmWEZR1tVRWtafu\",\"access_token\":\"ya29.GlvNBXTeueBJLfreI3EDrH94aLY48MF_vj7_e08FGGiUifWP0Q4av1K1D9TVsG3AOfKGXv3hQNAq2No_-IC7yJCynf3sHbXLne_Htn-GMR_eq2KJnKueW25dOL7f\",\"expires_in\":3600,\"token_google_id\":2}';
echo '<pre>';
print_r(json_decode($json,true));
echo '</pre>';
die();
echo '<pre>';
print_r(date('Y-m-d H:i:s', 1527526800));
echo '</pre>';
die();
echo '<pre>';
print_r(md5('chiennn@100990#715#!&#!@##'));
echo '</pre>';
die();

$arr = [
    '2018-05',
    '2018-07',
    '2018-04',
    '2017-12'
];
sort($arr);

$count = count($arr);
for ($i = 1; $i <= $count; $i++) {
    $current = $arr[$i];
    $prev = $arr[$i - 1];
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
//    die();
    echo '<pre>';
    print_r([
        $current,
        $prev
    ]);
    echo '</pre>';
//    die();
    echo '<pre>';
    print_r(date('Y-m', strtotime($prev . "+1 month")));
    echo '</pre>';
    die();
    if (date('Y-m', strtotime($prev . "+1 month")) != $current) {
        $valid = false;
        break;
    }
}

echo '<pre>';
print_r($arr);
echo '</pre>';
die();

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');

ini_set("display_errors", 1);

//Root path
define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

if (APPLICATION_ENV !== 'production') {
    ini_set('opcache.enable', '0');
}

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

// Run the application!
Zend\Mvc\Application:: init(require 'config/application.config.php')->run();

//init function debug
if (isset($_GET['d_pro_p'])) {
    $domain = $_SERVER['SERVER_NAME'];
    if ($_GET['d_pro_p'] == 'true') {
        setcookie('d_pro_p', 'd_pro_p', time() + 3600, '/', $domain);
    } else {
        setcookie('d_pro_p', '', time() - 3600, '/', $domain);
    }
}
