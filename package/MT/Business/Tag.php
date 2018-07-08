<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 29/06/2017
 * Time: 21:16
 */

namespace MT\Business;
use MT\Business;

class Tag
{
    public static function getTag($cate_id){
        $arr_tags = [
            Business\Category::FUNNY => [
                'khampha.tech',
                'hai huoc',
                'hài hước',
                'vui nhộn',
                'funny',
                'góc thư giãn',
                'vui nhon',
                'goc hai huoc',
                'cuoi vo bung',
                'cười vỡ bụng',
                'cười bể bụng bò',
                'goc hai huoc',
                'cười',
                'cuoi',
                'zui la chinh',
                'vui là chính',
                'video hài hước',
                'clip hài hước'
            ],
            Business\Category::FILM => [
                'khampha.tech',
                'Phim hành động hay',
                'Phim hành động hay nhất 2017',
                'phim hành động',
                'phim hành động mới nhất 2017',
                'phim võ thuật',
                'phim võ thuật hay nhất 2017',
                'phim võ thuật mới nhất 2017',
                'phim chưởng',
                'phim mới nhất',
                'phim hành động gay cấn',
                'phim mới',
                'phim bom tấn',
                'phim bom tấn hay nhất 2017',
                'Phim',
                'phim bộ hay'
            ],
            Business\Category::CARTOON => [
                'khampha.tech',
                'hoạt hình',
                'Phim hoạt hình',
                'phim hoạt hình hay',
                'phim hoạt hình vui nhộn',
                'phim doremon',
                'phim lavar',
                'phim hoạt hình mới nhất',
                'phim hoạt hình hay nhất 2017',
                'phim hoạt hình cổ tích'
            ],
            Business\Category::GENERAL => [
                'khampha.tech',
                'Clip tổng hợp',
                'clip hài hước',
                'hài hươc, vui nhộn',
                'vui nhộn',
                'khám phá thế giới',
                'khám phá xã hội',
                'tổng hợp video',
                'tổng hợp tinh tức',
                'video khám khá',
                'clip khám phá'
            ]
        ];

        return $arr_tags[$cate_id];
    }
}