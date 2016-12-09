<?php

namespace LockModule;

return array(
    'router' => array(
        'routes' => array(
            'LockModuleRestApi' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => PRODUCT_VERSION . '/api/check-module/index[/:id]',
                    'constraints' => array(
                        'id' => '[a-zA-Z0-9_-_.,-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'LockModule\Controller\IndexRest',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'LockModule\Controller\IndexRest' => 'LockModule\Controller\IndexRestController',
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
);

