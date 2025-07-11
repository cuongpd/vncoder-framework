<form action="{{route('auth.login')}}" class="form-horizontal" method="post">
    <?= csrf_field() ?>
    <div class="form-group mb-3">
        <input type="email" class="form-control" id="email" name="email"  placeholder="Email Address" required>
    </div>
    <div class="form-group mb-3">
        <input type="password" class="form-control" id="password-input" name="password" placeholder="Password" required>
    </div>
    <div class="d-flex mt-1 justify-content-between align-items-center">
        <div class="form-check">
            <input class="form-check-input input-primary" type="checkbox" id="rememberMe" checked="">
            <label class="form-check-label text-muted" for="rememberMe">Remember me?</label>
        </div>
        <h6 class="text-secondary f-w-400 mb-0"><a href="{{route('auth.reset')}}" class="text-muted">Forgot password?</a></h6>
    </div>
    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary">Login</button>
    </div>
    <div class="d-flex justify-content-between align-items-end mt-4">
        <h6 class="f-w-500 mb-0">Don't have an Account?</h6>
        <a href="{{route('auth.register')}}" class="link-primary">Create Account</a>
    </div>
</form>