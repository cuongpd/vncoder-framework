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
    <link href="{{core_static('css/style.css')}}?v=1.0.2" rel="stylesheet" type="text/css" media="all" />
    <link href="{{core_static('css/style-preset.css')}}?v=1.0.2" rel="stylesheet" type="text/css" media="all" />
</head>
<body data-pc-theme="dark">
<div class="auth-main">
    <div class="auth-wrapper v3">
        <div class="auth-form">
            <div class="card my-5">
                <div class="card-body">
                    <div class="text-center">
                        <a href="{{url('/')}}"><img src="{{core_static('images/logo.png')}}" alt="" height="72"></a>
                        @if ($__message = session('__message'))
                            <div class="alert alert-borderless alert-warning text-center py-2 my-2" role="alert">
                                {{$__message}}
                            </div>
                        @endif
                    </div>
                    <div class="auth-form-inner mt-3">
                        <h4 class="text-center f-w-500 mb-3">Đăng nhập vào hệ thống</h4>
                        <form action="{{route('backend.login')}}" class="form-horizontal" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group mb-3">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required />
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" class="form-control" id="password-input" name="password" placeholder="Password" required />
                            </div>
                            <div class="d-flex mt-1 justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input input-primary" type="checkbox" id="rememberMe" checked="checked">
                                    <label class="form-check-label text-muted" for="rememberMe">Remember me?</label>
                                </div>
                                <h6 class="text-secondary f-w-400 mb-0"><a href="{{route('backend.reset_password')}}" class="text-muted">Forgot password?</a></h6>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
