<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\Model;

use ADX\Entity;
use ADX\DAO;
use ADX\Business;

class Deal extends Entity\Deal
{
    const PACKAGE_TYPE_DEFAULT =  1;
    const PACKAGE_IS_BIDDING_DEFAULT = 1;

    public static function getParamsInfo()
    {
        return array(
            'object' => 'deal',
            'type' => Metric::TYPE_METRIC_PACKAGE_LIST,
            'model' => 'User',
            'function_name' => 'getPerformance',
            'function_caching' => 'getDeal',
            'private_key' => 'package_id'
        );
    }
}