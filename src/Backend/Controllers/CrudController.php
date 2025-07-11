<?php

namespace VnCoder\Backend\Controllers;

use Illuminate\Http\Request;
use VnCoder\Backend\Controllers\BackendController;
use VnCoder\Models\VnModel;

class CrudController extends BackendController
{
    protected VnModel $model;
    protected string $modelName = '';
    protected string $crudModel = '';

    function __construct()
    {
        parent::__construct();
        if(empty($this->crudModel)){
            die('Please define $crudName and $crudModel in your controller');
        }
        $this->model = app($this->crudModel);
        $this->modelName = $this->model->modelName;
    }

    public function Index_Action(){
        return $this->gotoListItem();
    }

    public function List_Action(){
        $this->setData['crudName'] = $this->modelName;
        $this->metaData->title = "Dữ liệu bảng : " . $this->modelName;
        $this->initDataTable(true);
        $this->setData['crudColumn'] = $this->model->tableConfig();
        $this->setData['crudData'] = $this->model->crudData();
        return $this->views('admin.crud.list', true);
    }

    public function Edit_Action(Request $request){
        $id = getParamInt('id', 0);
        $formData = $this->model->getFormData($id);
        if(!$formData || ($id > 0 && $formData['id']['value'] != $id)){
            return $this->gotoListItem("Không tìm thấy dữ liệu với ID : $id");
        }
        $this->metaData->title = $id > 0 ? "Chỉnh sửa " . $this->modelName : "Thêm mới " . $this->modelName;
        $this->setData['formData'] = $formData;
        return $this->views('admin.crud.edit', true);
    }

    public function Edit_Action_Submit(Request $request){
        $id = $request->input('id', 0);
        $updateId = $this->model->submitFormData($request);
        if($updateId > 0){
            return $this->gotoListItem("Đã cập nhật dữ liệu thành công!");
        }else{
            if($id > 0){
                return redirect()->to( $this->linkAction('edit?id=' . $id));
            }else{
                return redirect()->to( $this->linkAction('list'));
            }
        }
    }

    public function Delete_Action(Request $request){
        $id = getParamInt('id', 0);
        $modelData = $this->model->find($id);
        if($modelData){
            $this->model->deleteItem($id);
            return $this->gotoListItem("Đã xóa dữ liệu thành công!");
        }
        return $this->gotoListItem("Không tìm thấy dữ liệu với ID : $id");
    }

    public function List_Delete_Action(){
        $this->setData['crudName'] = $this->modelName;
        $this->metaData->title = "Dữ liệu " . $this->modelName . " đã xóa";
        $this->initDataTable();
        $this->setData['crudColumn'] = $this->model->tableConfig();
        $this->setData['crudData'] = $this->model->crudDeletedData();
        return $this->views('admin.crud.list-delete', true);
    }

    public function Restore_Action(Request $request){
        $id = getParamInt('id', 0);
        $modelData = $this->model->find($id);
        if($modelData){
            $this->model->restoreItem($id);
            return $this->gotoListItem("Đã khôi phục dữ liệu thành công!");
        }
        return $this->gotoListItem("Không tìm thấy dữ liệu với ID : $id");
    }

    protected function gotoListItem($message = ''){
        return $this->gotoActionPage('list', $message);
    }

}
