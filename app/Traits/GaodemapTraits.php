<?php

namespace App\Traits;

trait GaodemapTraits
{

    public $ak = "26ebd7fdc55795c7d7fc6502f42726bd";

    /**
     * [address_get_point description]根据地址获取经纬度
     * @param  [type] $name [description]
     * @param  [type] $city [description]
     * @return [type]       [description]
     */
    public function address_get_point($name, $city='')
    {
        if ($city) {
            $key = $this->ak;
            $url = "http://restapi.amap.com/v3/geocode/geo?address=" . $name . "&city=" . $city . "&output=json&key=" . $key;
        } else {
            $key = $this->ak;
            $url = "http://restapi.amap.com/v3/geocode/geo?address=" . $name . "&output=json&key=" . $key;
        }
        $res = curl_do($url);
        return $res;
    }

    /**
     * 根据经纬度得到地址
     */
    public function point_get_address($lng, $lat)
    {
        $location = $lng . ',' . $lat;
        $key = $this->ak;
        $url = "http://restapi.amap.com/v3/geocode/regeo?location=" . $location . "&poitype=&radius=&extensions=base&batch=false&roadlevel=&output=json&key=" . $key;

        $res = curl_do($url);
        return $res;
    }

}