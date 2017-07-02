@inject('tablePresenter','App\Presenters\Admin\TablePresenter')
        <!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理</title>

    <script>
        var colums = [
                {!! $tablePresenter->jsCheckbox() !!}
                {!! $tablePresenter->jsColums('ID','id','true') !!}
                {!! $tablePresenter->jsColums('类型','type') !!}
                {!! $tablePresenter->jsColums('是否推荐','sort') !!}
                {!! $tablePresenter->jsColums('标题','title') !!}
                {!! $tablePresenter->jsColums('工作项目','fanwei') !!}
                {!! $tablePresenter->jsColums('服务区域','fuwu') !!}
                {!! $tablePresenter->jsColums('配套服务','peitao') !!}
//                {
//                    'field': 'id',
//                    'title': '审核',
//                    'align': 'center',
//                    'sortable': false,
//                    'formatter': function (value, row, index) {
//                        var str = '<a onclick="dislog(\'/admin/member/project-img/' + value + '\')" class="picture btn btn-xs btn-outline btn-warning tooltips" href="javascript:void(0)" title="相册">相册 <i class="fa fa-edit"></i></a>　';
//                        str += '<a class="btn btn-xs btn-outline btn-danger tooltips" href="javascript:void(0)" title="红包">红包 <i class="fa fa-edit"></i></a>';
//
//                        return str;
//                    }
//                },
                {!! $tablePresenter->jsColums('加入时间','addtime','true') !!}
            {!! $tablePresenter->jsEvents() !!}
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