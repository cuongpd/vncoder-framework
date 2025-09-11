<form action="{{route('auth.reset-password')}}" method="POST" autocomplete="off" id="formData">
    {!! csrf_field() !!}
    <h3>Tìm lại mật khẩu</h3>
    <div class="form-group position-relative clearfix">
        <label for="email" class="form-label text-start text-success d-block fw-bold">Email Address</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="Email Address" value="{{$inputEmail}}" required>
        @if($errorEmail)<div class="d-block valid-feedback text-start text-danger">{!! $errorEmail !!}</div>@endif
    </div>
    <div class="form-group clearfix">
        <button type="submit" class="btn btn-primary btn-lg btn-theme">Gửi Email cho tôi</button>
    </div>
    <div class="clearfix"></div>
    <div class="extra-login clearfix">
        <span>Or Login With</span>
    </div>
</form>