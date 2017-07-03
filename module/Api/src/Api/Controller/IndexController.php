<?php

namespace Api\Controller;

use Zend\View\Model\ViewModel;
use MT\Model;
use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        try{
            echo '<pre>';
            print_r('aaaaa');
            echo '</pre>';
            die();
            return false;
            return new ViewModel([]);
            $google_config = Model\Common::getConfigGoogle();
            echo '<pre>';
            print_r($google_config);
            echo '</pre>';
            die();
            $google_config = $google_config[10];
            $client = new \Google_Client();
            $client->setClientId($google_config['client_id']);
            $client->setClientSecret($google_config['client_secret']);
            $client->setAccessType("offline");
            $client->setApprovalPrompt("force");
            $client->refreshToken($google_config['refresh_token']);
            $new_token = $client->getAccessToken();
            echo '<pre>';
            print_r($new_token);
            echo '</pre>';
            die();
        }catch (\Exception $ex){
            echo '<pre>';
            print_r([
                $ex->getCode(),
                $ex->getMessage()
            ]);
            echo '</pre>';
            die();
        }
    }
}
