<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CartonBox Guru | Login </title>
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" />

    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body>
    <section class="container-fluid p-0">
        @if (session('status'))
            <div class="alert alert-dismissible fade show toast-success" role="alert"
                style="position: fixed; top: 100px; right: 40px; z-index: 1050;">
                <div class="toast-content">
                    <div class="toast-icon">
                        <i class="fa-solid fa-check-circle" style="color: #16A34A"></i>
                    </div>
                    <span class="toast-text"> {!! nl2br(e(session('status'))) !!}</span>&nbsp;&nbsp;
                    <button class="toast-close-btn"data-bs-dismiss="alert" aria-label="Close">
                        <i class="fa-thin fa-xmark" style="color: #16A34A"></i>
                    </button>
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert  alert-dismissible fade show toast-danger" role="alert"
                style="position: fixed; top: 100px; right: 40px; z-index: 1050;">
                <div class="toast-content">
                    <div class="toast-icon">
                        <i class="fa-solid fa-triangle-exclamation" style="color: #EF4444"></i>
                    </div>
                    <span class="toast-text">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </span>&nbsp;&nbsp;
                    <button class="toast-close-btn"data-bs-dismiss="alert" aria-label="Close">
                        <i class="fa-solid fa-xmark" style="color: #EF4444"></i>
                    </button>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="alert  alert-dismissible fade show toast-danger" role="alert"
                style="position: fixed; top: 100px; right: 40px; z-index: 1050;">
                <div class="toast-content">
                    <div class="toast-icon">
                        <i class="fa-solid fa-triangle-exclamation" style="color: #EF4444"></i>
                    </div>
                    <span class="toast-text">
                        {{ session('error') }}
                    </span>&nbsp;&nbsp;
                    <button class="toast-close-btn"data-bs-dismiss="alert" aria-label="Close">
                        <i class="fa-solid fa-xmark" style="color: #EF4444"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="row m-0">
            <div
                class="col-md-6 col-12 d-flex flex-column justify-content-center align-items-center pt-5 cb_login text-center order-2 order-md-1">
                <div class="px-5 pt-5">
                    <h5 class="py-4 cb_auth_title">Login to your account</h5>
                    <p class="login-text">You're just one step away from securing your awesome purchase!
                        Sign up or log in now to complete your order effortlessly</p>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/images/bg_img1.webp') }}" alt="header_logo"
                        class="img-fluid cb_intro_img" />
                </div>
            </div>

            <div
                class="col-md-6 col-12 d-flex justify-content-center align-items-center cp_login_container order-1 order-md-2">
                <div class="d-flex flex-column justify-content-center px-lg-5 mx-lg-4 w-100">
                    <h3 class="cb_auth_title text-center my-4">Login/Register</h3>
                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3 email-container">
                            <input type="email" class="form-control" id="email" name="email" value=""
                                placeholder="Email" />
                            <span class="error text-danger" id="emailError"
                                style="display: none; font-size: 12px;"></span>
                        </div>
                        <div class="mb-3 password-container">
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    aria-label="password" placeholder="Password">
                                <span class="input-group-text" id="togglePassword"
                                    style="cursor: pointer; background:#fff;">
                                    <i class="fa fa-eye" id="eyeIconPassword"></i>
                                </span>
                            </div>
                            <span class="error text-danger" id="passwordError"
                                style="display: none; font-size: 12px;"></span>
                        </div>

                        <div class="mb-1">
                            <button type="submit" class="btn btn-light cb_li_txt login-btn w-100">Login</button>
                        </div>
                        <div class="d-flex justify-content-between text-center">
                            <a class="text-start" href="{{ url('forgot-password') }}"
                                style="color: #cd8245;font-size:12px;">Forgot your
                                password?</a>
                            <p class="text-end" style="font-size:12px;">Don't have an account? <span>
                                    <a href="register" style="color: #cd8245;font-size:12px;">Sign Up</a></span>
                            </p>
                        </div>
                        <div class="d-flex justify-content-center align-items-center mb-3 line-divider-container">
                            <hr class="line-divider" />
                            <span class="mx-2 line-divider-text" style="color: #A2A2A2">or</span>
                            <hr class="line-divider" />
                        </div>
                        <div class="mb-3 row text-center">
                            <div class="col-12 mb-2 mb-md-0">
                                <a href="{{ url('auth/google') }}" style="text-decoration: none">
                                    <button type="button" class="btn btn-light social-btn w-100 text-nowrap">
                                        <img src="{{ asset('assets/images/home/google.webp') }}" class="img-fluid "
                                            alt="google_logo" width="22px">
                                        &nbsp;&nbsp;<span style="font-size: small">Login with Google</span>
                                    </button>
                                </a>
                            </div>
                            {{-- <div class="col-12 col-md-6">
                                <a href="auth/facebook">
                                    <button type="button" class="btn btn-light social-btn w-100 text-nowrap">
                                        <img src="{{ asset('assets/images/home/facebook.webp') }}" class="img-fluid "
                                            alt="facebook_logo" width="22px">
                                        &nbsp;&nbsp;<span style="font-size: small">Login with Facebook</span>
                                    </button>
                                </a>
                            </div> --}}
                        </div>
                        <div class="text-center">
                            <p class="mb-4 mt-1" style="font-size:12px;">Don't have an account? &nbsp; <a
                                    href="{{ url('register') }}" style="color: #cd8245">Register</a></p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://kit.fontawesome.com/5b8838406b.js" crossorigin="anonymous"></script>

    <!-- ✅ Add jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            let cartnumber = localStorage.getItem('cartnumber') || null;
            $('#cart_number').val(cartnumber);

            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const eyeIcon = $('#eyeIconPassword');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text'); // Show password
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password'); // Hide password
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Form Validation
            $("#loginForm").on("submit", function(e) {
                e.preventDefault();

                let email = $("#email").val().trim();
                let password = $("#password").val().trim();
                let isValid = true;

                // Validate Email
                if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                    $("#emailError").text("Enter a valid email address.").show();
                    isValid = false;
                }

                // Validate Password
                if (password.length < 8) {
                    $("#passwordError").text("Password must be at least 8 characters.").show();
                    isValid = false;
                }

                if (!isValid) return;

                // Disable Button & Show Loading
                let submitButton = $("button[type='submit']");
                submitButton.prop("disabled", true).html(
                    `<span class="spinner-border spinner-border-sm me-2"></span> Logging in...`
                );

                this.submit();
            });

            $("#email").on("input", function() {
                $("#emailError").hide();
            });

            $("#password").on("input", function() {
                $("#passwordError").hide();
            });
        });
    </script>
</body>

</html>
