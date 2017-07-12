<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\District;
use App\Models\UserShop;
use App\Models\UserShopGoods;
use App\Traits\ImageTraits;
use Illuminate\Http\Request;
use DB;

class UserShopController extends BaseController
{
    use ImageTraits;

    private $goods;
    private $shop;

    public function __construct(UserShopGoods $goods, UserShop $shop)
    {
        $this->goods = $goods;
        $this->shop = $shop;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $where[] = ['is_del', '=', 0];
            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $this->shop->ajaxData($this->shop->getTable(), $param, $where, 'name');

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/shop/index'), [
                'addUrl' => null,
                'editUrl' => url('admin/shop/goods-edit'),
                'removeUrl' => null,
                'autoSearch' => true
            ]);

            return view('admin/Shop/index', compact('reponse'));
        }

    }

    public function edit($id, Request $request, District $district)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $logo = $request->input(['imgs']);
            $sign_img = $request->input('imgs2');
            if ($logo) {
                $data['logo'] = $this->thumbImg($logo[0], 'shop');
            }
            if ($sign_img) {
                $data['sign_img'] = $this->thumbImg($sign_img[0], 'shop');
            }

            $b = $this->shop->updateData($this->shop->getTable(), $id, $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $provinces = curl_do(url('components/get-district/0'));
            $provinces = \GuzzleHttp\json_decode($provinces);

            $info = $this->shop->get($id);
            $province_id = DB::table($district->getTable())->where('name', $info->province)->where('level', 1)->first(['id']);
            $city_id = DB::table($district->getTable())->where('name', 'like', '%' . $info->city . '%')->where('level', 2)->first(['id']);
            $info->province_id = $province_id ? $province_id->id : 0;
            $info->city_id = $city_id ? $city_id->id : 0;

            $this->createField('text', '公司名称', 'data[company_name]', $info->company_name);
            $this->createField('text', '地址', 'data[address]', $info->address);
            $this->createField('radio', '状态', 'data[status]', [
                [
                    'text' => '开启',
                    'value' => 1,
                    'checked' => $info->status == 1 ? 'true' : 'fasle'
                ],
                [
                    'text' => '禁用',
                    'value' => 0,
                    'checked' => $info->status == 0 ? 'true' : 'fasle'
                ]
            ]);

            $reponse = $this->responseForm('编辑信息', $this->formField);
            return view('admin/Shop/edit', compact('reponse', 'info', 'provinces'));
        }
    }

    public function del()
    {
        $ids = $this->getDelIds();

        $result = DB::table($this->goods->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }

    public function goods(Request $request)
    {
        if ($request->ajax()) {

            $where[] = ['is_del', '=', 0];
            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $this->goods->ajaxData($this->goods->getTable(), $param, $where, 'title');
            foreach ($result['rows'] as &$v) {
                $covers = DB::table($this->goods->goodsImgDb())->where('goods_id', $v['id'])->where('is_cover', 1)->first();
                $v['cover'] = $covers ? $covers->img : '';
            }

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/shop/goods'), [
                'addUrl' => null,
                'editUrl' => url('admin/shop/goods-edit'),
                'removeUrl' => url('admin/shop/goods-del'),
                'autoSearch' => true
            ]);

            return view('admin/Shop/goods', compact('reponse'));
        }
    }

    public function goodsAdd($user_id, Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $data['user_id'] = $user_id;
            $imgs = $request->input(['imgs']);
            if (empty($imgs))
                $this->responseApi(1004);

            $id = $this->goods->createData($this->goods->getTable(), $data);
            if ($id) {
                foreach ($imgs as $k => $v) {

                    $img = $this->thumbImg($v, 'goods');
                    $imgData = [
                        'goods_id' => $id,
                        'img' => $img,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    if ($k == 0) {
                        $imgData['is_cover'] = 1;
                    }
                    DB::table($this->goods->goodsImgDb())->insertGetId($imgData);

                }
            }

            return $id ? $this->responseApi(0) : $this->responseApi(9000);

        } else {

            $this->createField('text', '商品名称', 'data[title]');
            $this->createField('textarea', '详情', 'data[content]');
            $this->createField('radio', '状态', 'data[status]', [
                [
                    'text' => '开启',
                    'value' => 1,
                    'checked' => 'true'
                ],
                [
                    'text' => '禁用',
                    'value' => 0,
                ]
            ]);

            $reponse = $this->responseForm('编辑信息', $this->formField);
            return view('admin/Shop/goods_add', compact('reponse'));
        }
    }

    public function goodsEdit($id, Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $imgs = $request->input(['imgs']);

            // 判断是否删除旧图片
            $this->delImg($this->goods->goodsImgDb());

            // 判断是否切换封面


            // 更新数据
            $id = $this->goods->updateData($this->goods->getTable(), $id, $data);
            if (!$id)
                $this->responseApi(9000);

            // 判断是否新增图片
            if (empty($imgs)) {
                $this->responseApi(0);
            }

            // 判断是否已有封面
            $check = DB::table($this->goods->goodsImgDb())->where('is_cover', 1)->where('goods_id', $id)->count();
            foreach ($imgs as $k => $v) {
                $img = $this->thumbImg($v, 'goods');
                $imgData = [
                    'goods_id' => $id,
                    'img' => $img,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                if (!$check && $k == 0) {
                    $imgData['is_cover'] = 1;
                }

                DB::table($this->goods->goodsImgDb())->insertGetId($imgData);
            }

            return $id ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->goods->getInfo($this->goods->getTable(), $id);
            $imgs = $this->goods->getAll($this->goods->goodsImgDb(), [['goods_id', '=', $id]]);

            $this->createField('text', '商品名称', 'data[title]', $info->title);
            $this->createField('textarea', '详情', 'data[content]', $info->content);
            $this->createField('radio', '状态', 'data[status]', [
                [
                    'text' => '开启',
                    'value' => 1,
                    'checked' => $info->status == 1 ? 'true' : 'false'
                ],
                [
                    'text' => '禁用',
                    'value' => 0,
                    'checked' => $info->status == 0 ? 'true' : 'false'
                ]
            ]);

            $reponse = $this->responseForm('编辑信息', $this->formField);
            return view('admin/Shop/goods_edit', compact('reponse', 'imgs'));
        }
    }

    public function goodsDel()
    {
        $ids = $this->getDelIds();

        DB::table($this->goods->goodsImgDb())->whereIn('goods_id', $ids)->delete();
        $result = DB::table($this->goods->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }


}
