<?php

namespace VnCoder\Models;

use Intervention\Image\ImageManager;


define('PHOTO_DIR', 'uploads/photo/');
define('FILES_DIR', 'uploads/data/');

class VnUploads
{
    public static function data($file){
        $clientExt = $file->getClientOriginalExtension();
        if (!in_array($clientExt, ['doc', 'docx', 'pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'txt'])) {
            return ['success' => false, 'message' => 'Định dạng ' . $clientExt . ' không được phép upload, chỉ hỗ trợ doc, docx, pdf, ppt, pptx, xls, xlsx, zip, rar và txt'];
        }
        $getSize = $file->getSize();
        if ($getSize > 50000000) {
            return ['success' => false, 'message' => 'Dung lượng file quá lớn!'];
        }
        $getFilename = str_replace('.' . $clientExt, '', $file->getClientOriginalName());
        $fileName = safe_text(str_replace('.' . $clientExt, '', $getFilename)) . '.' . $clientExt;
        $akey = md5($fileName . '-' . time());
        $media_slug = substr($akey, 0, 2) . '/' . substr($akey, 2, 2) . '/';
        $dirFile = storage_path(FILES_DIR . $media_slug);
        makeDir($dirFile);
        $file->move($dirFile, $fileName);
        return ['success' => true, 'message' => 'Upload thành công!', 'path' => FILES_DIR . $media_slug . $fileName];
    }

    public static function photo($file, $name = ''){
        $clientExt = $file->getClientOriginalExtension();
        if (!in_array($clientExt, ['jpg', 'png', 'gif', 'jpeg', 'webp'])) {
            return ['success' => false, 'message' => 'Định dạng ' . $clientExt . ' không được phép upload, chỉ hỗ trợ webp, png, jpg, jpeg và gif'];
        }
        $getSize = $file->getSize();
        if ($getSize > 50000000) {
            return ['success' => false, 'message' => 'Dung lượng file ảnh quá lớn!'];
        }
        if(!$name){
            $name = $file->getClientOriginalName();
            $getFilename = str_replace('.' . $clientExt, '', $name);
            $media_name = safe_text(str_replace('.' . $clientExt, '', $getFilename)) . '.webp';
            $akey = md5($media_name . '-' . time());
        }else{
            $media_name = safe_text(str_replace('.' . $clientExt, '', $name)) . '.webp';
            $akey = md5($media_name);
        }
        $media_slug = substr($akey, 0, 2) . '/' . substr($akey, 2, 2) . '/';
        $dirPhoto = storage_path(PHOTO_DIR . $media_slug);
        makeDir($dirPhoto);
        $manager = new ImageManager(['driver' => 'gd']);
        $media = $manager->make($file);

        if ($clientExt == 'gif' || $media->width() < 300 || $media->height() < 300) {
            $file->move($dirPhoto, $media_name);
        } else {
            $media->widen(1080, function ($constraint) {
                $constraint->upsize();
            })->encode('webp', 75)->save($dirPhoto . '/' . $media_name, 85);
        }
        return ['success' => true, 'message' => 'Upload thành công!', 'path' => PHOTO_DIR . $media_slug . $media_name];
    }

}
