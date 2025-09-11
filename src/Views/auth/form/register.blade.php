<form action="{{route('auth.register')}}" method="POST" autocomplete="off" id="formData">
    {!! csrf_field() !!}
    <h3>Đăng kí tài khoản</h3>
    <div class="form-group position-relative clearfix">
        <label for="name" class="form-label text-start text-success d-block fw-bold">Họ và tên</label>
        <input id="name" name="name" type="text" class="form-control" placeholder="Nguyễn Văn A" value="{{$inputName}}" required>
        @if($errorName)<div class="d-block valid-feedback text-start text-danger">{!! $errorName !!}</div>@endif
    </div>
    <div class="form-group position-relative clearfix">
        <label for="email" class="form-label text-start text-success d-block fw-bold">Email Address</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="Email Address" value="{{$inputEmail}}"  required>
        @if($errorEmail)<div class="d-block valid-feedback text-start text-danger">{!! $errorEmail !!}</div>@endif
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group clearfix position-relative password-wrapper">
                <label for="password" class="form-label text-start text-success d-block fw-bold">Mật khẩu</label>
                <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" placeholder="Mật khẩu đăng nhập" required>
                @if($errorPassword)<div class="d-block valid-feedback text-start text-danger">{!! $errorPassword !!}</div>@endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group clearfix position-relative password-wrapper">
                <label for="re-password" class="form-label text-start text-success d-block fw-bold">Nhập lại mật khẩu</label>
                <input id="re-password" name="re_password" type="password" class="form-control" autocomplete="new-password" placeholder="Nhập lại mật khẩu" required>
            </div>
        </div>
    </div>

    <div class="form-group checkbox clearfix">
        <div class="clearfix float-start">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberme" checked="checked">
                <label class="form-check-label" for="rememberme">I agree to the terms of service</label>
            </div>
        </div>
    </div>
    <div class="form-group clearfix">
        <button type="submit" class="btn btn-primary btn-lg btn-theme">Register</button>
    </div>
    <div class="clearfix"></div>
    <div class="extra-login clearfix">
        <span>Or Login With</span>
    </div>
</form>