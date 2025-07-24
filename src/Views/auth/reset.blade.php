<form action="{{route('auth.reset-password')}}" class="form-horizontal" method="post">
    {!! csrf_field() !!}

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" id="email" placeholder="Enter Email" required="required">
    </div>
    <div class="text-center">
        <button class="btn btn-success" type="submit"><i class="bx bx-reset"></i>Reset Password</button>
    </div>

    <div class="d-flex mt-1 justify-content-between align-items-center">
        <div class="form-check">
            <input class="form-check-input input-primary" type="checkbox" id="rememberMe" checked readonly>
            <label class="form-check-label text-muted" for="rememberMe">Remember me?</label>
        </div>
        <h6 class="text-secondary f-w-400 mb-0"><a href="{{route('auth.reset-password')}}" class="text-muted">Forgot password?</a></h6>
    </div>
    <div class="mb-3">
        <h6 class="f-w-500 mb-0">Remembered your password?</h6>
        <a href="{{route('auth.login')}}" class="link-primary">Login</a>
    </div>
</form>




