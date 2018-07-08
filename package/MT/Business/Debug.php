<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 18/12/2017
 * Time: 09:00
 */

namespace MT\Business;


class Debug
{
    public static function metricAction($params)
    {
//echo "[TI]" . strtotime("now") . "-" . "[SN]" . $_SERVER['SERVER_NAME'] . "-" .  "[HH]" . $_SERVER['HTTP_HOST'] . "-" .  "[AE]" . $_SERVER['APPLICATION_ENV'] . "-" .  "[AZ]" . $_SERVER['APPLICATION_ZONE'];

        $metric_current = array();

        if (isset($_GET['g1']) && !empty($_GET['g1'])) {
            $metric_current[] = $_GET['g1'];
        }

        if (isset($_GET['g2']) && !empty($_GET['g2'])) {
            $metric_current[] = $_GET['g2'];
        }

        if (isset($_GET['g3']) && !empty($_GET['g3'])) {
            $metric_current[] = $_GET['g3'];
        }

        if (isset($_GET['g4']) && !empty($_GET['g4'])) {
            $metric_current[] = $_GET['g4'];
        }

        if (isset($_GET['g5']) && !empty($_GET['g5'])) {
            $metric_current[] = $_GET['g5'];
        }

        if (isset($_GET['g6']) && !empty($_GET['g6'])) {
            $metric_current[] = $_GET['g6'];
        }

        if (isset($_GET['g7']) && !empty($_GET['g7'])) {
            $metric_current[] = $_GET['g7'];
        }


        $dimension_main_buyer = array(1, 3, 5, 7, 9);
        $dimension_main_seller = array(2, 4, 6, 8);
        $dimension_target_buyer = array(11, 33, 55, 77, 99);
        $dimension_target_seller = array(22, 44, 66, 88);
        $dimension_target_common = array(12, 14, 16, 18);
        $metric_buyer = array(111, 333, 555, 777, 999);
        $metric_seller = array(222, 444, 666, 888);

        if (isset($_GET['debug']) && $_GET['debug'] == 'khamdb') {
            if (isset($_GET['metric_current']) && !empty($_GET['metric_current'])) {
                $metric_current = explode(',', $_GET['metric_current']);
            }

            $dimension_main_buyer = array('ADVERTISER_NAME', 'LINEITEM_NAME', 'LINEITEM_ID', 'CAMPAIGN_NAME', 'CAMPAIGN_ID', 'CREATIVE_NAME', 'CREATIVE_ID');
            $dimension_main_seller = array('PUBLISHER_NAME', 'WEBSITE_NAME', 'WEBSITE_ID', 'ZONE_NAME', 'ZONE_ID', 'PLACEMENT', 'PLACEMENT_ID', 'PLACEMENT_NAME');
            $dimension_target_buyer = array('TOPIC_NAME', 'TOPIC_ID', 'INTEREST_NAME', 'INMARKET_NAME', 'AUDIENCE_NAME', 'AUDIENCE', 'AUDIENCE_ID', 'AGE_RANGE_ID', 'CARRIER_ID', 'DEVICE_MODEL', 'OS', 'OS_NAME', 'GENDER_ID', 'BROWSER_ID', 'BROWSER_VERSION');
            $dimension_target_seller = array('ZONE_FORMAT');
            $dimension_target_common = array('BID_STRATEGY_TYPE', 'DEVICE_TYPE_ID', 'DEVICE_ID', 'COUNTRY_NAME', 'CITY_NAME', 'SECTION_NAME', 'TARGETED_SECTION_NAME', 'DEVICE_TYPE');
            $metric_buyer = array('LINEITEM_TYPE', 'LINEITEM_STATUS', 'CAMPAIGN_TYPE', 'CAMPAIGN_STATUS', 'CREATIVE_STATUS', 'CREATIVE_TYPE', 'CREATIVE_REVIEW', 'BUDGET', 'OS_VERSION', 'A_CONVERTED_CLICK', 'A_CONVERSION', 'A_CONV_VALUE', 'A_CONVERTED_VALUE', 'A_CIR', 'A_CPO', 'F_CONVERSION', 'F_CONV_VALUE', 'F_CIR', 'F_CPO', 'L_CONVERSION', 'L_CONV_VALUE', 'L_CIR', 'L_CPO');
            $metric_seller = array('AD_REQUEST', 'FILL_RATE', 'VIEWABLE_AD_REQUEST', 'PASSBACK_AD_REQ', 'ADREQUEST_CTR');

            //enable where have dimension default disable
            $attribute_buyer = array(
                'LINEITEM_NAME' => array('LINEITEM_STATUS', 'BUDGET'),
                'CAMPAIGN_NAME' => array('CAMPAIGN_TYPE', 'CAMPAIGN_STATUS'),
                'CREATIVE_ID' => array('CREATIVE_STATUS', 'CREATIVE_TYPE', 'CREATIVE_REVIEW')
            );
            //disible where have dimension $dimension_main_seller, $metric_seller
            $attribution_buyer = array(
                'A_CONVERTED_CLICK',
                'A_CONVERSION',
                'A_CONV_VALUE', 'A_CONVERTED_VALUE', 'A_CIR', 'A_CPO', 'F_CONVERSION', 'F_CONV_VALUE', 'F_CIR', 'F_CPO', 'L_CONVERSION', 'L_CONV_VALUE', 'L_CIR', 'L_CPO');
        }

        $dimension_combine = array(
            'dimension_main_buyer',
            'dimension_main_seller',
            'dimension_target_common'
        );

        $dimension_combine_current = array();
        $dimension_target_combine = array_merge($dimension_target_buyer, $dimension_target_seller, $dimension_target_common);
        $action = array();
        foreach ($metric_current as $item_current) {

            /*
             *  if current item is main dimension buyer :
             *  => enable : - main dimension buyer
             *              - main dimension seller
             *              - all target dimension buyer
             *              - all target dimension common
             *              - all metric buyer
             *  => disable : - all target dimension seller
             *               - all metric seller
             *
             * => check this main dimension buyer in $dimension_combine_current
             */
            if (in_array($item_current, $dimension_main_buyer)) {
                $action['enable'][] = array(
                    'dimension_main_buyer',
                    'dimension_main_seller',
                    'dimension_target_buyer',
                    'dimension_target_common',
                    'metric_buyer'
                );
                $action['disable'][] = array(
                    'dimension_target_seller',
                    'metric_seller'
                );

                if (!in_array('dimension_main_buyer', $dimension_combine_current)) {
                    $dimension_combine_current[] = 'dimension_main_buyer';
                }
            }

            /*
            *  if current item is main dimension seller :
            *  => enable : - main dimension buyer
            *              - main dimension seller
            *              - all target dimension seller
            *              - all target dimension common
            *              - all metric seller
            *  => disable : - all target dimension buyer
            *               - all metric buyer
            *   => check this main dimension seller in $dimension_combine_current
            */
            if (in_array($item_current, $dimension_main_seller)) {
                $action['enable'][] = array(
                    'dimension_main_buyer',
                    'dimension_main_seller',
                    'dimension_target_seller',
                    'dimension_target_common',
                    'metric_seller'
                );
                $action['disable'][] = array(
                    'dimension_target_buyer',
                    'metric_buyer'
                );
                //
                if (!in_array('dimension_main_seller', $dimension_combine_current)) {
                    $dimension_combine_current[] = 'dimension_main_seller';
                }
            }

            /*
            *  if current item is target dimension buyer :
            *  => enable : - main dimension buyer
            *              - all target dimension buyer
            *              - all metric buyer
            *  => disable : - main dimension seller
            *               - all target dimension seller
            *               - all metric seller
            *               - all metric common
            */
            if (in_array($item_current, $dimension_target_buyer)) {
                $action['enable'][] = array(
                    'dimension_target_buyer',
                    'dimension_main_buyer',
                    'metric_buyer'
                );
                $action['disable'][] = array(
                    'dimension_main_seller',
                    'dimension_target_seller',
                    'dimension_target_common',
                    'metric_seller'
                );
            }

            /*
            *  if current item is target dimension seller :
            *  => enable : - main dimension seller
            *              - all target dimension seller
            *              - all metric seller
            *  => disable : - main dimension buyer
            *               - all target dimension buyer
            *               - all metric buyer
            *               - all metric common
            */
            if (in_array($item_current, $dimension_target_seller)) {
                $action['enable'][] = array(
                    'dimension_target_seller',
                    'dimension_main_seller',
                    'metric_seller'
                );
                $action['disable'][] = array(
                    'dimension_main_buyer',
                    'dimension_target_buyer',
                    'dimension_target_common',
                    'metric_buyer'
                );
            }

            /*
            *  if current item is target dimension common :
            *  => enable : - main dimension buyer
            *              - main dimension seller
            *  => disable : - all target dimension buyer
            *               - all target dimension seller
            *
            * => check this main dimension seller in $dimension_combine_current
            */
            if (in_array($item_current, $dimension_target_common)) {
                $action['enable'][] = array(
                    'dimension_target_common', //noted
                    'dimension_main_buyer',
                    'dimension_main_seller',
                    'metric_buyer', //noted
                    'metric_seller' //noted
                );
                $action['disable'][] = array(
                    'dimension_target_buyer',
                    'dimension_target_seller'
                );
                //
                if (!in_array('dimension_main_seller', $dimension_combine_current)) {
                    $dimension_combine_current[] = 'dimension_target_common';
                }
            }

            /*
            *  if current item is metric buyer :
            *  => enable : - main dimension buyer
            *              - all target dimension buyer
            *              - all metric buyer
            *  => disable : - main dimension seller
            *               - all target dimension seller
            *               - all metric seller
            */
            if (in_array($item_current, $metric_buyer)) {
                $action['enable'][] = array(
                    'metric_buyer',
                    'dimension_main_buyer',
                    'dimension_target_buyer',
                    'dimension_target_common' //noted
                );
                $action['disable'][] = array(
                    'dimension_main_seller',
                    'dimension_target_seller',
                    'metric_seller'
                );
            }

            /*
            *  if current item is metric seller :
            *  => enable : - main dimension seller
            *              - all target dimension seller
            *              - all metric seller
            *  => disable : - main dimension buyer
            *               - all target dimension buyer
            *               - all metric buyer
            */
            if (in_array($item_current, $metric_seller)) {
                $action['enable'][] = array(
                    'metric_seller',
                    'dimension_main_seller',
                    'dimension_target_seller',
                    'dimension_target_common' //noted
                );
                $action['disable'][] = array(
                    'dimension_main_buyer',
                    'dimension_target_buyer',
                    'metric_buyer'
                );
            }
        }

//
        $metric_enable = array();
        $metric_disable = array();
//
        foreach ($action as $key => $item) {
            if ($key == 'enable') {
                foreach ($item as $data_item) {
                    $metric_enable = array_merge($metric_enable, $data_item);
                }
            } else {
                foreach ($item as $data_item) {
                    $metric_disable = array_merge($metric_disable, $data_item);
                }
            }
        }
        /*
        echo '<pre>';
        print_r(array('metric_current' => $metric_current, 'metric_enable' => $metric_enable, 'metric_disable' => $metric_disable, 'dimension_combine' => $dimension_combine));
        */
//
        $metric_enable = array_unique($metric_enable);
        $metric_disable = array_unique($metric_disable);

        $data_enable = array_diff($metric_enable, $metric_disable);

        $data_disable = $metric_disable;//array_intersect($metric_disable, $metric_enable);

        $note = 'target enable only me';

        if (count($dimension_combine_current) > 1) {
            $dimension_combine_disable = array_diff($dimension_combine, $dimension_combine_current);
            //
            if (empty($dimension_combine_disable)) {
                $note = 'error params';
                //
                $data_enable = array();
                $data_disable = array();
            } else {
                //
                foreach ($dimension_combine_disable as $item_dimension_combine_disable) {
                    //
                    unset($data_enable[array_search($item_dimension_combine_disable, $data_enable)]);
                    //
                    $data_disable[] = $item_dimension_combine_disable;
                }
            }
        }

        if (isset($_GET['debug']) && $_GET['debug'] == 'khamdb') {
            //
            $data_enable_resp = array();
            $data_disable_resp = array();
            //merge string to array
            foreach ($data_enable as $item_enable) {
                //
                $item_enable_data = ${$item_enable};
                //
                $data_enable_resp = array_merge($data_enable_resp, $item_enable_data);
            }
            //merge string to array
            foreach ($data_disable as $item_disable) {
                //
                $item_disable_data = ${$item_disable};
                //
                $data_disable_resp = array_merge($data_disable_resp, $item_disable_data);
            }
            //or target

            //
            $have_target = 0;
            foreach ($metric_current as $item_current) {
                //
                if (array_search($item_current, $dimension_target_combine)) {
                    $have_target = 1;
                    unset($dimension_target_combine[array_search($item_current, $dimension_target_combine)]);
                }
                //
                if (!isset($attribute_buyer[$item_current])) {
                    continue;
                }
                //
                unset($attribute_buyer[$item_current]);
            }

            //
            if (!empty($attribute_buyer)) {
                foreach ($attribute_buyer as $item_attribute_buyer) {
                    foreach ($item_attribute_buyer as $item_attribute_buyer_data) {
                        //
                        unset($data_enable_resp[array_search($item_attribute_buyer_data, $data_enable_resp)]);
                        //
                        $data_disable_resp[] = $item_attribute_buyer_data;
                    }
                }
            }

            if ($have_target) {
                //
                foreach ($dimension_target_combine as $item_dimension_target_combine) {
                    //
                    $index_enable = array_search($item_dimension_target_combine, $data_enable_resp);
                    $index_disable = array_search($item_dimension_target_combine, $data_disable_resp);
                    //
                    if ($index_enable) {
                        unset($data_enable_resp[array_search($item_dimension_target_combine, $data_enable_resp)]);
                    }
                    //
                    if (!$index_disable) {
                        $data_disable_resp[] = $item_dimension_target_combine;
                    }
                }
            }

            echo '<pre>';
            print_r(array('metric_current' => $metric_current, 'enable' => $data_enable_resp, 'disable' => $data_disable_resp));
            exit();
        }
        echo '<pre>';
        print_r(array('metric_current' => $metric_current, 'data_enable' => $data_enable, 'data_disable' => $data_disable, 'note' => $note));
        exit();
    }
}