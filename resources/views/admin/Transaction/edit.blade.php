@inject('imagePresenter','App\Presenters\Admin\ImagePresenter')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$reponse['formTitle']}}</title>
</head>
<body class="gray-bg">

@extends('admin.layout')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>编辑信息</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        {!! Form::open(['url' => array_get($reponse,'formUrl',''),'class'=>'form-horizontal m-t validform']) !!}

                        @component('admin.components.form_base',['formField'=>$reponse['formField']])
                        @endcomponent

                        <div class="control-group">
                            <label class="col-sm-2 control-label">内容图片</label>
                            <div class="col-sm-10">
                                <ul class="image-list" id="image-list">
                                    {!! $imagePresenter->showImg($imgs,'img_thumb') !!}
                                </ul>
                                <div class="upload-image" id="upload-image"></div>
                                <input class="image" id="image" type="file" accept="image/*">
                                <div class="clearfix"></div>
                                <p class="help-block">单击图片可指定封面图片，双击图片可删除，最多可上传1张图片</p>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-paper-plane-o"></i> 提交
                                </button>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('hplus/js/uploadpic.js')}}"></script>
    <script>
        $(".chosen-select").chosen({width: "150px"})
        $("#image-list").delegate("li", "dblclick", function () {
            var id = $(this).attr('data-id');
            $("#del-ids").append("<input type='hidden' name='dels[]' value='" + id + "'>");
            $(this).remove();
        }).delegate("li", "click", function () {
            $(this).addClass("cover").siblings().removeClass("cover");
            $("input[name='cover']").val($(this).index());
        });

        $("#upload-image").click(function () {
            $("#image").click();
        });

        if (typeof UploadPic != 'undefined') {
            var u = new UploadPic();
            u.init({
                maxWidth: 720,
                maxHeight: 720,
                quality: 1,
                input: document.querySelector("#image"),
                before: function () {
                    this.li = $('<li><img src="/hplus/img/loading.gif"><input name="imgs[]" type="hidden"></li>').appendTo("#image-list");
                },
                callback: function (base64) {
                    var _li = this.li;
                    if (base64.substr(22).length > 2097152) {
                        $.noty.closeAll();
                        noty({text: "图片不能大于2M", type: "error"});
                        _li.remove();
                    } else {
                        if ($("#image-list img").length >= 2) {
                            $.noty.closeAll();
                            noty({text: "图片不能超过10个", type: "error"});
                            _li.remove();
                        } else {
                            _li.find("input[name='imgs[]']").val(base64.substr(22)).end().find("img").attr("src", base64);
                        }
                    }
                }
            });


        }
    </script>

@endsection


</body>
</html>
