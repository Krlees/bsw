@inject('tablePresenter','App\Presenters\Admin\TablePresenter')
        <!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>菜单管理</title>
    <script>
        var colums = [
                {!! $tablePresenter->jsCheckbox() !!}
                {!! $tablePresenter->jsColums('ID','id','true') !!}
                {!! $tablePresenter->jsColums('留言人','username') !!}
                {!! $tablePresenter->jsColums('标题信息','title') !!}
                {!! $tablePresenter->jsColums('评论内容','content') !!}
                {!! $tablePresenter->jsColums('评论时间','created_at') !!}
                {!! $tablePresenter->jsEvents(['remove']) !!}
        ];

    </script>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">

    @component('admin.components.table',$reponse)
    @endcomponent
</div>

</body>
</html>
