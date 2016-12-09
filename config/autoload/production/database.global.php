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
    'db' => array(
        //other adapter when it needed...
        'adapters' => array(
            'info_slave' => array(
                'driver' => 'OCI8',
                'connection_string' => '//10.199.0.6:1521/ANTSORA02.LOCAL',
                'character_set' => 'AL32UTF8',
                'username' => 'ADX_BUYER_FE',
                'password' => 'W34872fw55V9cRs',
            ),
            'info_master' => array(
                'driver' => 'OCI8',
                'connection_string' => '//10.199.0.6:1521/ANTSORA02.LOCAL',
                'character_set' => 'AL32UTF8',
                'username' => 'ADX_BUYER_FE',
                'password' => 'W34872fw55V9cRs',
            ),
            'statistic_slave' => array(
                'driver' => 'OCI8',
                'connection_string' => '//10.199.0.5:1521/ANTSORA01.LOCAL',
                'character_set' => 'AL32UTF8',
                'username' => 'ADX_BUYER_STATS_FE',
                'password' => 'M92y5RS4yC6553h',
            )
        )
    )
);
