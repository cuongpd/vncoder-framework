<?php

namespace VnCoder\Models;

use VnCoder\Models\VnPosts;

class VnPostsCategory extends VnPosts
{

    protected string $postType = 'category';

    public function getPostsCategory(){
        return self::with('child', 'posts')->where('type', $this->postType)->where('parent_id', 0)->where('status', '>', 0)->get();
    }

    protected function formConfig()
    {
        return [
            'type' => ['type' => 'hidden', 'value' => $this->postType],
            'title' => ['label' => 'Tên danh mục', 'col' => 4, 'type' => 'text', 'required' => true],
            'parent_id' => ['type' => 'hidden', 'value' => 0],
            'photo' => ['label' => 'Ảnh đại diện', 'col' => 8, 'type' => 'photo'],
            'description' => ['label' => 'Mô tả', 'col' => 12, 'type' => 'textarea', 'required' => false],
            'content' => ['label' => 'Nội dung', 'col' => 12, 'type' => 'editor'],
            'tags' => ['label' => 'Tags', 'col' => 12, 'type' => 'text']
        ];
    }

    public function getCategoryTreeDataForm($parentId){
        $formData = $this->formConfig();
        $formData['type']['value'] = $this->postType;
        $formData['parent_id']['value'] = $parentId;
        return $formData;

    }

    public function getParentCategoryTree($id = 0){
        return self::select('id', 'title')->where('id', '<>', $id) ->where('type', $this->postType)->where('parent_id', 0)->where('status', '>', 0)->pluck('title', 'id');
    }

    public function getCategoryInfo($id){
        return self::where('id', $id)->where('type', $this->postType)->where('status', '>', 0)->first();
    }
}
