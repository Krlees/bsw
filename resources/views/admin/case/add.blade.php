<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$reponse['formTitle']}}</title>
</head>
<body class="gray-bg">
@component('admin.components.form',$reponse)
@endcomponent

<div class="control-group">
    <label class="control-label"><span style="color:#f00">*</span> 商品图片</label>
    <div class="controls">
        <ul id="image-list"></ul>
        <div id="upload-image"></div>
        <input id="image" type="file" accept="image/*">
        <div class="clearfix"></div>
        <p class="help-block">单击图片可指定封面图片，双击图片可删除，最多可上传10张图片</p>
    </div>
</div>
</body>
</html>
