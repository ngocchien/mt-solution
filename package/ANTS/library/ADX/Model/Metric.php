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

class Metric extends Entity\Metric
{
    const TYPE_METRIC_PACKAGE_LIST = 18;
    const TYPE_METRIC_PACKAGE_DETAIL = 19;
    //
    const TYPE_METRIC_REPORT_COLUMN = 11;
    const TYPE_METRIC_REPORT_FILTER = 12;
    const TYPE_METRIC_REPORT_CONDITION_COLUMN = 13;
    const TYPE_METRIC_REPORT_FILTER_LISTING = 14;
    const TYPE_METRIC_REMARKETING = 15;
    //
    const METRIC_TYPE_TEXT = 1;
    const METRIC_TYPE_NUMBER = 2;
    const METRIC_TYPE_PERCENT = 3;
    const METRIC_TYPE_MONEY = 4;

    public static function getDefaultMetricWidget($params)
    {
        return DAO\Metric::getDefaultMetricWidget($params);
    }

    public static function getMetricReportCondition($params = array()){
        return DAO\Metric::getMetric($params);
    }

}