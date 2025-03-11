<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carton Box Guru | Reset Password</title>
    <link rel="canonical" href="https://CartonBoxGuru.com/reset-password" />
    <link rel="icon" href="{{ asset('assets/images/home/favicon.ico') }}" />

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
            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div class="toast align-items-center cb_toast_succ border-0 show" role="alert" aria-live="assertive"
                    aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fa-solid fa-check-circle me-2"></i> {!! nl2br(e(session('status'))) !!}
                        </div>
                        <button type="button" class="btn-close me-2" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div class="toast align-items-center cb_toast_err border-0 show" role="alert" aria-live="assertive"
                    aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close me-2" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div class="toast align-items-center cb_toast_err border-0 show" role="alert" aria-live="assertive"
                    aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close me-2" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row m-0">
            <div class="col-md-6 col-12 d-flex flex-column justify-content-center align-items-center pt-5 bg_login login-text-container text-center"
                style="background: #ffcbde">
                <div class="px-5 pt-5">
                    <h5 class="py-4" style="color: #CC004D">Reset Password to your account</h5>
                    <p class="login-text">You're just one step away from securing your awesome purchase!
                        Sign up or log in now to complete your order effortlessly</p>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/images/bg_img1.webp') }}" alt="header_logo" class="img-fluid" />
                </div>
            </div>
            <div class="col-md-6 col-12 d-flex justify-content-center align-items-center login-container">
                <div class="d-flex flex-column justify-content-center px-lg-5 mx-lg-4 w-100">
                    <h3 class="login-title text-center my-4">Reset Password</h3>
                    <form id="resetpasswordForm" class="w-75" method="POST" action="{{ route('password.store') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="mb-3 email-container">
                            {{-- <label class="form-label">Email Address</label> --}}
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $request->email) }}" readonly />
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
                        <div class="mb-3 password-container">
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" aria-label="password_confirmation"
                                    placeholder="Confirm Password">
                                <span class="input-group-text" id="toggleConfirmPassword"
                                    style="cursor: pointer; background:#fff;">
                                    <i class="fa fa-eye" id="eyeIconConfirmPassword"></i>
                                </span>
                            </div>
                            <span class="error text-danger" id="confirmpasswordError"
                                style="display: none; font-size: 12px;"></span>
                            <div id="passwordMatchError" class="error text-danger"
                                style="display: none; font-size: 12px;">
                                Passwords do not match
                            </div>
                        </div>

                        <div class="mb-3 mt-5 text-center">
                            <button type="submit" class="btn btn-light w-100"
                                style="background-color: #ff0060; color: #fff;" id="submitButton">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script> <!-- jQuery -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://kit.fontawesome.com/5b8838406b.js" crossorigin="anonymous"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script>
        document
            .getElementById("resetpasswordForm")
            .addEventListener("submit", function(event) {
                let formIsValid = true;

                const toggleError = (id, message = "") => {
                    const errorElement = document.getElementById(id);
                    if (message) {
                        errorElement.style.display = "block";
                        errorElement.innerText = message;
                    } else {
                        errorElement.style.display = "none";
                    }
                };

                const password = document.getElementById("password").value;
                const confirmPassword = document.getElementById("password_confirmation").value;


                if (!password) {
                    toggleError("passwordError", "Password is required.");
                    formIsValid = false;
                } else if (password.length < 8) {
                    toggleError("passwordError", "Password must be at least 8 characters long.");
                    formIsValid = false;
                } else {
                    toggleError("passwordError");
                }

                // Validate Confirm Password
                if (!confirmPassword) {
                    toggleError("confirmpasswordError", "Confirm Password is required.");
                    formIsValid = false;
                } else {
                    toggleError("confirmpasswordError");
                }

                // Check if Password and Confirm Password match
                if (password && confirmPassword && password !== confirmPassword) {
                    toggleError("passwordMatchError", "Passwords do not match.");
                    // Hide "Confirm Password is required" error when passwords do not match
                    toggleError("confirmpasswordError");
                    formIsValid = false;
                } else {
                    toggleError("passwordMatchError");
                }

                // If validation fails, prevent form submission
                if (!formIsValid) {
                    event.preventDefault();
                    return;
                }

                const submitButton = document.querySelector("button[type='submit']");
                submitButton.disabled = true;

                const loader = document.createElement('span');
                loader.className = 'custom-loader';
                submitButton.appendChild(loader);

                setTimeout(() => {
                    this.submit();
                }, 1000);
            });

        document
            .getElementById("password_confirmation")
            .addEventListener("input", function() {
                const password = document.getElementById("password").value;
                const confirmPassword = this.value;
                const passwordMatchError = document.getElementById("passwordMatchError");

                if (password !== confirmPassword) {
                    passwordMatchError.style.display = "block";
                    passwordMatchError.innerText = "Passwords do not match.";
                    // Hide "Confirm Password is required" error when passwords do not match
                    toggleError("confirmpasswordError");
                } else {
                    passwordMatchError.style.display = "none";
                }
            });

        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', () => {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            // Toggle password visibility
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePassword.querySelector('i').classList.toggle('fa-eye');
                togglePassword.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Toggle confirm password visibility
            toggleConfirmPassword.addEventListener('click', () => {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                toggleConfirmPassword.querySelector('i').classList.toggle('fa-eye');
                toggleConfirmPassword.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>

</html>
