<?php

namespace App\Http\Controllers\Admin;

use App\Traits\GaodemapTraits;
use DB;

class BackupController
{
    use GaodemapTraits;

    public function test(){
        return curl_do("http://bsw.krlee.com/Api/transaction/get-vip-list");
    }

    // 产品表
    public function product()
    {
        $arr = DB::table('user_people')->get();
        foreach ($arr as $v) {
            $cate = DB::table('user')->where('id', $v->user_id)->update(['mark'=>$v->mark]);
        }
    }

    // 用户表
    public function user()
    {

        DB::table('bs_user')->orderBy('id', 'asc')->chunk(1000, function ($res) {
            foreach ($res as $v) {

                if ($v->user_identify == '真实手机用户')
                    $v->user_identify = 'mobile';
                elseif ($v->user_identify == 'weixin')
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
                    'created_at' => date('Y-m-d H:i:s', $v->addtime)
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

    public function transaction()
    {
        DB::table('bs_transaction')->orderBy('id', 'desc')->chunk(5000, function ($arr) {
            foreach ($arr as $v) {
                $jobs = DB::table('job_category')->where('cate_name', $v->job)->first(['id']);
                $data = [
                    'user_id' => $v->user_id,
                    'title' => $v->title,
                    'content' => $v->content,
                    'label_id' => $v->cid,
                    'created_at' => $v->addtime,
                    'updated_at' => $v->endtime,
                    'channel_id' => ($v->is_agent==1) ? 1 : $v->cate_id,
                    'city' => $v->city ?: '',
                    'address' => $v->area ?: '',
                    'ext1' => $v->extra_par1 ?: '',
                    'ext2' => $v->extra_par2 ?: '',
                    'send_type' => (int)$v->user_type,
                    'is_normal_pay' => (int)$v->r_payment,
                    'is_must_pay' => 0,
                    'is_juan_pay' => 0,
                    'is_wallet_pay' => 0,
                    'channel_type' => $v->flag ?: '',
                    'tel' => $v->username ?: '',
                    'label_remark' => $v->label_texts ?: '',
//                    'job_cate_id' => (int)$jobs ?: $jobs->id,
                    'belong' => (int)$v->belong,
                    'label_name_old' => $v->label_name,
                ];

                if ($jobs) {
                    $data['job_cate_id'] = (int)$jobs->id;
                } else {
                    $data['job_cate_id'] = 0;
                }

                // IP获取省份
                $addr = $this->address_get_point($v->area);
                $r = json_decode($addr);
                if (isset($r->info) && $r->info == 'OK') {
                    foreach ($r->geocodes as $v) {
                        $data['province'] = $v->province ?: '';
                        $data['city'] = $v->city ?: '';
                        $data['area'] = $v->township ?: '';
                    }
                }



                DB::table('transaction')->insert($data);


            }

            sleep(5);

        });
}


}