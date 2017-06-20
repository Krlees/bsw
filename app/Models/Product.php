<?php
/**
 * Created by PhpStorm.
 * User: liguanke
 * Date: 17/6/15
 * Time: 下午1:22
 */

namespace App\Models;


use App\Models\BaseModel;
use DB;

class Product extends BaseModel
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'product';

    protected $primaryKey = 'id';

    public function productCategoryTb()
    {
        return 'product_category';
    }

    public function get($id)
    {
        return DB::table($this->table)->find($id);
    }

    public function getList($cateId=0,$field=['*'])
    {
        $db = DB::table($this->table);
        if($cateId){
            $db->where('category_id',$cateId);
        }
        $result = $db->get($field);

        return obj2arr($result);
    }

}