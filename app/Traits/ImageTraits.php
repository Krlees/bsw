<?php

namespace App\Traits;


trait ImageTraits
{
    /**
     * 获取上传后的图片名称
     */
    public function thumbImg($originName, $path)
    {
        $savePath = public_path('Uploads/' . $path . '/' . date('Y-m-d'));
        if (!is_dir($savePath)) {
            \File::makeDirectory($savePath, 0777, true);
        }

        \Image::make(storage_path('uploads/' . $originName))->resize(320, 240)->save($savePath . '/' . $originName);

        return 'Uploads/' . $path . '/' . date('Y-m-d') . '/' . $originName;
//        return file_exists($savePath . '/' . $originName)
//            ? 'Uploads/' . $path . '/' . date('Y-m-d') . '/' . $originName
//            : '';
    }
}