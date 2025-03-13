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
                <li class="breadcrumb-item active"><span class="text-dark text-decoration-none">
                        @if ($deals->isNotEmpty())
                            {{ $deals->first()->name }}
                        {{-- @else
                            No Product --}}
                        @endif
                    </span>
                </li>
            </ol>
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
                                <h6 class="pb-3">Filter Products <span class="cb_text_primary">{{ $totaldeals }}
                                        products Available</span>
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
                                            Under 50
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="price[]"
                                            value="₹50-₹100" id="price_50_100"
                                            {{ in_array('₹50-₹100', request()->get('price', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="brand">
                                            50 - 100
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="price[]"
                                            value="₹100-₹150" id="price_100_150"
                                            {{ in_array('₹100-₹150', request()->get('price', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="brand">
                                            100 - 150
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="price[]"
                                            value="₹150-₹200" id="price_150_200"
                                            {{ in_array('₹150-₹200', request()->get('price', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="brand">
                                            150 -200
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="price[]"
                                            value="₹200-₹250" id="price_200_250"
                                            {{ in_array('₹200-₹250', request()->get('price', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="brand">
                                            200 - 250
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex flex-column">
                                    <h6 class="cb_fliters">Unit</h6>
                                </div>
                                <div class="p-2">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="unit[]"
                                            value="m" id="m"
                                            {{ in_array('m', request()->get('unit', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="m">Meters (m)</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="unit[]"
                                            value="cm" id="cm"
                                            {{ in_array('cm', request()->get('unit', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="cm">CentiMeters (cm)</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="unit[]"
                                            value="mm" id="mm"
                                            {{ in_array('mm', request()->get('unit', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="mm">MilliMeters (mm)</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="unit[]"
                                            value="in" id="in">
                                        <label class="form-check-label px-2" for="in">Inches (in)</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="unit[]"
                                            value="ft" id="ft">
                                        <label class="form-check-label px-2" for="ft">feet (ft)</label>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="cb_fliters">Pack</h6>
                                </div>
                                <div class="p-2">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="pack[]"
                                            value="0-10" id="pack_0_10"
                                            {{ in_array('0-10', request()->get('pack', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="pack_0_10">0 - 10</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="pack[]"
                                            value="10-20" id="pack_10_20"
                                            {{ in_array('10-20', request()->get('pack', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="pack_10_20">10 - 20</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="pack[]"
                                            value="20-30" id="pack_20_30"
                                            {{ in_array('20-30', request()->get('pack', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="pack_20_30">20 - 30</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="pack[]"
                                            value="30-50" id="pack_30_50"
                                            {{ in_array('30-50', request()->get('pack', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="pack_30_50">30 - 50</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="pack[]"
                                            value="50-75" id="pack_50_75"
                                            {{ in_array('50-75', request()->get('pack', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="pack_50_75">50 - 75</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="pack[]"
                                            value="75-100" id="pack_75_100"
                                            {{ in_array('75-100', request()->get('pack', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="pack_75_100">75 - 100</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input cb_check_input" type="checkbox" name="pack[]"
                                            value="100+" id="pack_100"
                                            {{ in_array('100+', request()->get('pack', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label px-2" for="pack_100">Above 100</label>
                                    </div>
                                </div>
                            </div>
                            <div class="cb_apply gap-4 p-2">
                                <button type="button" class="btn btn-button cb_outline_btn clear-button"
                                    id="clearButtonLarge">Clear
                                    All</button>
                                <button type="submit" class="btn btn-button cb_search_fliter apply-button"
                                    id="applyButton">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-9 col-12">
                @if (request()->routeIs('deals.subcategorybased'))
                    <div class="card cb_card_border my-3 topbarContainer ">
                        <div class="scroll-container">
                            <div class="d-flex overflow-auto topBar cb_fliter_cards"
                                style="width: 100%; white-space: nowrap;">
                                <a href="{{ route('deals.subcategorybased', ['slug' => 'all', 'category_group_id' => $categorygroup->id]) }}"
                                    class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2 {{ request('slug') === 'all' && request('category_group_id') == $categorygroup->id ? 'active' : '' }}">
                                    All
                                </a>
                                @foreach ($categorygroup->categories as $cat)
                                    <a href="{{ route('deals.subcategorybased', ['slug' => $cat->slug]) }}"
                                        class="cb_badge_2 btn btn-sm cb_text_primary m-2 me-2">
                                        {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>
                            <div class="custom-scrollbar">
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row m-0">
                    @foreach ($deals as $product)
                        <div
                            class="col-sm-6 col-md-6 col-lg-4 col-xl-3 col-12 mb-2 p-3 d-flex align-items-stretch justify-content-center">
                            <a href="{{ url('/deal/' . $product->id) }}" class="cb_products">
                                <div class="card h-100 position-relative cp_card"
                                    title="{{ $product->name }} {{ number_format($product->box_length, 0) }}{{ $product->unit }} X {{ number_format($product->box_width, 0) }}{{ $product->unit }} X {{ number_format($product->box_height, 0) }}{{ $product->unit }}(🔖Pack of {{ number_format($product->pack, 0) }})">
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
                                            of {{ number_format($product->box_height, 0) }})</h5>
                                        <div class="cp_price_cart">
                                            <p class="m-0"><span
                                                    class="cb_og_price">{{ $product->country->currency_symbol }}{{ number_format($product['original_price'], 0) }}</span>&nbsp;
                                                <span
                                                    class="cb_price">{{ $product->country->currency_symbol }}{{ number_format($product['discounted_price'], 0) }}</span>
                                            </p>
                                            <a href="#" class="btn cb_add_cart add-to-cart-btn"
                                                data-slug="{{ $product->slug }}" data-qty="1"
                                                onclick="event.stopPropagation();">Add to cart</a>
                                        </div>
                                        <p class="cp_pieces m-0">{{ $product['stock_quantity'] }} Pieces Available</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample"
            aria-labelledby="offcanvasExampleLabel" style="width: 90%">
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
            updateFilters();
        });

        document.getElementById('clearButtonLarge').addEventListener('click', function(event) {
            event.preventDefault();
            // Clear all query parameters by navigating to the base URL
            window.location.href = window.location.pathname;
        });

        function updateFilters() {
            var selectedFilters = {};
            document.querySelectorAll('.cb_check_input:checked').forEach(input => {
                if (!selectedFilters[input.name]) {
                    selectedFilters[input.name] = [];
                }
                selectedFilters[input.name].push(input.value);
            });

            // Example: Sending filters via URL (Modify as needed)
            var queryString = Object.keys(selectedFilters)
                .map(key => key + '=' + selectedFilters[key].join(','))
                .join('&');

            window.location.href = window.location.pathname + '?' + queryString;
        }
    </script>
@endsection
