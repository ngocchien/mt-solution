<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 30/06/2017
 * Time: 17:20
 */

namespace MT\Model;

use MT\DAO;

class Post
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_REMOVE = 0;

    const STATUS_ACTIVE_NAME = 'Active';
    const STATUS_INACTIVE_NAME = 'Hidden';
    const STATUS_REMOVE_NAME = 'Removed';

    public static function get($params)
    {
        return DAO\Post::get($params);
    }

    public static function create($params)
    {
        return DAO\Post::create($params);
    }

    public static function update($params, $id)
    {
        return DAO\Post::update($params, $id);
    }

    public static function renderStatus($status_id = '')
    {
        $arr_status = [
            self::STATUS_ACTIVE => self::STATUS_ACTIVE_NAME,
            self::STATUS_INACTIVE => self::STATUS_INACTIVE_NAME,
            self::STATUS_REMOVE => self::STATUS_REMOVE_NAME,
        ];

        if ($status_id) {
            return $arr_status[$status_id];
        }

        return $arr_status;
    }

    public static function updateByCondition($params, $condition)
    {
        return DAO\Post::updateByCondition($params, $condition);
    }
}