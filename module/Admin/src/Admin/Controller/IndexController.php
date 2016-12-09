<?php

namespace Admin\Controller;

use ADX\PubSub;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ADX\Model;
use ADX\Business;
use ADX\Utils;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        
        return new ViewModel();
    }
}
