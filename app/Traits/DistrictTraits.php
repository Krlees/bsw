<?php

namespace App\Traits;


use App\Models\District;

trait DistrictTraits
{
    public function nameGetId($name, $level = 2)
    {
        return app('App\Models\District')->nameGetId($name, $level);
    }

    public function getByCity($id)
    {
        return app('App\Models\District')->get($id);
    }

    public function getByList($pid)
    {
        return app('App\Models\District')->getList($pid);
    }
}