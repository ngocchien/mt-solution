<?php

namespace Index\Controller;

use MT\Search;
use MT\Controller\MyController;
use Zend\View\Model\JsonModel;
use MT\Business;
use MT\Model;

class IndexController extends MyController
{
    public function indexAction()
    {
        try{
            echo '<pre>';
            print_r(111);
            echo '</pre>';
            die();
            $google_config = Model\Common::getConfigGoogle();
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
