<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api;

use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'api' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/api/',
                    'defaults' => [
                        'controller' => 'Api\Controller\Index',
                        'action'     => 'index'
                    ]
                ]
            ],
            'indexRestApi' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => 'api/index[/:action]',
                    'defaults' => [
                        'controller'    => 'Api\Controller\Index',
                        'action'        => 'index'
                    ]
                ]
            ],
            'authenticationRestApi' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => 'api/auth[/:action]',
                    'defaults' => [
                        'controller'    => 'Api\Controller\Authentication',
                        'action'        => 'index'
                    ]
                ]
            ]
        ],
    ],
    'controllers' => [
        'invokables' => array(
            'Api\Controller\Index' => 'Api\Controller\IndexController',
            'Api\Controller\Authentication' => 'Api\Controller\AuthenticationController'
        )
    ],
    'module_layouts' => array(
        'Api' => 'api/layout',
    ),
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'index/layout'    => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            'application' => __DIR__ . '/../view'
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        )
    ],
];
