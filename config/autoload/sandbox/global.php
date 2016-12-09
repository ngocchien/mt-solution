<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
define('KEY_DEBUG', '0302df94085797da620e1eddd98ba93e');
define('UPLOAD_PATH', '/sandbox/data/cloud/ads/');
define('FORECAST_DOMAIN', 'http://10.198.0.2:9888');
define('API_KEY_FORECAST_DOMAIN', '6f30f7cf872c60bba41f3689edf27f6b');
define('SITE_URL_SOCKET', 'sandbox-ws-db-adx.ants.vn');
define('WEBSOCKET_PORT', 8888);
define('WEBSOCKET_IP', '10.198.0.204');
define('API_KEY_RM_COOKIE_DOMAIN', 'f63e2d75b84049648e8bcfb10a9af2d2');
define('RM_COOKIE_DOMAIN','http://10.198.0.12:8990');
##
define('AUTH_ADX_DOMAIN', '//sandbox-ogs.ants.vn/account/login');
define('API_BUYER_HOST', '//sandbox-adx.ants.vn/v3/api');

return array(// ...
);
