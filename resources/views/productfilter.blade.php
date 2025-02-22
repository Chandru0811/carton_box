@extends('layouts.master')
@section('content')
    <div class="container-fluid p-0">
        {{-- Breadcrumb navigate  --}}
        <div class="p-2 ps-lg-3 cb_search_breadcrumb w-100">
            <ol class="breadcrumb cb_breadcrumb">
                <li class="breadcrumb-item"><a class="text-dark text-decoration-none" href="/">Home</a></li>
                &nbsp;
                <span className="breadcrumb-separator"> &gt; </span>&nbsp;&nbsp;
                <li class="breadcrumb-item"><a class="text-dark text-decoration-none" href="#">Product</a>
                </li>&nbsp;
                <span className="breadcrumb-separator"> &gt; </span>&nbsp;&nbsp;
                <li class="breadcrumb-item active"><span class="text-dark text-decoration-none"> Large Storage Box</span>
                </li>
            </ol>

            <div class="cb_units">
                <label for="unitSelect" class="cb_text_primary">Unit:</label>
                <select id="unit" class="card cb_card_border border-2">
                    <option value="m">Meters (m)</option>
                    <option value="cm">Centimeters (cm)</option>
                    <option value="mm">Millimeters (mm)</option>
                    <option value="in">Inches (in)</option>
                    <option value="ft">Feet (ft)</option>
                </select>
            </div>
        </div>

        <div class="row m-0">
            <div class="col-lg-3 col-md-3 col-12">
                <div class="py-3" style="position:sticky; top:90px">
                    <button class="btn cb_card_border d-block d-md-none cb_social_media_icons" data-title="filter"
                        type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
                        aria-controls="offcanvasExample" id="offcanvasTrigger">
                        <i class="fas fa-bars"></i>
                    </button>


                    <div id="main-container">
                        <div id="card-element" class="card cb_card_border">
                            <div class="p-2 cb_fliter_card">
                                <h6 class="pb-3">Filter Products <span class="cb_text_primary">8 products Available</span>
                                </h6>
                                <div class="d-flex flex-column">
                                    <h6 class="cb_fliters">
                                        Price Filter
                                    </h6>
                                </div>
                                <div class="p-2">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" class="form-check-input"
                                            type="checkbox" name="price[]" value="₹0-₹50" id="price_0_50"
                                            {{ in_array('₹0-₹50', request()->get('price', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="brand">
                                            Under $50
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="price[]"
                                            value="₹50-₹100" id="price_50_100"
                                            {{ in_array('₹50-₹100', request()->get('price', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="brand">
                                            $50 - $100
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="brand"
                                            value="" id="brand">
                                        <label class="form-check-label px-2" for="brand">
                                            $100 - $150
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="brand"
                                            value="" id="brand">
                                        <label class="form-check-label px-2" for="brand">
                                            $150 - $200
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="brand"
                                            value="" id="brand">
                                        <label class="form-check-label px-2" for="brand">
                                            $200 - $250
                                        </label>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="cb_fliters">
                                        Length
                                    </h6>
                                </div>
                                <div class="p-2">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="brand"
                                            value="" id="brand">
                                        <label class="form-check-label px-2" for="brand">
                                            1
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="brand"
                                            value="" id="brand">
                                        <label class="form-check-label px-2" for="brand">
                                            2
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="brand"
                                            value="" id="brand">
                                        <label class="form-check-label px-2" for="brand">
                                            3
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="brand"
                                            value="" id="brand">
                                        <label class="form-check-label px-2" for="brand">
                                            4
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="brand"
                                            value="" id="brand">
                                        <label class="form-check-label px-2" for="brand">
                                            5
                                        </label>
                                    </div>

                                </div>
                            </div>
                            <div class="cb_apply p-2">
                                <button type="submit" class="btn btn-button cb_search_fliter apply-button"
                                    id="applyButton">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-12">
                <div class="card cb_card_border my-3 topbarContainer ">
                    <div class="scroll-container">
                        <div class="d-flex overflow-auto topBar cb_fliter_cards"
                            style="width: 100%; white-space: nowrap;">
                            <a href="#" class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2">
                                All
                            </a>
                            <a href="#" class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2">
                                For House Moving
                            </a>
                            <a href="#" class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2">
                                For Office Moving
                            </a>
                            <a href="#" class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2">
                                For Shipping
                            </a>
                            <a href="#" class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2">
                                For E-Commerce
                            </a>
                            <a href="#" class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2">
                                Cake Box
                            </a>
                            <a href="#" class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2">
                                Gift Box
                            </a>
                        </div>
                        <div class="custom-scrollbar">
                        </div>
                    </div>
                </div>
                <div class="row m-0">
                    @foreach ($deals as $product)
                        <div
                            class="col-sm-6 col-md-6 col-lg-4 col-xl-3 col-12 mb-2 p-3 d-flex align-items-stretch justify-content-center">
                            <a href="#" class="cb_products">
                                <div class="card h-100 position-relative cp_card">
                                    <div class="cb_badge">{{ number_format($product['discount_percentage'], 0) }}% OFF
                                    </div>
                                    @php
                                        $image = isset($product->productMedia)
                                            ? $product->productMedia->where('order', 1)->where('type', 'image')->first()
                                            : null;
                                    @endphp
                                    <img src="{{ $image ? asset($image->resize_path) : asset('assets/images/home/noImage.webp') }}"
                                        class="card-img-top" alt="{{ $product['name'] }}">
                                    <div class="cb_card_contents">
                                        <h5 class="card-title">{{ $product->name }} -
                                            {{ number_format($product->box_length, 0) }}{{ $product->unit }} X
                                            {{ number_format($product->box_width, 0) }}{{ $product->unit }} X
                                            {{ number_format($product->pack, 0) }}{{ $product->unit }} (🔖Pack
                                            of {{ number_format($product->box_height, 0) }}) |
                                            Free Home Delivery</h5>
                                        <div class="cp_price_cart">
                                            <p class="m-0"><span
                                                    class="cb_og_price">{{ $product->country->currency_symbol }}{{ number_format($product['original_price'], 0) }}</span>&nbsp;
                                                <span
                                                    class="cb_price">{{ $product->country->currency_symbol }}{{ number_format($product['discounted_price'], 0) }}</span>
                                            </p>
                                            <a href="#" class="btn cb_add_cart">Add to cart</a>
                                        </div>
                                        <p class="cp_pieces m-0">{{ $product['sku'] }} Pieces Available</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample"
            aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Offcanvas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <!-- Container where the card will be moved -->
                <div id="offcanvas-container"></div>
            </div>
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var offcanvasElement = document.getElementById('offcanvasExample');
            var offcanvas = new bootstrap.Offcanvas(offcanvasElement);
            var cardElement = document.getElementById('card-element');
            var mainContainer = document.getElementById('main-container');
            var offcanvasContainer = document.getElementById('offcanvas-container');

            function moveFilterToOffcanvas() {
                if (window.innerWidth < 768) {
                    offcanvasContainer.appendChild(cardElement);
                } else {
                    mainContainer.appendChild(cardElement);
                }
            }

            offcanvasElement.addEventListener('show.bs.offcanvas', function() {
                moveFilterToOffcanvas();
            });

            offcanvasElement.addEventListener('hidden.bs.offcanvas', function() {
                moveFilterToOffcanvas();
            });

            window.addEventListener('resize', moveFilterToOffcanvas);

            // Ensure it loads correctly on page load
            moveFilterToOffcanvas();
        });

        document.getElementById('applyButton').addEventListener('click', function(event) {
            event.preventDefault();
            updateFilters('mobile');
        });
    </script>
@endsection
