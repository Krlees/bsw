<?php

namespace App\Traits;

use App\Library\NetEaseServer;

trait NetEaseTraits
{


    /**
     * 获取云通讯token
     * @param $accid  用户id
     * @param $name   用户昵称
     * @param $icon   用户头像
     * @return array|bool
     */
    public function getNetToken($accid, $name, $icon)
    {
        $APPKey = '66663ffc76a208252a11787bb005cf3a';
        $AppSecret = '1a135a3de720';
        $ServerAPI = new NetEaseServer($APPKey, $AppSecret, 'curl');        //php curl库
        $props = '';
        $icon = picture_url($icon);
        $resServerAPI = $ServerAPI->createUserId($accid, $name, $props, $icon);
        if ($resServerAPI['code'] == 200) {
            return $resServerAPI;
        } else {
            $res = $ServerAPI->updateUserToken($accid);
            if ($res['code'] == 200) {
                return $res;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * 更新云通讯token
     * @param $accid 用户id
     * @param $name  用户昵称
     * @param $icon  用户头像
     * @return bool
     */
    public function updateAccidInfo($accid, $name, $icon)
    {
        $APPKey = '66663ffc76a208252a11787bb005cf3a';
        $AppSecret = '1a135a3de720';
        $ServerAPI = new NetEaseServer($APPKey, $AppSecret, 'curl');        //php curl库
        $icon = picture_url($icon);
        $ServerAPI->updateUinfo($accid, $name, $icon);
        return TRUE;
    }
}