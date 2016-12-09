<?php
/**
 * Created by PhpStorm.
 * User: CP7
 * Date: 01-Dec-16
 * Time: 11:31 AM
 */
namespace ADX\Business;

use ADX\Model;
use ADX\DAO;
use ADX\Business;
use ADX\Search\Placement;
use ADX\Utils;
use ADX\Nosql;

class Performance
{
    public function validateParamsPerformance($params)
    {
        try {
            $params['from_date'] = (
            isset($params['from_date']) && $params['from_date'] != ''
                ?
                (
                isset($params['from_time']) && !empty($params['from_time']) && $params['type'] == 'hour'
                    ?
                    date('Y-m-d', strtotime(Utils::formatDate($params['from_date']))) . ' ' . $params['from_time']
                    :
                    date('Y-m-d H:i:s', strtotime(preg_replace("/(\d+)\/(\d+)\/(\d+)/", "\\3-\\2-\\1", $params['from_date'])))
                )
                :
                Utils::getDate(0, 1, -7, 0)
            );

            $params['to_date'] = (
            isset($params['to_date']) && $params['to_date'] != ''
                ?
                (
                isset($params['to_time']) && !empty($params['to_time']) && $params['type'] == 'hour'
                    ?
                    date('Y-m-d', strtotime(Utils::formatDate($params['from_date']))) . ' ' . $params['to_time']
                    :
                    date('Y-m-d H:i:s', strtotime(preg_replace("/(\d+)\/(\d+)\/(\d+)/", "\\3-\\2-\\1", $params['to_date'])) + 86399)
                )
                :
                Utils::getDate(0, 0, 0, 0)
            );

            if (strtotime($params['to_date']) < strtotime($params['from_date'])) {
                $params['to_date'] = date('Y-m-d H:i:s', strtotime($params['from_date']) + 604800);
            }

            $columns = Business\Metric::getMetricPerformance($params);

            $params['limit'] = isset($params['limit']) ? max(1, intval($params['limit'])) : null;
            $params['offset'] = isset($params['page']) ? $params['limit'] * (max(1, intval($params['page'])) - 1) : null;

            //get column
            $column_info = array();
            $arr_metric_code_output = $arr_metric_code_performance = array();

            $column_is_lasted = Model\User::getLastedModifyColumn(
                array(
                    'user_id' => $params['user_id'],
                    'manager_id' => $params['manager_id'],
                    'network_id' => $params['network_id'],
                    'type' => $params['type'],
                    'is_lasted' => Model\Column::IS_LASTED
                )
            );
            //
            if ($column_is_lasted->count() > 0) {
                $column_is_lasted = $column_is_lasted[0];
                $columns_decode = json_decode($column_is_lasted->columns);
                //
                if (!empty($columns_decode)) {
                    foreach ($columns_decode as $metric) {
                        //
                        $metric_detail = DAO\Metric::getDetailMetric(
                            array(
                                'user_id' => $params['manager_id'],
                                'network_id' => $params['network_id'],
                                'type' => $params['type'],
                                'metric_id' => $metric->metric_id
                            )
                        );
                        //
                        if (count($metric_detail) > 0) {
                            $metric_detail = $metric_detail[0];
                            //
                            if (isset($params['format']) && $params['format'] = 'grid') {
                                $column_defined[] = array(
                                    'header' => $metric_detail->metric_name,
                                    'name' => strtolower($metric_detail->metric_code),
                                    'enableSorting' => $metric_detail->is_sort ? $metric_detail->is_sort : true,
                                    'type' => $metric_detail->data_type ? $metric_detail->data_type : Model\Metric::METRIC_TYPE_TEXT,
                                    'is_performance' => isset($metric_detail->is_performance) ? $metric_detail->is_performance : 0
                                );
                            }
                        }
                    }
                    //
                    if (!empty($column_defined)) {
                        $params['column_defined'] = $column_defined;
                    }
                    $params['field_info'] = self::getFieldInfo($params['object'], $columns_decode);
                }
            }

            //
            //Filter
            $params['sort_info'] = array();
            $params['sort_performance'] = array();
            $params['filter_info'] = array();
            $params['filter_performance'] = array();
            $filter_col = $filter_val = $filter_info = array();

            if (isset($params['filter']) && !empty(json_decode($params['filter'], true))) {
                foreach (json_decode($params['filter'], true) as $item_filter) {
                    foreach ($item_filter as $field => $row) {
                        if (array_key_exists(strtoupper($field), $columns)) {
                            $row = (array)$row;
                            foreach ($row as $op => $value) {

                                //filter info
                                if ($columns[strtoupper($field)]->is_performance == 0) {
                                    $filter_info[] = array(
                                        'filter' => strtolower($field),
                                        'operator' => $op,
                                        'value' => $value
                                    );
                                } else {
                                    //filter performance
                                    switch ($op) {
                                        case 'equals':
                                        case 'on':
                                        case 'is':
                                            $filter_col[] = strtoupper($field);
                                            $filter_val[] = '= ' . $value;
                                            break;
                                        case 'greater_than':
                                        case 'after':
                                        case 'better_than':
                                            $filter_col[] = strtoupper($field);
                                            $filter_val[] = '> ' . $value;
                                            break;
                                        case 'greater_than_equals':
                                            $filter_col[] = strtoupper($field);
                                            $filter_val[] = '>= ' . $value;
                                            break;
                                        case 'less_than':
                                        case 'before':
                                        case 'worse_than':
                                            $filter_col[] = strtoupper($field);
                                            $filter_val[] = '< ' . $value;
                                            break;
                                        case 'less_than_equals':
                                            $filter_col[] = strtoupper($field);
                                            $filter_val[] = '<= ' . $value;
                                            break;
                                        case 'contain':
                                        case 'matches_any':
                                        case 'contains_all':
                                        case 'contains_any':
                                        case 'contains_none':
                                            $filter_col[] = strtoupper($field);
                                            $filter_val[] = 'IN (' . $value . ')';
                                            break;
                                        case 'does_not_contain':
                                            $filter_col[] = strtoupper($field);
                                            $filter_val[] = 'NOT IN (' . $value . ')';
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($filter_info)) {
                $params['filter_info'] = $filter_info;
            }

            if (empty($filter_col) && empty($filter_val)) {
                $params['filter_performance']['performance'] = false;
            } else {
                $params['filter_performance']['performance'] = true;
            }

            //Filter date time
            if (isset($params['from_date']) && isset($params['to_date'])) {
                $filter_col[] = 'FROM_DATE';
                $filter_col[] = 'TO_DATE';
                $filter_val[] = $params['from_date'];
                $filter_val[] = $params['to_date'];
            }

            if (!empty($filter_col) && !empty($filter_val) && count($filter_col) == count($filter_val)) {

                $params['filter_performance']['filter_col'] = $filter_col;
                $params['filter_performance']['filter_val'] = $filter_val;

                unset($params['filter']);
            }

            // sort
            if (isset($params['sort']) && array_key_exists(strtoupper($params['sort']), $columns) && in_array(strtolower($params['az']), ['desc', 'asc'])) {
                $is_sorted_by_private_key = false;
                if (isset($params['private_key']) && strtoupper($params['sort'] == strtoupper($params['private_key']))) {
                    $is_sorted_by_private_key = true;
                }

                if ($columns[strtoupper($params['sort'])]->is_performance == 0 && !$is_sorted_by_private_key) {
                    //
                    $params['sort_info'] = array(
                        'sort' => strtoupper($params['sort']),
                        'az' => strtoupper($params['az']),
                    );
                } else {
                    $params['sort_performance'] = array(
                        'sort' => strtoupper($params['sort']),
                        'az' => strtoupper($params['az'])
                    );
                }

                unset($params['sort']);
                unset($params['az']);
                unset($params['sort_id']);
            }

            // Sort default ctime
            if (empty($params['sort_info']) && empty($params['sort_performance']) && isset($params['format'])
                && $params['format'] == 'grid'
            ) {
                $params['sort_info'] = array(
                    'sort' => 'ctime',
                    'az' => 'desc'
                );
            }

            return $params;

        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    private function getFieldInfo($object_name, $metrics)
    {
        try {
            $field_info = array();
            if (!empty($metrics)) {
                foreach ($metrics as $metric) {
                    if (!isset($metric->is_performance) || $metric->is_performance != 1) {
                        //
                        $object_field = self::getObjectFields($object_name, strtolower($metric->metric_code));
                        //
                        if (!empty($object_field)) {
                            if (isset($object_field['obj'])) {
                                $field_info[$object_field['obj']]['mapping'][] = array(
                                    'output_name' => $object_field['output_name'],
                                    'mapping_name' => $object_field['mapping_name']
                                );
                                $field_info[$object_field['obj']]['private_key'] = $object_field['private_key'];
                                //
                                if (!isset($object_field['mapping_object'])) {
                                    $field_info[$object_name]['mapping'][] = array(
                                        'output_name' => isset($object_field['output_key']) ? $object_field['output_key'] : $object_field['mapping_key'],
                                        'mapping_name' => $object_field['mapping_key']
                                    );
                                    $field_info[$object_name]['private_key'] = $object_field['private_key'];
                                } else {
                                    $field_info[$object_field['mapping_object']]['mapping'][] = array(
                                        'output_name' => isset($object_field['output_key']) ? $object_field['output_key'] : $object_field['mapping_key'],
                                        'mapping_name' => $object_field['mapping_key']
                                    );
                                    $field_info[$object_field['mapping_object']]['private_key'] = $object_field['private_key'];
                                }
                                //
                            } else if (isset($object_field['output_name'])) {
                                $field_info[$object_name]['mapping'][] = array(
                                    'output_name' => $object_field['output_name'],
                                    'mapping_name' => $object_field['mapping_name']
                                );
                                $field_info[$object_name]['private_key'] = $object_field['private_key'];
                            }
                        }
                    }
                }
            }
            return $field_info;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    private function getObjectFields($object_name, $field_name = null)
    {
        try {
            $object = [];
            switch ($object_name) {
                case 'deal':
                    switch ($field_name) {
                        case 'package_name':
                            $object = array(
                                'output_name' => "package_name",
                                'mapping_name' => "package_name"
                            );
                            break;
                        case 'price':
                            $object = array(
                                'output_name' => "sale_price",
                                'mapping_name' => "sale_price"
                            );
                            break;
                        case 'price_buy':
                            $object = array(
                                'output_name' => "buy_price",
                                'mapping_name' => "buy_price",
                            );
                            break;
                        case 'discount':
                            $object = array(
                                'output_name' => "discount",
                                'mapping_name' => "discount"
                            );
                            break;
                        case 'from_date':
                            $object = array(
                                'output_name' => "from_date",
                                'mapping_name' => "from_date"
                            );
                            break;
                        case 'to_date':
                            $object = array(
                                'output_name' => "to_date",
                                'mapping_name' => "to_date"
                            );
                            break;
                        case 'status':
                            $object = array(
                                'output_name' => "status",
                                'mapping_name' => "operational_status"
                            );
                            break;
                        case 'user_name':
                            break;
                    }
                    $object['private_key'] = 'package_id';
                    break;
            }

            return $object;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public static function processDataPerformance($params)
    {
        //Call function
        $model = $params['model'];
        $function_name = $params['function_name'];
        $performance = array();

        $arr_filter = array(
            'filter' => 'price',
            'operator' => 'equals',
            'value' => '12311',
        );
        $params['filter_info'][] = $arr_filter;

        //Filter performance
        if (empty($params['sort_info']) && empty($params['sort_performance']) && empty($params['filter_info']) &&
            !empty($params['filter_performance'])
        ) {

            if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                $params['filter_col'] = $params['filter_performance']['filter_col'];
                $params['filter_val'] = $params['filter_performance']['filter_val'];
            }

            //unset
            unset($params['filter_info']);
            unset($params['filter_performance']);
            unset($params['sort_info']);
            unset($params['sort_performance']);

            $performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array($params));
        }

        //Filter info + Filter performance
        if (empty($params['sort_info']) && empty($params['sort_performance']) && !empty($params['filter_info']) &&
            !empty($params['filter_performance'])
        ) {

            $filter = Model\Performance::getDataFilter(array_merge($params, array(
                'limit' => 10000,
                'offset' => 0,
                'filter' => $params['filter_info']
            )));

            if (isset($filter['rows']) && !empty($filter['rows'])) {

                $arr_object_id = array();

                foreach ($filter['rows'] as $row) {
                    if (isset($row[$params['private_key']])) {
                        $key = $row[$params['private_key']];
                        $arr_object_id[] = $key;
                    }
                }

                //Total records
                $params['total_records'] = isset($filter['total']) ? $filter['total'] : 0;

                if (!empty($arr_object_id)) {
                    $params[$params['private_key']] = $arr_object_id;
                }

                if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                    $params['filter_col'] = $params['filter_performance']['filter_col'];
                    $params['filter_val'] = $params['filter_performance']['filter_val'];
                }

                //unset
                unset($params['filter_info']);
                unset($params['filter_performance']);
                unset($params['sort_info']);
                unset($params['sort_performance']);

                $performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array($params));
            } else {
                $performance = array();
            }
        }

        //Sort info + filter info
        if (!empty($params['sort_info']) && empty($params['sort_performance']) && !empty($params['filter_info']) &&
            empty($params['filter_performance'])
        ) {

            $filter = Model\Performance::getDataFilter(array_merge($params, array(
                'limit' => 10000,
                'offset' => 0,
                'filter' => $params['filter_info']
            )));

            //
            if (isset($filter['rows']) && !empty($filter['rows'])) {

                $arr_object_id = array();

                foreach ($filter['rows'] as $row) {
                    if (isset($row[$params['private_key']])) {
                        $key = $row[$params['private_key']];
                        $arr_object_id[] = $key;
                    }
                }

                //Total records
                $params['total_records'] = isset($filter['total']) ? $filter['total'] : 0;

                if (!empty($arr_object_id)) {
                    $params[$params['private_key']] = $arr_object_id;
                }

                if (isset($params['sort_info']['sort']) && isset($params['sort_info']['az'])) {
                    $params['sort'] = strtolower($params['sort_info']['sort']);
                    $params['az'] = $params['sort_info']['az'];
                }

                $sorting = Model\Performance::getDataSorting($params);

                //
                if (isset($sorting['rows']) && !empty($sorting['rows'])) {
                    $arr_object_id = array();

                    foreach ($sorting['rows'] as $row) {
                        $arr_object_id[] = $row[$params['private_key']];
                    }

                    $params[$params['private_key']] = $arr_object_id;

                    $params['total_records'] = isset($sorting['total']) ? $sorting['total'] : 0;
                    $params['offset'] = 0;
                }

                if (isset($params['sort'])) {
                    unset($params['sort']);
                }

                if (isset($params['az'])) {
                    unset($params['az']);
                }

                if (isset($params['sort_id'])) {
                    unset($params['sort_id']);
                }

                if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                    $params['filter_col'] = $params['filter_performance']['filter_col'];
                    $params['filter_val'] = $params['filter_performance']['filter_val'];
                }

                //unset
                unset($params['filter_info']);
                unset($params['filter_performance']);
                unset($params['sort_info']);
                unset($params['sort_performance']);

                $performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array($params));

                //Sắp xếp lại
                if (!empty($performance)) {
                    foreach ($performance as $row) {
                        if (isset($row[$params['private_key']])) {
                            $rows[$row[$params['private_key']]] = $row;
                        }
                    }
                }

                if (isset($params[$params['private_key']]) && !empty($params[$params['private_key']])) {
                    $sorting = array();
                    foreach ($params[$params['private_key']] as $key) {
                        if (isset($rows[$key])) {
                            $sorting[] = $rows[$key];
                        }
                    }

                    $performance = $sorting;
                }
            } else {
                $performance = array();
            }
        }

        //Sort info + filter performance
        if (!empty($params['sort_info']) && empty($params['sort_performance']) && empty($params['filter_info']) &&
            !empty($params['filter_performance'])
        ) {

            if ($params['filter_performance']['performance']) {
                if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                    $params['filter_col'] = $params['filter_performance']['filter_col'];
                    $params['filter_val'] = $params['filter_performance']['filter_val'];
                }

                $performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array(array_merge($params, array(
                    'limit' => '',
                    'offset' => ''
                ))));

                $rows = array();
                $arr_object_id = array();
                if (!empty($performance)) {
                    foreach ($performance as $row) {
                        if (isset($row[$params['private_key']])) {
                            $key = $row[$params['private_key']];
                            $arr_object_id[] = $key;
                        }
                        $rows[$key] = $row;
                    }

                    //Total records
                    if (isset($performance[1]['row_count'])) {
                        $params['total_records'] = $performance[1]['row_count'];
                    }
                }

                if (!empty($arr_object_id)) {
                    $params[$params['private_key']] = $arr_object_id;
                }

                if (isset($params['sort_info']['sort']) && isset($params['sort_info']['az'])) {
                    $params['sort'] = strtolower($params['sort_info']['sort']);
                    $params['az'] = $params['sort_info']['az'];
                }

                $sorting = Model\Performance::getDataSorting($params);

                $resp = array();
                if (isset($sorting['rows']) && !empty($sorting['rows'])) {
                    foreach ($sorting['rows'] as $row) {
                        //
                        $key = '';
                        if (isset($row[$params['private_key']])) {
                            $key = $row[$params['private_key']];
                        }

                        if (isset($rows[$key])) {
                            $resp[] = $rows[$key];
                        }
                    }
                }

                $performance = $resp;

            } else {
                if (isset($params['sort_info']['sort']) && isset($params['sort_info']['az'])) {
                    $params['sort'] = strtolower($params['sort_info']['sort']);
                    $params['az'] = $params['sort_info']['az'];
                    $params['sort_id'] = isset($params['sort_info']['sort_id']) ? $params['sort_info']['sort_id'] : array();
                }

                $sorting = Model\Performance::getDataSorting($params);

                if (isset($sorting['rows']) && !empty($sorting['rows'])) {
                    $arr_object_id = array();

                    foreach ($sorting['rows'] as $row) {
                        $arr_object_id[] = $row[$params['private_key']];
                        break;
                    }

                    $params[$params['private_key']] = $arr_object_id;

                    $params['total_records'] = isset($sorting['total']) ? $sorting['total'] : 0;
                    $params['offset'] = 0;
                }

                if (isset($params['sort'])) {
                    unset($params['sort']);
                }

                if (isset($params['az'])) {
                    unset($params['az']);
                }

                if (isset($params['sort_id'])) {
                    unset($params['sort_id']);
                }

                if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                    $params['filter_col'] = $params['filter_performance']['filter_col'];
                    $params['filter_val'] = $params['filter_performance']['filter_val'];
                }

                $performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array($params));

                //Sắp xếp lại
                if (!empty($performance)) {
                    foreach ($performance as $row) {
                        if (isset($row[$params['private_key']])) {
                            $rows[$row[$params['private_key']]] = $row;
                        }
                    }
                }

                if (isset($params[$params['private_key']]) && !empty($params[$params['private_key']])) {
                    $sorting = array();
                    foreach ($params[$params['private_key']] as $key) {
                        if (isset($rows[$key])) {
                            $sorting[] = $rows[$key];
                        }
                    }

                    $performance = $sorting;
                }
            }
        }

        //Sort performance + filter performance
        if (empty($params['sort_info']) && !empty($params['sort_performance']) && empty($params['filter_info']) &&
            !empty($params['filter_performance'])
        ) {

            //
            if (isset($params['sort_performance']['sort']) && isset($params['sort_performance']['az'])) {
                $params['sort'] = strtoupper($params['sort_performance']['sort']) . ' ' . strtoupper($params['sort_performance']['az']);
            }

            if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                $params['filter_col'] = $params['filter_performance']['filter_col'];
                $params['filter_val'] = $params['filter_performance']['filter_val'];
            }

            $performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array($params));
        }

        //Sort performance + filter performance, info
        if (empty($params['sort_info']) && !empty($params['sort_performance']) && !empty($params['filter_info']) &&
            !empty($params['filter_performance'])
        ) {
            $filter = Model\Performance::getDataFilter(array_merge($params, array(
                'limit' => 10000,
                'offset' => 0,
                'filter' => $params['filter_info']
            )));

            if (isset($filter['rows']) && !empty($filter['rows'])) {

                $arr_object_id = array();

                foreach ($filter['rows'] as $row) {
                    if (isset($row[$params['private_key']])) {
                        $key = $row[$params['private_key']];
                        $arr_object_id[] = $key;
                    }
                }

                //Total records
                $params['total_records'] = isset($filter['total']) ? $filter['total'] : 0;

                if (!empty($arr_object_id)) {
                    $params[$params['private_key']] = $arr_object_id;
                }

                if (isset($params['sort_performance']['sort']) && isset($params['sort_performance']['az'])) {
                    $params['sort'] = strtoupper($params['sort_performance']['sort']) . ' ' . strtoupper($params['sort_performance']['az']);
                }

                if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                    $params['filter_col'] = $params['filter_performance']['filter_col'];
                    $params['filter_val'] = $params['filter_performance']['filter_val'];
                }

                $performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array($params));
            } else {
                $performance = array();
            }
        }

        //Sort info + filter performance, info
        if (!empty($params['sort_info']) && empty($params['sort_performance']) && !empty($params['filter_info']) &&
            !empty($params['filter_performance'])
        ) {

            if ($params['filter_performance']['performance']) {
                if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                    $params['filter_col'] = $params['filter_performance']['filter_col'];
                    $params['filter_val'] = $params['filter_performance']['filter_val'];
                }

                $performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array(array_merge($params, array(
                    'limit' => '',
                    'offset' => ''
                ))));

                $rows = array();
                $arr_object_id = array();

                if (!empty($performance)) {
                    foreach ($performance as $row) {
                        if (isset($row[$params['private_key']])) {
                            $key = $row[$params['private_key']];
                            $arr_object_id[] = $key;

                            $rows[$key] = $row;
                        }
                    }

                    //
                    if (!empty($arr_object_id)) {
                        $params[$params['private_key']] = $arr_object_id;
                    }

                    $filter = Model\Performance::getDataFilter(array_merge($params, array(
                        'limit' => 10000,
                        'offset' => 0,
                        'filter' => $params['filter_info']
                    )));

                    if (isset($filter['rows']) && !empty($filter['rows'])) {

                        $arr_object_id = array();

                        foreach ($filter['rows'] as $row) {
                            if (isset($row[$params['private_key']])) {
                                $key = $row[$params['private_key']];
                                $arr_object_id[] = $key;
                            }
                        }

                        //Total records
                        $params['total_records'] = isset($filter['total']) ? $filter['total'] : 0;

                        if (!empty($arr_object_id)) {
                            $params[$params['private_key']] = $arr_object_id;
                        }

                        //Sorting
                        if (isset($params['sort_info']['sort']) && isset($params['sort_info']['az'])) {
                            $params['sort'] = strtolower($params['sort_info']['sort']);
                            $params['az'] = $params['sort_info']['az'];
                        }

                        $sorting = Model\Performance::getDataSorting($params);

                        $resp = array();
                        if (isset($sorting['rows']) && !empty($sorting['rows'])) {
                            foreach ($sorting['rows'] as $row) {
                                //
                                if (isset($params['type'])) {
                                    $key = '';
                                    if (isset($row[$params['private_key']])) {
                                        $key = $row[$params['private_key']];
                                    }

                                    if (isset($rows[$key])) {
                                        $resp[] = $rows[$key];
                                    }
                                }
                            }
                        }

                        $performance = $resp;
                    } else {
                        $performance = array();
                    }
                }
            } else {
                $filter = Model\Performance::getDataFilter(array_merge($params, array(
                    'limit' => 10000,
                    'offset' => 0,
                    'filter' => $params['filter_info']
                )));

                if (isset($filter['rows']) && !empty($filter['rows'])) {
                    $arr_object_id = array();

                    foreach ($filter['rows'] as $row) {
                        if (isset($row[$params['private_key']])) {
                            $key = $row[$params['private_key']];
                            $arr_object_id[] = $key;
                        }
                    }

                    //Total records
                    $params['total_records'] = isset($filter['total']) ? $filter['total'] : 0;

                    if (!empty($arr_object_id)) {
                        $params[$params['private_key']] = $arr_object_id;
                    }

                    if (isset($params['sort_info']['sort']) && isset($params['sort_info']['az'])) {
                        $params['sort'] = strtolower($params['sort_info']['sort']);
                        $params['az'] = $params['sort_info']['az'];
                    }

                    $sorting = Model\Performance::getDataSorting($params);

                    if (isset($sorting['rows']) && !empty($sorting['rows'])) {
                        $arr_object_id = array();

                        foreach ($sorting['rows'] as $row) {
                            $arr_object_id[] = $row[$params['private_key']];
                        }

                        $params[$params['private_key']] = $arr_object_id;
                    }

                    if (isset($params['sort'])) {
                        unset($params['sort']);
                    }

                    if (isset($params['az'])) {
                        unset($params['az']);
                    }

                    if (isset($params['filter_performance']['filter_col']) && isset($params['filter_performance']['filter_val'])) {
                        $params['filter_col'] = $params['filter_performance']['filter_col'];
                        $params['filter_val'] = $params['filter_performance']['filter_val'];
                    }

                    //unset
                    unset($params['filter_info']);
                    unset($params['filter_performance']);
                    unset($params['sort_info']);
                    unset($params['sort_performance']);

                    //unset limit, offset
                    if (isset($params['limit'])) {
                        unset($params['limit']);
                    }

                    if (isset($params['offset'])) {
                        unset($params['offset']);
                    }

                    //$performance = call_user_func_array(array('ADX\\Model\\' . $model, $function_name), array($params));
                    $performance = array(
                        array(
                            'package_id' => 517298397,
                            'row_count' => 8,
                            'row_num' => 2
                        ),
                        array(
                            'package_id' => 517298406,
                            'row_count' => 8,
                            'row_num' => 2
                        ),
                    );
                    //Sap xep lai vi tri
                    $rows = array();
                    if (!empty($performance)) {
                        foreach ($performance as $row) {
                            if (isset($row[$params['private_key']])) {
                                $rows[$row[$params['private_key']]] = $row;
                            }
                        }
                    }

                    if (isset($params[$params['private_key']]) && !empty($params[$params['private_key']])) {
                        $sorting = array();
                        foreach ($params[$params['private_key']] as $key) {
                            if (isset($rows[$key])) {
                                $sorting[] = $rows[$key];
                            }
                        }

                        $performance = $sorting;
                    }
                } else {
                    $performance = array();
                }
            }
        }
        //mapping
        if (isset($params['field_info']) && !empty($params['field_info'])) {
            $result = self::mappingPerformance(array(
                'field_info' => $params['field_info'],
                'performance' => $performance,
                'user_id' => isset($params['user_id']) ? $params['user_id'] : '',
                'network_id' => isset($params['network_id']) ? $params['network_id'] : ''
            ));
        } else {
            $result = $performance;
        }

        //response data
        $response = self::responseDataPerformance($params, $result);

        return $response;
    }

    public function mappingPerformance($params)
    {
        try {
            $field_info = isset($params['field_info']) ? $params['field_info'] : array();
            $performance = isset($params['performance']) ? $params['performance'] : array();

            if (!empty($field_info) && !empty($performance)) {
                foreach ($field_info as $object => $fields) {

                    $performance_info = Model\Performance::getPerformanceInfo($object, $performance);
                    $performance_info = $performance_info['rows'];

                    if (!empty($performance_info)) {
                        foreach ($performance as &$row) {
                            //
                            $private_key = $fields['private_key'];
                            foreach ($fields['mapping'] as $field) {

                                if (!is_array($private_key)) {
                                    if (isset($row[$private_key]) && isset($performance_info[$row[$private_key]])
                                        && isset($performance_info[$row[$private_key]][$field['mapping_name']])) {
                                        //
                                        $row[$field['output_name']] = $performance_info[$row[$private_key]][$field['mapping_name']];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $performance;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function responseDataPerformance($params, $data)
    {
        if (isset($params['format'])) {
            switch ($params['format']) {
                case 'grid':
                    $result = self::responseDataGrid($params, $data);
                    break;
                case 'chart':
                    $result = self::responseDataChart($params, $data);
                    break;
                default:
                    $result = self::responseDataRaw($params, $data);
                    break;
            }
        } else {
            $result = self::responseDataRaw($params, $data);
        }

        return $result;
    }

    public function responseDataGrid($params, $arr_rows)
    {
        $data = array();
        if (isset($params['column_defined']) && !empty($params['column_defined'])) {
            $data['column_defs'] = $params['column_defined'];
        }
        $data['total_column'] = $params['total_column'];
        //
        if(isset($params['total_records'])){
            $data['total_records'] = $params['total_records'];
        }else{
            $data['total_records'] = !empty($arr_rows) && isset($arr_rows[0]['row_count']) ? $arr_rows[0]['row_count'] : 0;
        }

        $data['rows'] = $arr_rows;

        return $data;
    }

    public function responseDataChart($params, $rows)
    {
        //
        $result = array();
        $arr_data = array();
        $fields = isset($params['column_metrics']) ? $params['column_metrics'] : array();

        if (!empty($rows)) {
            $from_date = strtotime($params['from_date']);
            $to_date = strtotime($params['to_date']);

            switch ($params['time']) {
                case 'hour':
                    $arrRows = array();
                    foreach ($rows as &$row) {
                        if($row['day']){
                            $day = strtotime($row['day']);
                            $arrRows[$day] = $row;
                        }
                    }

                    while ($from_date <= $to_date) {
                        $next_date = strtotime("+1 hours", $from_date);
                        $row = array();
                        $row[0] = isset($arrRows[$from_date]) ? $arrRows[$from_date] : array();
                        $row[1] = array();

                        $val = array();
                        foreach ($fields as $key => $field) {
                            $val['title'] = date('H', $from_date) . 'h ' . date('F j, Y', $from_date);

                            $val[$field][0] = isset($row[0][$field]) ? $row[0][$field] : 0;
                        }

                        $from_date = $next_date;
                        $arr_data[] = $val;
                    }

                    break;
                case 'day':
                    $arrRows = array();
                    foreach ($rows as &$row) {
                        if($row['day']){
                            $day = strtotime($row['day']);
                            $arrRows[$day] = $row;
                        }
                    }

                    while ($from_date <= $to_date) {
                        $next_date = strtotime("+1 day", $from_date);
                        $row = array();
                        $row[0] = isset($arrRows[$from_date]) ? $arrRows[$from_date] : array();
                        $row[1] = array();

                        $val = array();
                        foreach ($fields as $key => $field) {
                            $val['title'] = date('F j, Y', $from_date);

                            $val[$field][0] = isset($row[0][$field]) ? $row[0][$field] : 0;
                        }

                        $from_date = $next_date;
                        $arr_data[] = $val;
                    }

                    break;
                case 'week':
                    $arrRows = array();

                    foreach ($rows as &$row) {
                        if($row['day']) {
                            $day = strtotime($row['day']);
                            $arrRows[$day] = $row;
                        }
                    }

                    while ($from_date <= $to_date) {
                        if (date('D', $from_date) != 'Mon') {
                            $from_date = strtotime('last monday', $from_date);
                        }

                        $next_date = strtotime("+1 week", $from_date);

                        $row = array();
                        $row[0] = isset($arrRows[$from_date]) ? $arrRows[$from_date] : array();
                        $row[1] = array();

                        $val = array();

                        foreach ($fields as $key => $field) {
                            $val['title'] = 'Week of ' . date('F j, Y', $from_date);

                            $val[$field][0] = isset($row[0][$field]) ? $row[0][$field] : 0;
                        }

                        $from_date = $next_date;
                        $arr_data[] = $val;
                    }

                    break;
                case 'month':
                    $arrRows = array();

                    foreach ($rows as &$row) {
                        if($row['day']) {
                            $day = strtotime($row['day']);
                            $arrRows[$day] = $row;
                        }
                    }

                    while ($from_date <= $to_date) {
                        if (date('j', $from_date) != '1') {
                            $from_date = strtotime('first day of this month', $from_date);
                        }

                        $next_date = strtotime("next month", $from_date);

                        $row = array();
                        $row[0] = isset($arrRows[$from_date]) ? $arrRows[$from_date] : array();
                        $row[1] = array();

                        $val = array();

                        foreach ($fields as $key => $field) {
                            $val['title'] = date('F Y', $from_date);

                            $val[$field][0] = isset($row[0][$field]) ? $row[0][$field] : 0;
                        }

                        $from_date = $next_date;
                        $arr_data[] = $val;
                    }

                    break;
                case 'quarter':
                    $arrRows = array();
                    $arrQuarter = array();

                    foreach ($rows as &$row) {
                        if($row['day']) {
                            $day = strtotime($row['day']);
                            $arrRows[$day] = $row;
                        }
                    }

                    while ($from_date <= $to_date) {
                        $next_date = strtotime("+1 month", $from_date);
                        $quarter = self::dateQuarter($from_date);
                        $year = date('Y', $from_date);

                        $val = array();
                        $first_date_quarter = '';

                        foreach ($fields as $key => $field) {
                            switch ($quarter) {
                                case 1:
                                    //quarter 1
                                    $row = array();
                                    $first_date_quarter = strtotime('01-01-' . $year);
                                    $row[0] = isset($arrRows[$first_date_quarter]) ? $arrRows[$first_date_quarter] : array();

                                    $val['title'] = 'Q1 ' . date('Y', $from_date);
                                    $val[$field][0] = isset($row[0][$field]) ? $row[0][$field] : 0;

                                    break;
                                case 2:
                                    //quarter 2
                                    $first_date_quarter = strtotime('01-04-' . $year);
                                    $row[0] = isset($arrRows[$first_date_quarter]) ? $arrRows[$first_date_quarter] : array();

                                    $val['title'] = 'Q2 ' . date('Y', $from_date);
                                    $val[$field][0] = isset($row[0][$field]) ? $row[0][$field] : 0;

                                    break;
                                case 3:
                                    //quarter 3
                                    $first_date_quarter = strtotime('01-07-' . $year);
                                    $row[0] = isset($arrRows[$first_date_quarter]) ? $arrRows[$first_date_quarter] : array();

                                    $val['title'] = 'Q3 ' . date('Y', $from_date);
                                    $val[$field][0] = isset($row[0][$field]) ? $row[0][$field] : 0;

                                    break;
                                case 4:
                                    //quarter 4
                                    $first_date_quarter = strtotime('01-10-' . $year);
                                    $row[0] = isset($arrRows[$first_date_quarter]) ? $arrRows[$first_date_quarter] : array();

                                    $val['title'] = 'Q4 ' . date('Y', $from_date);
                                    $val[$field][0] = isset($row[0][$field]) ? $row[0][$field] : 0;

                                    break;
                            }
                        }

                        $from_date = $next_date;
                        $arrQuarter[$first_date_quarter] = $val;
                    }

                    break;
            }

            if (!empty($arrQuarter)) {
                foreach ($arrQuarter as $quarter) {
                    $arr_data[] = $quarter;
                }
            }
        }

        $result['rows'] = $arr_data;
        $result['fields'] = $fields;

        return $result;
    }

    public function responseDataRaw($params, $rows)
    {
        return $rows;
    }

}