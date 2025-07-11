<?php

namespace VnCoder\Backend\Auth;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use VnCoder\Backend\Models\Admin;

class AuthController extends Controller
{

    public function Login_Action(Request $request){
        if (Admin::isLogin()) {
            flash_message('Bạn đã đăng nhập vào hệ thống quản trị!');
            return redirect()->route('backend.dashboard');
        }
        if ($request->isMethod('post')) {
            $email = $request->input('email');
            $password = $request->input('password');
            return Admin::Login($email, $password);
        }

        return view('backend::auth.login');
    }

    public function Reset_Password_Action(){
        return view('backend::auth.reset-password');
    }

    public function Logout_Action()
    {
        return Admin::Logout();
    }


}