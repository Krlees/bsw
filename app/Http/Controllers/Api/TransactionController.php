<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Label;
use App\Models\Transaction;
use App\Models\UserVip;
use App\Traits\DistrictTraits;
use Illuminate\Http\Request;

class TransactionController extends BaseController
{
    use DistrictTraits;

    /**
     * 获取消息详情
     * @param $id
     * @param Transaction $transaction
     */
    public function get($id, Transaction $transaction)
    {
        $data = $transaction->get($id);

        $this->responseApi(0, '', $data);
    }

    /**
     * 获取服务商资讯信息
     *     $whereType == 1,最外面的大分类,不用作逻辑判断
     * @param Request $request
     * @param Transaction $transaction
     */
    public function getVipList(Request $request, Label $label, Transaction $transaction, UserVip $userVip)
    {
        $city = $request->input('city'); //默认获取全部
        $pages = $this->pageInit();
        $channelId = 1;

        $where[] = ['channel_id', '=', $channelId];
        $return = [];
        $labelData = $label->getList($label->getTable(), 0, 4);
        foreach ($labelData as $key => $val) {
            $where[] = ['label_id', '=', $val['id']];
            $result = $transaction->getList($pages['page'], 4, $where);
            if (empty($result)) {
                continue;
            }

            // vip频道重要逻辑处理
            $result = $this->_helpVip($result, $userVip, $transaction);

            $return[$key]['label_id'] = $val['id'];
            $return[$key]['label_name'] = $val['name'];
            $return[$key]['list'] = $result;

        }


        $this->responseApi(0, '', $return);
    }


    /**
     * 获取vip频道内列表
     * @param Request $request
     * @param Label $label
     * @param Transaction $transaction
     * @param UserVip $userVip
     */
    public function getVipInfo(Request $request, Label $label, Transaction $transaction, UserVip $userVip)
    {
        $city = $request->input('city'); //默认获取全部
        $pages = $this->pageInit();
        $labelId = $request->input('label_id') or $this->responseApi(1004);
        $channelId = 1;

        $where[] = ['channel_id', '=', $channelId];
        $where[] = ['label_id', '=', $labelId];
        if ($city && $city != '全部') {
            $where[] = ['city', '=', $city];
        }

        $result = $transaction->getList($pages['page'], $pages['limit'], $where);
        if (empty($result)) {
            $this->responseApi(0, '', $result);
        }

        $result = $this->_helpVip($result, $userVip, $transaction);

        $this->responseApi(0, '', $result);

    }

    /**
     * 获取订单资讯信息
     * @param Request $request
     * @param Transaction $transaction
     */
    public function getOrderList()
    {

    }

    /**
     * 获取普通频道的信息
     * @param Request $request
     * @param Transaction $transaction
     */
    public function getList(Request $request, Transaction $transaction)
    {
        $channelId = $request->input('channel_id') or $this->responseApi(1004);
        $result = $transaction->getList($channelId);

    }

    /**
     * 检测用户是否已购买
     */
    private function checkUserPay($transId, $transaction)
    {
        $user_id = $this->user_ses ? $this->user_ses->id : false;
        if (!$user_id) {
            return false;
        }

        return $transaction->checkUserPay($transId, $user_id);

    }

    private function _helpVip($result, $userVip, $transaction)
    {
        if (empty($result))
            return $result;

        foreach ($result as $k => $v) {
            $lock = true;
            $isVip = false;


            /* 判断当前用户VIP是否有效 */
            if ($this->user_ses) {
                if ($userVip->checkExpires($v['label_id'], $this->user_ses->id))
                    $isVip = true;
            }

            // 1. 判断是否是自己发的，自己发的自动解锁
            if ($this->user_ses) {
                if ($this->user_ses->id == $v['user_id']) {
                    $lock = false;
                    $result[$k]['lock'] = $lock;
                    $result[$k]['isVip'] = $isVip;
                    continue;
                }
            }

            // 2. 检测用户是否购买
            if ($this->checkUserPay($v['id'], $transaction)) {
                unset($result[$k]);
                continue;
            }

            // 3. 判断是否是必须用钱购买，【VIP】无效,但是vip享受5折价格
            if ($v['is_must_pay']) {
                $result[$k]['lock'] = $lock;
                $result[$k]['isVip'] = $isVip;
                continue;
            }

            // 超过72小时，归为0
            $lockTime = time() - strtotime($v['created_at']); //剩余时间
            if ($lockTime > 72 * 60 * 60) {
                $lockTime = 0;
            }

            // 4. 判断是否需要正常购买，【VIP】有效
            if ($v['is_normal_pay']) {

                /* 判断VIP */
                if ($isVip) {
                    $lock = false;
                    $result[$k]['lock'] = $lock;
                    $result[$k]['isVip'] = $isVip;
                    continue;
                }
            }

            // 5. 超过72小时，可免费查看
            if ($lockTime == 0) {
                $lock = false;
            }

            $result[$k]['dueTime'] = $lockTime; //剩余时间
            $result[$k]['lock'] = $lock;
            $result[$k]['isVip'] = $isVip;
        }

        return $result;
    }

}