<?php

namespace App\Traits;


trait ImageTraits
{
    /**
     * 获取上传后的图片名称
     */
    public function thumbImg($data, $path)
    {
        $savePath = public_path('Uploads/' . $path . '/' . date('Y-m-d'));
        if (!is_dir($savePath)) {
            \File::makeDirectory($savePath, 0777, true);
        }

        $originName = uniqid() . '.jpg';
        file_put_contents(storage_path('uploads/' . $originName), base64_decode($data));
        \Image::make(storage_path('uploads/' . $originName))->resize(320, 240)->save($savePath . '/' . $originName);

        return '/Uploads/' . $path . '/' . date('Y-m-d') . '/' . $originName;
//        return file_exists($savePath . '/' . $originName)
//            ? 'Uploads/' . $path . '/' . date('Y-m-d') . '/' . $originName
//            : '';
    }

    public function getOriginImg($originName)
    {
        return storage_path('uploads/' . $originName);
    }

    public function delImg($tbName, $field = 'id')
    {
        // 判断是否删除旧图片
        $dels = request()->input('dels');
        if ($dels) {
            try {
                $r = \DB::table($tbName)->whereIn($field, $dels)->delete();
                return $r ? true : false;

            } catch (\Exception $e) {
                return false;
            }

        }

        return true;
    }

    public function tabCoverImg($tbName, $field, $fieldId)
    {
        $coverId = request()->input('cover');
        if ($coverId) {
            try {
                \DB::table($tbName)->where($field, $fieldId)->update(['is_cover' => 0]);
                \DB::table($tbName)->where('id', (int)$coverId)->update(['is_cover' => 1]);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }
}