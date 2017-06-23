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

        DB::table('bs_user')->orderBy('id','asc')->chunk(1000,function ($res){
            foreach ($res as $v){

                if($v->user_identify == '真实手机用户')
                    $v->user_identify = 'mobile';
                elseif($v->user_identify=='weixin')
                    $v->user_identify = 'wx';

                $data = [
                    'username' => $v->username ?: '',
                    'nickname' => $v->nickname ?: '',
                    'password' => $v->password ?: '',
                    'avatar' => $v->head_thumb ?: '',
                    'origin_img' => $v->head_img ?: '',
                    'desc' => $v->description ?: '',
                    'province' => $v->province ?: '',
                    'city' => $v->city ?: '',
                    'address' => $v->area ?: '',
                    'lng' => $v->lng ?: '',
                    'lat' => $v->lat ?: '',
                    'sex' => $v->sex ?: '',
                    'birthday' => $v->birthday ?: '',
                    'tel' => $v->telphone ?: '',
                    'intro' => $v->intro ?: '',
                    'hometown' => $v->hometown ?: '',
                    'interest' => $v->Interest ?: '',
                    'industry' => $v->industry ?: '',
                    'pid' => $v->p_id,
                    'tx_status' => $v->rank_cashout ?: '',
                    'mobile' => $v->bindphone ?: '',
                    'openid' => $v->unionid ?: '',
                    'aliname' => $v->aliname ?: '',
                    'aliaccount' => $v->aliaccount ?: '',
                    'registration_id' => $v->registration_id ?: '',
                    'netease_token' => $v->NeteaseToken ?: '',
                    'ws' => $v->wanshang ?: '',
                    'register_type' => $v->user_identify,

                ];

                $id = DB::table('user')->insertGetId($data);

                $data2 = [
                    'user_id' => $id,
                    'money' => $v->wallet,
                    'pwd' => $v->paypasswd,
                ];

                DB::table('user_wallet')->insert($data2);

            }
        });

    }


    // 评论表
    public function comment()
    {

    }


}