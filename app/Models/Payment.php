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

class Payment extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'channel';

    protected $primaryKey = 'id';


    
}