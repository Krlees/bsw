<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\RedPacket;
use Illuminate\Http\Request;
use DB;

class RedPacketController extends BaseController
{
    private $red;

    public function __construct(RedPacket $redPacket)
    {
        $this->red = $redPacket;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $results = $this->red->ajaxData($param);

            return $this->responseAjaxTable($results['total'], $results['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/redpacket/index'), [
                'addUrl' => null,
                'editUrl' => null,
                'removeUrl' => null,
                'autoSearch' => true
            ]);

            return view('admin/RedPacket/index', compact('reponse'));
        }
    }

    public function detail($id, Request $request)
    {
        $result = $this->red->get($id);

        return view('admin/RedPacket/detail', compact('result'));
    }


}
