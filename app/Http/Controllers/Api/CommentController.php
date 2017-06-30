<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\Comment;
use App\Models\Transaction;
use App\Traits\ImageTraits;
use Illuminate\Http\Request;
use DB;

class CommentController extends BaseController
{
    use ImageTraits;

    public function getList(Request $request, Comment $comment)
    {
        $where = [];
        $pages = $this->pageInit();
        $transId = $request->input('transation_id') or $this->responseApi(1004);
        if ($transId) {
            $where[] = ['transation_id', '=', $transId];
        }

        $result = $comment->getList($pages['page'], $pages['limit'], $where);
        $this->responseApi(0, '', $result);
    }

    /**
     * 发布评论
     */
    public function create(Request $request, Comment $comment)
    {
        $transaction_id = $request->input('transaction_id') or $this->responseApi(1004);
        $pid = $request->input('pid', 0);
        $content = $request->input('content') or $this->responseApi(1004);
        $imgs = $request->input('imgs');
        $created_at = date('Y-m-d H:i:s');

        if (empty($this->user_ses)) {
            $this->responseApi(1000);
        }

        $user_id = $this->user_ses->id;
        if (!$imgs) {
            $result = $comment->createData($comment->getTable(), compact('transaction_id', 'pid', 'content', 'created_at'));
            $result ? $this->responseApi(0) : $this->responseApi(9000);
        }

        DB::transaction();
        $id = $comment->createData($comment->getTable(), compact('transaction_id', 'pid', 'content', 'user_id', 'created_at'));
        if (!$id)
            $this->responseApi(9000);

        foreach ($imgs as $v) {
            $originName = $this->thumbImg($v, 'comment');
            $r = $comment->createData($comment->commentImgTb(), ['comment_id' => $id, 'img' => $originName]);
            if (!$r) {
                DB::rollBack();
                $this->responseApi(9000);
            }
        }

        DB::commit();
        $this->responseApi(0);

    }
}