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

class Transaction extends Model
{
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

}