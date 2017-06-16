<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Member;
use Illuminate\Http\Request;

class LabelController extends BaseController
{

    /**
     * 获取标签下的用户
     */
    public function getUser($id = 0, Request $request, Member $member)
    {
        $where = [];
        if ($id)
            $where[] = ['label_id', '=', $id];

        $pages = $this->pageInit();
        $result = $member->getList($pages['page'], $pages['limit'], $where);

        $this->responseApi(0, '', $result);
    }

    public function getList()
    {

    }
}