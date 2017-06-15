<?php
// +----------------------------------------------------------------------
// | BaseController: 基础控制器
// +----------------------------------------------------------------------
// | Author: yangyifan <yangyifanphp@gmail.com>
// +----------------------------------------------------------------------


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Traits\Admin\FormTraits;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Auth;

class BaseController extends Controller
{

    protected $user_ses; //存储的用户信息

    public function __construct()
    {
        $this->middleware('api.token') or $this->responseApi(1000);
        $this->user_ses = cache('user_ses');
    }

    /**
     * 统一回调
     *
     * @param $code     状态码
     * @param $msg      提示文字
     * @param $data     数据
     * @prams $href     跳转的网址
     * @author krlee <lkd0769@126.com>
     */
    public function responseApi($code = 0, $msg = '', $data = [])
    {

        if (!$msg) {
            $msg = custom_config($code);
        }

        echo json_encode(compact('code', 'msg', 'data', 'href'));
        exit;
    }

    /**
     * 检测必填参数
     * @Author Krlee
     *
     * @param array $arr
     * @param array $fill 排除检测字段
     * @return bool
     */
    public function checkRequireParams($arr, $fill = [])
    {
        if (!is_array($arr)) {
            return false;
        }

        if ($fill) {
            foreach ($fill as $v) {
                unset($arr[$v]);
            }
        }

        foreach ($arr as $k => $v) {
            if ($v !== 0 && empty($v))
                return false;
        }

        return true;

    }


}