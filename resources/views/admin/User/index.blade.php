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
                {!! $tablePresenter->jsColums('用户名','username') !!}
                {!! $tablePresenter->jsColums('昵称','nickname') !!}
                {!! $tablePresenter->jsColums('所在地址','area_info') !!}
                {!! $tablePresenter->jsColums('添加时间','created_at') !!}
                {!! $tablePresenter->jsColums('用户类型','register_type') !!}
                {!! $tablePresenter->jsColums('加入时间','created_at','true') !!}
            {
                'field': 'status',
                'title': '状态',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var state = row.status == 1 ? 0 : 1;
                    var classStr = row.status == 1 ? 'glyphicon glyphicon-ok ok' : 'glyphicon glyphicon-lock warn';

                    return "<i onclick='dislogConfirm(\"/admin/member/edit/" + row.id + "\",{\"data[status]\":" + state + "})' class='hand " + classStr + "' ></i>";
                }
            },
            {
                'field': 'tx_status',
                'title': '提现限制',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var state = row.tx_status == 1 ? 0 : 1;
                    var classStr = row.tx_status == 1 ? 'glyphicon glyphicon-ok ok' : 'glyphicon glyphicon-lock warn';

                    return "<i onclick='dislogConfirm(\"/admin/member/edit/" + row.id + "\",{\"data[tx_status]\":" + state + "})' class='hand " + classStr + "' ></i>";
                }
            },
            {
                'field': 'id',
                'title': '相册和红包',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var str = '<a onclick="dislog(\'/admin/member/project-img/' + value + '\')" class="picture btn btn-xs btn-outline btn-warning tooltips" href="javascript:void(0)" title="相册">相册 <i class="fa fa-edit"></i></a>　';
                    str += '<a class="btn btn-xs btn-outline btn-danger tooltips" href="javascript:void(0)" title="红包">红包 <i class="fa fa-edit"></i></a>';

                    return str;
                }
            },

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