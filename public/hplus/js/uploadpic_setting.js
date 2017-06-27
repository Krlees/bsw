$("#image-list").delegate("li", "dblclick", function() {
    $(this).remove();
}).delegate("li", "click", function() {
    $(this).addClass("cover").siblings().removeClass("cover");
    $("input[name='cover']").val($(this).index());
});

$("#upload-image").click(function() {
    $("#image").click();
});
if(typeof UploadPic != 'undefined') {
    var u = new UploadPic();
    u.init({
        maxWidth: 720,
        maxHeight: 720,
        quality: 1,
        input: document.querySelector("#image"),
        before: function() {
            this.li = $('<li><img src="/img/loading.gif"><input name="image[]" type="hidden"></li>').appendTo("#image-list");
        },
        callback: function (base64) {
            var _li = this.li;
            if(base64.substr(22).length > 2097152) {
                $.noty.closeAll();
                noty({ text: "图片不能大于2M", type: "error" });
                _li.remove();
            } else {
                if($("#image-list img").length >= 10) {
                    $.noty.closeAll();
                    noty({ text: "图片不能超过10个", type: "error" });
                    _li.remove();
                } else {
                    _li.find("input[name='image[]']").val(base64.substr(22)).end().find("img").attr("src", base64);
                }
            }
        }
    });
}