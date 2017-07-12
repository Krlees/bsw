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
                        <h5>添加用户</h5>
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
                                <select class="chosen-select areas" id="province" name="province">
                                    <option value="0">请选择</option>
                                    @foreach($provinces as $k=>$v)
                                        <option value="{{$v->id}}">{{$v->name}}</option>
                                    @endforeach
                                </select>
                                <select class="chosen-select areas" id="city" name="city">
                                    <option value="0">请选择</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">头像</label>
                            <div class="col-sm-10">
                                <img style="width: 110px" class="upload-avatar" id="upload-avatar" src="{{asset('hplus/img/user.png')}}">
                                <input id="avatar" type="file" accept="image/*">
                                <input name="avatar" type="hidden">
                                <div class="clearfix"></div>
                            </div>
                        </div>


                        <div class="hr-line-dashed"></div>
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
        $("#province").change(function () {
            getSub('/components/get-district', $(this).val(), 'city');
        })

        if (typeof UploadPic != 'undefined') {
            var avatar = new UploadPic();
            avatar.init({
                maxWidth: 480,
                maxHeight: 480,
                quality: 0.9,
                input: document.querySelector("#avatar"),
                callback: function (base64) {
                    if (base64.substr(22).length > 2097152) {
                        noty({text: "图片不能大于2M", type: "error"});
                    } else {
                        $("#upload-avatar").attr("src", base64).css('width', '110px');
                        $("input[name='avatar']").val(base64.substr(22));
                    }
                }
            });
        }
    </script>

@endsection


</body>
</html>
