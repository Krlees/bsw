function getSub(url, id, sub, init=true) {
    var initHtml = "";
    var chird = '#' + sub;
    var chirdChosen = '#' + sub + '_chosen';

    $(chird).chosen("destroy");
    $(chird).empty().chosen({width: "150px"});
    if (id > 0) {
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

}