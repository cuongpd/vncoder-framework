<div id="login-modal" class="modal fade oxyy-login-register" aria-hidden="true" style="display: none;">
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
                                    <h3 class="text-center text-4 mb-4">Login with Social Profile</h3>
                                    <div class="d-flex flex-column align-items-center mb-3">
                                        <ul class="social-icons social-icons-circle">
                                            <li class="social-icons-facebook"><a href="#" data-bs-toggle="tooltip" data-bs-original-title="Log In with Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                            <li class="social-icons-twitter"><a href="#" data-bs-toggle="tooltip" data-bs-original-title="Log In with Twitter"><i class="fab fa-twitter"></i></a></li>
                                            <li class="social-icons-google"><a href="#" data-bs-toggle="tooltip" data-bs-original-title="Log In with Google"><i class="fab fa-google"></i></a></li>
                                            <li class="social-icons-linkedin"><a href="#" data-bs-toggle="tooltip" data-bs-original-title="Log In with Linkedin"><i class="fab fa-linkedin-in"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="d-flex align-items-center my-3">
                                        <hr class="flex-grow-1">
                                        <span class="mx-2 text-2 text-muted">Or use your email account</span>
                                        <hr class="flex-grow-1">
                                    </div>
                                    <form id="loginForm" class="form-border" method="post">
                                        <div class="mb-3">
                                            <input type="email" class="form-control border-2" id="emailAddress" required="" placeholder="Enter Email">
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" class="form-control border-2" id="loginPassword" required="" placeholder="Enter Password">
                                        </div>
                                        <div class="row my-4">
                                            <div class="col">
                                                <div class="form-check">
                                                    <input id="remember-me" name="remember" class="form-check-input" type="checkbox">
                                                    <label class="form-check-label text-2" for="remember-me">Remember Me</label>
                                                </div>
                                            </div>
                                            <div class="col text-2 text-end"><a href="" data-bs-toggle="modal" data-bs-target="#forgot-password-modal" data-bs-dismiss="modal">Forgot Password ?</a></div>
                                        </div>
                                        <div class="d-grid my-4">
                                            <button class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                    </form>
                                    <p class="text-2 text-center mb-0">New to Oxyy? <a href="" data-bs-toggle="modal" data-bs-target="#register-modal" data-bs-dismiss="modal">Create an account</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>