<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\RedPacket;
use App\Traits\ImageTraits;
use Illuminate\Http\Request;

class RedPacketController extends BaseController
{
    use ImageTraits;

    /**
     * 发布一个新红包
     */
    public function create(Request $request, RedPacket $packet)
    {
        if (!$this->user_ses) {
            $this->responseApi(1000);
        }

        $user_id = $this->user_ses->id;
        $money = $request->input('money');
        $sum = $request->input('sum');
        $company_name = $request->input('company_name');
        $desc = $request->input('desc');
        $url = $request->input('url');
        $created_at = $request->input('created_at');
        $cover_img = $request->input('cover_img');
        if (!$money || !$sum || !$url || $desc || !$cover_img) {
            $this->responseApi(1004);
        }

        $enable_money = $money;
        $enable_sum = $sum;
        $cover_img = $this->thumbImg($cover_img, 'red_packet');

        $id = $packet->createData($packet->getTable(), compact('user_id', 'money', 'enable_money', 'enable_sum', 'sum', 'company_name', 'desc', 'url', 'created_at', 'cover_img'));
        $id ? $this->responseApi(0) : $this->responseApi(9000);
    }

    /**
     * 抢红包
     */
    public function postPacket(RedPacket $packet)
    {
        if (!$this->user_ses) {
            $this->responseApi(1000);
        }

        $user_id = $this->user_ses->id;
        $result = $packet->createPacketRecord($user_id);
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }
}