<?php
return array(
    'db' => array(
        'adapters' => array(
            'info_slave' => array(
                'driver' => 'Pdo_Mysql',
                'database' => 'mt_ping',
                'host' => 'localhost',
                'options' => array('buffer_results' => true),
                'driver_options' => array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
                ),
                'username' => 'root',
                'password' => 'T#C#P#@Mt#',
            )
        )
    )
);
