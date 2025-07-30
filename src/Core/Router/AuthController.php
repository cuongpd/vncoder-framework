<?php

namespace VnCoder\Core\Router;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class AuthController extends Controller
{
    protected array $setData = [];

    public function Login_Action(Request $request)
    {
        $hash = $request->input('hash', '');
        $requestData = decryptData($hash);
        $this->setData['inputEmail'] = $requestData['email'] ?? '';
        $this->setData['inputPassword'] = $requestData['password'] ?? '';
        return $this->views('core::auth.login' , "Đăng nhập");
    }

    public function Do_Login_Action(Request $request)
    {
        $email = $request->input('email', '');
        $password = $request->input('password', '');
        flash_message('Địa chỉ Email bạn đăng nhập không tồn tại trên hệ thống');

        $redirectData = [
            'email' => $email,
            'password' => $password,
        ];
        return redirect()->route('auth.login', ['hash' => encryptData($redirectData)]);
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

    public function Modal_Action(Request $request, $action)
    {
        $bladeFile = VNCODER_CORE_PATH . 'src/Views/auth/modal/' . $action . '.blade.php';
        if(!file_exists($bladeFile)) {
            return $this->jsonResponse([], -1, 'Action not found');
        }
        $data = view('core::auth.modal.' . $action, $this->setData)->render();
        return $this->jsonResponse(['html' => $data], 1, 'Success');
    }

    protected function jsonResponse($data = [], $status = 1, $message = '')
    {
        return response()->json(array(
            'status' => $status,
            'message' => $message,
            'data' => $data
        ));
    }



    protected function views($bladeName, $title = "")
    {
        $this->setData['__views'] = $bladeName;
        $this->setData['__title'] = $title;
        return view('core::auth.auth', $this->setData);
    }
}