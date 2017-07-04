<?php
/**
 * Created by PhpStorm.
 * User: liguanke
 * Date: 17/6/15
 * Time: 下午1:22
 */

namespace App\Models;


use App\Traits\BaseModelTraits;
use Illuminate\Database\Eloquent\Model;
use DB;

class RedPacket extends Model
{
    use BaseModelTraits;

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'red_packet';

    protected $primaryKey = 'id';

    protected function redPacketChart()
    {
        return 'red_packet_chart';
    }

    public function get($id)
    {
        $info = DB::table($this->table . ' as a')->leftjoin('user as b', 'a.user_id', '=', 'b.id')->where('b.id', $id)->get(['a.*', 'b.username', 'b.avatar']);
        $rows = DB::table($this->redPacketChart())->where('red_packet_id', $id)->get();
        foreach ($rows as $v) {
            $obj = DB::table('user')->where('id', $v->user_id)->first(['avatar', 'username']);
            $v->avatar = $obj->avatar;
            $v->username = $obj->username;
        }

        $return = [
            'info' => obj2arr($info),
            'rows' => obj2arr($rows)
        ];

        return $return;
    }

    /**
     * 抢红包
     */
    public function createPacketRecord($id, $userId)
    {
        $moneys = DB::table($this->table)->find($id, ['enable_money', 'enable_sum']);
        if ($moneys->enable_sum == 1) {
            $money = $moneys->enable_money;
        } else {
            $money = mt_rand(0.1, $moneys->enable_money);
        }

        $data = [
            'red_packet_id' => $id,
            'user_id' => $userId,
            'money' => $money,
            'created_at' => date('Y-m-d H:i:s')
        ];

        DB::transaction();
        $id = DB::table($this->table)->insertGetId($data);
        if (!$id) {
            DB::rollBack();
            return false;
        }

        $r1 = DB::table($this->table)->where('id', $id)->decrement('enable_sum');
        $r2 = DB::table($this->table)->where('id', $id)->decrement('enable_money', $money);

        if (!$r1 || $r2) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    public function ajaxData($param)
    {
        $where = [];
        if (isset($param['search'])) {
            $where[] = ['b.username', 'like', '%' . $param['search'] . '%', 'OR'];
        }

        $sort = array_get($param, 'sort') ?: $this->getKeyName();
        $order = array_get($param, 'order', 'desc');
        $rows = DB::table($this->table . ' as a')->join('user as b', 'a.user_id', '=', 'b.id')->where($where)->orderBy($sort, $order)->offset(array_get($param, 'offset', 0))->limit(array_get($param, 'limit', 10))->get(['a.*', 'b.username', 'b.avatar']);
        $rows = obj2arr($rows);

        $total = DB::table($this->table)->where($where)->count();

        return compact('rows', 'total');
    }

}