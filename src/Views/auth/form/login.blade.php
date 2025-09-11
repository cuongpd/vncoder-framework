<form action="{{route('auth.login')}}" method="POST" autocomplete="off" id="formData">
    {!! csrf_field() !!}
    <h3>Đăng nhập vào hệ thống</h3>
    <div class="form-group position-relative clearfix">
        <label for="email" class="form-label text-start text-success d-block fw-bold">Email Address</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="Email Address" value="{{$inputEmail}}">
        @if($errorEmail)<div class="d-block valid-feedback text-start text-danger">{!! $errorEmail !!}</div>@endif
    </div>
    <div class="form-group position-relative clearfix">
        <label for="password" class="form-label text-start text-success d-block fw-bold">Mật khẩu</label>
        <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" placeholder="Mật khẩu đăng nhập">
        @if($errorPassword)<div class="d-block valid-feedback text-start text-danger">{!! $errorPassword !!}</div>@endif
    </div>
    <div class="checkbox form-group clearfix">
        <div class="form-check float-start">
            <input class="form-check-input" type="checkbox" id="rememberme" checked="checked">
            <label class="form-check-label" for="rememberme">Ghi nhớ</label>
        </div>
        <p class="link-light float-end forgot-password text-danger"><a href="{{route('auth.reset-password')}}">Quên mật khẩu?</a></p>
    </div>
    <div class="form-group clearfix">
        <button type="submit" class="btn btn-primary btn-lg btn-theme">Đăng nhập</button>
    </div>
    <div class="clearfix"></div>
    <div class="extra-login clearfix">
        <span>Or Login With</span>
    </div>
</form>