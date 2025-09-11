<?php

namespace VnCoder\Models;

use Intervention\Image\Drivers\Gd\Driver;
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

    public static function photo($file, $name = '')
    {
        $clientExt = strtolower($file->getClientOriginalExtension());
        $allowed   = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($clientExt, $allowed, true)) {
            return ['success' => false, 'message' => 'Định dạng ' . $clientExt . ' không được phép upload, chỉ hỗ trợ webp, png, jpg, jpeg và gif'];
        }

        if ($file->getSize() > 50_000_000) {
            return ['success' => false, 'message' => 'Dung lượng file ảnh quá lớn!'];
        }

        if (!$name) {
            $name       = $file->getClientOriginalName();
            $getName    = pathinfo($name, PATHINFO_FILENAME);
            $media_name = safe_text($getName);
            $akey       = md5($media_name . '-' . time());
        } else {
            $getName    = pathinfo($name, PATHINFO_FILENAME);
            $media_name = safe_text($getName);
            $akey       = md5($media_name);
        }

        $media_slug = substr($akey, 0, 2) . '/' . substr($akey, 2, 2) . '/';
        $dirPhoto   = storage_path(PHOTO_DIR . $media_slug);
        makeDir($dirPhoto);

        $manager = new ImageManager(new Driver());
        $image   = $manager->read($file);

        $w = $image->width();
        $h = $image->height();

        if ($clientExt === 'gif') {
            $target = $dirPhoto . '/' . $media_name . '.gif';
            $file->move($dirPhoto, $media_name . '.gif');
            return ['success' => true, 'message' => 'Upload thành công!', 'path' => PHOTO_DIR . $media_slug . basename($target)];
        }
        $target = $dirPhoto . '/' . $media_name . '.webp';

        if ( $w < 300 || $h < 300) {
            $image->save($target, quality: 75);
            return ['success' => true, 'message' => 'Upload thành công!', 'path' => PHOTO_DIR . $media_slug . basename($target)];
        }
        $image->scaleDown(width: 1080);
        $image->save($target, quality: 75);
        return ['success' => true, 'message' => 'Upload thành công!', 'path' => PHOTO_DIR . $media_slug . basename($target)];
    }


}
