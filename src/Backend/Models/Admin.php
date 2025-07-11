<?php

namespace VnCoder\Backend\Models;
use VnCoder\Models\VnModelBase;

class Admin extends VnModelBase
{
    protected $table = '__admin';
    protected $fillable = ['role', 'name', 'email', 'password', 'birthday', 'gender', 'address', 'phone', 'avatar', 'reset_token','reset_expiration', 'status'];

    const ROLE_DATA = [
        'manager' => 'Quản lý',
        'admin' => 'Admin',
        'root' => 'Super Admin'
    ];

    public static function getUserInfo($uid){
        return self::select('id','role', 'name', 'email', 'birthday', 'gender', 'address', 'phone', 'avatar')->where('id', $uid)->where('status', '>' , 0)->first();
    }

    public function getAvatarAttribute()
    {
        return $this->avatar ?? core_static('images/avatar.png');
    }

    public function getRoleNameAttribute()
    {
        return self::ROLE_DATA[$this->role] ?? 'Khách hàng';
    }

    // user_status
    public function getUserStatusAttribute(){
        return $this->status > 0 ? 'Hoạt động' : 'Khóa';
    }

    public static function getData(){
        return self::get();
    }

    public static function getFormData($uid = 0){
        $formData = [];
        $userInfo = self::getUserInfo($uid);
        if($userInfo){
            $userInfo = $userInfo->toArray();
        }

        $formData['id'] = ['type' => 'hidden', 'value' => $uid, 'required' => true];
        $formData['role'] = ['label' => 'Nhóm quyền', 'col' => 2, 'type' => 'select', 'value' => $userInfo['role'] ?? '', 'options' => self::ROLE_DATA, 'required' => true];

        $formData['name'] = ['label' => 'Họ và tên', 'col' => 4, 'type' => 'text', 'value' => $userInfo['name'] ?? '', 'required' => true];
        $formData['email'] = ['label' => 'Email', 'col' => 3, 'type' => 'email', 'value' => $userInfo['email'] ?? '', 'required' => true];

        if($userInfo) $formData['email']['readonly'] = true;

        $formData['password'] = ['label' => 'Mật khẩu', 'col' => 3, 'type' => 'password', 'value' => '', 'required' => $uid == 0];

        $formData['phone'] = ['label' => 'Số điện thoại', 'col' => 4, 'type' => 'text', 'value' => $userInfo['phone'] ?? '', 'required' => false];
        $formData['address'] = ['label' => 'Địa chỉ', 'col' => 8, 'type' => 'text', 'value' => $userInfo['address'] ?? '', 'required' => false];

        return $formData;
    }

    public static function adminSubmitFormData($uid, $email, $password, $updateData){
        if($uid == 0){
            $checkUser = self::where('email', $email)->first();
            if($checkUser){
                flash_message('Email đã tồn tại trên hệ thống, vui lòng chọn email khác!');
                return false;
            }
            $updateData['email'] = $email;
            $updateData['password'] = self::encryptAdminPassword($password);
            $updateData['status'] = 1;
            self::create($updateData);
            flash_message('Tạo mới quản trị viên thành công!');
            return true;
        }else{
            $userInfo = self::getUserInfo($uid);
            if($userInfo){
                if($password){
                    $updateData['password'] = self::encryptAdminPassword($password);
                }
                $userInfo->update($updateData);
                flash_message('Cập nhật thông tin quản trị viên thành công!');
                return true;
            }else{
                flash_message('Không tìm thấy thông tin quản trị viên!');
                return false;
            }
        }
    }

    public static function LockUser($uid){
        if($uid == 1){
            flash_message('Không thể khóa tài khoản quản trị này!');
            return;
        }
        if($uid == session('admin_uid', 0)){
            flash_message('Không thể khóa tài khoản đang đăng nhập!');
            return;
        }
        $userInfo = self::getUserInfo($uid);
        if($userInfo){
            self::where('id', $uid)->update(['status' => -1]);
            flash_message('Khóa tài khoản quản trị viên thành công!');
        }else{
            flash_message('Không tìm thấy thông tin quản trị viên!');
        }
    }

    public static function UnlockUser($uid){
        $userInfo = self::getUserInfo($uid);
        if($userInfo){
            self::where('id', $uid)->update(['status' => 1]);
            flash_message('Mở khóa tài khoản quản trị viên thành công!');
        }else{
            flash_message('Không tìm thấy thông tin quản trị viên!');
        }
    }

    static function getUserData(){
        $uid = session('admin_uid', 0);
        $uid_token = session('admin_uid_token', '');
        $userData = [];
        if($uid && $uid_token && $uid_token == self::encryptAdminPassword($uid)){
            $userData = session('admin_data', []);
        }
        if(!$userData){
            $userInfo = self::getUserInfo($uid);
            if($userInfo){
                $userData = $userInfo->toArray();
            }else{
                return redirect()->route('backend.login');
            }
        }
        return $userData;
    }

    public static function isLogin()
    {
        $uid = session('admin_uid', 0);
        $uid_token = session('admin_uid_token', '');
        if($uid && $uid_token && $uid_token == self::encryptAdminPassword($uid)){
            return $uid;
        }
        // Login by cookie
        $cid = cookie('admin_uid', '');
        $cid_token = cookie('admin_uid_token', '');
        if($cid){
            $uid = decryptNumber($cid);
            if($uid && $cid_token == self::encryptAdminPassword($uid)){
                self::DoLogin($uid);
                return $uid;
            }
        }
        return false;
    }

    public static function Login($email, $password){
        if(!$email || !$password){
            flash_message('Vui lòng nhập đầy đủ thông tin đăng nhập!');
            return redirect()->route('backend.login');
        }
        $userInfo = self::where('email', $email)->first();
        if($userInfo){
            if($userInfo->password == self::encryptAdminPassword($password)) {
                if ($userInfo->status > 0) {
                    return self::DoLogin($userInfo->id);
                } else {
                    flash_message('Tài khoản của bạn chưa được kích hoạt hoặc đang bị khóa, vui lòng liên hệ quản trị viên để xử lý!');
                }
            }else{
                flash_message('Mật khẩu không chính xác!');
            }
        }else{
            flash_message('Địa chỉ Email bạn đăng nhập không tồn tại trên hệ thống');
        }
        return redirect()->route('backend.login');
    }

    public static function DoLogin($uid){
        $userInfo = self::getUserInfo($uid);
        if($userInfo){
            flash_message('Chào mừng bạn đã quay trở lại!');
            session([
                'admin_uid' => $userInfo->id,
                'admin_uid_token' => self::encryptAdminPassword($userInfo->id),
                'admin_data' => $userInfo->toArray()
            ]);
            self::setUserCookie($userInfo->id);
            return redirect()->to(backend('dashboard'));
        }else{
            flash_message('Tài khoản của bạn chưa được kích hoạt hoặc đang bị khóa, vui lòng liên hệ quản trị viên để xử lý!');
        }
        return redirect()->route('backend.login');
    }

    public static function setUserCookie($uid){
        cookie('admin_uid', encryptNumber($uid));
        cookie('admin_uid_token', self::encryptAdminPassword($uid));
    }

    public static function Logout(){
        session()->forget(['admin_uid', 'admin_uid_token', 'admin_data']);
        cookie('admin_uid', '', -1);
        cookie('admin_uid_token', '', -1);
        flash_message('Bạn đã đăng xuất thành công!');
        return redirect()->route('backend.login');
    }

    static function encryptAdminPassword($password){
        return md5('vn-' . md5($password) . '-1990');
    }
}