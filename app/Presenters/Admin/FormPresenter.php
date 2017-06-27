<?php
namespace App\Presenters\Admin;

use Collective\Html\FormBuilder;
use Form;

class FormPresenter
{
    /**
     * 生成表单html
     *
     * @param $type
     * @param $name
     * @param null $value
     * @param array $options
     */
    public function bulidFieldHtml($type, $name, $value = null, $options = [])
    {
        // 默认样式
        $opt = ['class' => 'form-control'];
        switch ($type) {

            case 'checkbox':
                $opt = ['class' => 'form-control checkbox-inline', 'style' => 'width: 20px'];
                $options = array_merge($options, $opt);
                if (!is_array($value) || empty($value)) {
                    return "error: 数据为空,请检查";
                }

                $htmls = '';
                foreach ($value as $k => $v) {
                    $htmls .= '<label class="checkbox-inline">' . Form::checkbox($name, $v['value'], isset($v['checked']) ? $v['checked'] : false, $options) . $v['text'] . '</label>';
                }

                return $htmls;
                break;

            case 'radio':
                $opt = ['class' => 'form-control radio-inline', 'style' => 'width: 20px'];
                $options = array_merge($options, $opt);
                if (!is_array($value) || empty($value)) {
                    return "error: 数据为空,请检查";
                }

                $htmls = '';
                foreach ($value as $k => $v) {
                    $htmls .= '<label class="radio-inline">' . Form::radio($name, $v['value'], isset($v['checked']) ? $v['checked'] : false, $options) . $v['text'] . '</label>';
                }

                return $htmls;
                break;

            case 'select':

                if (!is_array($value)) {
                    return "error: 数据错误,请检查";
                }

                // 插入最后, 利用krsort排序排到第一
                $value[] = [
                    'text' => '-请选择-',
                    'value' => 0
                ];
                krsort($value);

                $opt = ['class' => 'chosen-select'];
                $options = array_merge($options, $opt);

                $checked = false;
                foreach ($value as $k => $v) {
                    $list[$v['value']] = $v['text'];
                    if (isset($v['checked']) && $v['checked'] == true) {
                        $checked = $v['value'];
                    }
                }

                return Form::select($name, $list, $checked, $options);
                break;

            case 'textarea':
                $options = array_merge($options, $opt);
                return Form::textarea($name, $value, $options);
                break;

            case 'password':
                $options = array_merge($options, $opt);
                return Form::password($name, $options);
                break;

            case 'area':
                $options = array_merge($options, $opt);
                return <<<EOT
<select class="chosen-select areas" id="province" name="province">
    <option value="0">请选择</option>
</select>
<select class="chosen-select areas" id="city" name="city">
    <option value="0">请选择</option>
</select>
<!--<select class="chosen-select areas" id="area" name="area">-->
    <!--<option value="0">请选择</option>-->
<!--</select>-->
EOT;
                break;

            case 'image':
                $str = '
<div id="uploader" class="wu-example">
        <div class="queueList filled">
            <div id="dndArea" class="placeholder element-invisible">
                <div id="filePicker" class="webuploader-container"><div class="webuploader-pick">点击选择图片</div><div id="rt_rt_1bjk1r25vlch189r1j7011lc9s1" style="position: absolute; top: 0px; left: 75.0306px; width: 168px; height: 44px; overflow: hidden; bottom: auto; right: auto;"><input type="file" name="file" class="webuploader-element-invisible" multiple="multiple" accept="image/*"><label style="opacity: 0; width: 100%; height: 100%; display: block; cursor: pointer; background: rgb(255, 255, 255);"></label></div></div>
                <p>或将照片拖到这里，单次最多可选300张</p>
            </div>
        <ul class="filelist" style="display: block;">
            <li id="WU_FILE_1">';

                if($value){
                    $str .= '<p class="imgWrap">';
                    $str .= '<img src="/Uploads/transaction/2017-06-26/20170626-5950daeeb7839.png">';
                    $str .= '</p>';
                }
                else {
                    $str .= '<p class="imgWrap">图片</p>';
                }


                $str .= '<p class="progress"><span></span></p><div class="file-panel" style="height: 0px;"><span class="cancel">删除</span><span class="rotateRight">向右旋转</span><span class="rotateLeft">向左旋转</span></div></li></ul></div>
        <div class="statusBar" style="">
            <div class="btns">
                <div id="filePicker2" class="webuploader-container"><div class="webuploader-pick">继续添加</div><div id="rt_rt_1bjk1r2671ia112ff1mc1q2d1ep66" style="position: absolute; top: 0px; left: 10px; width: 94px; height: 42px; overflow: hidden; bottom: auto; right: auto;"><input type="file" name="file" class="webuploader-element-invisible" multiple="multiple" accept="image/*"><label style="opacity: 0; width: 100%; height: 100%; display: block; cursor: pointer; background: rgb(255, 255, 255);"></label></div></div>
                <div class="uploadBtn state-ready">开始上传</div>
            </div>
        </div>
    </div>';

                $str .= '<link rel="stylesheet" type="text/css" href="' . asset('hplus/js/plugins/webuploader/webuploader.css') . '"/>';
                $str .= '<link rel="stylesheet" type="text/css" href="' . asset('hplus/css/plugins/webuploader/webuploader.css') . '"/>';
                $str .= '<script src="' . asset('hplus/js/plugins/webuploader/webuploader.min.js') . '"></script>';
                $str .= '<script src="' . asset('hplus/js/uploads.setting.js') . '"></script>';

                return $str;
                break;

            default:
                if (in_array($type, ['text', 'date', 'datetime', 'url', 'tel', 'number', 'hidden', 'email', 'datetimeLocal', 'color'])) {
                    $options = array_merge($options, $opt);

                    return Form::$type($name, $value, $options);
                }
                return;

        }

    }


}