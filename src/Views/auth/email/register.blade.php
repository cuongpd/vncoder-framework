<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chào mừng đến với {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; color: #333; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 6px; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,.05); }
        h1 { font-size: 24px; margin-bottom: 20px; color: #2d89ef; }
        p { line-height: 1.6; }
        .footer { margin-top: 40px; font-size: 13px; color: #999; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Chào mừng, {{ $user->name ?? 'bạn' }}</h1>

    <p>Tài khoản của bạn đã được tạo thành công tại <strong>{{ config('app.name') }}</strong>.</p>
    <p>Bây giờ bạn có thể đăng nhập và bắt đầu khám phá các tính năng tuyệt vời mà chúng tôi cung cấp.</p>

    <p>Nếu bạn có bất kỳ câu hỏi nào, hãy phản hồi email này để được hỗ trợ.</p>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Mọi quyền được bảo lưu.
    </div>
</div>
</body>
</html>
