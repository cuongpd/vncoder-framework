<form action="{{route('auth.register')}}" class="needs-validation" method="post" id="register-form">
    {!! csrf_field() !!}
    <div class="mb-3">
        <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control form-control-lg border" id="name" placeholder="Nhập tên của bạn" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control form-control-lg border" id="email" placeholder="Enter email address" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="password-input">Password</label>
        <div class="position-relative auth-pass-inputgroup">
            <input type="password" name="password" class="form-control form-control-lg border pe-5 password-input" onpaste="return false" placeholder="Enter password" id="password-input" aria-describedby="passwordInput" required>
            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon">
                <i class="ri-eye-fill align-middle"></i>
            </button>
        </div>
    </div>
    <div class="mb-4">
        <p class="mb-0 fs-12 text-muted fst-italic">Lựa chọn đăng kí, bạn đã chấp nhận với <a href="javascript:void(0);" onclick="showPrivacyModal();" class="text-primary text-decoration-underline fst-normal fw-medium">điều khoản, chính sách</a> tại website {{getSiteConfig('name')}}</p>
    </div>
    <div class="mt-4">
        <button class="btn btn-success w-100" type="submit">Sign Up</button>
    </div>
</form>
<div class="modal fade" id="modal-info" tabindex="-1" role="dialog" aria-labelledby="modalInfoData" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content btn-light-success bg-success">
            <div class="modal-body">
                {!! getSiteConfig('privacy') !!}
            </div>
        </div>
    </div>
</div>

<script>
    function showPrivacyModal() {
        $('#modal-info').modal('show');
    }
</script>