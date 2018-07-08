<?php

namespace MT\Business;
use MT\Utils;

class RealTime
{
    const API_INSIGHT = 'http://sandbox-api-a.ants.vn/api-v3/';
    const URL_API_DATA_CHART = 'realtime/chart-time-frame';
    const URL_API_DATA_SUMMARY_METRICS = 'realtime/summary-metrics';
    const URL_API_DATA_TOP_DIMENSION = 'realtime/top-n-dimension';
    const DEFAULT_TIME_OUT_API = 60;
    const ACCESS_TOKEN_API_INSIGHT = '4f4cfe1dfdc281afbd1f28aa1c8ffc26';
    const DEFAULT_TIME_SPAN_CHART = 30;

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function apiGetDataChart($params)
    {
        try{
            $path = SERVER_PATH. '/job/config/'.APPLICATION_ENV.'/config-timeout-topic.php';
            $config = Utils::getConfigFromFile($path);
            $data = [];

            for($i=0; $i<=100; $i++){
                $data[] = [
                    'name' => 'Chiến , Tuấn , Khâm, NGhĩa, Trực, Khánh, Phước, Hoàn',
                    'value' => rand(10000,100000000)
                ];
            }
            return $data;


            $params_input = self::filterParamApiChart($params);
            $url = self::API_INSIGHT.self::URL_API_DATA_CHART;
            $response = self::callApiInsight($url,$params_input);
            return self::mappingDataResponseChart($response);
        }catch (\Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }

    }

    public static function apiGetDataSummaryMetric($params)
    {
        try{
            global $redis;
            echo '<pre>';
            print_r($redis);
            echo '</pre>';
            die();
            echo '<pre>';
            print_r($redis);
            echo '</pre>';
            die();
            $data = [];

            for($i=0; $i<=100; $i++){
                $data[] = [
                    'name' => 'Chiến , Tuấn , Khâm, NGhĩa, Trực, Khánh, Phước, Hoàn',
                    'value' => rand(10000,100000000)
                ];
            }
            return $data;

            $params_input = self::filterParamApiSummary($params);
            $url = self::API_INSIGHT.self::URL_API_DATA_SUMMARY_METRICS;
            $response = self::callApiInsight($url,$params_input);
            $data_response = self::mappingDataResponseSummary($response);

            $config_debug = Utils::getConfigDebugRealTime();
            if(!empty($config_debug) && isset($config_debug['debug']) && $config_debug['debug'] == true){
               if(isset($config_debug['summary_debug']) &&
                   $config_debug['summary_debug'] == true &&
                   !empty($config_debug['user_id']) &&
                   !in_array($params['user_id'], $config_debug['user_id'])
               ){
                   $data_response['is_skip'] = 1;
               }
            }

            return $data_response;
        }catch (\Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function apiGetDataTopDimension($params)
    {
        try{
            global $redis;
            $data = [];

            for($i=0; $i<=100; $i++){
                $data[] = [
                    'name' => 'Chiến , Tuấn , Khâm, NGhĩa, Trực, Khánh, Phước, Hoàn',
                    'value' => rand(10000,100000000)
                ];
            }
            return $data;
            $params_input = self::filterParamApiTopDimension($params);
            $url = self::API_INSIGHT.self::URL_API_DATA_TOP_DIMENSION;
            $response = self::callApiInsight($url,$params_input);
            $dimension = $params['dimension'];
            $network_id = $params['network_id'];
            $data_response = self::mappingDataResponseDimension($dimension, $response, $network_id);
            $config_debug = Utils::getConfigDebugRealTime();
            if(!empty($config_debug) && isset($config_debug['debug']) && $config_debug['debug'] == true){
                if(isset($config_debug['dimension']) && $config_debug['dimension'] == $dimension){
                    $data_response['is_skip'] = 1;
                }
                if(!empty($config_debug['user_id'])){
                    if(!in_array($params['user_id'],$config_debug['user_id'])){
                        $data_response['is_skip'] = 1;
                    }
                }
            }
            return $data_response;
        }catch (\Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function filterParamApiChart($params){
        try {
            $arr_filter_performance = [];
            $arr_mapping_column = self::mappingMeToInsight();

            if (!empty($params['filter_performance'])) {
                foreach ($params['filter_performance'] as $filter) {
                    $op = $filter['operator'];
                    $type_api = '';
                    switch ($op) {
                        case 'equals':
                            $type_api = 'equal';
                            break;
                        case 'greater_than':
                            $type_api = 'gt';
                            break;
                        case 'better_than':
                            $type_api = 'gt';
                            break;
                        case 'greater_than_equals':
                            $type_api = 'gte';
                            break;
                        case 'less_than':
                            $type_api = 'lt';
                            break;
                        case 'worse_than':
                            $type_api = 'lte';
                            break;
                        case 'less_than_equals':
                            $type_api = 'lte';
                            break;
                    }

                    $arr_filter_performance[] = [
                        'key' => isset($arr_mapping_column[$filter['filter']]) ? $arr_mapping_column[$filter['filter']] : $filter['filter'],
                        'type' => $type_api,
                        'value' => $filter['value']
                    ];
                }
            }

            $time_type = empty($params['time_type']) ? 'm' : $params['time_type'];
            $start_time = '';
            switch ($time_type){
                case 'm':
                    $start_time = empty($params['middle_time']) ? $start_time : $params['middle_time'];
                    break;
                case 'h':
                    $start_time = empty($params['end_time']) ? $start_time : $params['end_time'];
                    break;
            }
            $arr_metric = self::getMappingColumnApi($params['metrics']);
            $post_field = array(
                'network_id' => $params['network_id'],
                'time_type' => $time_type,
                'time_span' => empty($params['time_span']) ? self::DEFAULT_TIME_SPAN_CHART : $params['time_span'],
                'metrics' => empty($arr_metric) ? '' : implode(',',$arr_metric),
                'filters' => empty($params['filter']) ? '' : json_encode($params['filter']),
                'client_ids' => (string) $params['user_id'],
                'site_ids' => empty($params['site_ids']) ? '' : (string) implode(',', $params['site_ids']),
                'start_time' => $start_time
            );

            return $post_field;

        } catch (\Exception $exc) {
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function filterParamApiSummary($params){
        try {
            $time_type = empty($params['time_type']) ? 'm' : $params['time_type'];
            $arr_metric = self::getMappingColumnApi($params['metrics']);
            $post_field = array(
                'network_id' => $params['network_id'],
                'time_type' => $time_type,
                'time_span' => empty($params['time_span']) ? self::DEFAULT_TIME_SPAN_CHART : $params['time_span'],
                'metrics' => empty($arr_metric) ? '' : implode(',',$arr_metric),
                'client_ids' => (string) $params['user_id'],
                'site_ids' => empty($params['site_ids']) ? '' : (string) implode(',', $params['site_ids'])
            );

            return $post_field;

        } catch (\Exception $exc) {
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function filterParamApiTopDimension($params){
        try {
            if(empty($params['dimension'])){
                return false;
            }

            $time_type = empty($params['time_type']) ? 'm' : $params['time_type'];
            $arr_metric = self::getMappingColumnApi($params['metrics']);

            $metric_order = empty($params['order_by']) ? '' : $params['order_by'];
            $order_by = self::mappingOrderBy($metric_order);

            $post_field = array(
                'network_id' => $params['network_id'],
                'time_type' => $time_type,
                'time_span' => empty($params['time_span']) ? self::DEFAULT_TIME_SPAN_CHART : $params['time_span'],
                'metrics' => empty($arr_metric) ? '' : implode(',',$arr_metric),
                'client_ids' => (string) $params['user_id'],
                'site_ids' => empty($params['site_ids']) ? '' : (string) implode(',', $params['site_ids']),
                'oder_by' => $order_by,
                'limit' => empty($params['limit']) ? 10 : (int) $params['limit'],
                'order_direction' => empty($params['order_direction']) ? 'desc' : (string) $params['order_direction'],
                'dimension' => $params['dimension']
            );

            return $post_field;

        } catch (\Exception $exc) {
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function mappingOrderBy($metric){
        $arr_metric = self::mappingMeToInsight();
        if(empty($arr_metric[$metric])){
            return 'pageviews';
        }
        return $arr_metric[$metric];
    }

    public static function filterParamApiDimension($params){
        try {
            $arr_filter_performance = [];
            $arr_mapping_column = self::mappingMeToInsight();

            if (!empty($params['filter_performance'])) {
                foreach ($params['filter_performance'] as $filter) {
                    $op = $filter['operator'];
                    $type_api = '';
                    switch ($op) {
                        case 'equals':
                            $type_api = 'equal';
                            break;
                        case 'greater_than':
                            $type_api = 'gt';
                            break;
                        case 'better_than':
                            $type_api = 'gt';
                            break;
                        case 'greater_than_equals':
                            $type_api = 'gte';
                            break;
                        case 'less_than':
                            $type_api = 'lt';
                            break;
                        case 'worse_than':
                            $type_api = 'lte';
                            break;
                        case 'less_than_equals':
                            $type_api = 'lte';
                            break;
                    }

                    $arr_filter_performance[] = [
                        'key' => isset($arr_mapping_column[$filter['filter']]) ? $arr_mapping_column[$filter['filter']] : $filter['filter'],
                        'type' => $type_api,
                        'value' => $filter['value']
                    ];
                }
            }

            $time_type = empty($params['time_type']) ? 'm' : $params['time_type'];

            $arr_metric = self::getMappingColumnApi($params['metrics']);

            $post_field = array(
                'network_id' => $params['network_id'],
                'time_type' => $time_type,
                'time_span' => empty($params['time_span']) ? self::DEFAULT_TIME_SPAN_CHART : $params['time_span'],
                'metrics' => empty($arr_metric) ? '' : implode(',',$arr_metric),
                'client_ids' => (string) $params['user_id'],
                'site_ids' => empty($params['site_ids']) ? '' : (string) implode(',', $params['site_ids'])
            );

            return $post_field;

        } catch (\Exception $exc) {
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function callApiInsight($url , $params_input){
        try{
            $method = 'POST';
            $options = array(
                'timeout' => self::DEFAULT_TIME_OUT_API
            );
            $header = [
                'x-auth-token:' .self::ACCESS_TOKEN_API_INSIGHT
            ];
            return Utils::curl($url, $method, $options, $params_input, $header);
        }catch (\Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function mappingDataResponseChart($data){
        try{
            if(empty($data['code']) || $data['code'] != 200 || empty($data['data']['frames'])){
                return [
                    'code' => 400,
                    'status' => 'error',
                    'messages' => 'api error',
                    'data' => [
                        'rows' => [],
                        'total' => 0
                    ]
                ];
            }

            $data_mapping = [];
            foreach ($data['data']['frames'] as $frame){
                $data_metric = [];
                if(!empty($frame['metrics'])){
                    foreach ($frame['metrics'] as $metric => $value){
                        $data_metric[self::mappingInsightToMe($metric)] = $value;
                    }
                }
                $data_mapping[] = [
                    'time' => $frame['date_time'],
                    'metrics' => $data_metric
                ];
            }

            return [
                'code' => 200,
                'status' => 'success',
//                'total' => empty($data['data']['total_record']) ? 0 : $data['data']['total_record'],
                'data' => $data_mapping
            ];

        }catch (\Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function mappingDataResponseSummary($data){
        try{
            if(empty($data['code']) || $data['code'] != 200 || empty($data['data'])){
                return [
                    'code' => 400,
                    'status' => 'error',
                    'messages' => 'api error',
                    'data' => [
                        'rows' => [],
                        'total' => 0
                    ]
                ];
            }

            $data_mapping = [];
            foreach ($data['data'] as $metric => $value){
                $data_mapping[self::mappingInsightToMe($metric)] = $value;
            }

            echo '\n';
            print_r($data_mapping);
            echo '\n';

            return [
                'code' => 200,
                'status' => 'success',
                'data' => $data_mapping
            ];

        }catch (\Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function mappingDataResponseDimension($dimension, $data, $network_id = ''){
        try{
            if(empty($data['code']) || $data['code'] != 200 || empty($data['data']['list'])){
                return [
                    'code' => 400,
                    'status' => 'error',
                    'messages' => 'api error',
                    'data' => [
                        'rows' => []
                    ]
                ];
            }

            $data = $data['data']['list'];
            switch ($dimension){
                case 'countryCode':
                    $data_mapping = self::mappingWidgetCountry($data, $network_id);
                    break;
                case 'section':
                    $data_mapping = self::mappingWidgetSection($data, $network_id);
                    break;
                case 'refDomainId':
                    $data_mapping = self::mappingWidgetReferrerId($data, $network_id);
                    break;
                case 'siteId':
                    $data_mapping = self::mappingWidgetWebsite($data, $network_id);
                    break;
                case 'topics':
                    $data_mapping = self::mappingWidgetTopic($data, $network_id);
                    break;
                case 'gender':
                    $data_mapping = self::mappingWidgetGender($data, $network_id);
                    break;
                case 'ageRange':
                    $data_mapping = self::mappingWidgetAge($data, $network_id);
                    break;
                case 'browserId':
                    $data_mapping = self::mappingWidgetBrowser($data, $network_id);
                    break;
                case 'osId':
                    $data_mapping = self::mappingWidgetOs($data, $network_id);
                    break;
                case 'devTypeId':
                    $data_mapping = self::mappingWidgetDevice($data, $network_id);
                    break;
                case 'osVersion':
                    $data_mapping = self::mappingWidgetOsVersion($data, $network_id);
                    break;
                case 'intTime':
                    $data_mapping = self::mappingWidgetInterest($data, $network_id);
                    break;
                case 'imkTime':
                    $data_mapping = self::mappingWidgetInMarket($data, $network_id);
                    break;
                case 'keywords':
                    $data_mapping = self::mappingWidgetKeyword($data, $network_id);
                    break;
                case 'urlId':
                    $data_mapping = self::mappingWidgetUrl($data, $network_id);
                    break;
                case 'urlTitle':
                    $data_mapping = self::mappingWidgetUrlTitle($data, $network_id);
                    break;
                case 'cusTopics':
                    $data_mapping = self::mappingWidgetCusTopic($data, $network_id);
                    break;
                case 'utmSource':
                    $data_mapping = self::mappingWidgetUtmSource($data, $network_id);
                    break;
                case 'utmMedium':
                    $data_mapping = self::mappingWidgetUtmMedium($data, $network_id);
                    break;
                case 'utmCampaign':
                    $data_mapping = self::mappingWidgetUtmCampaign($data, $network_id);
                    break;
                case 'utmContent':
                    $data_mapping = self::mappingWidgetUtmContent($data, $network_id);
                    break;
                case 'utmTerm':
                    $data_mapping = self::mappingWidgetUtmTerm($data, $network_id);
                    break;
                case 'goals':
                    $data_mapping = self::mappingWidgetGoal($data, $network_id);
                    break;
                case 'resId':
                    $data_mapping = self::mappingWidgetCusResId($data, $network_id);
                    break;
                case 'browserVerId':
                    $data_mapping = self::mappingWidgetBrowserVersion($data, $network_id);
                    break;
                case 'deviceBrand':
                    $data_mapping = self::mappingWidgetDeviceBrand($data, $network_id);
                    break;
                case 'provinceId':
                    $data_mapping = self::mappingWidgetProvince($data, $network_id);
                    break;
                default:
                    $data_mapping = [
                        'rows' => [],
                        'column_defs' => []
                    ];
                    break;
            }

            echo '\n';
            print_r($data_mapping);
            echo '\n';
            echo '<pre>';

            return [
                'code' => 200,
                'status' => 'success',
                'data' => $data_mapping
            ];

        }catch (\Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public static function mappingWidgetCountry($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'id'){
                    $filter[] = [
                        'location_code' => strtoupper($value),
                        'network_id' => $network_id
                    ];
                    $data['location_code'] = strtoupper($value);
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('location_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['location_name'] = '';
                $key = $item['location_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['location_name'] = $data_info[$key]['location_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Location Name',
                'name' => 'location_name'
            ],
            [
                'header' => 'Location Id',
                'name' => 'location_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetSection($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'id'){
                    $filter[] = [
                        'section_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['section_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('sections', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['section_name'] = '';
                $key = $item['section_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['section_name'] = $data_info[$key]['section_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Category Name',
                'name' => 'section_name'
            ],
            [
                'header' => 'Category Id',
                'name' => 'section_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetReferrerId($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'refDomainId'){
                    $filter[] = [
                        'ref_domain_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['ref_domain_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('ref_domains', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['ref_domain_name'] = '';
                $key = $item['ref_domain_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['ref_domain_name'] = $data_info[$key]['ref_domain_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Referrer Domain Name',
                'name' => 'ref_domain_name'
            ],
            [
                'header' => 'Referrer Domain Id',
                'name' => 'ref_domain_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetWebsite($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'id'){
                    $filter[] = [
                        'website_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['website_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('website', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['website_name'] = '';
                $key = $item['website_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['website_name'] = $data_info[$key]['website_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Website Name',
                'name' => 'website_name'
            ],
            [
                'header' => 'Website Id',
                'name' => 'website_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetTopic($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'topics'){
                    $filter[] = [
                        'topic_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['topic_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('topic_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['topic_name'] = '';
                $key = $item['topic_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['topic_name'] = $data_info[$key]['topic_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Topic Name',
                'name' => 'topic_name'
            ],
            [
                'header' => 'Topic Id',
                'name' => 'topic_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetGender($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'id'){
                    $filter[] = [
                        'gender_id' => $value,
//                        'network_id' => $network_id
                        'network_id' => 17713
                    ];
                    $data['gender_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }
        //get info
        $data_info = ElasticSearch\Common::getDataInfo('gender_network', $filter);

        $network_id = 17713;
        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['gender_name'] = '';
                if(!empty($data_info[$item['gender_id']])){
                    $item['gender_name'] = $data_info[$item['gender_id']]['gender_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Gender Name',
                'name' => 'gender_name'
            ],
            [
                'header' => 'Gender Id',
                'name' => 'gender_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetAge($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'ageRange'){
                    $filter[] = [
                        'age_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['age_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('age_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['age_name'] = '';
                $key = $item['age_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['age_name'] = $data_info[$key]['age_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Age Name',
                'name' => 'age_name'
            ],
            [
                'header' => 'Age Id',
                'name' => 'age_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetBrowser($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'browserId'){
                    $filter[] = [
                        'browser_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['browser_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('browser_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['browser_name'] = '';
                $key = $item['browser_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['browser_name'] = $data_info[$key]['browser_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Browser Name',
                'name' => 'browser_name'
            ],
            [
                'header' => 'Browser Id',
                'name' => 'browser_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetOs($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'osId'){
                    $filter[] = [
                        'os_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['os_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('os_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['os_name'] = '';
                $key = $item['os_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['os_name'] = $data_info[$key]['os_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Os Name',
                'name' => 'os_name'
            ],
            [
                'header' => 'Os Id',
                'name' => 'os_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetDevice($params, $network_id){
        $network_id = 17713;
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'id'){
                    $filter[] = [
                        'device_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['device_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('device_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['device_name'] = '';
                if(!empty($data_info[$item['device_id']])){
                    $item['device_name'] = $data_info[$item['device_id']]['device_name'];
                }
            }
        }

        //calculator for chart

        echo '<pre>';
        print_r($data_mapping);
        echo '</pre>';
        die();

        //define column
        $define_column = [
            [
                'header' => 'Device Name',
                'name' => 'device_name'
            ],
            [
                'header' => 'Device Id',
                'name' => 'device_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetOsVersion($params , $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'osVersion'){
                    $filter[] = [
                        'os_version_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['os_version_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('os_version_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['os_version_name'] = '';
                $key = $item['os_version_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['os_version_name'] = $data_info[$key]['os_version_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Os Version Name',
                'name' => 'os_version_name'
            ],
            [
                'header' => 'Os Version Id',
                'name' => 'os_version_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetInterest($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'intTime'){
                    $filter[] = [
                        'interest_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['interest_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('interest_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['interest_name'] = '';
                $key = $item['interest_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['interest_name'] = $data_info[$key]['interest_name_vn'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Interest Name',
                'name' => 'interest_name'
            ],
            [
                'header' => 'Interest Id',
                'name' => 'interest_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetInMarket($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'imkTime'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetKeyword($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'keywords'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetUrl($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'urlId'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetUrlTitle($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'urlTitle'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetCusTopic($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'cusTopics'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetUtmSource($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'utmSource'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetUtmMedium($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'utmMedium'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetUtmCampaign($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'utmCampaign'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetUtmContent($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'utmContent'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetUtmTerm($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'utmTerm'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetGoal($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'goals'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetCusResId($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'resId'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetBrowserVersion($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'browserVerId'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetDeviceBrand($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'deviceBrand'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingWidgetProvince($params, $network_id){
        $data_mapping = $filter = $columns =[];
        foreach ($params as $row){
            $data = [];
            foreach ($row as $metric => $value){
                if($metric == 'provinceId'){
                    $filter[] = [
                        'inmarket_id' => $value,
                        'network_id' => $network_id
                    ];
                    $data['inmarket_id'] = $value;
                    continue;
                }
                if(!in_array(self::mappingInsightToMe($metric), $columns)){
                    $columns[] = self::mappingInsightToMe($metric);
                }
                $data[self::mappingInsightToMe($metric)] = $value;
            }
            $data_mapping[] = $data;
        }

        //get info
        $data_info = ElasticSearch\Common::getDataInfo('inmarket_network', $filter);

        if(!empty($data_info)){
            foreach ($data_mapping as &$item){
                $item['inmarket_name'] = '';
                $key = $item['inmarket_id'] . $network_id;
                if(!empty($data_info[$key])){
                    $item['inmarket_name'] = $data_info[$key]['inmarket_name'];
                }
            }
        }

        //define column
        $define_column = [
            [
                'header' => 'Inmakert Name',
                'name' => 'inmarket_name'
            ],
            [
                'header' => 'Inmarket Id',
                'name' => 'inmarket_id'
            ],
        ];
        foreach ($columns as $column){
            $define_column[] = [
                'header' => self::mappingDefineColumn($column),
                'name' => $column
            ];
        }

        return [
            'rows' => $data_mapping,
            'column_defs' => $define_column
        ];
    }

    public static function mappingDefineColumn($input_column){
        $arr_columns = [
            'page_view' => 'Page views',
            'visitor' => 'Users',
            'session' => 'Sessions',
            'new_session' => 'New Sessions',
            'percent_new_session' => 'Percent New Sessions',
            'avg_session_duration' => 'Avg Session Duration',
            'avg_time_on_page' => 'Avg Time On Page',
            'bounces' => 'Bounces',
            'bounce_rate' => 'Bounce Rate',
            'page_views_per_session' => 'Page Views Per Session',
            'page_views_per_user' => 'Page Views Per User',
            'clicks' => 'Clicks',
            'impressions' => 'Impressions',
            'ctr' => 'CTR'
        ];
        if(empty($arr_columns[$input_column])){
            return '';
        }

        return $arr_columns[$input_column];
    }

    public static function getColumnApiInsight()
    {
        return [
            'pageviews',
            'users'
        ];
    }

    public static function mappingInsightToMe($metric_insight){
        $arr_mapping = [
            'pageviews' => 'page_view',
            'users' => 'visitor',
            'sessions' => 'session',
            'newSessions' => 'new_session',
            'percentNewSessions' => 'percent_new_session',
            'percentNewUsers' => 'percent_new_visitor',
            'avgSessionDuration' => 'avg_session_duration',
            'avgTimeOnPage' => 'avg_time_on_page',
            'bounces' => 'bounce',
            'bounceRate' => 'bounce_rate',
            'pageviewsPerSession' => 'page_views_per_session',
            'pageviewsPerUser' => 'page_views_per_user',
            'clicks' => 'click',
            'impressions' => 'impression',
            'ctr' => 'ctr'
        ];

        if(!isset($metric_insight) && empty($metric_insight)){
            return $arr_mapping;
        }

        if(!empty($arr_mapping[$metric_insight])){
            return $arr_mapping[$metric_insight];
        }

        return false;
    }

    public static function mappingMeToInsight(){
        return [
            'page_view' => 'pageviews',
            'visitor' => 'users',
            'session' => 'sessions',
            'new_session' => 'newSessions',
            'percent_new_visitor' => 'percentNewUsers',
            'percent_new_session' => 'percentNewSessions',
            'avg_session_duration' => 'avgSessionDuration',
            'avg_time_on_page' => 'avgTimeOnPage',
            'bounces' => 'bounces',
            'bounce_rate' => 'bounceRate',
            'page_views_rer_session' => 'pageviewsPerSession',
            'clicks' => 'clicks',
            'impressions' => 'impressions',
            'ctr' => 'ctr'
        ];
    }

    public static function getMappingColumnApi($input_metric){

        if(empty($input_metric)){
            return [
                'pageviews',
                'users'
            ];
        }

        if(!is_array($input_metric)){
            $input_metric = (array) $input_metric;
        }

        $arr_metric_insight = self::mappingMeToInsight();
        $data = [];
        foreach ($input_metric as $metric){
            if(!empty($arr_metric_insight[$metric])){
                $data[] = $arr_metric_insight[$metric];
            }
        }

        return $data;
    }

    public static function testCallApiInsight($url , $params_input){
        try{
            $data = [
                'avgTimeOnSession' => rand(3000, 10000), //89196.50188080939,
                'sessionNonBounces' => rand(500000, 1000000), //577153,
                'users' => rand(1000000, 10000000), // 1447764,
                'sessions' => rand(1000000, 5000000),
                'pagePerSession ' => mt_rand (1*10, 3*10) / 10, //2.3872715029697336,
//                    'status' => 1,
                'pageviewsPerSession' => mt_rand (1*10, 3*10) / 10, //2.3872715029697336,
                'bounceRate' => mt_rand (50*10, 100*10) / 10, //68.3005236441768,
                'pagePerUser' => mt_rand (1*10, 10*10) / 10, //3.002222737960054,
                'bounces' => rand(1000000, 7000000), //1243549,
                'timeSpent' => rand(10000000000, 20000000000), //19633755601,
                'sessionBounces' => rand(1000000, 7000000),
                'avgTimeOnSite' => mt_rand (3000*10, 6000*10) / 10,
                'percentNewUsers'=> mt_rand (10*10, 80*10) / 10,
                'avgSessionDuration'=> mt_rand (50000*10, 150000*10) / 10,
                'pageviewsPerUser' => mt_rand (1*10, 5*10) / 10,
                'percentNewSession' => mt_rand (10*10, 100*10) / 10,
                'sessionsTimeExist' => rand(100000, 500000),
                'newSession'=> rand(100000, 700000),
                'avgTimeOnPage' => mt_rand (1000*10, 10000*10) / 10,
                'pageviewsTimeExist' => rand(1000000, 7000000),
                'pageviews'=> rand(1000000, 10000000),
                'newSessions'=> rand(100000, 1000000),
                'percentNewSessions' => mt_rand (10*10, 100*10) / 10
            ];

            $data_response = [];
            $arr_metric = explode(',', $params_input['metrics']);

            foreach ($arr_metric as $metric){
                $data_response[$metric] = 0;
                if(!empty($data[$metric])){
                    $data_response[$metric] = $data[$metric];
                }
            }
            return [
                'code' => 200,
                'data' => $data_response
            ];
        }catch (\Exception $exc){
            if(APPLICATION_ENV !== 'production'){
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }
    }
}