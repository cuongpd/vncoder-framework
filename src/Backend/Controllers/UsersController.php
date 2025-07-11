<?php

namespace VnCoder\Backend\Controllers;

use Illuminate\Http\Request;
use VnCoder\Backend\Models\Admin;
use VnCoder\Backend\Models\VnUserRole;

class UsersController extends BackendController
{
    public function Index_Action(){
        return $this->redirectUrl('list');
    }

    public function List_Action(){
        $this->initDataTable();
        $this->metaData->title = 'Danh sách quản trị viên';
        $this->setData['linkActive'] = $this->linkAction('active-action');
        $this->setData['linkLock'] = $this->linkAction('lock-action');
        $this->setData['linkUnlock'] = $this->linkAction('unlock-action');
        $this->setData['userData'] = Admin::getData();
        return $this->views('users.user-data');
    }

    public function Edit_Action(Request $request){
        $uid = getParamInt('uid', 0);
        $this->metaData->title = $uid > 0 ? 'Chỉnh sửa quản trị viên' : 'Tạo quản trị viên mới';
        $this->setData['formData'] = Admin::getFormData($uid);
        return $this->views('users.user-edit');
    }

    public function Edit_Action_Submit(Request $request){
        $uid = $request->input('id', 0);
        $email = $request->input('email', '');
        $password = $request->input('password', '');
        $updateData = [
            'role' => $request->input('role', 'admin'),
            'name' => $request->input('name', ''),
            'phone' => $request->input('phone', ''),
            'address' => $request->input('address', ''),
        ];

        $sendSubmit = Admin::adminSubmitFormData($uid, $email, $password, $updateData);
        if($sendSubmit || $uid == 0){
            return $this->redirectUrl('list');
        }else{
            return $this->redirectUrl('edit?uid=' . $uid);
        }
    }

    public function Lock_User_Action(Request $request){
        $uid = $request->input('uid', 0);
        Admin::LockUser($uid);
        return $this->redirectUrl('list');
    }

    public function Unlock_User_Action(Request $request){
        $uid = $request->input('uid', 0);
        Admin::UnlockUser($uid);
        return $this->redirectUrl('list');
    }
















    public function Role_Action(){
        $this->metaData->title = 'Quản lý Phân quyền';
        $this->setData['linkEdit'] = $this->linkAction('role-edit');
        $this->setData['linkPermissions'] = $this->linkAction('role-permission');
        $this->setData['userRoleData'] = VnUserRole::getData();
        return $this->views('users.user-role');
    }

    public function Role_Edit_Action(){
        $id = getParamInt('id', 0);
        $formData = app(VnUserRole::class)->getFormData($id);
        if($id == 1 || ($id > 0 && $formData['id']['value'] != $id)){
            return redirect()->to( $this->linkAction('role'));
        }
        $this->metaData->title = $id > 0 ? 'Chỉnh sửa nhóm quyền' : 'Tạo nhóm quyền mới';
        $this->setData['formData'] = $formData;
        return $this->views('users.user-role-edit');
    }

    public function Role_Edit_Action_Submit(Request $request){
        $id = getParamInt('id', 0);
        $updateId = app(VnUserRole::class)->submitFormData($request);
        if($updateId > 0){
            return redirect()->to( $this->linkAction('role'));
        }else{
            if($id > 0){
                return redirect()->to( $this->linkAction('role-edit?id=' . $id));
            }else{
                return redirect()->to( $this->linkAction('role'));
            }
        }
    }

    public function Role_Permission_Action(){
        $roleId = getParamInt('id', 0);
        $roleData = VnUserRole::find($roleId);
        if(!$roleData){
            return $this->redirectUrl('role');
        }
        $this->metaData->title = 'Phân quyền cho nhóm : ' . $roleData->name;
        $this->setData['roleData'] = $roleData;
        $this->setData['linkRole'] = $this->linkAction('role');
        $this->setData['linkRoleEdit'] = $this->linkAction('role-edit?id=' . $roleId);
        $this->setData['permissionData'] = VnUserRole::getPermissionData($roleId);

        $this->setData['permission'] = VnUserRole::getAllPermission();

        dd($this->setData['permission']);

//        return $this->views('users.user-role-permission');
    }



}
