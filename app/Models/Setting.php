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
use Cache;

class Setting extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'setting';

    protected $primaryKey = 'id';

    public function get($classify,$key)
    {

        $setting = cache('setting');
        if(empty($setting)){
            $setting = DB::table($this->table)->get();
            $setting = obj2arr($setting);
            foreach($setting as $item) {
                $setting[$item['classify']][$item['key']] = $item['value'];
            }

            cache()->forever('setting',$setting);
        }

        return $classify ? ($key ? $setting[$classify][$key] : $setting[$classify]) : $setting;

    }

    public function set($classify,$key,$value)
    {
        $mod_setting = DB::table($this->table);
        $count = $mod_setting->where(array('name' => $key, 'classify' => $classify))->count();
        if($count > 0) {
            try{
                $affected = $mod_setting->where(array('name' => $key, 'classify' => $classify))->update(array('value' => $value));
                return bool($affected);
            }
            catch (\Exception $e){
                return false;
            }
        }

        try{
            $mod_setting->insert(array('name' => $key, 'value' => $value, 'classify' => $classify));
        }
        catch (\Exception $e){
            return false;
        }

    }

    public function clear()
    {
        cache()->forget('setting');
        return cache()->has('setting') ? false : true;
    }
    
}