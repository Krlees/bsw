<link href="{{asset('hplus/css/plugins/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('hplus/css/plugins/bootstrap-table/bootstrap-editable.css')}}" rel="stylesheet">
<style>
    #searchFrom select, input {
        width: 100%;
    }

    .fixed-table-pagination {
        padding: 0 12px;
    }
    .fixed-table-toolbar .columns{
        margin-top: 0;
    }
</style>



<!-- End Panel Other -->
<script src="{{asset('hplus/js/plugins/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('hplus/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js')}}"></script>
<script src="{{asset('hplus/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js')}}"></script>
<script src="{{asset('hplus/js/plugins/bootstrap-table/bootstrap-table-export.js')}}"></script>
<script src="{{asset('hplus/js/plugins/bootstrap-table/bootstrap-table-editable.js')}}"></script>
<script src="{{asset('hplus/js/plugins/bootstrap-table/bootstrap-editable.js')}}"></script>
<script src="{{asset('hplus/js/plugins/bootstrap-table/jquery.plugins.export.js')}}"></script>

<script>

    var $table = $('#table'),
        $remove = $('#remove'),
        selections = [], // 默认选中项
        uniqueId = 'id'; // 主键key

    $(function () {
        initTable();
        $('#searchRefresh').click(function () {
            $table.bootstrapTable('refresh');
        });

        $("#ok").click(function () {
            $table.bootstrapTable('refresh');
        });
    });

    /* 获取表单检索参数查询 */
    function getParamSearch() {

        var paramObj = {};

        /* 获取表单检索元素 */
        var Inputs = $('#searchFrom input');
        var Selects = $('#searchFrom select');
        var element_name;
        var element_value;

        Inputs.each(function (i, v) {
            element_name = v.getAttribute('name');
            element_value = v.value;

            paramObj[element_name] = element_value;
        });

        Selects.each(function (i, v) {
            element_name = v.getAttribute('name');
            element_value = v.value;

            paramObj[element_name] = element_value;
        });

        return paramObj;
    }


    /* 初始化表格 */
    function initTable() {
        $table.bootstrapTable({
            height: getHeight(),
            url: "{{array_get($tables,'actionUrl','')}}",
            toolbar: "#toolbar",
            showColumns: true,
            pagination: true,
            showRefresh: true,
            showToggle: true,
            showExport: true,
            detailView: true,
            detailFormatter: "detailFormatter",
            cellStyle: true,
            striped: true,
            cache: false,
            search: '{{array_get($tables,'search',false)}}',
            sortOrder: "asc",
            uniqueId: uniqueId, // 设置主键
            pageList: [10, 25, 50],
            sidePagination: "server",
            responseHandler: "responseHandler",
            columns: colums,
            queryParams: function (params) {   //设置查询参数
                var paramForm = getParamSearch();
                return $.extend({}, params, paramForm); // 合并参数
            },
        });

        setTimeout(function () {
            $table.bootstrapTable('resetView');
        }, 200);

        /* checkbox选择事件，包括单选，全选 */
        $table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function () {

            // 木有选择一项时
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);

            // save your data, here just save the current page
            selections = getIdSelections();
            // push or splice the selections if you want to save all data selections
        });

        /* 当点击详细图标展开详细页面的时候触发。 */
//        $table.on('expand-row.bs.table', function (e, index, row, $detail) {});

        /* 删除事件 */
        $remove.click(function () {
            var ids = getIdSelections();
            $.getJSON("{{array_get($tables,'removeUrl','')}}", {ids: ids.join(',')}, function (result) {
                if (result.code == '0') {
                    $table.bootstrapTable('remove', {
                        field: uniqueId,
                        values: ids
                    });
                    $remove.prop('disabled', true);
                }
                else {
                    layer.msg("操作失败");
                }
            });

        });

        $(window).resize(function () {
            $table.bootstrapTable('resetView', {
                height: getHeight()
            });
        });


    }

    /* 获取选择的唯一ID */
    function getIdSelections() {
        return $.map($table.bootstrapTable('getSelections'), function (row) {
            return row[uniqueId]
        });

    }

    function responseHandler(res) {
        $.each(res.rows, function (i, row) {
            row.state = $.inArray(row.id, selections) !== -1;
        });
        return res;
    }

    /**
     * 操作项
     * @param row   行数据
     * @param show  需要展示的操作项['edit','remove']
     * @returns {string}
     */
    function operateFormatter(row, show) {
        var checks = [];
        var strs = "";
        if ((typeof show == 'object') && show.constructor == Array) {
            checks = show;
        }
        else {
            checks[0] = show;
        }

        var lengths = checks.length;
        for (var i = 0; i < lengths; i++) {
            switch (checks[i]) {
                case 'edit':
                    strs += '<a class="edit btn btn-xs btn-outline btn-warning tooltips" href="javascript:void(0)" title="编辑"><i class="fa fa-edit"></i></a>　';
                    break;
                case 'remove':
                    strs += '<a class="remove btn btn-xs btn-outline btn-danger tooltips destroy_item" href="javascript:void(0)" title="删除"> <i class="fa fa-trash"></i></a>　';
                    break;
            }
        }

        return strs;

    }

    /* 操作选项 */
    window.operateEvents = {
        'click .edit': function (e, value, row, index) {
            window.location.href = "{{array_get($tables,'editUrl','')}}/" + row[uniqueId];
//            alert('You click like action, row: ' + JSON.stringify(row));
        },
        'click .remove': function (e, value, row, index) {
            $.getJSON("{{array_get($tables,'removeUrl')}}", {ids: row[uniqueId]}, function (result) {
                if (result.code == '0') {
                    $table.bootstrapTable('removeByUniqueId', row[uniqueId]);   // 根据uniqueId删除指定行
                }
                else {
                    layer.msg("操作失败");
                }
            });

        }
    };

    /* 点击+号展开详细内容 */
    function detailFormatter(index, row) {
        var html = [];
        $.each(row, function (key, value) {
            html.push('<p><b>' + key + ':</b> ' + value + '</p>');
        });
        return html.join('');
    }

    /* 表格高度 */
    function getHeight() {
        return '';
    }


</script>
