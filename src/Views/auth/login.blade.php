<form action="{{route('auth.login')}}" class="form-horizontal" method="post">
    <?= csrf_field() ?>
    <div class="form-group mb-3">
        <input type="email" class="form-control" id="email" name="email" value="{{$inputEmail}}" placeholder="Email Address" required>
    </div>
    <div class="form-group mb-3 position-relative">
        <input type="password" class="form-control pe-5" id="password-input" name="password" value="{{$inputPassword}}" placeholder="Password" required>
        <span id="toggle-password" class="position-absolute end-0 top-50 translate-middle-y me-3"
              style="cursor:pointer; font-size: 1.2em;" onclick="togglePasswordVisibility(this)">
        👁️
    </span>
        <small id="passwordHelpBlock" class="form-text text-danger">
            Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
        </small>
    </div>
    <div class="d-flex mt-1 justify-content-between align-items-center">
        <div class="form-check">
            <input class="form-check-input input-danger" type="checkbox" id="rememberMe" name="rememberMe" checked disabled>
            <label class="form-check-label text-white" for="rememberMe">Remember me?</label>
        </div>
        <h6 class="text-secondary f-w-400 mb-0"><a href="{{route('auth.reset-password')}}" class="text-muted">Forgot password?</a></h6>
    </div>
    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary">Login</button>
    </div>
    <div class="d-flex justify-content-between align-items-end mt-4">
        <h6 class="f-w-500 mb-0">Don't have an Account?</h6>
        <a href="{{route('auth.register')}}" class="link-primary">Create Account</a>
    </div>
</form>