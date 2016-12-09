<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\Business;

use ADX\Model;
use ADX\Utils;

class Metric
{
    public static function getMetricPerformance($params)
    {
        //Metrics for filter, draw chart
        $result = Model\User::getMetric(array(
            'user_id' => $params['user_id'],
            'network_id' => $params['network_id'],
            'by_fnc' => 'MODIFY_COL',
            'columns' => 'METRIC_ID, METRIC_NAME, METRIC_CODE, METRIC_LEVEL, IS_PERFORMANCE',
            'type' => isset($params['type']) ? $params['type'] : 1 //LineItem, Campaign, Creative
        ));

        $arr_metrics = array();
        if (!empty($result)) {
            foreach ($result as $metric) {
                if ($metric->metric_level == 2 && $metric->metric_code != '') {
                    $arr_metrics[$metric->metric_code] = $metric;
                }
            }
        }

        return $arr_metrics;
    }
}