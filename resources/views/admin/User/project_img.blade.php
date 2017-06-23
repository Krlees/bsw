<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理</title>
    @include('admin.common.css')
    @include('admin.common.js')
</head>
<body class="gray-bg">
<div class="row">
    <div class="col-sm-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            <ul class="notes">
                @foreach($result as $v)
                    <li>
                        <div>
                            <small>{{date('Y-m-d H:i:s',$v->created_at)}}</small>
                            <h4>经营项目图片</h4>
                            <p><img style="width: 140px;" src="{{$v->img_thumb}}"></p>
                            <a href="#"><i class="fa fa-trash-o "></i></a>
                        </div>
                    </li>
                @endforeach

            </ul>
        </div>
    </div>
</div>
</body>
</html>