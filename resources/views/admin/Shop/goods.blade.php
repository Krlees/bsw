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
                {!! $tablePresenter->jsColums('商家id','user_id','true') !!}
            {
                'field': 'cover',
                'title': '封面',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    return '<img width="110px" src="' + value + '">';
                }
            },
                {!! $tablePresenter->jsColums('标题','title') !!}
                {!! $tablePresenter->jsColums('内容','content') !!}
                {!! $tablePresenter->jsColums('点击数','click') !!}
            {
                'field': 'status',
                'title': '状态',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var state = row.status == 1 ? 0 : 1;
                    var classStr = row.status == 1 ? 'glyphicon glyphicon-ok ok' : 'glyphicon glyphicon-lock warn';

                    return "<i onclick='dislogConfirm(\"/admin/shop/goods-edit/" + row.id + "\",{\"data[status]\":" + state + "})' class='hand " + classStr + "' ></i>";
                }
            },
            {!! $tablePresenter->jsColums('加入时间','created_at') !!}
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
