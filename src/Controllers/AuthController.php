<?php

namespace VnCoder\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller;
use VnCoder\Models\VnConfig;
use VnCoder\Models\VnUser;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    protected array $setData = [];

    public function Login_Action(Request $request)
    {
        $requestData = session('__requestData');
        $errorData = session('__errorData');
        $this->setData['inputEmail'] = $requestData['email'] ?? '';
        $this->setData['inputPassword'] = $requestData['password'] ?? '';
        $this->setData['errorEmail'] = $errorData['email'] ?? '';
        $this->setData['errorPassword'] = $errorData['password'] ?? '';
        return $this->views('login');
    }

    public function Register_Action(Request $request)
    {
        $requestData = session('__requestData');
        $errorData = session('__errorData');
        $this->setData['inputEmail'] = $requestData['email'] ?? '';
        $this->setData['inputPassword'] = $requestData['password'] ?? '';
        $this->setData['inputName'] = $requestData['name'] ?? '';
        $this->setData['errorEmail'] = $errorData['email'] ?? '';
        $this->setData['errorPassword'] = $errorData['password'] ?? '';
        $this->setData['errorName'] = $errorData['name'] ?? '';
        return $this->views('register');
    }

    public function Reset_Password_Action(){
        $requestData = session('__requestData');
        $errorData = session('__errorData');
        $this->setData['inputEmail'] = $requestData['email'] ?? '';
        $this->setData['errorEmail'] = $errorData['email'] ?? '';
        return $this->views('reset-password');
    }

    public function Logout_Action(){
        VnUser::logout();
        return redirect()->route('auth.login');
    }

    public function Do_Login_Action(Request $request){
        $email = $request->input('email', '');
        $password = $request->input('password', '');
        $errorData = [];
        if(!$email){
            $errorData['email'] = 'Email không được để trống';
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errorData['email'] = 'Email không đúng định dạng';
        }
        if(!$password){
            $errorData['password'] = 'Mật khẩu không được để trống';
        }elseif(strlen($password) < 6){
            $errorData['password'] = 'Mật khẩu phải từ 6 ký tự trở lên';
        }
        session()->flash('__requestData', ['action' => 'login', 'email' => $email]);
        if($errorData){
            session()->flash('__errorData', $errorData);
            return redirect()->route('auth.login');
        }else{
            $resultData = VnUser::doLogin($email, $password);
            if($resultData['status']) {
                $redirectUrl = session('__redirectAfterLogin');
                session()->forget('__redirectAfterLogin');
                return redirect()->to($redirectUrl);
            }else{
                session()->flash('__errorData', ['email' => $resultData['message']]);
                return redirect()->route('auth.login');
            }
        }
    }

    public function Do_Register_Action(Request $request){
        $name = $request->input('name', '');
        $email = $request->input('email', '');
        $password = $request->input('password', '');

        $errorData = [];
        if(!$name){
            $errorData['name'] = 'Tên hiển thị không được để trống';
        }
        if(!$email){
            $errorData['email'] = 'Email không được để trống';
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errorData['email'] = 'Email không đúng định dạng';
        }else{
            $checkEmail = VnUser::where('email', $email)->count();
            if($checkEmail){
                $errorData['email'] = 'Email đã tồn tại trên hệ thống';
            }
        }
        if(!$password){
            $errorData['password'] = 'Mật khẩu không được để trống';
        }elseif(strlen($password) < 6){
            $errorData['password'] = 'Mật khẩu phải từ 6 ký tự trở lên';
        }
        $re_password = $request->input('re_password', '');
        if($password != $re_password){
            $errorData['password'] = 'Mật khẩu nhập lại không khớp';
        }
        session()->flash('__requestData', ['action' => 'register', 'name' => $name, 'email' => $email]);
        if($errorData){
            session()->flash('__errorData', $errorData);
            return redirect()->route('auth.register');
        }else{
            $resultData = VnUser::registerUser($email, $password, $name);
            if($resultData['status']) {
                flash_message($resultData['message']);
                return redirect()->route('auth.login');
            } else {
                session()->flash('__errorData', ['email' => $resultData['message']]);
                return redirect()->route('auth.register');
            }
        }
    }

    public function Do_Reset_Password_Action(Request $request){
        $errorData = [];
        $email = $request->input('email', '');
        if(!$email){
            $errorData['email'] = 'Email không được để trống';
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errorData['email'] = 'Email không đúng định dạng';
        }
        session()->flash('__requestData', ['action' => 'reset', 'email' => $email]);
        if($errorData){
            session()->flash('__errorData', $errorData);
            return redirect()->route('auth.reset-password');
        }else{
            $resultData = VnUser::sendLinkResetPassword($email);
            if($resultData['status']) {
                flash_message($resultData['message']);
                return redirect()->route('auth.login');
            } else {
                session()->flash('__errorData', ['email' => $resultData['message']]);
                return redirect()->route('auth.reset-password');
            }
        }
    }

    protected function views($action){
        $this->setData['__formActionView'] = 'core::auth.form.' . $action;
        $this->setData['formAction'] = $action;

        $this->setData['authConfig'] = VnConfig::authConfigData();

        return view('core::auth.auth', $this->setData);
    }




    public function Auth_Action(Request $request)
    {
        $action = $request->input('action', 'login');
        if($action == 'logout'){
            VnUser::logout();
            return redirect()->route('auth', ['action' => 'login']);
        }
        if(VnUser::isLogin()) return redirect()->to('/');

        $requestData = session('__requestData');
        $errorData = session('__errorData');
        if(isset($requestData['action'])) {
            $action = $requestData['action'];
        }
        $this->setData['inputEmail'] = $requestData['email'] ?? '';
        $this->setData['inputPassword'] = $requestData['password'] ?? '';
        $this->setData['inputName'] = $requestData['name'] ?? '';

        $this->setData['errorEmail'] = $errorData['email'] ?? '';
        $this->setData['errorPassword'] = $errorData['password'] ?? '';
        $this->setData['errorName'] = $errorData['name'] ?? '';
        $this->setData['errorCaptcha'] = $errorData['captcha'] ?? '';

        $this->setData['formAction'] = $action;
        $this->setData['__formActionView'] = 'core::auth.form.' . $action;

        return view('core::auth.auth', $this->setData);
    }

    public function Session_Action_Submit(Request $request){
        $idTokenString = $request->input('idToken');
        if (!$idTokenString) return response()->json([ 'error' => 'Missing idToken' ], 422);
        $auth = (new Factory())->withServiceAccount( storage_path(env('FIREBASE_SERVICE_ACCOUNT_PATH', 'app/firebase/service-account.json')))->createAuth();
        try {
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
        } catch (FailedToVerifyToken $e) {
            return response()->json([ 'status' => -1, 'message' => 'The token is invalid: '.$e->getMessage()], 401);
        }
        $uid = $verifiedIdToken->claims()->get('sub');
        $userInfo = $auth->getUser($uid) ?? null;
        if($userInfo){
            $email = $userInfo->email;
            $name = $userInfo->displayName ?: explode('@', $email)[0];
            $avatar = $userInfo->photoUrl ?: '';
            $resultData = VnUser::authLogin($email, $uid,  $name, $avatar);
            if(!$resultData['status']){
                return response()->json([ 'status' => -1, 'message'=> $resultData['message']], 401);
            }
        }
        $expiresIn = 14 * 86400;
        $sessionCookie = $auth->createSessionCookie($idTokenString, $expiresIn);
        cookie('firebase_session', $sessionCookie, $expiresIn);
        return response()->json(['status' => 1, 'message' => '', 'data' => $uid] );
    }

}