<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @section('head_links')
        <title>CartonBoxGuru | Contact Us</title>
        <meta name="description" content="Privacy Policy for CartonBoxGuru." />
        <link rel="canonical" href="https://CartonBoxGuru/contactus" />
        <link rel="icon" href="{{ asset('assets/images/home/favicon.ico') }}" />

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">

        <!-- Vendor CSS Files -->
        {{-- Boostrap css  --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        {{-- Custom Css  --}}
        <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    @show
</head>

<body>
    <section class="home-section">
        @include('nav.header')
        <div class="home-content container">
            <div class="main-container">
                <!-- Form Overlay -->
                <div class="form-overlay my-5 px-2">
                    <div class="container card shadow py-5">
                        <div class="row">
                            <div class="col-md-6 col-12 px-5">
                                <h1 class="contact-heading mb-4">Contact Us</h1>
                                <br />
                                <form id="contactForm">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-12">
                                            <div class="input-group mb-4">
                                                <input type="text"
                                                    class="form-control contact-icon-group input-text-color"
                                                    style="box-shadow: none" placeholder="First Name *" id="first_name"
                                                    name="first_name" aria-label="First Name *"
                                                    aria-describedby="basic-addon1" required />
                                                <div class="error mt-0 mb-3 col-12"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="input-group mb-4">
                                                <input type="text"
                                                    class="form-control contact-icon-group input-text-color"
                                                    style="box-shadow: none" placeholder="Last Name" id="last_name"
                                                    name="last_name" aria-label="Last Name "
                                                    aria-describedby="basic-addon1" />
                                                <div class="error mt-0 mb-3 col-12"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group mb-4">
                                        <input type="text" class="form-control contact-icon-group input-text-color"
                                            style="box-shadow: none" placeholder="Email *" id="email" name="email"
                                            aria-label="Email *" aria-describedby="basic-addon1" required />
                                        <div class="error mt-0 mb-3 col-12"></div>
                                    </div>
                                    <div class="input-group mb-4">
                                        <input type="text" class="form-control contact-icon-group input-text-color"
                                            style="box-shadow: none" placeholder="Phone *" id="mobile" name="mobile"
                                            aria-label="Phone *" aria-describedby="basic-addon1" required />
                                        <div class="error mt-0 mb-3 col-12"></div>
                                    </div>
                                    <div class="mb-5">
                                        <label for="description_info" class="form-label text-muted">Leave your
                                            Message</label>
                                        <textarea class="form-control input-text-color" style="box-shadow: none" id="description_info" name="description_info"
                                            rows="4"></textarea>
                                        <div class="error mt-0 mb-3 col-12"></div>
                                    </div>
                                    <button type="submit" style="width: 100%"
                                        class="btn btn-button py-2 mb-5 contact-button">
                                        Send Message
                                    </button>
                                </form>
                            </div>
                            <div class="borderline col-md-6 col-12 px-5">
                                <h2 class="contact-heading mb-4">Get In Touch</h2>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link contact-nav  active" id="india-tab"
                                            onclick="showAddress('india')" href="javascript:void(0);">
                                            <img src="https://ecsaio.com/assets/images/contactus/Flag_of_Singapore.webp"
                                                alt="india_flag" class="shadow  m-2 mx-4"
                                                style="width: 40px; height: 30px" />
                                        </a>
                                    </li>
                                </ul>

                                <!-- Address for India -->
                                <div id="india" class="address-content active-address pt-3">
                                    <p>
                                        The Alexcier, 237 Alexandra Road, #04-10, <br>
                                        Singapore-159929.
                                    </p>
                                </div>

                                <a id="phone-link" href="tel:8894 1306"
                                    class="contact-subheading text-muted text-decoration-none d-flex" target="_blank"
                                    rel="noopener noreferrer">
                                    <h6 class="text-muted mt-4">
                                        <i class="fa-solid fa-mobile icon-color"></i>&nbsp; Phone
                                        <span>
                                            <h3 class="text-dark"><b id="phone-number">
                                                    +65 8894 1306</b></h3>
                                        </span>
                                    </h6>
                                </a>
                                <a href="mailto:info@ecsaio.com" class="text-decoration-none text-dark"
                                    target="_blank" rel="noopener noreferrer">
                                    <h6 class="text-muted mt-4 contact-subheading">
                                        <i class="fa-solid fa-envelope icon-color"></i>&nbsp; Email
                                    </h6>
                                    <p>info@ecsaio.com</p>
                                </a>
                                <br /><br />
                                <div class="d-flex flex-wrap my-2 socialicons">
                                    <a href="https://www.linkedin.com/in/deals-machi-2b4944331"
                                        class="p-1 text-decoration-none text-white" target="_blank"
                                        rel="noopener noreferrer">
                                        <span
                                            class="mediaIconBgIcon text-center d-flex justify-content-center align-items-center">
                                            <i class="fa-brands soc_icons text-center fa-linkedin-in"></i>
                                        </span>
                                    </a>
                                    <a href="https://www.facebook.com/profile.php?id=61566743978973"
                                        class="p-1 text-decoration-none text-white" target="_blank"
                                        rel="noopener noreferrer">
                                        <span class="mediaIconBgIcon d-flex justify-content-center align-items-center">
                                            <i class="fa-brands soc_icons text-center fa-facebook-f"></i>
                                        </span>
                                    </a>
                                    <a href="https://www.instagram.com/dealsmachi/"
                                        class="p-1 text-decoration-none text-white" target="_blank"
                                        rel="noopener noreferrer">
                                        <span class="mediaIconBgIcon d-flex justify-content-center align-items-center">
                                            <i class="fa-brands soc_icons text-center fa-instagram"></i>
                                        </span>
                                    </a>
                                    <a href="https://www.youtube.com/channel/UCAyH2wQ2srJE8WqvII8JNrQ"
                                        class="p-1 text-decoration-none text-white" target="_blank"
                                        rel="noopener noreferrer">
                                        <span class="mediaIconBgIcon d-flex justify-content-center align-items-center">
                                            <i class="fa-brands soc_icons text-center fa-youtube"></i>
                                        </span>
                                    </a>
                                    <a href="https://x.com/dealsmachi_in" class="p-1 text-decoration-none text-white"
                                        target="_blank" rel="noopener noreferrer">
                                        <span class="mediaIconBgIcon d-flex justify-content-center align-items-center">
                                            <i class="fa-brands soc_icons text-center fa-x-twitter"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>


                    <!-- Success Modal -->
                    <div class="modal fade text-center" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content modalContent">
                            <div class="modal-header d-flex align-content-center justify-content-between"
                                style="background: green; color: white;">
                                <h5 class="modal-title" id="successModalLabel" style="font-size: 28px;">
                                    That's all required!
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    style="color: white;"></button>
                            </div>
                            <div class="modal-body pt-0">
                                <!-- Success Icon -->
                                <i class="fa-solid fa-circle-check my-5" style="color: #28a745; font-size: 80px;"></i>
                                <p class="mb-0 SuccessMagnetSubHeading pb-4">
                                    We will get back to you soon!
                                </p>
    
                                <!-- Back to Home Button -->
                                <a href="/">
                                    <button type="button" class="btn successMagnetButton">Back to Home</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

        @include('nav.footer')
    </section>
    <!-- ======= Script  ======= -->

    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://kit.fontawesome.com/5b8838406b.js" crossorigin="anonymous"></script>

    <!-- Page Scripts -->
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
</body>

</html>
