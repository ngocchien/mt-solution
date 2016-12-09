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

class Column extends Entity\Column
{
    const IS_LASTED = 1;
    const COLUMN_TYPE_TEXT = 1;
    const MODIFY_COLUMN = 0;
    const MODIFY_COLUMN_CUSTOM = 1;
    const STATUS_COLUMN_ACTIVE = 1;
    const BRANDING_CONVERSION_DEFAULT_ID = array(1004, 1005, 1006, 1007, 1003, 1008,1009,1010,1011,1012,1013,1014,1015,1016,1017,1018,1019,1020,1021,1022,1023,1027,1028);
    const DEFAULT_CUSTOM_COLUMN = array(1000,1001,1002,1009,1012,1015,1018,1021,1024,1026);

    const ADD_COLUMN_CUSTOM = 2;
    const ADD_COLUMN = 1;

    public static function getModifyColumn($params)
    {
        return DAO\Column::getModifyColumn($params);
    }
}