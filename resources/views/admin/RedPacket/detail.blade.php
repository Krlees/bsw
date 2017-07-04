@include('admin.common.css')

<div class="modal-body">
    <form class="form-horizontal">
        <div class="form-group">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="col-md-2 text-center">头像</th>
                                <th class="col-md-10 text-center">用户名</th>
                                <th class="col-md-10 text-center">抢到金额</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($result['rows'] as $v)
                                <tr>
                                    <td>
                                        <div class="col-md-4">
                                            <label> <img style="width: 110px;" src="{{$v['avatar']}}"> </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-md-4">
                                            <label> {{$v['username']}} </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-md-4">
                                            <label> {{$v['money']}} </label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
