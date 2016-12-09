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
##
define('KEY_DEBUG', '0302df94085797da620e1eddd98ba93e');
##
define('UPLOAD_PATH', '/data/cloud/ads/');
##
define('FORECAST_DOMAIN', 'http://10.199.0.12:9090');
define('API_KEY_FORECAST_DOMAIN', '6f30f7cf872c60bba41f3689edf27f6b');
##
define('SITE_URL_SOCKET', 'ws-db-adx.ants.vn');
define('WEBSOCKET_PORT', 8888);
define('WEBSOCKET_IP', '10.199.0.1');
##
define('AUTH_ADX_DOMAIN', '//ogs.ants.vn/account/login');
define('API_BUYER_HOST', '//adx.ants.vn/v3/api');

return array(// ...
);
