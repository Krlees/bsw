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
                'field': 'logo',
                'title': 'Logo',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    return '<img width="110px" src="' + value + '">';
                }
            },
                {!! $tablePresenter->jsColums('公司名称','name') !!}
                {!! $tablePresenter->jsColums('地址','address') !!}
                {!! $tablePresenter->jsColums('营业执照','sign_img') !!}
                {!! $tablePresenter->jsColums('状态','status') !!}
                {!! $tablePresenter->jsColums('加入时间','created_at') !!}
            {
                'field': 'user_id',
                'title': '添加商品',
                'align': 'center',
                'sortable': false,
                'formatter': function (value, row, index) {
                    var str = '<a onclick="dislog(\'/admin/shop/goods-add/' + value + '\')" class="picture btn btn-xs btn-outline btn-warning tooltips" href="javascript:void(0)" title="">添加商品 <i class="fa fa-edit"></i></a>　';
                    return str;
                }
            },
            {!! $tablePresenter->jsEvents(['edit']) !!}
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
