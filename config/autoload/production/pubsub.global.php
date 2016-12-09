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

return array(
    'redis' => array(
        'adapters' => array(
            'info_buyer' => array(
                'prefix' => 'adx_v3',
                'component' => 'font-end',
                'host' => '10.199.0.3',
                'port' => '6501',
                'timeout' => '0'
            ),
            'info_adx' => array(
                'prefix' => 'adx_v1',
                'component' => 'font-end',
                'host' => '10.199.0.3',
                'port' => '6501',
                'timeout' => '0'
            ),
            'pubsub_remarketing' => array(
                'prefix' => 'adx_v1',
                'component' => 'font-end',
                'host' => '10.199.0.3',
                'port' => '6530',
                'timeout' => '0'
            )
        )
    )
);