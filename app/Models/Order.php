<?php
/**
 * Created by PhpStorm.
 * User: liguanke
 * Date: 17/6/15
 * Time: 下午1:22
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use DB;

class Order extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'order';

    protected $primaryKey = 'id';

    public function get($id)
    {
        return DB::table($this->table)->find($id);
    }

    public function getList($userId = 0)
    {
        $db = DB::table($this->table);
        if ($userId)
            $db->where('user_id', $userId);
        $result = $db->get();

        return obj2arr($result);
    }

    public function create($data)
    {
        try {
            $id = DB::table($this->table)->insertGetId($data);

            return $id;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateData($id,$data)
    {
        try {
            $b = DB::table($this->table)->where('id',$id)->update($data);
            return $b!==false;
        } catch (\Exception $e) {
            return false;
        }
    }


}