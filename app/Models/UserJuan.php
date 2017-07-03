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

class UserJuan extends Model
{
    use BaseModelTraits;

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'user_juan';

    protected $primaryKey = 'id';



    
}