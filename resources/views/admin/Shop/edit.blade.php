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
                        <h5>编辑商户</h5>
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

                        <div class="form-group">
                            <label for="省市区" class="col-sm-2 control-label">省市</label>
                            <div class="col-sm-10">
                                <select class="chosen-select areas" id="province" name="data[province]">
                                    <option value="0">请选择</option>
                                    @foreach($provinces as $k=>$v)
                                        <option value="{{$v->id}}">{{$v->name}}</option>
                                    @endforeach
                                </select>
                                <select class="chosen-select areas" id="city" name="data[city]">
                                    <option value="0">请选择</option>
                                    <option value="{{$info->city_id}}" selected>{{$info->city}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="col-sm-2 control-label">logo</label>
                            <div class="col-sm-10">
                                <ul class="image-list" id="image-list">
                                    <li><img src="{{$info->logo}}" alt=""></li>
                                </ul>
                                <div class="upload-image" id="upload-image"></div>
                                <input class="image" id="image" type="file" accept="image/*">
                                <div class="clearfix"></div>
                                <p class="help-block">单击图片可指定封面图片，双击图片可删除，最多可上传1张图片</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="col-sm-2 control-label">营业执照</label>
                            <div class="col-sm-10">
                                <ul class="image-list" id="image-list2">
                                    <li><img src="{{$info->sign_img}}" alt=""></li>
                                </ul>
                                <div class="upload-image" id="upload-image2"></div>
                                <input class="image" id="image2" type="file" accept="image/*">
                                <div class="clearfix"></div>
                                <p class="help-block">单击图片可指定封面图片，双击图片可删除，最多可上传4张图片</p>
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
        $("#province").val("{{$info->province_id}}");
        $(".chosen-select").chosen({width: "150px"})
        $("#province").change(function () {
            getSub('/components/get-district', $(this).val(), 'city');
        })
        $("#image-list").delegate("li", "dblclick", function () {
            $(this).remove();
        }).delegate("li", "click", function () {
            $(this).addClass("cover").siblings().removeClass("cover");
            $("input[name='cover']").val($(this).index());
        });

        $("#upload-image").click(function () {
            $("#image").click();
        });
        $("#image-list2").delegate("li", "dblclick", function () {
            $(this).remove();
        }).delegate("li", "click", function () {
            $(this).addClass("cover").siblings().removeClass("cover");
            $("input[name='cover']").val($(this).index());
        });

        $("#upload-image2").click(function () {
            $("#image2").click();
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
                        if ($("#image-list img").length >= 1) {
                            $.noty.closeAll();
                            noty({text: "图片不能超过10个", type: "error"});
                            _li.remove();
                        } else {
                            _li.find("input[name='imgs[]']").val(base64.substr(22)).end().find("img").attr("src", base64);
                        }
                    }
                }
            });

            var u2 = new UploadPic();
            u2.init({
                maxWidth: 720,
                maxHeight: 720,
                quality: 1,
                input: document.querySelector("#image2"),
                before: function () {
                    this.li = $('<li><img src="/hplus/img/loading.gif"><input name="imgs2[]" type="hidden"></li>').appendTo("#image-list2");
                },
                callback: function (base64) {
                    var _li = this.li;
                    if (base64.substr(22).length > 2097152) {
                        $.noty.closeAll();
                        noty({text: "图片不能大于2M", type: "error"});
                        _li.remove();
                    } else {
                        if ($("#image-list2 img").length >= 1) {
                            $.noty.closeAll();
                            noty({text: "图片不能超过10个", type: "error"});
                            _li.remove();
                        } else {
                            _li.find("input[name='imgs2[]']").val(base64.substr(22)).end().find("img").attr("src", base64);
                        }
                    }
                }
            });
        }
    </script>

@endsection


</body>
</html>
