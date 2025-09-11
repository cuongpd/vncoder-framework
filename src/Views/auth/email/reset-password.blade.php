<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Yêu cầu đặt lại mật khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 6px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,.05);
        }
        h1 {
            font-size: 22px;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #2d89ef;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Xin chào {{ $user->name ?? 'bạn' }},</h1>
    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
    <p>Nhấn vào nút bên dưới để tạo mật khẩu mới:</p>

    <a href="{{ url('auth/reset-password?token=' . urlencode($user->reset_token ?? '')) }}" class="btn">
        Đặt lại mật khẩu
    </a>

    <p>Nếu bạn không yêu cầu thao tác này, vui lòng bỏ qua email này.</p>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Mọi quyền được bảo lưu.
    </div>
</div>
</body>
</html>
