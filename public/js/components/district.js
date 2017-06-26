$(function () {

    var cityEle = $("#city");
    var areaEle = $("#area");
    if (!cityEle.val()) {
        $('#city_chosen').hide();
    }
    if (!areaEle.val()) {
        $('#area_chosen').hide();
    }

    getSubDistrict('/components/get-district', 0, 'province', false);
    $(document).on('change', '#province', function () {
        var Value = $(this).val();
        //$('#area_chosen').chosen("destroy").empty().chosen({width: "150px"});

        getSubDistrict('/components/get-district', $(this).val(), 'city', false);
    });
    $(document).on('change', '#city', function () {
        var Value = $(this).val();

        getSubDistrict('/components/get-district', $(this).val(), 'area', false);
    });

});

function getSubDistrict(url, id, sub, init=true) {
    var initHtml = "";
    var chird = '#' + sub;
    var chirdChosen = '#' + sub + '_chosen';

    $(chird).chosen("destroy");
    $(chird).empty().chosen({width: "150px"});

    $.getJSON(url + "/" + id, {}, function (result) {
        if(init)
            initHtml = "<option value='" + id + "'>-请选择-</option>";

        var selectHtmls = "";
        if (result) {
            $.each(result, function (i, v) {
                selectHtmls += "<option value='" + v.id + "'>" + v.name + "</option>";
            });
        }

        if (selectHtmls != "") {
            $(chird).chosen("destroy");
            $(chird).html(initHtml + selectHtmls).chosen({width: "150px"});
            $(chirdChosen).show();
        }
        else {
            $(chirdChosen).hide();
        }

    });

}
