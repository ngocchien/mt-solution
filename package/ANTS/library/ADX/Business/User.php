<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\Business;

use Zend\View\Model\JsonModel;
use ADX\Model;
use ADX\Utils;
use ADX\Nosql;

class User
{
    public static function checkUserSuspend($params = array())
    {
        //
        $user = Model\User::getNetworkUser($params);
        $operational_status = $user['OPERATIONAL_STATUS'];

        return array('status' => $operational_status);
    }
}