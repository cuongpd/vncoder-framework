<?php

namespace VnCoder\Models;

use Illuminate\Http\Request;
use VnCoder\Models\VnModel;
use VnCoder\Models\VnUploads;

class VnPosts extends VnModel
{
    protected $table = '__posts';
    protected string $postType = 'post'; // // 'post', 'category', 'page
    public string $modelName = 'Bài viết';
    protected $fillable = ['id', 'parent_id', 'type', 'title', 'slug', 'description', 'photo', 'content' , 'tags'];

    public function parent()
    {
        return $this->hasOne(VnPosts::class, 'id', 'parent_id')->where('type', 'category')->where('status', '>', 0);
    }

    public function child()
    {
        return $this->hasMany(VnPosts::class, 'parent_id', 'id')->where('type', 'category')->where('status', '>', 0);
    }

    public function posts()
    {
        return $this->hasMany(VnPosts::class, 'parent_id', 'id')->where('type', 'post')->where('status', '>', 0);
    }

    public function tableConfig(){
        return [
            'photo' => 'Photo',
            'category' => 'Category',
            'title' => 'Name',
            'link' => 'Link',
            'description' => 'Description',
        ];
    }

    protected function formConfig()
    {
        return [
            'type' => ['type' => 'hidden', 'value' => $this->postType],
            'title' => ['label' => 'Tên bài viết', 'col' => 4, 'type' => 'text', 'required' => true, 'maxlength' => 70],
            'parent_id' => ['label' => 'Danh mục', 'col' => 2, 'type' => 'select', 'options' => $this->getCategoryTree(), 'required' => true],
            'photo' => ['label' => 'Ảnh', 'col' => 6, 'type' => 'photo'],
            'description' => ['label' => 'Mô tả', 'col' => 6, 'type' => 'textarea', 'required' => false, 'maxlength' => 160],
            'tags' => ['label' => 'Từ khóa', 'col' => 6, 'type' => 'textarea', 'required' => false, 'maxlength' => 255],
            'content' => ['label' => 'Nội dung', 'col' => 12, 'type' => 'editor', 'rows' => 25]
        ];
    }

    public function getCategoryAttribute()
    {
        return $this->parent->title ?? 'Uncategorized';
    }

    public function getLinkAttribute()
    {
        if($this->type == 'category'){
            return url('danh-muc/' . $this->slug);
        }
        if($this->type == 'page'){
            return url( 'trang-' . $this->slug);
        }
        return url($this->slug);
    }

    public function getItemInfo($id)
    {
        return self::where('id', $id)->where('type', $this->postType)->first();
    }

    public function crudData(){
        if($this->postType == 'post'){
            return $this->with('parent')->where('type', $this->postType)->where('status', '>', 0)->get();
        }
        return $this->where('type', $this->postType)->where('status', '>', 0)->get();
    }

    public function getSlug($slug)
    {
        return self::where('type', $this->postType)->where('slug', $slug)->first();
    }

    public function getUniqueSlug($title)
    {
        $slug = $slug_default = safe_text($title);
        $counter = 1;
        while (self::getSlug($slug)) {
            $counter++;
            $slug = $slug_default . '-'. $counter;
        }
        return $slug;
    }

    public function getFormData($id = 0){
        $formData = parent::getFormData($id);
        if($formData['type']['value'] != $this->postType){
            return false;
        }
        return $formData;
    }

    public function submitFormData(Request $request){
        $data = $request->except('__token');
        $id = $data['id'] ?? 0;
        if($request->hasFile('photo')){
            $uploadData = VnUploads::photo($request->file('photo'));
            if($uploadData['success']){
                $data['photo'] = $uploadData['path'];
            }else{
                unset($data['photo']);
            }
        }
        unset($data['id']);

        if($id > 0){
            $postData = $this->getItemInfo($id);
            if(!$postData){
                return 0;
            }
            if(!$postData->slug){
                $data['slug'] = $this->getUniqueSlug($data['title']);
            }
            $data['updated'] = TIME_NOW;
            $this->where('id', $id)->update($data);
        }else{
            $data['slug'] = $this->getUniqueSlug($data['title']);
            $data['type'] = $this->postType;
            $data['created'] = TIME_NOW;
            if(!$data['parent_id']){
                $data['parent_id'] = 0;
            }
            $id = $this->insertGetId($data);
        }
        return $id;
    }

    public function getCategoryTree(){
        $data = [];
        $query = self::select('id', 'title', 'parent_id')->with('child')->where('type', 'category')->where('parent_id', 0)->where('status', '>', 0)->get();
        foreach ($query as $item) {
            $data[$item->id] = $item->title;
            foreach ($item->child as $i) {
                $data[$i->id] = '---- ' . $i->title;
            }
        }
        return $data;
    }

}
