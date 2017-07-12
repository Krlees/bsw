<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Channel;
use App\Models\Label;
use App\Models\Member;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\UserCase;
use App\Traits\ImageTraits;
use Illuminate\Http\Request;
use DB;

class TransactionController extends BaseController
{
    use ImageTraits;

    private $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function index($channel_id, Request $request, Member $member)
    {
        if ($request->ajax()) {

            $sendTypeArr = ['前端普通用户', '服务商后台', '总后台'];
            $where[] = ['channel_id', '=', $channel_id];

            if ($request->has('channel_type')) {
                $where[] = ['channel_type', '=', '求购'];
            }

            // 过滤参数
            $param = $this->cleanAjaxPageParam();
            $result = $this->transaction->ajaxData($this->transaction->getTable(), $param, $where, 'title');
            foreach ($result['rows'] as &$v) {
                $username = DB::table($member->getTable())->find($v['user_id'], ['username']);

                $v['username'] = $username ? $username->username : '';
                $v['send_type'] = $sendTypeArr[$v['send_type']];
                $v['created_at'] = date('Y-m-d H:i:s', $v['created_at']);

            }

            return $this->responseAjaxTable($result['total'], $result['rows']);

        } else {
            $reponse = $this->responseTable(url('admin/transaction/index/' . $channel_id), [
                'addUrl' => null,
                'editUrl' => url('admin/transaction/edit'),
                'removeUrl' => url('admin/transaction/del'),
                'autoSearch' => true
            ]);

            return view('admin/Transaction/index', compact('reponse'));
        }

    }

    public function add($userId, Request $request, Channel $channel, Label $label)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $data['user_id'] = $userId;
            $data['logo'] = $this->thumbImg($request->input(['imgs'])[0], 'Head');
            $data['cphoto'] = $this->thumbImg($request->input(['imgs2'])[0], 'Head');

            $b = $this->transaction->createData($this->transaction->getTable(), $data);
            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $channelData = $channel->getAll($channel->getTable());
            $labelData = $label->getAll($label->getTable());

            $this->createField('select', '频道', 'data[channel_id]', $this->cleanSelect($channelData));
            $this->createField('text', '频道类型', 'data[channel_type]', '', ['placeholder' => '请填写频道类型，求购,出售,招聘 ,维修,设计等等，可为空']);
            $this->createField('select', '标签', 'data[label_id]', $this->cleanSelect($labelData));
            $this->createField('text', '标题', 'data[title]');
            $this->createField('textarea', '内容', 'data[content]');
            $this->createField('text', '标题', 'data[title]');
            $this->createField('text', '电话', 'data[tel]');
            $this->createField('radio', '是否显示', 'data[is_show]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => 'true'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                ]
            ]);
            $this->createField('radio', '是否必须付费', 'data[is_must_pay]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => 'true'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                ]
            ]);
            $this->createField('radio', '是否正常收费', 'data[is_normal_pay]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => 'true'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                ]
            ]);
            $this->createField('radio', '是否必须劵购买', 'data[is_juan_pay]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => 'true'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                ]
            ]);
            $this->createField('radio', '是否必须余额购买', 'data[is_wallet_pay]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => 'true'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                ]
            ]);

            $reponse = $this->responseForm('添加信息', $this->formField);
            return view('admin/transaction/add/' . $userId, compact('reponse'));
        }
    }

    public function edit($id, Request $request, Channel $channel, Label $label)
    {
        if ($request->ajax()) {
            $data = $request->input('data');
            $imgs = $request->input(['imgs']);


            DB::beginTransaction();
            $id = $this->transaction->createData($this->transaction->getTable(), $data);
            if (!$id) {
                DB::rollBack();
                $this->responseApi(9000);
            }

//            foreach ($imgs as $k=>$img ){
//                $img = $this->thumbImg($img,'transaction');
//                $imgData = [
//                    'transaction_id' => $id,
//                    'img_thumb' => $img,
//                    'is_cover' =>
//                ];
//            }
            $this->transaction->createData($this->transaction->transactionImg(),[

            ]);

            return $b ? $this->responseApi(0) : $this->responseApi(9000);

        } else {
            $info = $this->transaction->get($id, ['*']);
            $imgs = $this->transaction->getAll($this->transaction->transactionImg(), [['transaction_id', '=', $id]]);

            $channelData = $channel->getAll($channel->getTable());
            $labelData = $label->getAll($label->getTable());

            $this->createField('select', '频道', 'data[channel_id]', $this->cleanSelect($channelData, 'name', 'id', $info['channel_id']));
            $this->createField('text', '频道类型', 'data[channel_type]', '', ['placeholder' => '请填写频道类型，求购,出售,招聘 ,维修,设计等等，可为空']);
            $this->createField('select', '标签', 'data[label_id]', $this->cleanSelect($labelData, 'name', 'id', $info['label_id']));
            $this->createField('text', '标题', 'data[title]', $info['title']);
            $this->createField('textarea', '内容', 'data[content]', $info['content']);
            $this->createField('text', '标题', 'data[title]', $info['title']);
            $this->createField('text', '电话', 'data[tel]', $info['tel']);
            $this->createField('radio', '是否显示', 'data[is_show]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => $info['is_show'] == 1 ? 'true' : 'false'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                    'checked' => $info['is_show'] == 0 ? 'true' : 'false'
                ]
            ]);
            $this->createField('radio', '是否必须付费', 'data[is_must_pay]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => $info['is_must_pay'] == 1 ? 'true' : 'false'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                    'checked' => $info['is_must_pay'] == 0 ? 'true' : 'false'
                ]
            ]);
            $this->createField('radio', '是否正常收费', 'data[is_normal_pay]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => $info['is_normal_pay'] == 1 ? 'true' : 'false'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                    'checked' => $info['is_normal_pay'] == 0 ? 'true' : 'false'
                ]
            ]);
            $this->createField('radio', '是否必须劵购买', 'data[is_juan_pay]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => $info['is_juan_pay'] == 1 ? 'true' : 'false'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                    'checked' => $info['is_juan_pay'] == 0 ? 'true' : 'false'
                ]
            ]);
            $this->createField('radio', '是否必须余额购买', 'data[is_wallet_pay]', [
                [
                    'text' => '是',
                    'value' => 1,
                    'checked' => $info['is_wallet_pay'] == 1 ? 'true' : 'false'
                ],
                [
                    'text' => '否',
                    'value' => 0,
                    'checked' => $info['is_wallet_pay'] == 0 ? 'true' : 'false'
                ]
            ]);

            $reponse = $this->responseForm('编辑信息', $this->formField);
            return view('admin/Transaction/edit', compact('reponse', 'info', 'imgs'));
        }

    }

    public function del()
    {
        $ids = $this->getDelIds();

        DB::table($this->transaction->transactionImg())->whereIn('transaction_id', $ids)->delete();
        $result = DB::table($this->transaction->getTable())->whereIn('id', $ids)->delete();
        $result ? $this->responseApi(0) : $this->responseApi(9000);
    }


}
