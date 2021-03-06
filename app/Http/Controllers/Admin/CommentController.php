<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Comment;
use Illuminate\Http\Request;
use DB;

class CommentController extends BaseController
{
    private $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function index($type, Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $where[] = ['channel_id', '=', $type];
            $result = $this->comment->ajaxData($param, $where);

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/comment/index/' . $type), [
                'addUrl' => url('admin/comment/add'),
                'editUrl' => url('admin/comment/edit'),
                'removeUrl' => url('admin/comment/del'),
                'autoSearch' => true
            ]);

            return view('admin/Comment/index', compact('reponse'));
        }

    }

    public function del()
    {
        $ids = $this->getDelIds();

        DB::table($this->comment->commentImgTb())->whereIn('comment_id', $ids)->delete();
        $result = DB::table($this->comment->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }


}
