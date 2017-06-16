<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        $this->responseApi(0,'',$this->user_ses);
    }
}