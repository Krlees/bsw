<?php
/**
 * Created by PhpStorm.
 * User: liguanke
 * Date: 17/6/15
 * Time: 下午1:22
 */

namespace App\Models;


use App\Traits\DistrictTraits;
use Illuminate\Database\Eloquent\Model;
use DB;

class Transaction extends Model
{
    use DistrictTraits;

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


        $trans->avatar = picture_url($avatars->avatar);


        return obj2arr($trans);

    }

    public function getList($page, $limit = 20, $where = null, $is_show = 1)
    {
        $db = DB::table($this->table)->where('is_show', $is_show);
        if ($where) {
            $db->where($where);
        }


        $result = $db->offset($page * $limit)->limit($limit)->get(['id', 'user_id', 'title', 'content', 'label_id', 'created_at', 'city', 'ext1', 'is_must_pay', 'is_normal_pay', 'is_wallet_pay', 'is_juan_pay', 'days', 'user_id']);
        $result = obj2arr($result);
        foreach ($result as $k => $v) {
            $label = DB::table('label')->find($v['label_id'], ['name']);
            $result[$k]['label_name'] = $label->name;
            $result[$k]['city'] = $this->getByCity($v['city']);

            $imgs = $this->getImg($v['id']);
            if ($imgs) {
                foreach ($imgs as $img) {
                    if ($img->is_cover == 1)
                        $result[$k]['cover'] = $img->img;

                    $result[$k]['imgs'][] = $img->img;
                }
            }


            $avatars = DB::table('user')->find($v['user_id'], ['avatar']);
            $result[$k]['head_img'] = picture_url($avatars->avatar);

        }

        return $result;
    }

    public function checkUserPay($id, $userId)
    {
        $count = DB::table($this->transactionPayRecordTb())->where(['transaction_id' => $id, 'user_id' => $userId])->count();
        return $count ? true : false;
    }

    public function getCitys($labelId)
    {
        $result = DB::table($this->table)->where('label_id', $labelId)->where(['city' => ""])->groupBy('city')->get(['city']);

        return obj2arr($result);
    }
}