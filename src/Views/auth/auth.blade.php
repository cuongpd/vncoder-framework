<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <link rel="icon" type="image/png" href="{{core_static('images/favicon.png')}}">
    <title>Đăng nhập vào hệ thống - {{getSiteConfig('name')}}</title>
    <meta name="robots" content="nofollow"/>
    <meta name="copyright" content="2021 by VnCoder CMS"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{url('/')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="{{core_static('libraries/fontawesome/css/fontawesome.min.css')}}?v=1.2" rel="stylesheet" type="text/css" />
    <link href="{{core_static('css/style.css')}}?v=1.2" rel="stylesheet" type="text/css" />
    <link href="{{core_static('libraries/vncoder/core.min.css')}}?v=1.2" rel="stylesheet" type="text/css" />
    <link href="{{core_static('auth/css/auth.min.css')}}?v=1.0" rel="stylesheet" type="text/css" />
    <script>const BASE_URL = "{{BASE_URL}}", CSRF_TOKEN = "{{csrf_token()}}", TIME_NOW = {{TIME_NOW}}; {!! $authConfig['firebase_config'] !!}</script>
    <script type="text/javascript" src="{{core_static('libraries/jquery/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{core_static('libraries/vncoder/core.js')}}"></script>
    <script src="https://www.gstatic.com/firebasejs/{{$authConfig['firebase_version']}}/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/{{$authConfig['firebase_version']}}/firebase-auth-compat.js"></script>
</head>
<body>
<div class="mega-form">
    <div class="container-fluid">
        <div class="row login-box">
            <div class="col-lg-6 align-self-center pad-0 form-section">
                <div class="form-inner">
                    <div class="logo2 p-b-20">
                        <a href="{{url('/')}}"><img src="{{getSiteConfig('logo')}}" alt="{{getSiteConfig('name')}}" height="72"></a>
                    </div>
                    @if ($__message = session('__message'))
                        <div class="alert alert-borderless alert-warning text-center py-2 my-2" role="alert">
                            {{$__message}}
                        </div>
                    @endif
                    @includeIf($__formActionView)
                    <div class="clearfix"></div>
                    <div class="social-list clearfix">
                        @foreach($authConfig['firebase_sign_in_providers'] as $provider)
                            <div class="icon {{$provider}}" onclick="cAuth.login('{{$provider}}')">
                                <div class="tooltip">{{ucfirst($provider)}}</div>
                                <span><i class="fa-brands fa-{{$provider}}"></i></span>
                            </div>
                        @endforeach
                    </div>
                    @if($formAction == 'login')
                        <p>Bạn chưa có tài khoản? <a href="{{route('auth.register')}}"> Đăng kí tài khoản</a></p>
                    @else
                        <p>Đã đăng kí thành viên? <a href="{{route('auth.login')}}"> Đăng nhập ngay</a></p>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 pad-0 none-992 bg-img">
                <div class="lines">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
                <div class="info">
                    <div class="animated-text">{!! $authConfig['welcome_text'] ?? '' !!}</div>
                    <p style="font-size: 16px;">{{ $authConfig['info_text'] ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{core_static('js/bootstrap.min.js')}}?v=5.3.2"></script>
<script type="text/javascript" src="{{core_static('auth/js/auth.min.js')}}?v=1.2"></script>
</body>
</html>
