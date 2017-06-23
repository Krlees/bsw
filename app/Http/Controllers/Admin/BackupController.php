<?php

namespace App\Http\Controllers\Admin;

use DB;
class BackupController
{

    // 产品表
    public function product()
    {
        $arr = DB::table('product_old')->get();
        foreach ($arr as $v){
            $data = [
                'category_id' => '',
                'name' => $v->product_name,
                'price' => $v->product_price,
                'desc' => $v->unit,
                'attrs' => $v->product_attr,
                'times' => $v->pro_times
            ];
            $cate = DB::table('product_category')->where('name',$v->shuyu)->first();
            if($cate){
                $cate = obj2arr($cate);
                $data['category_id'] = array_get($cate,'id');
            }
            DB::table('product')->insert($data);
        }
    }

    // 用户表
    public function user()
    {

    }

    // 评论表
    public function comment()
    {

    }


}