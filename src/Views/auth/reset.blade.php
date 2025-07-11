<form action="{{route('auth.reset')}}" class="form-horizontal" method="post">
    {!! csrf_field() !!}
    <div class="alert alert-borderless alert-warning text-center mb-2 mx-2" role="alert">
        <?=$__error['email'] ?? "Enter your email and instructions will be sent to you!" ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" id="email" placeholder="Enter Email" required="required">
    </div>
    <div class="text-center mt-4">
        <button class="btn btn-danger" type="submit"><i class="bx bx-reset"></i>Reset Password</button>
    </div>
</form>


@push('footer')
    <p class="mb-0">Có vẻ như tôi vẫn nhớ mật khẩu của mình... <a href="{{route('auth.login')}}" class="fw-semibold text-primary text-decoration-underline"> Đăng nhập ngay </a> </p>
@endpush
