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
                $str = <<<EOT
<div id="uploader" class="wu-example">
    <div class="queueList filled">
        <div id="dndArea" class="placeholder element-invisible">
            <div id="filePicker" class="webuploader-container"><div class="webuploader-pick">点击选择图片</div><div id="rt_rt_1bjk1r25vlch189r1j7011lc9s1" style="position: absolute; top: 0px; left: 75.0306px; width: 168px; height: 44px; overflow: hidden; bottom: auto; right: auto;"><input type="file" name="file" class="webuploader-element-invisible" multiple="multiple" accept="image/*"><label style="opacity: 0; width: 100%; height: 100%; display: block; cursor: pointer; background: rgb(255, 255, 255);"></label></div></div>
            <p>或将照片拖到这里，单次最多可选300张</p>
        </div>
    <ul class="filelist" style="display: block;">
        <li id="WU_FILE_1">
            <p class="title">googlelogo_color_272x92dp.png</p>
            <p class="imgWrap">
                <img src="data:image/jpeg;base64,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HQqcVtweM9WhiCFo5MfxMuTWReWVxYT+TcxlHxnBqCtY1KtJtRbRm4QmrtXCiiisTQKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACp7Gyl1C7S2gx5j9MnFQVJbTva3Mc8Zw8bAiqjbmXNsJ3todpp3ga3jw99KZW/uLwK6a1srayj8u2gSJf9lcZqLStQj1PT47mMg7hhh6HvVuvq6FGjCKdNHh1KlSTtNnP+K9DOp2n2iBc3EI6D+IeledkFSQRgjgivZKxr3wrpl9ctcSRsrt12HANceMwLqy56e/U6MPiVBcstjzSiiivnj1QooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKANjw5rj6PeYck28n319PevSYJ47iFZYXDowyCK8erX0PxFc6NJs5ktyfmjPb6V6eCxvsvcnt+Rx4jD8/vR3PTaKpabq9nqsW+2lBbHKHqPwq7X0MZRkrxd0eU04uzPG6KKK+LPoQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigB0U0kEgkidkcdCpwRW3D4x1eGMJ5qPju65NYVFaQq1KfwOxEoRl8SP/Z"><input type="hidden" name="images[]" value="data:image/jpeg;base64,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HQqcVtweM9WhiCFo5MfxMuTWReWVxYT+TcxlHxnBqCtY1KtJtRbRm4QmrtXCiiisTQKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACp7Gyl1C7S2gx5j9MnFQVJbTva3Mc8Zw8bAiqjbmXNsJ3todpp3ga3jw99KZW/uLwK6a1srayj8u2gSJf9lcZqLStQj1PT47mMg7hhh6HvVuvq6FGjCKdNHh1KlSTtNnP+K9DOp2n2iBc3EI6D+IeledkFSQRgjgivZKxr3wrpl9ctcSRsrt12HANceMwLqy56e/U6MPiVBcstjzSiiivnj1QooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKANjw5rj6PeYck28n319PevSYJ47iFZYXDowyCK8erX0PxFc6NJs5ktyfmjPb6V6eCxvsvcnt+Rx4jD8/vR3PTaKpabq9nqsW+2lBbHKHqPwq7X0MZRkrxd0eU04uzPG6KKK+LPoQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigB0U0kEgkidkcdCpwRW3D4x1eGMJ5qPju65NYVFaQq1KfwOxEoRl8SP/Z">
            </p>
            <p class="progress">
                <span></span>
            </p>
            <div class="file-panel">
                <span class="cancel">删除</span>
                <span class="rotateRight">向右旋转</span>
                <span class="rotateLeft">向左旋转</span>
            </div></li></ul></div>
    <div class="statusBar" style="">
        <div class="progress" style="display: none;">
            <span class="text">0%</span>
            <span class="percentage" style="width: 0%;"></span>
        </div>
        <div class="info">选中1张图片，共13.19K。</div>
        <div class="btns">
            <div id="filePicker2" class="webuploader-container"><div class="webuploader-pick">继续添加</div><div id="rt_rt_1bjk1r2671ia112ff1mc1q2d1ep66" style="position: absolute; top: 0px; left: 10px; width: 94px; height: 42px; overflow: hidden; bottom: auto; right: auto;"><input type="file" name="file" class="webuploader-element-invisible" multiple="multiple" accept="image/*"><label style="opacity: 0; width: 100%; height: 100%; display: block; cursor: pointer; background: rgb(255, 255, 255);"></label></div></div>
            <div class="uploadBtn state-ready">开始上传</div>
        </div>
    </div>
</div>
EOT;

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