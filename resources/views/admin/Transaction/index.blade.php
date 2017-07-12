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
                {!! $tablePresenter->jsColums('发布人','nickname') !!}
                {!! $tablePresenter->jsColums('发布位置','send_type') !!}
                {!! $tablePresenter->jsColums('标题','title') !!}
                {!! $tablePresenter->jsColums('发布时间','created_at','true') !!}
            {
                'field': 'days',
                'title': '有效期',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    return value + '天';
                }
            },
            {
                'field': 'is_must_pay',
                'title': '是否必须付费',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var state = value == 1 ? 0 : 1;
                    var classStr = value == 1 ? 'glyphicon glyphicon-ok ok' : 'glyphicon glyphicon-lock warn';

                    return "<i onclick='dislogConfirm(\"/admin/transaction/edit/" + row.id + "\",{\"data[is_must_pay]\":" + state + "})' class='hand " + classStr + "' ></i>";
                }
            },
            {
                'field': 'is_normal_pay',
                'title': '是否正常收费',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var state = value == 1 ? 0 : 1;
                    var classStr = value == 1 ? 'glyphicon glyphicon-ok ok' : 'glyphicon glyphicon-lock warn';

                    return "<i onclick='dislogConfirm(\"/admin/transaction/edit/" + row.id + "\",{\"data[is_normal_pay]\":" + state + "})' class='hand " + classStr + "' ></i>";
                }
            },
            {
                'field': 'is_juan_pay',
                'title': '是否必须劵支付',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var state = value == 1 ? 0 : 1;
                    var classStr = value == 1 ? 'glyphicon glyphicon-ok ok' : 'glyphicon glyphicon-lock warn';

                    return "<i onclick='dislogConfirm(\"/admin/transaction/edit/" + row.id + "\",{\"data[is_juan_pay]\":" + state + "})' class='hand " + classStr + "' ></i>";
                }
            },
            {
                'field': 'is_wallet_pay',
                'title': '是否必须余额支付',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var state = value == 1 ? 0 : 1;
                    var classStr = value == 1 ? 'glyphicon glyphicon-ok ok' : 'glyphicon glyphicon-lock warn';

                    return "<i onclick='dislogConfirm(\"/admin/transaction/edit/" + row.id + "\",{\"data[is_wallet_pay]\":" + state + "})' class='hand " + classStr + "' ></i>";
                }
            },
            {
                'field': 'is_show',
                'title': '是否显示',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var state = value == 1 ? 0 : 1;
                    var classStr = value == 1 ? 'glyphicon glyphicon-ok ok' : 'glyphicon glyphicon-lock warn';

                    return "<i onclick='dislogConfirm(\"/admin/transaction/edit/" + row.id + "\",{\"data[is_show]\":" + state + "})' class='hand " + classStr + "' ></i>";
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
