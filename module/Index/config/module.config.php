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
//            'post' => [
//                'type'    => Segment::class,
//                'options' => [
//                    'route'    => '/bai-viet[/:name]/',
//                    'constraints' => array(
//                        'name' => '[a-zA-Z0-9_-]*'
//                    ),
//                    'defaults' => [
//                        'module' => 'Index',
//                        'controller'    => 'Application\Controller\Product',
//                        'action'        => 'index',
//                    ],
//                ],
//            ],
//            'news' => [
//                'type'    => Segment::class,
//                'options' => [
//                    'route'    => '/tin-tuc[/:name]/',
//                    'constraints' => array(
//                        'name' => '[a-zA-Z0-9_-]*'
//                    ),
//                    'defaults' => [
//                        'controller'    => 'Application\Controller\News',
//                        'action'        => 'index',
//                    ],
//                ],
//            ],
//            'category' => [
//                'type'    => Segment::class,
//                'options' => [
//                    'route'    => '/the-loai[/:name]/',
//                    'constraints' => array(
//                        'name' => '[a-zA-Z0-9_-]*'
//                    ),
//                    'defaults' => [
//                        'module' => 'Application',
//                        'controller'    => 'Application\Controller\Category',
//                        'action'        => 'index',
//                    ],
//                ],
//            ],
        ],
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
            'index/layout'    => __DIR__ . '/../view/layout/layout.phtml',
            'index/header'      => __DIR__ . '/../view/layout/header.phtml',
            'index/footer'      => __DIR__ . '/../view/layout/footer.phtml',
            'index/left-menu'      => __DIR__ . '/../view/layout/left-menu.phtml',
        ],
        'template_path_stack' => [
            'application' => __DIR__ . '/../view'
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        )
    ],
];
