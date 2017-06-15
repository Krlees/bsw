<?php
// +----------------------------------------------------------------------
// | BaseController: 基础控制器
// +----------------------------------------------------------------------
// | Author: yangyifan <yangyifanphp@gmail.com>
// +----------------------------------------------------------------------


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Traits\Admin\FormTraits;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Auth;

class BaseController extends Controller
{
    use FormTraits;

    protected $users;

    /**
     * 构造方法
     *
     * @author krlee <lkd0769@126.com>
     */
    public function __construct()
    {
        //设置错误级别
        $this->setErrorLevel();

        $this->users = Auth::user();
    }

    /**
     * 设置错误级别
     *
     * @author krlee <lkd0769@126.com>
     */
    private function setErrorLevel()
    {
        //如果不是debug模式，则关闭waring
        if (env('APP_DEBUG', false) == true) {
            error_reporting(E_ALL ^ E_NOTICE);
        } else {
            error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
        }
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
    public function responseData($code = 0, $msg = '', $data = [], $href = '')
    {

        if (!$msg) {
            $msg = custom_config($code);
        }

        echo json_encode(compact('code', 'msg', 'data', 'href'));
        exit;
    }


    /**
     * 返回bootstrap-table需要的数据格式
     *
     * @param $total  总条数
     * @param $rows
     * @return array
     */
    public function responseAjaxTable($total, $rows)
    {
        return json_encode(compact('total', 'rows'));
    }

    /**
     * 过滤并初始化好参数,只使用于bootstrap-table的表格组件
     *
     * @param $param
     */
    public function cleanAjaxTableParam($param)
    {
        $param['offset'] = array_get($param, 'offset', 0);
        $param['offset'] = array_get($param, 'limit', 10);
        $param['sort'] = array_get($param, 'sort', false);
        $param['order'] = array_get($param, 'order', 'desc');

        return $param;
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