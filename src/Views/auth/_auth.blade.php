<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <link rel="icon" type="image/png" href="{{core_static('images/favicon.png')}}">
    <title>{{$__title}} - {{getSiteConfig('name')}}</title>
    <meta name="robots" content="nofollow"/>
    <meta name="copyright" content="2021 by VnCoder CMS"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{url('/')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="{{core_static('css/style.css')}}?v=1.2" rel="stylesheet" type="text/css" />
    <link href="{{core_static('css/style-preset.css')}}?v=1.2" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="{{core_static('js/jquery.min.js')}}"></script>
</head>
<body data-pc-preset="preset-1" data-pc-theme="dark">
<div class="auth-main">
    <div class="auth-wrapper v1">
        <div class="auth-form">
            <div class="card my-5">
                <div class="card-body">
                    <div class="text-center">
                        <a href="{{url('/')}}"><img src="{{getSiteConfig('logo')}}" alt="{{getSiteConfig('name')}}" height="72"></a>
                        @if ($__message = session('__message'))
                            <div class="alert alert-borderless alert-warning text-center py-2 my-2" role="alert">
                                {{$__message}}
                            </div>
                        @endif
                    </div>
                    <div class="auth-form-inner mt-3">
                        <h4 class="text-center f-w-500 mb-3">{{$__title}}</h4>
                        @includeIf($__views)
                    </div>
                    <div class="saprator my-3"><span>OR</span></div>
                    <div class="d-grid">
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{route('auth.provider', ['provider' => 'google'])}}" class="btn btn-light-success bg-light d-inline-flex px-2">
                                <img src="{{core_static('images/auth/google.svg')}}" alt="img"> <span>with Google</span>
                            </a>
                            <a href="{{route('auth.provider', ['provider' => 'facebook'])}}" class="btn btn-light-success bg-light d-inline-flex px-2">
                                <img src="{{core_static('images/auth/facebook.svg')}}" alt="img"> <span>with Facebook</span>
                            </a>
                            <a href="{{route('auth.provider', ['provider' => 'facebook'])}}" class="btn btn-light-success bg-light d-inline-flex px-2">
                                <img src="{{core_static('images/auth/facebook.svg')}}" alt="img"> <span>with Facebook</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{core_static('js/bootstrap.min.js')}}?v=5.3.2"></script>
</body>
</html>
