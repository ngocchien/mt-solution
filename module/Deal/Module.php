<?php

namespace Deal;

use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        //
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $em = $app->getEventManager();
        //
        $listener = $sm->get('ADX\Listener\Authentication');
        //
        $em->getSharedManager()->attach(
            'Admin\Api\Controller',
            'dispatch',
            $listener,
            100
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
