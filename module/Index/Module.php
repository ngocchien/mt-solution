<?php

namespace Index;

use Zend\ModuleManager\ModuleManager;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class Module
{
    public function init(ModuleManager $mm)
    {
        $mm->getEventManager()->getSharedManager()->attach(__NAMESPACE__,
            'dispatch', function ($e) {
                $e->getTarget()->layout('index/layout');
            });

        $sessionManager = new SessionManager();
        $sessionManager->rememberMe(SESSION_EXPIRED);
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
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
