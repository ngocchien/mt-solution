<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\BusinessTest;

use ADX\Business;
use PHPUnit_Framework_TestCase;

class AccountTest extends PHPUnit_Framework_TestCase
{
    public static function testDebug()
    {
        $data = Business\Account::getDebug();

        echo '<pre>';
        print_r($data);
        exit();
    }

}