<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Comment;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CommentController extends BaseController
{
    public function getList(Request $request, Comment $comment)
    {
        $where = [];
        $pages = $this->pageInit();
        $transId = $request->input('transation_id') or $this->responseApi(1004);
        if ($transId) {
            $where[] = ['transation_id', '=', $transId];
        }

        $result = $comment->getList($pages['page'], $pages['limit'], $where);
        $this->responseApi(0,'',$result);
    }
}