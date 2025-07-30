<div id="otp-modal" class="modal fade oxyy-login-register" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-body p-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 me-sm-n4 mt-sm-n4" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="row g-0">
                    <div class="col-lg-5 bg-primary rounded-start">
                        <div class="row g-0 h-100">
                            <div class="col-10 col-lg-9 d-flex flex-column mx-auto">
                                <h3 class="text-white mt-5 mb-4">Login</h3>
                                <p class="text-4 text-light lh-base mb-4">To keep connected with us please login with your personal info.</p>
                                <div class="mt-auto mb-4"><img class="img-fluid" src="images/login-vector.png" alt="Oxyy"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 d-flex align-items-center bg-white rounded-end">
                        <div class="container my-auto py-5">
                            <div class="row">
                                <div class="col-11 col-lg-10 mx-auto">
                                    <h3 class="text-center text-6 mb-4">Two-Step Verification</h3>
                                    <p class="text-center"><img class="img-fluid" src="images/otp-icon.png" alt="verification"></p>
                                    <p class="text-muted text-center">Please enter the OTP (one time password) to verify your account. A Code has been sent to <span class="text-dark text-4">+1*******179</span></p>
                                    <form id="otp-screen" class="form-border" method="post">
                                        <div class="row g-3">
                                            <div class="col">
                                                <input type="text" class="form-control border-2 text-center text-6 px-0 py-2" maxlength="1" required="" autocomplete="off">
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control border-2 text-center text-6 px-0 py-2" maxlength="1" required="" autocomplete="off">
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control border-2 text-center text-6 px-0 py-2" maxlength="1" required="" autocomplete="off">
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control border-2 text-center text-6 px-0 py-2" maxlength="1" required="" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="d-grid my-4">
                                            <button class="btn btn-primary shadow-none" type="submit">Verify</button>
                                        </div>
                                    </form>
                                    <p class="text-2 text-center">Not received your code? <a href="#">Resend code</a></p>
                                    <p class="text-2 text-center mb-0"><a href="#">Call me</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- OTP Form End -->
                </div>
            </div>
        </div>
    </div>
</div>