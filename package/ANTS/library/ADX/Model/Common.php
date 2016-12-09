<?php
namespace ADX\Model;

use ADX\Entity;
use ADX\DAO;
use ADX\Business;

class Common extends Entity\Common
{
    const TYPE_PACKAGE_LIST = 18;
    const TYPE_PACKAGE_DETAIL = 19;
    //
    const TYPE_METRIC_REPORT_COLUMN = 11;
    const TYPE_METRIC_REPORT_FILTER = 12;
    const TYPE_METRIC_REPORT_CONDITION_COLUMN = 13;
    const TYPE_METRIC_REPORT_FILTER_LISTING = 14;
    const TYPE_METRIC_REMARKETING = 15;
    
    const ACTION_ADD = 1;
    const ACTION_REMOVE = 0;

    const RM_STATUS_OPEN = 1;
    const RM_STATUS_CLOSE = 2;
    const RM_STATUS_REMOVE = 0;
    const RM_STATUS_REMOVE_V3 = 80;

    const TYPE_LINE_ITEM = 1;
    const TYPE_CAMPAIGN = 2;
    const TYPE_CREATIVE = 3;
    const TYPE_TARGET_AUDIENCE = 4;
    const TYPE_TARGET_TOPIC = 5;
    const TYPE_TARGET_SECTION = 6;
    const TYPE_TARGET_GENDER = 7;
    const TYPE_TARGET_AGE = 8;
    const TYPE_SETTING = 9;
    const TYPE_HOME = 10;
    const TYPE_SUPPORT_LINE_ITEM = 10;
    const TYPE_REMARKETING = 15;
    const REMARKETING_ONLY_TRUE = 1;


    const OBJ_COLUMN = 10;
    const OBJ_COLUMN_HOME = 13;
    const OBJ_FILTER = 11;
    const OBJ_CHART = 12;
    const OBJ_REPORT = 14;

    const OBJ_LINK_TARGET_AGE = 1;
    const OBJ_LINK_TARGET_GENDER = 2;

    const OBJ_LINK_TARGET_INTEREST = 1;
    const OBJ_LINK_TARGET_INMARKET = 2;
    const OBJ_LINK_TARGET_REMARKETING = 3;

    const TYPE_UPDATE_BIDDING = 1;
    const TYPE_UPDATE_BIDDING_GRID = 2;
    const TYPE_UPDATE_ENABLE_UPDATE_BID = 3;
    const TYPE_UPDATE_STATUS_TARGET = 4;
    const TYPE_UPDATE_BID_PRICE_CAMP = 5;

    const STATUS_REMOVE_TARGET = 0;
    const STATUS_PAUSE_TARGET = 2;
    const STATUS_ENABLE_TARGET = 1;

    const TARGET_INTEREST = 6;
    const TARGET_INMARKET = 10;
    const TARGET_TOPIC = 5;
    const TARGET_REMARKETING = 4;
    const TARGET_WEBSITE = 1;
    const TARGET_AGE = 11;
    const TARGET_GENDER = 12;
    const TARGET_DEMO_GRAPHICS = 13;
    const TARGET_AUDIENCE = 14;

    const DOWNLOAD_USE_TEMPLATE = 1;
    const DOWNLOAD_MULTIPLE_SHEET = 2;
    const DOWNLOAD_WITHOUT_TEMPLATE = 3;

    public static function listGender($id)
    {
        $arrGender = [
            1 => 'Nam',
            2 => 'Nữ',
            3 => 'Unknow'
        ];

        return $id ? (empty($arrGender[$id]) ? 'Không tồn tại' : $arrGender[$id]) : $arrGender;
    }

    public static function getFrequenceLifeTime($id)
    {
        $arrFrequenceLifeTime = [
            1 => 'Per Hour',
            2 => 'Per Day',
            3 => 'Per Week',
            5 => 'Per Month'
        ];

        return $id ? (empty($arrFrequenceLifeTime[$id]) ? 'Không tồn tại' : $arrFrequenceLifeTime[$id]) : $arrFrequenceLifeTime;
    }

    public static function getFrequenceType($id)
    {
        $arrFrequenceType = [
            1 => 'Per Creatives',
            2 => 'Per Campaign',
            3 => 'Per Line Item'
        ];

        return $id ? (empty($arrFrequenceType[$id]) ? 'Không tồn tại' : $arrFrequenceType[$id]) : $arrFrequenceType;
    }
}