    <!-- Header Start -->
    @php
        $selectedAddressId = session('selectedId');
        $default_address = $addresses->firstWhere('default', true) ?? null; // Add fallback to null
        $categoryGroups = $categoryGroups ?? collect();
    @endphp

    <nav class="navbar navbar-expand-lg bg-body-tertiary cb_bg_header">
        <div class="container-fluid">
            <a class="navbar-brand active mx-lg-3" href="/"><img src="{{ asset('assets/images/cb_logo1.png') }}"
                    alt="" class="img-fluid cb_logo py-2"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-lg-auto gap-lg-3 mx-lg-5 mb-2 mb-lg-0">
                    @if (isset($categoryGroups) && $categoryGroups->isNotEmpty())
                        @foreach ($categoryGroups as $category)
                            <li class="nav-item dropdown">
                                <a class="nav-link cb_nav_items dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $category->name }}
                                </a>
                                <ul class="dropdown-menu cb_sub_menu">
                                    @if ($category->categories->isNotEmpty())
                                        @foreach ($category->categories as $subcategory)
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ url(($country_code ?? 'default') . '/categories/' . $subcategory->slug) }}">
                                                    {{ $subcategory->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @else
                                        <li><a class="dropdown-item" href="#">No Subcategories</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endforeach
                    @endif
                </ul>
                <form action="{{ url(request()->route('country_code') . '/search') }}" method="GET"
                    class="d-flex pe-lg-5" role="search">
                    <div class="position-relative cb_serch_header">
                        <input id="searchInput" class="form-control ps-5 me-2 text-dark" name="q"
                            value="{{ request()->input('q') }}" type="text" placeholder="Search" aria-label="Search">
                        <i class="fa fa-search position-absolute text-dark"
                            style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                    </div>
                </form>
                {{-- whatsapp icon  --}}
                <div class="d-flex justify-content-center align-items-center text-center gap-3 pe-lg-3 cp_icons_head">
                    <a href="https://wa.me/6588941306" target="blank" class="cb_social_media_icons cb_title"
                        data-title="Whatsapp">
                        <i class="fab fa-whatsapp" style="font-size: 28px"></i>
                    </a>
                    <button class="cb_social_media_icons cb_cart_dd" data-title="Shopping Cart" id="cartButton">
                        <i class="fa-thin fa-cart-shopping cart-screen" style="font-size: 26px"></i>
                        <span id="cart-count" class="total-counts translate-middle d-xl-block"
                            style="position: absolute; top: 0px; right: -16px;">
                        </span>
                        <div class="cb_cart_dropdown" style="left: 10px; transform: translate(-85%, 0);">
                            @include('nav.cartdropdown')
                        </div>
                    </button>
                    @auth
                        <div class="user_dorpdown dropdown">
                            <a href="#" class="text-decoration-none" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <span class="">
                                    <i class="fa-regular fa-circle-user fa-xl cb_social_media_icons cb_title"></i>
                                </span>
                            </a>
                            <div class="cb_cart_dropdown shadow-lg border-0"
                                style="left: 45%; top:35px; transform: translate(-85%, 0);">
                                <div class="dropdown_child text-start p-2">
                                    <!-- User Dropdown Items -->
                                    <div class="d-flex justify-content-start align-items-start mb-2">
                                        <a class="dropdown-item user_list" href="#" data-bs-toggle="modal"
                                            data-bs-target="#profileModal">
                                            <i class="user_list_icon fa-light fa-user"></i>
                                            &nbsp;&nbsp;&nbsp;Profile
                                        </a>
                                    </div>
                                    <div class="d-flex justify-content-start align-items-start mb-2">
                                        <a class="dropdown-item user_list" href="{{ url('orders') }}"><i
                                                class="user_list_icon fa-light fa-bags-shopping"></i>
                                            &nbsp;&nbsp;Orders</a>
                                    </div>
                                    <div class="d-flex justify-content-start align-items-start mb-2">
                                        <a class="dropdown-item user_list" href="{{ url('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                                class="user_list_icon fa-light fa-power-off"></i>
                                            &nbsp;&nbsp;&nbsp;Log Out</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Hidden logout form -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <a href="{{ url('login') }}" class="cb_social_media_icons cb_title">
                            <i class="fa-regular fa-circle-user fa-xl" style="font-size: 26px"></i>
                        </a>
                    @endauth
                </div>

            </div>
        </div>

        <!-- Modal inside the Dropdown -->
        <div class="dropdown">
            <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal_border">
                        <div class="modal-header">
                            <h5 class="modal-title" id="profileModalLabel">Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card" style="border: none">
                                <div class="d-flex align-items-center">
                                    <div class="col-2">
                                        <img src="{{ asset('assets/images/home/user.jpg') }}" alt="Profile Picture"
                                            width="50" height="50" class="rounded-circle" />
                                    </div>
                                    <div class="col-4 text-start">
                                        <h6 class="mt-2">{{ $user->name ?? '' }}</h6>
                                    </div>
                                </div>
                                <hr />
                                <p><strong>Email :</strong> {{ $user->email ?? '' }}</p>
                                <p><strong>Phone :</strong>
                                    {{ $default_address && $default_address->phone ? '(+65) ' . $default_address->phone : '--' }}
                                </p>
                                <hr />
                                <div class="d-flex justify-content-between align-items-center defaultAddress">
                                    <h6 class="fw-bold">Delivery Addresses</h6>
                                    @if ($default_address)
                                        <span class="badge badge_infos py-1" data-bs-toggle="modal"
                                            data-bs-target="#myAddressModal">Change</span>
                                    @else
                                        <button type="button" class="btn primary_new_btn" style="font-size: 12px"
                                            data-bs-toggle="modal" data-bs-target="#newAddressModal"
                                            onclick="checkAddressAndOpenModal()">
                                            <i class="fa-light fa-plus"></i> Add New Address
                                        </button>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <div class="selected-address">
                                        @if ($default_address)
                                            <p class="m-0 p-0">
                                                <strong>{{ $default_address->first_name ?? '' }}
                                                    {{ $default_address->last_name ?? '' }} (+65)
                                                    {{ $default_address->phone ?? '' }}</strong>&nbsp;&nbsp;<br>
                                                {{ $default_address->address ?? '' }}, -
                                                {{ $default_address->postalcode ?? '' }}
                                                <span>
                                                    @if ($default_address->default)
                                                        <span
                                                            class="badge badge_danger py-1">Default</span>&nbsp;&nbsp;
                                                    @endif
                                                </span>
                                            </p>
                                        @else
                                            <p>Your address details are missing. Add one now to make checkout faster
                                                and easier!</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Address Modal -->
        <div class="modal fade" id="newAddressModal" tabindex="-1" aria-labelledby="newAddressModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content modal_border">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newAddressModalLabel">Add New Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Address Form -->
                        <form id="addressNewForm" action="{{ route('address.create') }}" method="POST">
                            @csrf
                            <div class="row">
                                <!-- First Name -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="first_name" class="form-label address_lable">First Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control address_input" name="first_name"
                                        id="first_name" placeholder="Enter your first name" required />
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="last_name" class="form-label  address_lable">Last Name
                                        (Optional)</label>
                                    <input type="text" class="form-control address_input" name="last_name"
                                        id="last_name" placeholder="Enter your last name" />
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="phone" class="form-label address_lable">Phone Number <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control address_input" name="phone"
                                        id="phone" placeholder="Enter your phone number" required />
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="email" class="form-label address_lable">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control address_input" name="email"
                                        id="email" placeholder="Enter your email" required />
                                </div>

                                <!-- Address -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="address" class="form-label address_lable">Address <span
                                            class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control address_input" name="address" id="address"
                                        placeholder="Enter your Address" required></textarea>
                                </div>
                                <!-- Postal Code -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="postalcode" class="form-label address_lable">Postal Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control address_input" name="postalcode"
                                        id="postalcode" placeholder="Enter your Postal Code" required />
                                </div>

                                <!-- Unit (Optional) -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="unit" class="form-label address_lable">Additional Info
                                        (Optional)</label>
                                    <input type="text" class="form-control address_input" name="unit"
                                        id="unit" placeholder="Landmark" />
                                </div>

                                <!-- Address Type -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label class="form-label address_lable">Address Type <span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div>
                                            <input type="radio" name="type" id="home_mode" value="home_mode"
                                                class="form-check-input" required>
                                            <label for="home_mode">Home</label>
                                        </div>
                                        <div>
                                            <input type="radio" name="type" id="work_mode" value="work_mode"
                                                class="form-check-input" required>
                                            <label for="work_mode">Work</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 address">
                                    <input type="checkbox" name="default" class="default_address"
                                        id="defaultAddressCheckbox">
                                    <label class="form-check-label" for="defaultAddressCheckbox">Set as Default
                                        Address</label>
                                </div>

                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#myAddressModal">Back</button>
                                <button type="submit" class="btn btn-sm outline_primary_btn" id="saveAddress">Save
                                    Address</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <!-- My Address Modal -->
        <div class="modal fade" id="myAddressModal" tabindex="-1" aria-labelledby="myAddressModalLabel"
            aria-hidden="true">
            <form id="addressForm" action="{{ route('address.change') }}" method="POST">
                @csrf
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content modal_border">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myAddressModalLabel">My Address</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="min-height: 24rem">
                            <div class="allAddress">
                                @foreach ($addresses as $addr)
                                    <div class="row p-2">
                                        <div class="col-10">
                                            <div class="d-flex text-start">
                                                <div class="px-1">
                                                    <input type="radio" name="selected_id"
                                                        id="selected_id_{{ $addr->id }}"
                                                        value="{{ $addr->id }}"
                                                        {{ $selectedAddressId == $addr->id ? 'checked' : ($default_address && $addr->id == $default_address->id && !$selectedAddressId ? 'checked' : '') }} />
                                                </div>
                                                <p class="text-turncate fs_common">
                                                    <span class="px-2">
                                                        {{ $addr->first_name }} {{ $addr->last_name ?? '' }} |
                                                        <span style="color: #c7c7c7;">&nbsp;
                                                            {{ $addr->phone }}</span>
                                                    </span><br>
                                                    <span class="px-2" style="color: #c7c7c7">{{ $addr->address }},
                                                        {{ $addr->postalcode }}.</span>
                                                    <br>
                                                    @if ($addr->default)
                                                        <span class="badge badge_primary">Default</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <div class="d-flex gap-2 delBadge">
                                                    <button type="button" class="badge_edit" data-bs-toggle="modal"
                                                        data-address-id="{{ $addr->id }}"
                                                        data-bs-target="#editAddressModal">
                                                        Edit
                                                    </button>
                                                    @if (!$addr->default)
                                                        <button type="button" class="badge_del"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteAddressModal"
                                                            data-address-id="{{ $addr->id }}">
                                                            Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn primary_new_btn" style="font-size: 12px"
                                data-bs-toggle="modal" data-bs-target="#newAddressModal"
                                onclick="checkAddressAndOpenModal()">
                                <i class="fa-light fa-plus"></i> Add New Address
                            </button>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn outline_secondary_btn"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn outline_primary_btn"
                                    id="confirmAddressBtn">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Delete Address Modal -->
        <div class="modal fade" id="deleteAddressModal" tabindex="-1" aria-labelledby="deleteAddressModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal_border">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAddressModalLabel">Delete Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this address?</p>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn outline_secondary_btn"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn outline_primary_btn" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Address Modal -->
        <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal_border">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAddressModalLabel">Change Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Address Form -->
                        <form id="addressEditForm" action="{{ route('address.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="address_id" name="address_id">
                            <div class="row">
                                <!-- First Name -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="first_name" class="form-label address_lable">First Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control address_input first_name"
                                        name="first_name" placeholder="Enter your first name" required />
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="last_name" class="form-label address_lable">Last Name
                                        (Optional)</label>
                                    <input type="text" class="form-control address_input last_name"
                                        name="last_name" placeholder="Enter your last name" />
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="phone" class="form-label address_lable">Phone Number <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control address_input phone" name="phone"
                                        placeholder="Enter your phone number" required />
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="email" class="form-label address_lable">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control address_input email" name="email"
                                        placeholder="Enter your email" required />
                                </div>

                                <!-- Address -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="address" class="form-label address_lable">Address <span
                                            class="text-danger">*</span></label>
                                    <textarea type="text" class="form-control address_input address" name="address" placeholder="Enter your Address"
                                        required></textarea>
                                </div>

                                <!-- Postal Code -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="postalcode" class="form-label address_lable">Postal Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control address_input postalcode"
                                        name="postalcode" placeholder="Enter your Postal Code" required />
                                </div>

                                <!-- Unit (Optional) -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="unit" class="form-label address_lable">Unit Info
                                        (Optional)</label>
                                    <input type="text" class="form-control address_input unit" name="unit"
                                        placeholder="Landmark" />
                                </div>

                                <!-- Address Type -->
                                <div class="col-md-6 col-12 mb-3">
                                    <label class="form-label address_lable">Address Type <span
                                            class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div>
                                            <input type="radio" name="type" class="home_mode" value="home_mode"
                                                class="form-check-input" required>
                                            <label for="home_mode">Home</label>
                                        </div>
                                        <div>
                                            <input type="radio" name="type" class="work_mode" value="work_mode"
                                                class="form-check-input" required>
                                            <label for="work_mode">Work</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 address">
                                    <input type="checkbox" name="default_address" id="default_address"
                                        class="default_address">
                                    <label class="form-check-label" for="default_address">Set as Default
                                        Address</label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#myAddressModal">Back</button>
                                <button type="submit" class="btn btn-sm outline_primary_btn"
                                    id="saveEditAddress">Save Address</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>



    </nav>
    <script>
        //     document.getElementById('addressNewForm').addEventListener('submit', function(e) {
        //         // Prevent the default form submission
        //         e.preventDefault();

        //         // Get the save button
        //         const saveButton = document.getElementById('saveAddress');

        //         // Disable the button to prevent multiple submissions
        //         saveButton.disabled = true;

        //         // Add a spinner to the button
        //         saveButton.innerHTML = `
    //     <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    //     Saving...
    // `;

        //         // Submit the form programmatically
        //         this.submit();
        //     });
        document.getElementById("searchInput").addEventListener("input", function() {
            let query = this.value.trim();
            if (query.length < 2) return;

            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                fetchSearchResults(query);
            }, 500); // Delay to reduce requests
        });

        function fetchSearchResults(query) {
            // Get the country_code from the current URL
            const pathSegments = window.location.pathname.split('/');
            const countryCode = pathSegments[1]; // Assuming the country_code is the first segment after the domain

            // Fetch search results with the country_code
            fetch(`/${countryCode}/search?q=${encodeURIComponent(query)}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("searchResults").innerHTML = data;
                })
                .catch(error => console.error("Error fetching search results:", error));
        }

        function showDropdown(dropdown) {
            clearTimeout(dropdown.hideTimeout);
            dropdown.style.display = "block";
        }

        // Function to hide dropdown
        function hideDropdown(dropdown) {
            dropdown.hideTimeout = setTimeout(function() {
                dropdown.style.display = "none";
            }, 100); // Delay of 500ms before hiding
        }

        // Cart Dropdown
        const cartDropdown = document.querySelector(".cb_cart_dropdown");
        const cartButton = document.querySelector(".cb_cart_dd");

        cartButton.addEventListener("mouseover", () => showDropdown(cartDropdown));
        cartButton.addEventListener("mouseleave", () => hideDropdown(cartDropdown));
        cartDropdown.addEventListener("mouseover", () => showDropdown(cartDropdown));
        cartDropdown.addEventListener("mouseleave", () => hideDropdown(cartDropdown));

        // User Dropdown
        const userDropdown = document.querySelector(".user_dorpdown .cb_cart_dropdown");
        const userButton = document.querySelector(".user_dorpdown");

        userButton.addEventListener("mouseover", () => showDropdown(userDropdown));
        userButton.addEventListener("mouseleave", () => hideDropdown(userDropdown));
        userDropdown.addEventListener("mouseover", () => showDropdown(userDropdown));
        userDropdown.addEventListener("mouseleave", () => hideDropdown(userDropdown));
    </script>
