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

class UserLabelCard extends Model
{
    use BaseModelTraits;

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_label_card';

    protected $primaryKey = 'id';

    public function create($label_id, $user_id, $data)
    {
        DB::beginTransaction();

        try {
            $del = DB::table($this->table)->where('label_id', $label_id)->where('user_id', $user_id)->delete();
            foreach ($data as $v) {
                $id = DB::table($this->table)->insert([
                    'user_id' => $user_id,
                    'label_id' => $label_id,
                    'nav_label_cate_id' => $v['nav_label_cate_id'],
                    'nav_label_cate_name' => $v['nav_label_cate_name'],
                ]);
                if (!$id) {
                    DB::rollBack();
                    return false;
                }
            }

        } catch (\Exception $e) {
            return false;
        }

        DB::commit();
        return true;

    }


}