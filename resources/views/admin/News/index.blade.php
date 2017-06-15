@inject('tablePresenter','App\Presenters\Admin\TablePresenter')
@extends('admin.layout')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <!-- Panel Other -->
        <div class="ibox float-e-margins">
            <div class="ibox-content">

                <div class="row row-lg">
                    <div class="col-sm-12">
                        <!-- Example Events -->
                        {{--<div class="alert alert-success" id="examplebtTableEventsResult" role="alert">--}}
                        {{--事件结果--}}
                        {{--</div>--}}
                        <div class="btn-group" id="toolbar" role="group">
                            {!! $tablePresenter->bulidCreateAction(array_get($tables,'addUrl')) !!}
                            {!! $tablePresenter->bulidRemoveAction(array_get($tables,'removeUrl')) !!}
                        </div>
                        <table id="table">
                        </table>
                    </div>
                    <!-- End Example Events -->
                </div>

            </div>
        </div>
    </div>

    <script>
        var colums = [
            {!! $tablePresenter->jsCheckbox() !!}
            {!! $tablePresenter->jsColums('ID','id','true') !!}
        ];
    </script>

    @component('admin.components.table_base',compact('tables'))
    @endcomponent


@endsection

