<?php
/**
 * Created by PhpStorm.
 * User: liguanke
 * Date: 17/6/15
 * Time: 下午1:22
 */

namespace App\Models;


use App\Traits\BaseModelTraits;
use App\Traits\DistrictTraits;
use Illuminate\Database\Eloquent\Model;
use DB;

class Transaction extends Model
{
    use DistrictTraits;
    use BaseModelTraits;

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'transaction';

    protected $primaryKey = 'id';

    public function transactionClickRecordTb()
    {
        return 'transaction_click_record';
    }

    public function transactionImg()
    {
        return 'transaction_img';
    }

    public function transactionPayRecordTb()
    {
        return 'transaction_pay_record';
    }

    public function transctionFollowTb()
    {
        return 'transaction_follow';
    }

    public function getImg($transId)
    {
        return DB::table($this->transactionImg())->where('transaction_id', $transId)->get();
    }

    public function get($id)
    {
        $trans = DB::table($this->table)->find($id, ['id', 'user_id', 'title', 'content', 'ext1', 'province', 'city', 'address', 'days']);
        if (empty($trans))
            return [];

        $imgs = $this->getImg($trans->id);
        if ($imgs) {
            foreach ($imgs as $img) {
                if ($img->is_cover == 1)
                    $trans->cover = $img->img;

                $trans->imgs[] = $img->img;
            }
        }

        $avatars = DB::table('user')->find($trans->user_id, ['avatar']);

        $trans->avatar = $avatars ? picture_url($avatars->avatar) : '';


        return obj2arr($trans);

    }

    public function getList($page, $limit = 20, $where = null, $is_show = 1)
    {
        $db = DB::table($this->table)->where('is_show', $is_show);
        if ($where) {
            $db->where($where);
        }


        $result = $db->orderBy('created_at', 'desc')->offset($page * $limit)->limit($limit)->get(['id', 'user_id', 'title', 'content', 'label_id', 'created_at', 'city', 'ext1', 'is_must_pay', 'is_normal_pay', 'is_wallet_pay', 'is_juan_pay', 'days', 'user_id']);
        $result = obj2arr($result);
        foreach ($result as $k => $v) {
            $label = DB::table('label')->find($v['label_id'], ['name']);
            $result[$k]['label_name'] = $label ? $label->name : '';
            $result[$k]['addtime'] = ($v['created_at'] - time() - 24 * 2 * 3600 < 0) ? mdate($v['created_at']) : date('Y-m-d H:i:s', $v['created_at']);


            $result[$k]['imgs'] = [];
            $result[$k]['cover'] = '';
            $imgs = $this->getImg($v['id']);
            if ($imgs) {
                foreach ($imgs as $img) {
                    if ($img->is_cover == 1)
                        $result[$k]['cover'] = picture_url($img->img);

                    $result[$k]['imgs'][] = picture_url($img->img);
                }
            }


            $avatars = DB::table('user')->find($v['user_id'], ['avatar']);
            $result[$k]['head_img'] = $avatars ? picture_url($avatars->avatar) : '';

        }

        return $result;
    }

    public function checkUserPay($id, $userId)
    {
        $count = DB::table($this->transactionPayRecordTb())->where(['transaction_id' => $id, 'user_id' => $userId])->count();
        return $count ? true : false;
    }

    public function getCitys($labelId, $channelId)
    {
        $result = DB::table($this->table)->where('label_id', $labelId)->where('channel_id', $channelId)->where('city', '<>', '')->groupBy('city')->orderByRaw('count(city) desc')->get(['city']);

        return obj2arr($result);
    }
}