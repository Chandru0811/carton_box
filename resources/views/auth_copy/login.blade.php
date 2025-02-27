{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carton Box Guru | Login </title>
    <link rel="canonical" href="https://CartonBoxGuru.com/login" />
    <meta name="description" content="Carton Box Guru Shop Smart, Save Big!" />
    <link rel="icon" href="{{ asset('assets/images/home/favicon.ico') }}" />

    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body>
    <section class="container-fluid p-0">
        @if (session('status'))
            <div class="alert alert-dismissible fade show toast-success" role="alert"
                style="position: fixed; top: 70px; right: 40px; z-index: 1050;">
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
                style="position: fixed; top: 70px; right: 40px; z-index: 1050;">
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
                style="position: fixed; top: 70px; right: 40px; z-index: 1050;">
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

        <div class="row m-0 ">
            <div class="col-md-6 col-12 d-flex flex-column justify-content-center align-items-center pt-5 bg_login login-text-container text-center order-2 order-md-1"
                style="background: #ffcbde">
                <div class="px-5 pt-5">
                    <h5 class="py-4" style="color: #CC004D">Login to your account</h5>
                    <p class="login-text">You're just one step away from securing your awesome purchase!
                        Sign up or log in now to complete your order effortlessly</p>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/images/bg_img.svg') }}" alt="header_logo" class="img-fluid" />
                </div>
            </div>

            <div
                class="col-md-6 col-12 d-flex justify-content-center align-items-center login-container order-1 order-md-2">
                <div class="d-flex flex-column justify-content-center align-items-center w-100">
                    @if ($errors->has('msg'))
                        <div class="alert alert-danger">
                            {{ $errors->first('msg') }}
                        </div>
                    @endif
                    <h3 class="login-title text-center mb-4">Login/Register</h3>
                    <form id="loginForm" class="w-75" method="POST" action="{{ route('login') }}">
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
                            <button type="submit" class="btn btn-light login-btn w-100"
                                style="color: #fff; background:#FF0060">Login</button>
                        </div>
                        <div class="d-flex justify-content-between text-center">
                            <a href="{{ url('forgot-password') }}" style="color: #FF0060;font-size:12px;">Forgot your
                                password?</a>
                            <p style="font-size:12px;">Don't have an account? <span>
                                    <a href="register" style="color: #FF0060;font-size:12px;">Sign Up</a></span>
                            </p>
                        </div>
                        <div class="d-flex justify-content-center align-items-center mb-3 line-divider-container">
                            <hr class="line-divider" />
                            <span class="mx-2 line-divider-text" style="color: #A2A2A2">or</span>
                            <hr class="line-divider" />
                        </div>
                        <div class="mb-3 row">
                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <a href="{{ url('auth/google') }}" style="text-decoration: none">
                                    <button type="button" class="btn btn-light social-btn w-100">
                                        <img src="{{ asset('assets/images/home/google.webp') }}" class="img-fluid "
                                            alt="google_logo" width="22px">
                                        &nbsp;&nbsp;<span style="font-size: small">Login with Google</span>
                                    </button>
                                </a>
                            </div>
                            <div class="col-12 col-md-6">
                                <a href="auth/facebook">
                                    <button type="button" class="btn btn-light social-btn w-100 ">
                                        <img src="{{ asset('assets/images/home/facebook.webp') }}" class="img-fluid "
                                            alt="facebook_logo" width="22px">
                                        &nbsp;&nbsp;<span style="font-size: small">Login with Facebook</span>
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="mb-0">Don't have an account? &nbsp; <a href="{{ url('register') }}"
                                    style="color: #FF0060">Register</a></p>
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

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIconPassword');

            if (passwordField.type === 'password') {
                passwordField.type = 'text'; // Show password
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password'; // Hide password
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html> --}}
