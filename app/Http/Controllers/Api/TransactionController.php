<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Label;
use App\Models\Transaction;
use App\Models\UserVip;
use App\Traits\DistrictTraits;
use App\Traits\GaodemapTraits;
use App\Traits\ImageTraits;
use App\Traits\JpushTraits;
use Illuminate\Http\Request;
use DB;

class TransactionController extends BaseController
{
    use DistrictTraits;
    use GaodemapTraits;
    use ImageTraits;
    use JpushTraits;

    /**
     * 获取消息详情
     * @param $id
     * @param Transaction $transaction
     */
    public function get($id, Transaction $transaction)
    {
        $data = $transaction->get($id);
        $data['is_collect'] = false;
        $data['mobile'] = '';
        if ($this->user_ses) {
            $data['is_collect'] = $transaction->checkFollow($id, $this->user_ses->id);
            $mobiles = DB::table('user')->find($this->user_ses->id, ['mobile']);
            $data['mobile'] = $mobiles ? $mobiles->mobile : '';
        }

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

        $pages = $this->pageInit();
        $channelId = 1;

        $return = [];
        $labelData = $label->getList($label->getTable());
        foreach ($labelData as $key => $val) {
            $where = [
                ['channel_id', '=', $channelId],
                ['label_id', '=', $val['id']]
            ];
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
        sort($return);


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
        foreach ($result as $k => $v) {
            $label = DB::table('label')->find($v['label_id'], ['name']);
            $result[$k]['label_name'] = $label->name;

            $avatars = DB::table('user')->find($v['user_id'], ['avatar']);
            $result[$k]['head_img'] = $avatars ? picture_url($avatars->avatar) : '';

        }

        $result = $this->_helpVip($result, $userVip, $transaction);

        $this->responseApi(0, '', $result);

    }

    /**
     * 获取订单频道资讯信息
     * @param Request $request
     * @param Transaction $transaction
     */
    public function getOrderList(Request $request, Label $label, Transaction $transaction, UserVip $userVip)
    {
        $pages = $this->pageInit();
        $channelId = 2; //订单频道

        $return = [];
        $labelData = $label->getList($label->getTable());
        foreach ($labelData as $key => $val) {
            $where = [
                ['channel_id', '=', $channelId], ['label_id', '=', $val['id']]
            ];
            $result = $transaction->getList($pages['page'], 4, $where);
            if (empty($result)) {
                continue;
            }

            // 订单频道逻辑处理
            $result = $this->_helpOrder($result, $userVip, $transaction);

            $return[$key]['label_id'] = $val['id'];
            $return[$key]['label_name'] = $val['name'];
            $return[$key]['list'] = $result;

        }
        sort($return);

        $this->responseApi(0, '', $return);
    }

    /**
     * 获取订单频道内列表
     * @param Request $request
     * @param Label $label
     * @param Transaction $transaction
     * @param UserVip $userVip
     */
    public function getOrderInfo(Request $request, Label $label, Transaction $transaction, UserVip $userVip)
    {
        $city = $request->input('city'); //默认获取全部
        $pages = $this->pageInit();
        $labelId = $request->input('label_id') or $this->responseApi(1004);
        $channelId = 2;

        $where[] = ['channel_id', '=', $channelId];
        $where[] = ['label_id', '=', $labelId];
        if ($city && $city != '全部') {
            $where[] = ['city', '=', $city];
        }

        $result = $transaction->getList($pages['page'], $pages['limit'], $where);
        if (empty($result)) {
            $this->responseApi(0, '', $result);
        }

        $result = $this->_helpOrder($result, $userVip, $transaction);

        $this->responseApi(0, '', $result);

    }

    /**
     * 获取普通频道的信息
     * @param Request $request
     * @param Transaction $transaction
     */
    public function getList(Request $request, Transaction $transaction)
    {
        $channelId = $request->input('channel_id') or $this->responseApi(1004);
        $channelType = $request->input('channel_type');
        if ($channelType)
            $where[] = ['channel_type', '=', $channelType];

        $where[] = ['channel_id', '=', $channelId];
        $pages = $this->pageInit();

        $result = [];

        // 判断是否是职位下的求职频道
        if ($channelId == 5 && $channelType == '求职') {

            // 职位下的求职频道格式不一样
            $citys = $transaction->getCitys(false, $channelId);
            foreach ($citys as $k => $v) {
                $where[] = ['city', '=', $v['city']];
                $arr = $transaction->getList($pages['page'], 4, $where);
                if (empty($arr))
                    continue;

                $result[$k]['label_name'] = $v['city'];
                $result[$k]['list'] = $arr;
            }

        } else {
            $result = $transaction->getList($pages['page'], $pages['limit'], $where);
        }


        $this->responseApi(0, '', $result);

    }

    public function getCitys(Request $request, Transaction $transaction)
    {
        $labelId = $request->input('label_id') or $this->responseApi(1004);
        $channelId = $request->input('channel_id') or $this->responseApi(1004);
        $results = $transaction->getCitys($labelId, $channelId);
        $arr = array_column($results, 'city');
        array_unshift($arr, '全部');

        return $arr;
    }

    public function create(Request $request, Transaction $transaction)
    {
        $title = $request->input('title') or $this->responseApi(1004);
        $content = $request->input('content') or $this->responseApi(1004);
        $ext1 = $request->input('ext1', '');
        $imgs = $request->input('imgs') or $this->responseApi(1004);
        $created_at = $request->input('created_at');
        $address = $request->input('address') or $this->responseApi(1004);
        $days = $request->input('days', '');
        $channel_id = $request->input('channel_id') or $this->responseApi(1004);
        $channel_type = $request->input('channel_type', '');
        $created_at = $created_at ? strtotime($created_at) : time();
        $send_type = 1;
        $user_id = $this->user_ses ? $this->user_ses->id : $this->responseApi(1000);
        $data = compact('user_id', 'title', 'content', 'ext1', 'created_at', 'days', 'address', 'send_type', 'channel_id', 'channel_type');


        // 高德定位
        $res = $this->address_get_point($address);
        $res = \GuzzleHttp\json_decode($res, true);
        if ($res['info'] == 'OK' && $res['geocodes']) {
            $data['province'] = $res['geocodes'][0]['province'];
            $data['city'] = $res['geocodes'][0]['city'];
            list($data['lng'], $data['lat']) = explode(",", $res['geocodes'][0]['location']);
        }

        $id = $transaction->createData($transaction->getTable(), $data);
        if ($id) {
            $check = $transaction->checkTransImg($id); //检测有木有默认封面图片
            foreach ($imgs as $k => $v) {
                $imgData = [];
                if ($k == 0 && !$check)
                    $imgData['is_cover'] = 1;

                $imgData['img_thumb'] = $this->thumbImg($v, 'transaction');
                $imgData['transaction_id'] = $id;

                $b = $transaction->createData($transaction->transactionImg(), $imgData);
            }

            // 发送极光推送
            $this->push('all', $title);

        }

        $this->responseApi(9000);

    }

    public function postFollow(Request $request, Transaction $transaction)
    {
        $id = $request->input('id') or $this->responseApi(1004);
        if (empty($this->user_ses))
            $this->responseApi(80001, '用户未登录');

        $count = DB::table('transaction_follow')->where('transaction_id', $id)->where('user_id', $this->user_ses->id)->count();
        if ($count) {
            $result = DB::table('transaction_follow')->where(['transaction_id' => $id, 'user_id' => $this->user_ses->id])->delete();
        } else {
            $result = DB::table('transaction_follow')->insert(['transaction_id' => $id, 'user_id' => $this->user_ses->id, 'created_at' => time()]);
        }

        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    public function postClick(Request $request, Transaction $transaction)
    {
        $id = $request->input('id') or $this->responseApi(1004);
        if (empty($this->user_ses))
            $this->responseApi(80001, '用户未登录');


        $result = DB::table($transaction->transactionClickRecordTb())->insert(['transaction_id' => $id, 'user_id' => $this->user_ses->id, 'created_at' => time()]);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    private function _helpJobList()
    {
//        $result[$k]['dueTime'] = $lockTime; //剩余时间
//        $result[$k]['lock'] = $lock;
//        $result[$k]['isVip'] = $isVip;
    }

    /**
     * 检测用户是否已购买
     */
    private function _checkUserPay($transId, $transaction)
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
            if ($this->_checkUserPay($v['id'], $transaction)) {
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
            $lockTime = 3600 * 72 + $v['created_at'] - time(); //剩余时间
            if ($lockTime < 0) {
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

            unset($result[$k]['imgs']);

        }

        return $result;
    }

    private function _helpOrder($result, $userVip, $transaction)
    {
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
            if ($this->_checkUserPay($v['id'], $transaction)) {
                unset($result[$k]);
                continue;
            }

            // 超过72小时，归为0
            $lockTime = 3600 * 72 + $v['created_at'] - time(); //剩余时间
            if ($lockTime < 0) {
                $lockTime = 0;
            }


            // 3. 判断是否需要正常购买，【VIP】有效
            if ($v['is_normal_pay']) {

                /* 判断VIP */
                if ($isVip) {
                    $lock = false;
                    $result[$k]['lock'] = $lock;
                    $result[$k]['isVip'] = $isVip;
                    continue;
                }
            }

            // 4. 超过72小时，可免费查看
            if ($lockTime == 0) {
                $lock = false;
            }

            $result[$k]['dueTime'] = $lockTime; //剩余时间
            $result[$k]['lock'] = $lock;
            $result[$k]['isVip'] = $isVip;

            unset($result[$k]['imgs']);
        }

        return $result;
    }

}