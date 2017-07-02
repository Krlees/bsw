@inject('tablePresenter','App\Presenters\Admin\TablePresenter')
        <!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订单管理</title>
    <script>
        var colums = [
            {!! $tablePresenter->jsCheckbox() !!}
            {!! $tablePresenter->jsColums('ID','id','true') !!}
            {!! $tablePresenter->jsColums('订单号','order_sn') !!}
            {!! $tablePresenter->jsColums('价格','price') !!}
            {!! $tablePresenter->jsColums('用户','username') !!}
            {!! $tablePresenter->jsColums('支付时间','pay_time') !!}
            {!! $tablePresenter->jsColums('支付方式','pay_Type') !!}
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
