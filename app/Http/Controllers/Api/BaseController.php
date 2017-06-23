<?php
// +----------------------------------------------------------------------
// | BaseController: 基础控制器
// +----------------------------------------------------------------------
// | Author: yangyifan <yangyifanphp@gmail.com>
// +----------------------------------------------------------------------


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserToken;
use App\Traits\Admin\FormTraits;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Auth;

class BaseController extends Controller
{

    protected $user_ses = []; //存储的用户信息

    public function __construct()
    {
        if (request()->has('token')) {
            $this->user_ses = cache(request()->input('token'));
        }
    }

    public function pageInit()
    {
        $page = request('page', 0);
        $limit = request('limit', 20);

        return compact('page', 'limit');
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

        echo response()->json(compact('code', 'msg', 'data', 'href'));
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