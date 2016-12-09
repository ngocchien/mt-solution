<?php
/**
 * Created by PhpStorm.
 * User: Hi
 * Date: 11/04/2016
 * Time: 11:24 AM
 */

namespace ADX\Model;

use ADX\Config;
use ADX\Entity;
use ADX\DAO;
use ADX\Nosql;
use ADX\Search\Placement;
use ADX\Utils;

class User extends Entity\User
{
    public static function getColumns()
    {
        return array('USER_ID', 'FULL_NAME', 'EMAIL', 'ADDRESS', 'PHONE', 'CONFIG', 'CTIME', 'UTIME', 'G2FA', 'G2FA_SECRET');
    }

    public static function getListSupport($params)
    {
        return DAO\Account::getListSupport($params);
    }

    public static function getListUserSupport($params)
    {
        return DAO\Account::getListUserSupport($params);
    }

    public static function getUsersSupported($params)
    {
        //get caching

        $data = DAO\Account::getUsersSupported($params);
        return $data;
    }

    public static function getUserRegister($params)
    {
        return DAO\User::getUserRegiter($params);
    }

    public static function getUser($params)
    {
        return DAO\User::getUser($params);
    }

    public static function getListLabel($params)
    {
        $data = DAO\Label::getListLabel($params);

        return $data;
    }

    public static function getListSegment($params)
    {

        $data = DAO\Segment::getListSegment($params);

        return $data;
    }

    public static function addNewLabel($params)
    {
        $result = DAO\Label::add($params);

        return $result;
    }

    public static function getLineItemPerformance($params)
    {
        return DAO\LineItem::getLineItemPerformance($params);
    }

    public static function getCampaignPerformance($params)
    {
        return DAO\Campaign::getCampaignPerformance($params);
    }

    public static function getCreativePerformance($params)
    {
        return DAO\Creative::getCreativePerformance($params);
    }

    public static function getPerformance($params)
    {
        return DAO\Common::getPerformance($params);
    }

    public static function getTargetPerformance($params)
    {
        return DAO\Target::getPerformance($params);
    }

    public static function getReportPerformance($params)
    {
        return DAO\Report::getReportPerformance($params);
    }

    public static function addLabel($params)
    {
        $result = DAO\Label::add($params);
        //
//        $params['option'] = 'labels';
//        Utils::publishRedisCaching(
//            array(
//                'channel' => Job::CHANNEL_CACHING_USER_INFO,
//                'data' => $params
//            )
//        );

        return $result;
    }

    public static function updateLabel($params)
    {
        $result = DAO\Label::update($params);

//        $params['option'] = 'labels';
//        Utils::publishRedisCaching(
//            array(
//                'channel' => Job::CHANNEL_CACHING_USER_INFO,
//                'data' => $params
//            )
//        );

        return $result;
    }

    public static function checkLogin($username, $password, $network_id)
    {
        $params['username'] = $username;
        $params['password'] = $password;
        $params['network_id'] = $network_id;

        return DAO\User::checkLogin($params);
    }

    public static function getListModifyColumn($params = array())
    {
        //get caching
        $data = Utils::getCachingObject('modify_columns', $params['manager_id'], $params['user_id'] . ':' . $params['type'], false);

        if (!$data) {
            //
            $data = DAO\Column::getListModifyColumn($params);

            $params['option'] = 'modify_columns';
            Utils::publishRedisCaching(
                array(
                    'channel' => Job::CHANNEL_CACHING_USER_INFO,
                    'data' => $params
                )
            );
        }

        return $data;
    }

    public static function addModifyColumn($params = array())
    {
        $result = DAO\Column::addModifyColumn($params);

        $params['option'] = 'modify_columns';
        Utils::publishRedisCaching(
            array(
                'channel' => Job::CHANNEL_CACHING_USER_INFO,
                'data' => $params
            )
        );

        return $result;
    }

    public static function deleteModCol($params)
    {
        $result = DAO\Column::delModifyColumn($params);

        $params['option'] = 'modify_columns';
        Utils::publishRedisCaching(
            array(
                'channel' => Job::CHANNEL_CACHING_USER_INFO,
                'data' => $params
            )
        );

        return $result;
    }

    public static function checkNameColumn($params = array())
    {
        return DAO\Column::checkNameColumn($params);
    }

    public static function getLastedModifyColumn($params)
    {
        return DAO\Column::getLastedModifyColumn($params);
    }

    public static function getCustomColumn($params)
    {
        return DAO\Column::getCustomColumn($params);
    }

    public static function updModifyColumn($params)
    {
        return DAO\Column::updModifyColumn($params);
    }

    public static function getMetric($params = array())
    {
        //get caching
        $data = Utils::getCachingObject('metrics', $params['by_fnc'], $params['type'], true);
        if (!$data) {
            //
            $data = DAO\Metric::getMetric($params);

            $params['option'] = 'metrics';
            Utils::publishRedisCaching(
                array(
                    'channel' => Job::CHANNEL_CACHING_USER_INFO,
                    'data' => $params
                )
            );
        }

        return $data;
    }

    public static function addFilter($params = array())
    {
        $result = DAO\Filter::addFilter($params);

        $params['option'] = 'filter';
        Utils::publishRedisCaching(
            array(
                'channel' => Job::CHANNEL_CACHING_USER_INFO,
                'data' => $params
            )
        );

        return $result;
    }

    public static function updateFilter($params = array())
    {
        $status = DAO\Filter::updateFilter($params);

        $params['option'] = 'filter';
        Utils::publishRedisCaching(
            array(
                'channel' => Job::CHANNEL_CACHING_USER_INFO,
                'data' => $params
            )
        );

        return $status;
    }

    public static function getFilterWidgetList($params)
    {
        return DAO\Filter::getFilterWidgetList($params);
    }

    public static function addCustomMetric($params)
    {
        return DAO\Metric::addCustomMetric($params);
    }

    public static function getCustomMetricByName($params)
    {
        return DAO\Metric::getCustomMetricByName($params);
    }

    public static function deleteCustomMetric($params)
    {
        return DAO\Metric::deleteCustomMetric($params);
    }

    public static function getCustomMetricDetail($params)
    {
        return DAO\Metric::getCustomMetricDetail($params);
    }

    public static function updCustomMetric($param)
    {
        return DAO\Metric::updCustomMetric($param);
    }

    public static function getFilterList($params = array())
    {
        //get caching
        $data = Utils::getCachingObject('users', $params['manager_id'], 'filter:' . $params['user_id'] . ':' . $params['type'], true);

        if (!$data) {
            //
            $data = DAO\Filter::getFilterList($params);

            $params['option'] = 'filter';
            Utils::publishRedisCaching(
                array(
                    'channel' => Job::CHANNEL_CACHING_USER_INFO,
                    'data' => $params
                )
            );
        }

        return $data;

    }

    public static function getFilterById($params = array())
    {
        return DAO\Filter::getFilterById($params);
    }

    public static function deleteFilter($params = array())
    {
        $result = DAO\Filter::deleteFilter($params);

        $params['option'] = 'filter';
        Utils::publishRedisCaching(
            array(
                'channel' => Job::CHANNEL_CACHING_USER_INFO,
                'data' => $params
            )
        );

        return $result;

    }

    public static function checkLabelName($params)
    {
        return DAO\Label::checkLabelName($params);
    }

    public static function getLineItems($params)
    {
        return DAO\LineItem::getLineItems($params);
    }

    public static function getSettings($params)
    {
        return DAO\Setting::get($params);
    }

    public static function getCampaigns($params)
    {
        return DAO\Campaign::getCampaigns($params);
    }

    public static function getCreatives($params)
    {
        return DAO\Creative::getCreatives($params);
    }

    public static function getLocation($params)
    {
        return DAO\Location::get($params);
    }

    public static function getCarrier($params)
    {
        return DAO\Carrier::get($params);
    }

    public static function getDevice($params)
    {
        return DAO\Device::get($params);
    }

    public static function getBrowser($params)
    {
        return DAO\Browser::get($params);
    }

    public static function getOs($params)
    {
        return DAO\Os::get($params);
    }

    public static function getInterest($params)
    {
        return DAO\Interest::getInterest($params);
    }

    public static function getInMarket($params)
    {
        return DAO\InMarket::getInMarket($params);
    }

    public static function getTopic($params)
    {
        return DAO\Topic::getTopic($params);
    }

    public static function getWebsite($params)
    {
        return DAO\Website::getWebsite($params);
    }

    public static function getRemarketing($params)
    {
        return DAO\Remarketing::getRemarketing($params);
    }

    public static function checkSupportUser($params = array())
    {
        return DAO\User::checkSupportUser($params);
    }

    public static function checkActiveUser($params = array())
    {
        //get caching
        $data = Utils::getCachingObject('users', $params['user_id'], 'info');

        if ($data) {
            $status = $data['active'];
        } else {
            $status = DAO\User::checkActiveUser($params);

            Utils::publishRedisCaching(
                array(
                    'channel' => Job::CHANNEL_CACHING_USER_INFO,
                    'data' => array(
                        'user_id' => $params['user_id'],
                        'network_id' => $params['network_id'],
                        'option' => 'active'
                    )
                )
            );
        }

        return $status;
    }

    public static function getWebsiteList($params = array())
    {
        return DAO\Merchant::getWebsiteList($params);
    }

    public static function getPriceList($params = array())
    {
        return DAO\Merchant::getPriceList($params);
    }

    public static function getFileList($params = array())
    {
        return DAO\Merchant::getFileList($params);
    }

    public static function getCateList($params = array())
    {
        return DAO\Merchant::getCateList($params);
    }

    //Creative
    public static function getAllCreatives($params)
    {
        $params['columns'] = 'CREATIVE_ID, CREATIVE_NAME, PLACEMENT_ID, CAMPAIGN_ID, USER_ID, NETWORK_ID, RESOURCE_ID, ADS_ID, CLICK_URL, FROM_DATE,
                        TO_DATE, DISCOUNT, DAILY_BUDGET, TOTAL_BUDGET, DAILY_IMP, TOTAL_IMP, DAILY_CLICK, TOTAL_CLICK, DAILY_INSTALL, TOTAL_INSTALL, SPENT_BUDGET,
                        SPENT_CLICK, SPENT_IMP, SPENT_INSTALL, FILES, STATUS, FORMAT, UNIT_PRICE, RUN_TYPE, TIME_FRAMES, BOOKING_EXPIRE, CONTRACT_NO, ANNEX_CONTRACT_NO,
                        CONTRACT_NOTE, THIRD_PARTY_URL, FREQ_LIFETIME, FREQ_DAILY, FREQ_HOURLY, CONTENT, CATEGORY_ID, PROFILE_ID, META_DATA, DISPLAY_URL,
                        GENDER_TARGET, LOCATIONS_TARGET, PAYMENT_MODEL, AGE_TARGET, MFR_TARGET, OS_TARGET, CARRIER_TARGET, PRIORITY, APPORTION, CREATIVE_SIGNATURE,
                        CTIME, UTIME, IS_COMMIT, TOTAL_DAY, BRAND_ID, FROM_CREATIVE_ID, MANAGER_ID, GROUP_ID, PROPERTIES, MERCHANT_WEBSITE_ID, MERCHANT_FILE_ID,
                        MERCHANT_CATEGORY_ID, MERCHANT_PRICE_ID, IS_BIDDING, BIDDING_INFO, WIDTH, HEIGHT, FILTER_CONDITION, PACKAGE_ID, SPENT_DATE, SPENT_BUDGET_LAST2DAYS,
                        SPENT_CLICK_LAST2DAYS, SPENT_IMP_LAST2DAYS, LINEITEM_ID';
        return DAO\Creative::getAllCreatives($params);
    }

    public static function getAge($params)
    {
        return DAO\Age::getAge($params);
    }

    public static function getListPaymentModel($params)
    {
        return DAO\Marketing::getListPaymentModel($params);
    }

    public static function getReportMetric($params)
    {
        return DAO\Report::getReportMetric($params);
    }

    public static function addModifyReports($params)
    {
        return DAO\Report::addModifyReports($params);
    }

    public static function updModifyReport($params)
    {
        return DAO\Report::updModifyReport($params);
    }

    public static function delModifyReport($params)
    {
        return DAO\Report::delModifyReport($params);
    }

    public static function getListModifyReport($params)
    {
        return DAO\Report::getListModifyReport($params);
    }

    public static function getDetailModifyReport($params)
    {
        return DAO\Report::getDetailModifyReport($params);
    }

    public static function getPreDefinedReport($params = array())
    {
        return DAO\Report::getPreDefinedReport($params);
    }

    public static function getPredefineReport($params = array())
    {
        return DAO\Report::getPredefineReport($params);
    }

    public static function getSectionPrice($params = array())
    {
        return DAO\Target::getSectionPrice($params);
    }

    public static function getNetworkUser($params = array())
    {
        return DAO\User::getInfoNetworkUser($params);
    }

    public static function getPackageDetail($params = [])
    {
        return DAO\Package::get($params);
    }

    public static function searchDataDeal($params = [])
    {
        $instanceSearch = new \ADX\Search\Deal();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataPlacement($params = [])
    {
        $instanceSearch = new \ADX\Search\Placement();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataLocation($params = [])
    {
        $instanceSearch = new \ADX\Search\Location();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataDevice($params = [])
    {
        $instanceSearch = new \ADX\Search\Device();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataBrowser($params = [])
    {
        $instanceSearch = new \ADX\Search\Browser();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataCarrier($params = [])
    {
        $instanceSearch = new \ADX\Search\Carrier();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataOsVersion($params = [])
    {
        $instanceSearch = new \ADX\Search\OsVersion();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataSection($params = [])
    {
        $instanceSearch = new \ADX\Search\Section();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataTopic($params = [])
    {
        $instanceSearch = new \ADX\Search\Topic();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataRemarketing($params = [])
    {
        $instanceSearch = new \ADX\Search\Remarketing();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataInterest($params = [])
    {
        $instanceSearch = new \ADX\Search\Interest();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataInmarket($params = [])
    {
        $instanceSearch = new \ADX\Search\Inmarket();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataAge($params = [])
    {
        $instanceSearch = new \ADX\Search\Age();
        return $instanceSearch->searchData($params);
    }

    public static function searchDataUser($params = [])
    {
        $instanceSearch = new \ADX\Search\User();
        return $instanceSearch->searchData($params);
    }

    public static function createDeal($params = [])
    {
        return \ADX\DAO\Deal::add($params);
    }


}