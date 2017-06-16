<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(Request $request)
    {
        $this->middleware('api.token') or $this->responseApi(1000);
        parent::__construct();
    }


}