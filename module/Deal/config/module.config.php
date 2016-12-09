<?php

namespace Deal;

return array(
    'router' => array(
        'routes' => array(
            'dealIndexRestApi' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => PRODUCT_VERSION . '/api/deal/index[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Deal\Controller\IndexRest',
                    ),
                ),
            ),
            'dealPerformanceApi' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => PRODUCT_VERSION . '/api/deal/performance[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Deal\Controller\PerformanceRest',
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
            'Deal\Controller\IndexRest' => 'Deal\Controller\IndexRestController',
            'Deal\Controller\PerformanceRest' => 'Deal\Controller\PerformanceRestController'
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
