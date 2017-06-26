<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\DistrictTraits;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Storage;

class ComponentsController extends Controller
{

    use DistrictTraits;

    /**
     * 图片上传
     *
     * @author: <krlee>
     * @param Request $request
     * @return int
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');

        // 文件是否上传成功
        if (!$file->isValid()) {
            return 0;
        }

        // 获取文件相关信息
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $type = $file->getClientMimeType();     // image/jpeg

        // 上传文件
        $filename = date('Ymd') . '-' . uniqid() . '.' . $ext;
        $bool = Storage::disk('local')->put($filename, file_get_contents($realPath));
        if (!$bool)
            return 0;

//        if (!is_dir(storage_path('uploads/s')) || !is_dir(storage_path('uploads/l'))) {
//            mkdir(storage_path('uploads/s'));
//        }

//        Image::make(storage_path('uploads/' . $filename))->resize(180, 180)->save(storage_path('uploads/s/' . $filename));

        return $filename;

    }

    public function getDistrict($id)
    {
        $result = $this->getByList($id);

        return $result;
    }

}