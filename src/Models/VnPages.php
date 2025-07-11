<?php

namespace VnCoder\Models;

use VnCoder\Models\VnPosts;

class VnPages extends VnPosts
{
    public string $modelName = 'Trang Tĩnh';
    protected string $postType = 'page';

    public function tableConfig(){
        return [
            'photo' => 'Photo',
            'title' => 'Name',
            'link' => 'Link',
            'description' => 'Description',
        ];
    }

    protected function formConfig()
    {
        return [
            'type' => ['type' => 'hidden', 'value' => $this->postType],
            'parent_id' => ['type' => 'hidden', 'value' => 0],
            'title' => ['label' => 'Tên trang', 'col' => 4, 'type' => 'text', 'required' => true,'maxlength' => 70],
            'photo' => ['label' => 'Ảnh', 'col' => 8, 'type' => 'photo'],
            'description' => ['label' => 'Mô tả', 'col' => 6, 'type' => 'textarea', 'required' => false, 'maxlength' => 160],
            'tags' => ['label' => 'Từ khóa', 'col' => 6, 'type' => 'textarea', 'required' => false,'maxlength' => 250],
            'content' => ['label' => 'Nội dung', 'col' => 12, 'type' => 'editor', 'rows' => 25],

        ];
    }

    public static function getPageInfo($slug){
        return self::where('type', 'page')->where('slug', $slug)->first();
    }

}
