    <!-- Footer Start  -->
    <section class="cb_footer py-5">
        <footer class="text-lg-start">
            <div class="container py-2">
                <div class="row justify-content-between">
                    <!-- Logo and Social Media Icons -->
                    <div class="col-md-3 text-md-start text-center mb-4 mb-md-0">
                        <div class="">
                            <p class="text-light fw-medium" style="font-size: 1.5rem">
                                <img src="{{ asset('assets/images/cb_logo4.png') }}" alt=""
                                    class="img-fluid cb_logo_footer"></a>
                            </p>
                        </div>
                        <div class="cb_social-icons">
                            <a href="#" class="text-decoration-none me-2"><i class="fab fa-facebook"
                                    style="font-size: 20px"></i></a>
                            <a href="#" class="text-decoration-none me-2"><i class="fab fa-instagram"
                                    style="font-size: 20px"></i></a>
                            <a href="#" class="text-decoration-none me-2"><i class="fab fa-linkedin"
                                    style="font-size: 20px"></i></a>
                            <a href="#" class="text-decoration-none me-2"><i class="fab fa-youtube"
                                    style="font-size: 20px"></i></a>
                            <a href="#" class="text-decoration-none me-2"><i class="fab fa-whatsapp"
                                    style="font-size: 20px"></i></a>
                            <a href="#" class="text-decoration-none"><i class="fab fa-x-twitter"
                                    style="font-size: 20px"></i></a>
                            <a href="#" class="text-decoration-none">&nbsp;<i class="fab fa-tiktok"
                                    style="font-size: 20px"></i></a>
                        </div>
                    </div>

                    <!-- Product Section -->
                    <div class="col-md-2 col-12 mb-4 text-md-start text-center">
                        <h6 class="text-uppercase fw-medium mb-3 text-light" style="font-size: 16px">
                            Categories
                        </h6>
                        <ul class="list-unstyled">
                            {{-- @foreach ($categoryGroups as $group)
                                <li class="mb-2">
                                    <a href="{{ route('deals.subcategorybased', ['country_code' => session('selected_country_code'), 'slug' => $category->slug]) }}"
                                        class="text-light text-decoration-none">{{ $group->name }}</a>
                                </li>
                            @endforeach --}}
                        </ul>
                    </div>

                    <!-- Company Section -->
                    <div class="col-md-2 col-12 mb-4 text-md-start text-center">
                        <h6 class="text-uppercase fw-medium mb-3 text-light" style="font-size: 16px">
                            Company
                        </h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="{{ url('/contactus') }}" class="text-light text-decoration-none">Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr />
            <div class="text-center text-light">
                <p class="d-flex justify-content-center cp_termsfooter px-1" style="font-weight: 300">
                    <a href="#" class="text-light text-decoration-none me-1">Terms and
                        Conditions | </a>
                    <a href="#" class="text-light text-decoration-none me-1">Privacy Policy |
                    </a>
                    2025 &copy; Copyright
                    ECS Cloud Infotech Pte. Ltd. All Rights Reserved.
                </p>
            </div>
        </footer>
    </section>
    <!-- Footer End  -->
