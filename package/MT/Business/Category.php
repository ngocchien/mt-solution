<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 29/06/2017
 * Time: 21:14
 */

namespace MT\Business;


class Category
{
    const FUNNY = 1;
    const FILM = 2;
    const CARTOON = 3;
    const GENERAL = 4;

    public static function getDescription($cate_id){
        $arr_description = [
            self::FUNNY => 'Tổng hợp clip vui nhộn, hài hước, click troll bá đạo, clip hài hước , clip cười không nhặt được mồm, những pha troll banh bá đạo nhất :)!
                                Chúc các bạn có những giây phút thư giãn thật vui vẻ!
                                Subcribe kênh ủng hộ mình nha các bạn https://goo.gl/VmGSEs!
                                http://khampha.tech',
            self::FILM => 'Tổng hợp phim chưởng, phim kiếm hiệp hay, phim hành động, phim võ thuật , phim bom tấn hot nhất 2017!
                                Chúc các bạn có những giây phút thư giãn thật vui vẻ!
                                Subcribe kênh ủng hộ mình nha các bạn https://goo.gl/VmGSEs!
                                http://khampha.tech',
            self::CARTOON => 'Tổng hợp phim hoạt hình, videos hoạt hình dành cho trẻ em, hoạt hình hạt giống tâm hồn, dạy điều hay lẽ phải cho trẻ!
                                Chúc các bạn có những giây phút thư giãn thật vui vẻ!
                                Subcribe kênh ủng hộ mình nha các bạn https://goo.gl/VmGSEs!
                                http://khampha.tech',
            self::GENERAL => 'Tổng hợp clip , clip tổng hợp tin tức, clip khám phá cuộc sống - khoa học, clip hài hước , videos vui nhộn, clip khám phá cuộc sống!
                                Chúc các bạn có những giây phút thư giãn thật vui vẻ!
                                Subcribe kênh ủng hộ mình nha các bạn https://goo.gl/VmGSEs
                                http://khampha.tech',
        ];

        return $arr_description[$cate_id];
    }

    public static function mappingCate($id = ''){
        $arr = [
            self::FUNNY => 24,
            self::FILM => 1,
            self::CARTOON => 31,
            self::GENERAL => 28
        ];
        if(!empty($id) && !empty($arr[$id])){
            return $arr[$id];
        }
        return $arr;
    }
}