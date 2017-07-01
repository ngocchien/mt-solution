<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 29/06/2017
 * Time: 21:19
 */

namespace MT\Business;

use MT\Model;
use MT\Business;

class Post
{
    public static function getChannel($cate_id = ''){
        $arr_channel = [
            Business\Category::FUNNY => [
                'UCKKZkAD3pxJ1buulcBXtZog',
                'UCKZCRjVvf41T_iHUXs7chAQ',
                'UCtGdKK57UfWVz7QREaE-WzA',
                'UC6YPflvZQxjWDU4nmDhT5lQ',
                'UCdYLomsfEN5MrzdzXmYqk3w',
                'UCapufnXbulJ6neUBEZ4YG3g',
                'UC3rEyck_sZCtiGp1sqrI6lw',
                'UCBhx1T3IBOAGZcL_fecDNSw',
                'UCoXlAYmyv1oXX1HdgJA3uGA',
                'UChTmETn8tcsE4B9WD4GPYbQ'
            ],
            Business\Category::FILM => [
                'UCcanVTAMenfoQkLv7siboCg',
                'UCUKkvyhQ-uqobL3pH7dtxRw',
                'UCXSCwpo-tNLP76rz74LB5Rw',
                'UCYNgXcIqMOFzz1sUIWE_fpg',
                'UCjMfAfICXjh7MZ-bsnZWlcA',
                'UCyCNEB2FnJjLmC_LgjnLPaQ',
                'UCYUfm0vI_sNXJ_APUBdXQSg',
//                'UCAc4VQ26-ql2ScJLdCEPulA',
                'UCIOIRlf-bzIXt5tOXbSuBuA',
                'UCSTN0-GBEOX5A0bEXjGfIog',
                'UCWnc-qfpMsg_h8tCwfZHzqA',
                'UCGo-clPVHCpZ114pD-RmOlQ',
                'UCfg6BRiAIvo6uWagh_ftUIw',
                'UCoh2FoepPGPL49hfdxkECng',
                'UC8L1sLOZl0ZXvHhUkbnhHwA'
//                'UCUYTNYuLnHpDa6Jr_g_st5g'
            ],
            Business\Category::CARTOON => [
                'UCR41PFucEfU4JnNxC6YbK_g',
                'UC3IUdPJMHrhASeBUPBH8gJg',
                'UCSvlT8PHGLSEJGtoG46uHzA',
                'UCDSI83PbPosUBySGyoc56TA'
            ],
            Business\Category::GENERAL => [
                'UCKzIX0n7XoMJbhfVm6BX6Lw',
                'UCNodQIAYVU_wQ-5eEOuv1yA',
                'UCL5GVSmQAo4PIJcyroB24lQ',
                'UC51Kt7oddJ6PoWzyCRHq8cQ',
                'UCQC4wbpwNrza3DmT8jqRhwA',
                'UChil6258LeQ0zpQCDAldAog',
                'UCOH84fFEhvpybl_n2O_F5JA',
                'UCGE6fdGyo_s83QyIn6oqHLA',
                'UCHKxnJSW-IiWApkZV5FXDdg',
                'UCQAYKkTRSQbtcACCgBTMsFA',
                'UCjDR6Hz68254I5hF4Wo7qdA',
                'UC6YPflvZQxjWDU4nmDhT5lQ',
                'UCmTrfqjDACWG0Eh7Rf3SXJg',
                'UCRfnXJ6_3PXIfQNp6zXfg5w',
                'UCPW9yQN93Sgxev5SVCE3gyQ',
                'UC52Tu_IK22naPwlUzy7zikw',
                'UCA8TIKE6u1NqApjz-FV9wJw',
                'UCj6B4cj9mB9JXWW65T4Sh4w',
                'UC5fMzuq_02DF2daBrOJ5Grg',
                'UCflhIoPmMRXx5I0byHPPMhQ',
                'UC970gUmZWWeIU8BWVjZRsIw',
                'UCswAeN6MrJxe3P0Ik7IK8sQ',
                'UC1CeYWtAz6CpHIgOC3DXQhw'
//                'UCswAeN6MrJxe3P0Ik7IK8sQ'
            ]
        ];
        if(!empty($cate_id)){
            if(!empty($arr_channel[$cate_id])){
                return $arr_channel[$cate_id];
            }else{
                return [];
            }
        }
        return $arr_channel;
    }

    public static function create($params){

        if(empty($params['source_id']) || empty($params['my_id'])){
            $params['error'] = __FUNCTION__;
            return $params;
        }

        $post_title =  trim(strip_tags($params['post_title']));
        $cate_id = empty($params['cate_id']) ? 0 : $params['cate_id'];
        $status = 1;
        $my_id = $params['my_id'];
        $source_id = $params['source_id'];

        //check name
        $exist = Model\Post::get([
            'source_id' => $source_id,
            'limit' => 1,
            'offset' => 0,
            'not_status' => Model\Post::STATUS_REMOVE,
            'cate_id' => $cate_id
        ]);

        if(!empty($exist['rows'])){
            $params['error'] = __FUNCTION__.'_'.__LINE__;
            return $params;
        }

        $id = Model\Post::create([
            'post_title' => $post_title,
            'status' => $status,
            'created_date' => time(),
            'cate_id' => $cate_id,
            'my_id'=> $my_id,
            'source_id'=> $source_id
        ]);

        if(!$id){
            $params['error'] = __FUNCTION__;
            return $params;
        }

        return [
            'success' => true,
            'post_id' => $id
        ];
    }

    public static function get($params){
        $limit = empty($params['limit']) ? 10 : (int)$params['limit'];
        $page = empty($params['page']) ? 1 : (int)$params['page'];
        $offset = $limit * ($page - 1);
        $params['offset'] = $offset;
        $params['limit'] = $limit;
        $result = Model\Post::get($params);
        return $result;
    }

//    public static function update($params){
//        if(empty($params['post_name']) || empty($params['post_content']) || empty($params['meta_title']) || empty($params['meta_keyword']) ||  empty($params['meta_description'])){
//            $params['error'] = 'Vui lòng nhập đầy đủ nội dung!';
//            return $params;
//        }
//
//        $post_name =  trim(strip_tags($params['post_name']));
//        $post_content = $params['post_content'];
//        $cate_id = empty($params['cate_id']) ? 0 : $params['cate_id'];
//        $status = (int)$params['status'];
//        $images = empty($params['fid']) ? : $params['fid'];
//        $meta_title = empty($params['meta_title']) ? : $params['meta_title'];
//        $meta_description = empty($params['meta_description']) ? : $params['meta_description'];
//        $meta_keyword = empty($params['meta_keyword']) ? : $params['meta_keyword'];
//        $post_id = $params['post_id'];
//
//        //check name
//        $exist = Model\Post::get([
//            'post_name' => $post_name,
//            'limit' => 1,
//            'offset' => 0,
//            'not_status' => Model\Post::POST_STATUS_REMOVE,
//            'cate_id' => $cate_id,
//            'not_post_id' => $post_id
//        ]);
//
//        if(!empty($exist['rows'])){
//            $params['error'] = 'Tiêu đề bài viết này đã tồn tại trong hệ thống!';
//            return $params;
//        }
//
//        //update
//        $updated = Model\Post::update([
//            'post_name' => $post_name,
//            'post_slug' => Utils::getSlug($post_name),
//            'status' => $status,
//            'updated_date' => time(),
//            'user_updated' => USER_ID,
//            'cate_id' => $cate_id,
//            'images'=> $images,
//            'post_content' => $post_content,
//            'meta_keyword' => $meta_keyword,
//            'meta_description' => $meta_description,
//            'meta_title' => $meta_title
//        ],$post_id);
//
//        if(!$updated){
//            $params['error'] = 'Xảy ra lỗi trong quá trình xử lý! Thử lại sau giây lát';
//            return $params;
//        }
//
//        return [
//            'success' => true
//        ];
//    }

//    public static function delete($params){
//
//        if(empty($params['arr_post_id'])){
//            return [
//                'st' => -1,
//                'ms' => __FUNCTION__,
//                'error' => 'error'
//            ];
//        }
//
//        $arr_id = $params['arr_post_id'];
//
//        //get info product
//        $posts = Model\Post::get([
//            'in_post_id' => $arr_id,
//            'not_status' => Model\Post::STATUS_REMOVE
//        ]);
//
//        if(empty($posts['rows'])){
//            return [
//                'st' => -1,
//                'ms' => __FUNCTION__,
//                'error' => 'error'
//            ];
//        }
//
//
//        //delete
//        $status = Model\Post::updateByCondition([
//            'status' => Model\Post::STATUS_REMOVE,
//            'updated_date' => time(),
//            'user_updated' => USER_ID
//        ],[
//            'in_post_id' => $arr_id
//        ]);
//
//        if(!$status){
//            return [
//                'st' => -1,
//                'ms' => 'Xảy ra lỗi trong quá trình xử lý! Vui lòng thử lại!',
//                'error' => 'error'
//            ];
//        }
//
//        return [
//            'st' => 1,
//            'ms' => 'Xóa bài viết thành công!',
//            'success' => 'success'
//        ];
//    }
}