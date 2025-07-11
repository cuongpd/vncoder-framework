<?php

namespace VnCoder\Backend\Controllers;

use Illuminate\Http\Request;
use VnCoder\Backend\Controllers\CrudController;
use VnCoder\Models\VnPosts;
use VnCoder\Models\VnPostsCategory;

class PostController extends CrudController
{
    protected string $crudModel = VnPosts::class;

    public function Category_Action(){
        $this->metaData->title = 'Danh mục bài viết';
        $this->setData['linkEdit'] = $this->linkAction('category-edit');
        $this->setData['linkAddCategoryTree'] = $this->linkAction('category-add-tree');
        $this->setData['linkDelete'] = $this->linkAction('category-delete');
        $this->setData['categoryData'] = app(VnPostsCategory::class)->getPostsCategory();
        return $this->views('post.category');
    }

    public function Category_Edit_Action(){
        $id = getParamInt('id', 0);
        $formData = app(VnPostsCategory::class)->getFormData($id);
        if($id > 0 && $formData['id']['value'] != $id){
            return redirect()->to( $this->linkAction('category'));
        }
        $this->metaData->title = $id > 0 ? 'Chỉnh sửa danh mục' : 'Tạo danh mục cha mới';
        $this->setData['formData'] = $formData;
        return $this->views('post.category-edit');
    }

    public function Category_Edit_Action_Submit(Request $request){
        $id = getParamInt('id', 0);
        $updateId = app(VnPostsCategory::class)->submitFormData($request);
        flash_message('Cập nhật danh mục thành công', 'success');
        if($updateId > 0){
            return redirect()->to( $this->linkAction('category'));
        }else{
            if($id > 0){
                return redirect()->to( $this->linkAction('category-edit?id=' . $id));
            }else{
                return redirect()->to( $this->linkAction('category'));
            }
        }
    }

    public function Category_Add_Tree_Action(){
        $parentId = getParamInt('parent_id', 0);
        $categoryInfo = app(VnPostsCategory::class)->getCategoryInfo($parentId);
        if(!$categoryInfo){
            flash_message('Danh mục cha không tồn tại', 'error');
            return redirect()->to( $this->linkAction('category'));
        }
        $this->metaData->title = 'Danh mục con : ' . $categoryInfo->title;
        $this->setData['formData'] = app(VnPostsCategory::class)->getCategoryTreeDataForm($parentId);
        return $this->views('post.category-edit');
    }

    public function Category_Add_Tree_Action_Submit(Request $request){
        app(VnPostsCategory::class)->submitFormData($request);
        return redirect()->to( $this->linkAction('category'));
    }

    public function Category_Delete_Action(Request $request){
        $id = getParamInt('id', 0);
        app(VnPostsCategory::class)->deleteItem($id);
        flash_message('Xóa danh mục thành công', 'success');
        return redirect()->to( $this->linkAction('category'));
    }
}
