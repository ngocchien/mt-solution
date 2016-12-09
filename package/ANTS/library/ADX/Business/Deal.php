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
use ADX\Nosql;

class Deal
{
    public static function validateParams($params)
    {
        $arr_return = [
            'code' => 519,
            'status' => 'error'
        ];


        if (empty($params)) {
            return $arr_return;
        }

        //validate package id
        $validate = self::validateCampaignId($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate campaign name
        $validate = self::validatePackageName($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate from_date and to_date
        $validate = self::validateDate($params);

        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate payment_model
        $validate = self::validatePaymentModel($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate location
        $validate = self::validateLocation($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate timeframe
        $validate = self::validateTimeframe($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //sale_price
        $validate = self::validateSalePrice($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }
        $params['price'] = $params['sale_price'];

        //buy price
        $params['price_buy'] = $params['buy_price'];

        //validate device
        $validate = self::validateDevice($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate placement
        $validate = self::validatePlacement($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate Browser
        $validate = self::validateBrowser($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate Carrier
        $validate = self::validateCarrier($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate OsVersion
        $validate = self::validateOsVersion($params);
        if (!empty($validate) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate Section
        $validate = self::validateSection($params);
        if (!empty($validate) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate Topic
        $validate = self::validateTopic($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate InterestRemarketing
        $validate = self::validateInterestRemarketing($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }


        //validate demographics
        $validate = self::validateDemographic($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //validate frequency
        $validate = self::validateFrequency($params);
        if (!empty($validate['status']) && $validate['status'] == 'error') {
            return $validate;
        }

        //return
        $params['properties'] = json_encode($params['properties']);
        $params['is_bidding'] = \ADX\Model\Deal::PACKAGE_IS_BIDDING_DEFAULT;
        $params['package_type'] = \ADX\Model\Deal::PACKAGE_TYPE_DEFAULT;
        $params['status'] = 'success';
        return $params;
    }

    public static function validatePaymentModel($params)
    {
        if (empty($params['payment_model'])) {
            return [
                'status' => 'error',
                'code' => 629
            ];
        }

        $arrPaymentModel = [
            Model\Creative::CREATIVE_PAYMENT_MODEL_CPC,
            Model\Creative::CREATIVE_PAYMENT_MODEL_CPM,
            Model\Creative::CREATIVE_PAYMENT_MODEL_CPMI
        ];

        if (!in_array((int)$params['payment_model'], $arrPaymentModel)) {
            return [
                'status' => 'error',
                'code' => 630
            ];
        }

        return true;
    }

    public static function validateCampaignId(&$params)
    {
        if (empty($params['package_id'])) {
            return true;
        }
        $arr_package = \ADX\Model\User::searchDataDeal([
            'user_id' => $params['_user_id'],
            'package_id' => (int)$params['package_id'],
            'network_id' => $params['network_id']
        ]);

        if (empty($arr_package)) {
            return [
                'status' => 'error',
                'code' => 609
            ];
        }

        $params['package_old'] = current($arr_package['rows']);

        return true;
    }

    public static function validatePackageName($params)
    {
        if (empty($params['package_name']) || Utils::str_len($params['package_name']) < 10) {
            return [
                'status' => 'error',
                'code' => 628
            ];
        }

        //kiểm tra name có tồn tại trong hệ thống
        $package = \ADX\Model\User::searchDataDeal(
            [
                'package_name' => $params['package_name'],
                'network_id' => $params['network_id'],
                'not_package_id' => empty($params['package_id']) ? null : $params['package_id']
            ]
        );

        if ($package['total'] > 0) {
            return [
                'status' => 'error',
                'code' => 632
            ];
        }

        return true;
    }

    public static function validateDate(&$params)
    {
        if (empty($params['from_date'])) {
            return [
                'status' => 'error',
                'code' => 435
            ];
        }

        if (empty($params['to_date'])) {
            return [
                'status' => 'error',
                'code' => 436
            ];
        }

        $from_date = Utils::formatDate($params['from_date']);
        $to_date = Utils::formatDate($params['to_date']);
        $today = date('Y-m-d');

        if ($from_date > $to_date) {
            return [
                'status' => 'error',
                'code' => 437
            ];
        }

        if ($today > $to_date) {
            return [
                'status' => 'error',
                'code' => 436
            ];
        }

        if (empty($params['package_id'])) {
            if ($from_date < $today) {
                return [
                    'status' => 'error',
                    'code' => 435
                ];
            }
        } else {
            if ($from_date . ' 00:00:00' != $params['package_old']['from_date']) {
                if ($from_date < $today) {
                    return [
                        'status' => 'error',
                        'code' => 435
                    ];
                }
            }
        }

        $params['from_date'] = $from_date . ' 00:00:00';
        $params['to_date'] = $to_date . ' 23:59:59';

        return true;
    }

    public static function validateSalePrice($params)
    {
        if (empty($params['sale_price'])) {
            return true;
        }

        if ((int)$params['sale_price'] < 0) {
            return [
                'status' => 'error',
                'code' => 631
            ];
        }
        return true;
    }

    public static function validateLocation(&$params)
    {
        $arr_location = \ADX\Model\User::searchDataLocation(
            [
                'source' => ['location_id'],
                'limit' => 10000,
                'in_location_id' => empty($params['list_location_id']) ? null : explode(',', $params['list_location_id'])
            ]
        );

        $arr_location_id = [];
        $type = [];
        if (!empty($arr_location['rows'])) {
            foreach ($arr_location['rows'] as $location) {
                $arr_location_id[] = $location['location_id'];
                $type[] = 1;
            }
        }

        $params['properties']['country'] = [
            'name' => 'country',
            'data' => [
                'id' => $arr_location_id,
                'type' => $type
            ],
            'extends' => [
                'creative' => implode(',', $arr_location_id)
            ]
        ];
        unset($params['list_location_id'], $arr_location_id, $type, $arr_condition);
        return true;
    }

    public static function validateDevice(&$params)
    {
        $arr_device = \ADX\Model\User::searchDataDevice(
            [
                'in_device_id' => empty($params['list_device_id']) ? [] : explode(',', $params['list_device_id']),
                'source' => ['device_id']
            ]
        );

        if (empty($arr_device['rows'])) {
            return [
                'status' => 'error',
                'code' => 635
            ];
        }

        $device_id = [];
        $device_type = [];
        foreach ($arr_device['rows'] as $device) {
            $device_id[] = $device['device_id'];
            $device_type[] = 1;
        }

        $params['properties']['device'] = [
            'name' => 'device',
            'data' => [
                'id' => $device_id,
                'type' => $device_type
            ]
        ];

        unset($arr_device_id, $arr_device, $params['list_device_id'], $device_id, $device_type);
        return true;
    }

    public static function validateTimeframe(&$params)
    {

        if (empty($params['list_frame_time_id'])) {
            return [
                'status' => 'error',
                'code' => 631
            ];
        }
        $times = explode(',', $params['list_frame_time_id']);
        $arr_time_frame = \ADX\Model\TimeFrame::formatTimeFrame();
        $arr_extend = [];
        $arr_format = [];
        $type = [];
        foreach ($arr_time_frame as $key => $value) {
            $type[] = 1;
            foreach ($value as $time) {
                if (in_array($time, $times)) {
                    $arr_extend[] = $time;
                    $arr_format[$key][] = 1;
                } else {
                    $arr_format[$key][] = 0;
                }
            }
        }

        $params['properties']['time'] = [
            'name' => 'time',
            'data' => [
                'id' => array_keys($arr_format),
                'type' => $type,
                'child' => array_values($arr_format)
            ],
            'extends' => [
                'creative' => implode(',', $arr_extend)
            ]
        ];
        unset($params['list_frame_time_id'], $type, $arr_format, $arr_time_frame, $arr_extend, $times);
        return true;
    }

    public static function validatePlacement(&$params)
    {
        if (empty($params['list_placement_id'])) {
            return [
                'status' => 'error',
                'code' => 634
            ];
        }

        $arr_placement_id = explode(',', $params['list_placement_id']);
        $arr_placement = \ADX\Model\User::searchDataPlacement(
            [
                'in_network_id' => [$params['network_id'], 0],
                'in_placement_id' => $arr_placement_id,
                'source' => ['placement_id'],
                'limit' => 10000
            ]
        );

        if (empty($arr_placement['rows'])) {
            return [
                'status' => 'error',
                'code' => 635
            ];
        }

        $placement_id = [];
        $placement_type = [];
        foreach ($arr_placement['rows'] as $placement) {
            $placement_id[] = $placement['placement_id'];
            $placement_type[] = 1;
        }

        $params['properties']['placement'] = [
            'name' => 'placement',
            'data' => [
                'id' => $placement_id,
                'type' => $placement_type
            ]
        ];

        unset($arr_placement_id, $arr_placement, $params['list_placement_id'], $placement_id, $placement_type);
        return true;
    }

    public static function validateBrowser(&$params)
    {
        $arrBrowser = \ADX\Model\User::searchDataBrowser([
            'in_browser_id' => empty($params['list_browser_id']) ? [] : explode(',', $params['list_browser_id']),
            'source' => ['browser_id'],
            'limit' => 1000
        ]);

        if (empty($arrBrowser['rows'])) {
            return [
                'status' => 'error',
                'code' => 635
            ];
        }

        $browser_id = [];
        $browser_type = [];

        foreach ($arrBrowser['rows'] as $browser) {
            $browser_id[] = $browser['browser_id'];
            $browser_type[] = 1;
        }

        $params['properties']['browser'] = [
            'name' => 'browser',
            'data' => [
                'id' => $browser_id,
                'type' => $browser_type
            ],
        ];

        unset($arrBrowser, $arr_browser_id, $params['list_browser_id'], $browser_id, $browser_type);
        return true;
    }

    public static function validateCarrier(&$params)
    {
        $arrCarrier = \ADX\Model\User::searchDataCarrier([
            'in_carrier_id' => empty($params['list_carrier_id']) ? [] : explode(',', $params['list_carrier_id']),
            'source' => ['carrier_id'],
            'limit' => 10
        ]);

        if (empty($arrCarrier['rows'])) {
            return [
                'status' => 'error',
                'code' => 636
            ];
        }

        $id = [];
        $type = [];
        foreach ($arrCarrier['rows'] as $carrier) {
            $id[] = $carrier['carrier_id'];
            $type[] = 1;
        }
        $params['properties']['carrier'] = [
            'name' => 'carrier',
            'data' => [
                'id' => $id,
                'type' => $type
            ],
        ];

        unset($arrCarrier, $arr_carrier_id, $params['list_carrier_id'], $id, $type);
        return true;
    }

    public static function validateOsVersion(&$params)
    {
        $arrOsVersion = \ADX\Model\User::searchDataOsVersion([
            'in_version_id' => empty($params['list_os_version_id']) ? [] : explode(',', $params['list_os_version_id']),
            'source' => ['version_id'],
            'limit' => 1000
        ]);

        if (empty($arrOsVersion['rows'])) {
            return [
                'status' => 'error',
                'code' => 637
            ];
        }

        $id = [];
        $type = [];
        foreach ($arrOsVersion['rows'] as $os_version) {
            $id[] = $os_version['version_id'];
            $type[] = 1;
        }

        $params['properties']['os_version'] = [
            'name' => 'os_version',
            'data' => [
                'id' => $id,
                'type' => $type
            ],
        ];
        unset($arrOsVersion, $os_version, $arr_os_version_id, $params['list_os_version_id'], $id, $type);
        return true;
    }

    public static function validateSection(&$params)
    {
        if (empty($params['section_id'])) {
            return false;
        }

        if (empty($params['section_id'][\ADX\Model\Common::TARGET_WEBSITE])) {
            return false;
        }

        $arrSection = \ADX\Model\User::searchDataSection([
            'in_section_id' => $params['section_id'][\ADX\Model\Common::TARGET_WEBSITE],
            'source' => ['section_id'],
            'limit' => 10000
        ]);

        if (empty($arrSection['rows'])) {
            return [
                'status' => 'error',
                'code' => 638
            ];
        }

        $id = [];
        $type = [];
        foreach ($arrSection['rows'] as $section) {
            $id[] = $section['section_id'];
            $type[] = 1;
        }
        $params['properties']['section'] = [
            'name' => 'section',
            'data' => [
                'id' => $id,
                'type' => $type
            ],
        ];

        unset($arrSection, $arr_section_id, $params['section_id'], $id, $type);
        return true;
    }

    public static function validateTopic(&$params)
    {
        if (empty($params['topic_id'])) {
            return false;
        }

        if (empty($params['topic_id'][\ADX\Model\Common::TARGET_TOPIC])) {
            return false;
        }

        $arrTopic = \ADX\Model\User::searchDataTopic([
            'in_topic_id' => $params['topic_id'][\ADX\Model\Common::TARGET_TOPIC],
            'source' => ['topic_id'],
            'limit' => 10000
        ]);

        if (empty($arrTopic['rows'])) {
            return [
                'status' => 'error',
                'code' => 639
            ];
        }

        $id = [];
        $type = [];
        foreach ($arrTopic['rows'] as $topic) {
            $id[] = $topic['topic_id'];
            $type[] = 1;
        }

        $params['properties']['topic'] = [
            'name' => 'topic',
            'data' => [
                'id' => $id,
                'type' => $type
            ]
        ];


        unset($arrTopic, $arr_topic_id, $params['topic_id'], $id, $type);
        return true;
    }

    public static function validateInterestRemarketing(&$params)
    {
        if (empty($params['interest_marketing_id'])) {
            return true;
        }

        //remarketing
        if (!empty($params['interest_marketing_id'][\ADX\Model\Common::TARGET_REMARKETING])) {
            $arrRemarketing = \ADX\Model\User::searchDataRemarketing([
                'in_remarketing_id' => $params['interest_marketing_id'][\ADX\Model\Common::TARGET_REMARKETING],
                'network_id' => $params['network_id'],
                'limit' => 10000,
                'source' => ['remarketing_id']
            ]);

            if (empty($arrRemarketing['rows'])) {
                return [
                    'status' => 'error',
                    'code' => 641
                ];
            }

            foreach ($arrRemarketing['rows'] as $row) {
                $id[][] = $row['remarketing_id'];
                $type[] = 1;
            }

            $params['properties']['remarketing'] = [
                'name' => 'remarketing',
                'data' => [
                    'id' => $id,
                    'type' => $type
                ]
            ];
        }

        //inmarket
        if (!empty($params['interest_marketing_id'][\ADX\Model\Common::TARGET_INMARKET])) {
            $arrInmarket = \ADX\Model\User::searchDataInmarket([
                'in_inmarket_id' => $params['interest_marketing_id'][\ADX\Model\Common::TARGET_INMARKET],
                'network_id' => $params['network_id'],
                'limit' => 10000,
                'source' => ['inmarket_id']
            ]);

            if (empty($arrInmarket['rows'])) {
                return [
                    'status' => 'error',
                    'code' => 642
                ];
            }

            $id = [];
            $type = [];
            foreach ($arrInmarket['rows'] as $row) {
                $id[] = $row['inmarket_id'];
                $type[] = 1;
            }

            $params['properties']['inmarket'] = [
                'name' => 'inmarket',
                'data' => [
                    'id' => $id,
                    'type' => $type
                ]
            ];
        }

        //interest
        if (!empty($params['interest_marketing_id'][\ADX\Model\Common::TARGET_INTEREST])) {
            $arrInterest = \ADX\Model\User::searchDataInterest([
                'in_interest_id' => $params['interest_marketing_id'][\ADX\Model\Common::TARGET_INTEREST],
                'network_id' => $params['network_id'],
                'limit' => 10000,
                'source' => ['interest_id']
            ]);

            if (empty($arrInterest['rows'])) {
                return [
                    'status' => 'error',
                    'code' => 643
                ];
            }

            $id = [];
            $type = [];
            foreach ($arrInterest['rows'] as $row) {
                $id[] = $row['interest_id'];
                $type[] = 1;
            }

            $params['properties']['interest'] = [
                'name' => 'interest',
                'data' => [
                    'id' => $id,
                    'type' => $type
                ]
            ];
        }

        unset($arrInterest, $arrInterest, $arrRemarketing, $params['interest_marketing_id'], $id, $type);

        return true;
    }

    public static function validateDemographic(&$params)
    {
        if (empty($params['demographic_id'])) {
            return true;
        }

        if (!empty($params['demographic_id'][\ADX\Model\Common::TARGET_AGE])) {
            $arrAge = \ADX\Model\User::searchDataAge([
                    'in_age_id' => $params['demographic_id'][\ADX\Model\Common::TARGET_AGE],
                    'source' => ['age_id']
                ]
            );
            if (empty($arrAge['rows'])) {
                return [
                    'status' => 'error',
                    'code' => 644
                ];
            }

            $type = [];
            $type = [];
            foreach ($arrAge['rows'] as $row) {
                $id[] = $row['age_id'];
                $type[] = 1;
            }
            $params['properties']['age'] = [
                'name' => 'age',
                'data' => [
                    'id' => $id,
                    'type' => $type
                ]
            ];
        }

        if (!empty($params['demographic_id'][\ADX\Model\Common::TARGET_GENDER])) {
            $arrGender = \ADX\Model\Common::listGender();
            $id = [];
            $type = [];
            foreach ($arrGender as $key => $item) {
                if (in_array($key, $params['demographic_id'][\ADX\Model\Common::TARGET_GENDER])) {
                    $id[] = $key;
                    $type[] = 1;
                }
            }

            if (!empty($id)) {
                $params['properties']['gender'] = [
                    'name' => 'gender',
                    'data' => [
                        'id' => $id,
                        'type' => $type
                    ]
                ];
            }
        }

        unset($arrGender, $arrAge, $params['demographic_id'], $id, $type);
        return true;
    }

    public static function validateFrequency(&$params)
    {
        if (empty($params['frequency_number'])) {
            return true;
        }

        if ($params['frequency_number'] < 1) {
            return [
                'status' => 'error',
                'code' => 645
            ];
        }

        if (empty($params['frequency_lifetime']) || !in_array($params['frequency_lifetime'], array_keys(\ADX\Model\Common::getFrequenceLifeTime()))) {
            return [
                'status' => 'error',
                'code' => 646
            ];
        }

        if (empty($params['frequency_type']) || !in_array($params['frequency_type'], array_keys(\ADX\Model\Common::getFrequenceType()))) {
            return [
                'status' => 'error',
                'code' => 647
            ];
        }

        $params['properties']['frequency'] = [
            'name' => 'frequency',
            'data' => [
                'frequency_number' => $params['frequency_number'],
                'frequency_lifetime' => $params['frequency_lifetime'],
                'frequency_type' => $params['frequency_type']
            ]
        ];

        return true;
    }


    public static function createDeal($params)
    {
        $package_id = \ADX\DAO\Deal::add($params);

        if (!$package_id) {
            return [
                'status' => 'error',
                'code' => 648
            ];
        }
        Utils::runJob(
            'info_buyer',
            'TASK\ElasticSearch',
            'indexESObject',
            'doHighBackgroundTask',
            'admin_elastic',
            array(
                'object_id' => $package_id,
                'object' => 'packages',
                'network_id' => $params['network_id'],
                'source' => 'v3.admin',
                'actor' => __FUNCTION__
            )
        );

        //phân quyền
        if (!empty($params['list_user_id'])) {
            $arr_user = \ADX\Model\User::searchDataUser(
                [
                    'in_user_id' => explode(',', $params['list_user_id']),
                    'network_id' => $params['network_id']
                ]
            );
            if (!empty($arr_user['rows'])) {
                foreach ($arr_user['rows'] as $row) {
//                    Adx_DAO_UserPrivs::create(array(
//                        'user_id' => $user_be_per->user_id,
//                        'network_id' => $user_be_per->network_id,
//                        'grantor' => $user->user_id,
//                        'grant_option' => 1,
//                        'action_id' => array($params['action_id']),
//                        'resource_id' => array(RESOURCE_PACKAGE),
//                        'object_type' => array(RESOURCE_PACKAGE),
//                        'object_id' => array($package_id)
//                    ));
                }
            }
        }

        return [
            'package_id' => $package_id
        ];
    }

    public static function getDealPerformance($params)
    {
        return Performance::processDataPerformance($params);
    }
}