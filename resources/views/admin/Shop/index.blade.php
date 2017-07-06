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
            {!! $tablePresenter->jsColums('logo','logo') !!}
            {!! $tablePresenter->jsColums('公司名称','name') !!}
            {!! $tablePresenter->jsColums('地址','address') !!}
            {!! $tablePresenter->jsColums('营业执照','sign_img') !!}
            {!! $tablePresenter->jsColums('状态','status') !!}
            {!! $tablePresenter->jsColums('加入时间','created_at') !!}
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
