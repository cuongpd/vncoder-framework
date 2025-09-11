<?php

namespace VnCoder\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class VnUser extends VnModelBase
{
    protected $table = '__users';

    protected $fillable = [
        "role","uuid","nickname","token","name","email","password","birthday",
        "gender","address","phone","avatar","provider","authentication","sender",
        "reset_token","reset_expiration","status"
    ];

    protected $casts = [
        'birthday'         => 'date',
        'reset_expiration' => 'integer',
        'status'           => 'integer',
    ];

    public static function getUid(): int
    {
        $uid = (int) session('uid');
        if ($uid > 0) {
            return $uid;
        }
        $cid = (string) cookie('cid');
        $cidToken = (string) cookie('cid_token');

        if (!$cid || !$cidToken || $cidToken !== hash('sha256', $cid)) {
            return 0;
        }

        $uid = (int) decryptNumber($cid);
        if ($uid <= 0) return 0;
        self::loginUser($uid);
        return $uid;
    }

    public static function isLogin(): bool
    {
        return self::getUid() > 0;
    }

    public static function userInfo(): ?array
    {
        $cached = session('user_data');
        if (is_array($cached) && !empty($cached['id'])) return $cached;
        $uid = self::getUid();
        if ($uid <= 0) return null;
        $user = self::select('id','uuid','name','email','avatar','role','status','provider','phone','address')->find($uid);
        if (!$user) return null;
        $arr = $user->toArray();
        session(['user_data' => $arr]);
        return $arr;
    }

    public static function logout(): void
    {
        session(['uid'=> -1,'user_data'=> []]);
        cookie('cid', '-1', -1);
        cookie('cid_token', '-1', -1);
        cookie('firebase_session', '-1', -1);
    }

    public static function loginUser($uid): void
    {
        $userInfo = self::select('id','uuid','name','email','avatar','role','status','provider','phone','address')->find($uid);
        if($userInfo && $userInfo->status > 0){
            $cid = encryptNumber($uid);
            $cidToken = hash('sha256', $cid);
            session([
                'uid' => $uid,
                'user_data' => $userInfo->toArray()
            ]);
            cookie('cid', $cid, 7 * 86400);
            cookie('cid_token', $cidToken, 7 * 86400);
        }
    }

    public static function authLogin(string $email, string $uuid, string $name, string $avatar = '', string $provider = 'system'): array
    {
        $email = trim(mb_strtolower($email));
        $user  = self::where('email', $email)->first();

        if ($user) {
            if ((int)$user->status < 0) {
                return ['status'=>false,'message'=>'Tài khoản bị khóa. Liên hệ quản trị viên.'];
            }

            $user->fill([
                'uuid'     => $uuid ?: $user->uuid,
                'name'     => $name ?: $user->name,
                'avatar'   => $avatar ?: $user->avatar,
                'provider' => $provider,
            ]);
            $user->save();
            self::loginUser($user->id);
            return ['status'=>true,'message'=>'Đăng nhập thành công.'];
        }

        $new = new self();
        $new->fill([
            'token'    => bin2hex(random_bytes(16)),
            'uuid'     => $uuid,
            'email'    => $email,
            'name'     => $name,
            'password' => '',
            'avatar'   => $avatar,
            'provider' => $provider,
            'status'   => 1,
            'role'     => 'user'
        ]);
        $new->save();
        self::loginUser($new->id);
        return ['status'=>true,'message'=>'Đăng nhập thành công.'];
    }

    public static function doLogin(string $email, string $password): array
    {
        $email = trim(mb_strtolower($email));
        $user  = self::where('email', $email)->first();

        if (!$user) return ['status'=>false,'message'=>'Email không tồn tại.'];
        if ((int)$user->status === 0) return ['status'=>false,'message'=>'Tài khoản chưa kích hoạt.'];
        if ((int)$user->status < 0) return ['status'=>false,'message'=>'Tài khoản bị khóa.'];

        if (!Hash::check($password, (string)$user->password)) {
            return ['status'=>false,'message'=>'Mật khẩu không chính xác.'];
        }
        self::loginUser($user->id);
        return ['status'=>true,'message'=>'Đăng nhập thành công.'];
    }

    public static function registerUser(string $email, string $password, string $name): array
    {
        $email = trim(mb_strtolower($email));
        if (self::where('email', $email)->exists()) {
            return ['status'=>false,'message'=>'Email đã tồn tại.'];
        }

        $user = new self();
        $user->fill([
            'token'    => bin2hex(random_bytes(16)),
            'email'    => $email,
            'name'     => trim($name),
            'password' => Hash::make($password),
            'status'   => 0,
            'role'     => 'user',
        ]);
        $user->save();

        sendMail(
            $email,
            "Xác nhận đăng ký tài khoản",
            view('core::auth.email.register',['user'=>$user])->render()
        );

        return [
            'status'=>true,
            'message'=>'Đăng ký thành công. Vui lòng xác nhận email.',
            'user_id'=>$user->id
        ];
    }

    public static function sendLinkResetPassword(string $email): array
    {
        $email = trim(mb_strtolower($email));
        $user = self::where('email',$email)->first();
        if (!$user) return ['status'=>false,'message'=>'Email không tồn tại.'];
        if ((int)$user->status <= 0) return ['status'=>false,'message'=>'Tài khoản chưa kích hoạt hoặc bị khóa.'];

        $token = bin2hex(random_bytes(16));
        $expires = Carbon::now()->addMinutes(120)->getTimestamp();

        $user->reset_token = $token;
        $user->reset_expiration = $expires;
        $user->save();

        sendMail(
            $email,
            "Yêu cầu đặt lại mật khẩu",
            view('core::auth.email.reset-password', [
                'user'=>$user,
                'token'=>$token,
                'expiration'=>$expires
            ])->render()
        );

        return [
            'status'=>true,
            'message'=>'Đã gửi liên kết đặt lại mật khẩu (kiểm tra cả thư mục Spam).'
        ];
    }
}
