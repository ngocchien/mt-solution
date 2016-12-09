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
            'caching' => array(
                'host' => '10.198.0.208',
                'port' => '6404',
                'timeout' => '0'
            ),
            'log_budget' => array(
                'host' => '10.198.0.208',
                'port' => '6405',
                'timeout' => '0'
            ),
            'real_time' => array(
                'host' => '10.198.0.208',
                'port' => '6402',
                'timeout' => '0'
            ),
            'delivery' => array(
                'host' => '10.198.0.208',
                'port' => '6402',
                'timeout' => '0'
            ),
            'monitor' => array(
                'host' => '10.198.0.208',
                'port' => '6403',
                'timeout' => '0'
            ),
            'remarketing' => array(
                'host' => '10.198.0.207',
                'port' => '6379',
                'timeout' => '0'
            ),
            'caching_adx' => array(
                'host' => '10.198.0.208',
                'port' => '6404',
                'timeout' => '0'
            ),
            'subscribe_caching' => array(
                'host' => '10.198.0.208',
                'port' => '6403',
                'timeout' => '0'
            )
        )
    )
);