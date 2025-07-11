<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Chào mừng bạn đến với website {{getSiteConfig('name')}}</title>
    <style type="text/css">
        a:hover {text-decoration: underline !important;}
    </style>
</head>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
<table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8">
    <tr>
        <td>
            <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                   align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="height:80px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:center;">
                        <a href="{{BASE_URL}}" title="{{getSiteConfig('name')}}" target="_blank">
                            <img height="60" src="{{getSiteConfig('logo')}}" title="{{getSiteConfig('name')}}" alt="{{getSiteConfig('name')}}">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="height:20px;">&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                               style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                            <tr>
                                <td style="height:40px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="padding:0 35px;">
                                    <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:25px;font-family:'Rubik',sans-serif;">Kích hoạt tài khoản</h1>
                                    <span style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                    <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">Xin chào <strong>{{$__name}}</strong>,</p>
                                    <p style="color:#455056; font-size:14px;line-height:24px; margin:0;">Bạn hoặc ai đó đã đăng kí tài khoản trên hệ thống <strong>{{getSiteConfig('name')}}</strong> với địa chỉ email của bạn ({{$__email}})</p>
                                    <p style="color:#455056; font-size:14px;line-height:24px; margin:0;">Để kích hoạt tài khoản và tiếp tục sử dụng website vui lòng kích hoạt tài khoản bằng liên kết bên dưới.</p>
                                    <a href="{{$__linkActive}}" style="background:#20e277;text-decoration:none !important; font-weight:500; margin-top:35px;margin-bottom:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;">Kích hoạt tài khoản</a>
                                    <p style="color:#455056; font-size:14px;line-height:24px; margin:0;">Nếu bạn không bấm được vào liên kết vui lòng copy liên kết và mở trong trình duyệt:</p>
                                    <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">{{$__linkActive}}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="height:40px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                <tr>
                    <td style="height:20px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:center;">
                        <p style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;">&copy; <strong><?=getSiteConfig('name')?></strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="height:80px;">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>

</html>
