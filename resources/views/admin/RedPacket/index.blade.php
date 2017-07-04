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
                {!! $tablePresenter->jsColums('用户名','username') !!}
                {!! $tablePresenter->jsColums('广告图','cover_img') !!}
                {!! $tablePresenter->jsColums('广告链接','url') !!}
                {!! $tablePresenter->jsColums('红包总金额','money') !!}
                {!! $tablePresenter->jsColums('剩余金额','enable_money') !!}
                {!! $tablePresenter->jsColums('红包总人数','sum') !!}
                {!! $tablePresenter->jsColums('剩余人数','enable_sum') !!}
                {!! $tablePresenter->jsColums('添加时间','created_at') !!}
            {
                'field': 'id',
                'title': '操作',
                'align': 'center',
                'events': 'operateEvents',
                'formatter': function (value, row, index) {
                    return '<a class="btn btn-xs btn-outline btn-warning tooltips" onclick="dislog(\'{{url('admin/redpacket/detail')}}/' + value + '\')">查看详情</a>';
                }
            }
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
