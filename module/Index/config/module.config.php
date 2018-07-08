<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Index;

use Zend\Mvc\Router\Http\Literal;
use Zend\Mvc\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Index\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'index' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/index[/:action]',
                    'defaults' => [
                        'controller'    => 'Index\Controller\Index',
                        'action'        => 'index',
                    ],
                ],
            ],
        ]
    ],
    'controllers' => [
        'invokables' => array(
            'Index\Controller\Index' => 'Index\Controller\IndexController'
        )
    ],
    'module_layouts' => array(
        'Index' => 'index/layout',
    ),
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'index/layout'    => __DIR__ . '/../view/layout/layout.phtml'
        ],
        'template_path_stack' => [
            'index' => __DIR__ . '/../view'
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        )
    ],
];
