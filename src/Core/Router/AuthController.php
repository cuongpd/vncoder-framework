<?php

namespace VnCoder\Core\Router;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class AuthController extends Controller
{
    protected array $setData = [];

    public function Login_Action(Request $request)
    {
        return $this->views('core::auth.login' , "Đăng nhập");
    }

    public function Do_Login_Action(Request $request)
    {
        $email = $request->input('email', '');
        $password = $request->input('password', '');
        flash_message('Địa chỉ Email bạn đăng nhập không tồn tại trên hệ thống');
        return redirect()->route('auth.login');
    }

    public function Register_Action(Request $request)
    {
        return $this->views('core::auth.register' , "Đăng kí tài khoản");
    }

    public function Logout_Action(Request $request)
    {
        dd('`12');
    }

    public function Reset_Password_Action(Request $request)
    {
        return $this->views('core::auth.reset' , "Thiết lập lại mật khẩu");
    }

    public function Provider_Action(Request $request, $provider)
    {
        dd($provider);
    }

    public function Provider_Callback_Action(Request $request, $provider)
    {
        dd($provider);
    }



    protected function views($bladeName, $title = "")
    {
        $this->setData['__views'] = $bladeName;
        $this->setData['__title'] = $title;
        return view('core::auth.auth', $this->setData);
    }
}