@extends('layouts.master')
@section('content')
    <div class="container">
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
                        <i class="fa-solid fa-xmark" style="color: #ff0060"></i>
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
                        <i class="fa-solid fa-xmark" style="color: #ff0060"></i>
                    </button>
                </div>
            </div>
        @endif
        {{-- Breadcrumb navigate  --}}
        <div class="my-3 ps-lg-3">
            <ol class="breadcrumb cb_breadcrumb">
                <li class="breadcrumb-item"><a class="cb_breadcrumb_link" href="/">Home</a></li>
                &nbsp;
                <span className="breadcrumb-separator"> &gt; </span>&nbsp;&nbsp;
                <li class="breadcrumb-item"><a class="cb_breadcrumb_link" href="#">Product</a>
                </li>&nbsp;
                <span className="breadcrumb-separator"> &gt; </span>&nbsp;&nbsp;
                <li class="breadcrumb-item active"><span class="cb_breadcrumb_link"> {{ $product->name }}</span></li>
            </ol>
        </div>
        {{-- product start  --}}
        <div class="row m-0 p-2">
            <div class="col-md-6 column">
                <div class="row m-0" style="position:sticky; top:100px">
                    @php
                        $hasMedia = $product->productMedia->whereIn('type', ['image', 'video'])->isNotEmpty();
                    @endphp
                    <div class="col-md-2 col-2 pe-md-0 image_slider_vw">
                        <div class="text-center arrow-button mb-2">
                            <button type="button" style="border:none; background-color: #eaeaea; font-size:10px"
                                id="scrollUpBtn" title="Scroll up" aria-label="Scroll up" onclick="scrollUp()">
                                <i class="fa fa-angle-up"></i>
                            </button>
                        </div>

                        <div class="thumbnail" id="thumbnailContainer">
                            @foreach ($product->productMedia->sortBy('order') as $media)
                                @if ($media->type == 'image')
                                    <div>
                                        <img class="thumb-img" data-zoom="{{ asset($media->path) }}"
                                            src="{{ asset($media->resize_path) }}" alt="Image" />
                                    </div>
                                @elseif ($media->type == 'video')
                                    @php
                                        $videoId = preg_match(
                                            '/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=))([\w-]+)/',
                                            $media->resize_path,
                                            $matches,
                                        )
                                            ? $matches[1]
                                            : $media->resize_path;
                                    @endphp
                                    <div>
                                        <img src="https://img.youtube.com/vi/{{ $videoId }}/0.jpg"
                                            class="thumbnail img-fluid"
                                            style="height: 60px; cursor: pointer; object-fit: cover;" data-bs-toggle="modal"
                                            data-bs-target="#videoModal" onclick="updateVideoModal('{{ $videoId }}')">
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="text-center arrow-button mt-2">
                            <button type="button" style="border:none; background-color: #eaeaea; font-size:10px"
                                id="scrollDownBtn" title="Scroll down" aria-label="Scroll down" onclick="scrollDown()">
                                <i class="fa fa-angle-down"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-10 col-10 p-lg-0">
                        <div class="thumbnail-container">
                            @php
                                $firstImage = $product->productMedia->sortBy('order')->firstWhere('type', 'image');
                            @endphp
                            <img id="main-image" alt="Product Image" class="drift-demo-trigger image-fluid"
                                data-zoom="{{ $firstImage ? asset($firstImage->path) : asset('assets/images/home/noImage.webp') }}"
                                src="{{ $firstImage ? asset($firstImage->resize_path) : asset('assets/images/home/noImage.webp') }}" />
                        </div>
                    </div>
                    <div class="col-md-2 col-3  d-none d-md-block"></div>
                    <div class="col-md-10 col-9 mt-5">
                        <div class="cb_add_cart_btns d-flex justify-content-center gap-3">
                            <button class="btn cb_cart_btn text-nowrap add-to-cart-btn" data-slug="{{ $product->slug }}">
                                <i class="fa-solid fa-cart-shopping"></i>&nbsp;&nbsp;Add to Cart
                            </button>

                            <form action="{{ route('cart.add', ['slug' => $product->slug]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="saveoption" id="saveoption" value="buy now">
                                <input type="hidden" name="quantity" id="quantity" value="1">
                                <button type="submit" class="cb_Buy_btn text-nowrap buy-now-direct-btn"
                                    data-slug="{{ $product->slug }}">
                                    <i class="fa-solid fa-cart-shopping"></i>&nbsp;&nbsp;Buy Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 ps-4 mt-3 column">
                <span class="details" style="position:fixed; top:100px"></span>
                <h5 class="cb_product_name">
                    {{ $product->name }} - {{ number_format($product->box_length, 0) }}{{ $product->unit }} X
                    {{ number_format($product->box_width, 0) }}{{ $product->unit }} X
                    {{ number_format($product->box_height, 0) }}{{ $product->unit }} (🔖Pack
                    of {{ number_format($product->pack, 0) }})
                </h5>

                <p class="cb_pd_price"><del
                        class="fw-bold">{{ $product->country->currency_symbol }}{{ $product->original_price }}</del>&nbsp;
                    <span class="fw-bold">{{ $product->country->currency_symbol }}{{ $product->discounted_price }}</span>
                </p>
                <p class="cb_sku">SKU : {{ $product->coupon_code }}</p>
                <div class="cb_stock">
                    <p class="text-nowrap fw-semibold">Availability : <span>{{ $product->stock_quantity }} in stock</span>
                    </p>
                    <div class="cb_qty text-nowrap">
                        <p class="fw-semibold">QTY :</p>
                        <p>
                        <div class="cb_quantity_container">
                            <button class="qty-btn" onclick="changeQty(-1)">-</button>
                            <input type="text" id="quantityInput" value="1" readonly>
                            <button class="qty-btn" onclick="changeQty(1)">+</button>
                        </div>
                        </p>
                    </div>
                </div>
                <div class="text-start">
                    <h6>Description</h6>
                </div>
                <p class="p-2">
                    {{ $product->description }}
                </p>
                <div class="text-start">
                    <h6>Specification</h6>
                </div>
                <p class="p-2">
                    {{ $product->specifications }}
                </p>
                <div class="pt-2">
                    <p class="fw-semibold">Share on Social Media</p>
                    <div class="cb_link_conents" style="width: fit-content">
                        <p class="cb_social_links">
                            <a href="#" class="text-center"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-center"><i class="fab fa-x-twitter"></i></a>
                            <a href="#" class="text-center"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-center"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="text-center"><i class="fab fa-telegram"></i></a>
                            <a href="#" class="text-center"><i class="fab fa-instagram"></i></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="text-center py-4 mt-lg-5 mt-md-4 mt-3">
            Related Products
        </h5>
        @if ($relatedProducts->isNotEmpty())
            <div class="card p-3 mb-lg-5 cb_related_cards owl-carousel owl-theme">
                @foreach ($relatedProducts as $relatedProduct)
                    <div class="item">
                        <a href="{{ url('/deal/' . $relatedProduct->id) }}" class="cb_products">
                            <div class="card h-100 position-relative cp_card mb-2"
                                title="{{ $product->name }} {{ number_format($product->box_length, 0) }}{{ $product->unit }} X {{ number_format($product->box_width, 0) }}{{ $product->unit }} X {{ number_format($product->box_height, 0) }}{{ $product->unit }}(🔖Pack of {{ number_format($product->pack, 0) }})">
                                <div class="cb_badge">{{ number_format($relatedProduct['discount_percentage'], 0) }}% OFF
                                </div>
                                <img src="{{ !empty($relatedProduct->productMedia->first()) &&
                                file_exists(public_path($relatedProduct->productMedia->first()->resize_path))
                                    ? asset($relatedProduct->productMedia->first()->resize_path)
                                    : asset('assets/images/home/noImage.webp') }}"
                                    class="card-img-top" alt="{{ $relatedProduct->name }}">
                                <div class="cb_card_contents">
                                    <h5 class="card-title">{{ $relatedProduct->name }} -
                                        {{ number_format($relatedProduct->box_length, 0) }}{{ $relatedProduct->unit }} X
                                        {{ number_format($relatedProduct->box_width, 0) }}{{ $relatedProduct->unit }} X
                                        {{ number_format($relatedProduct->box_height, 0) }}{{ $product->unit }}
                                        (🔖Pack of {{ number_format($relatedProduct->pack, 0) }})
                                    </h5>
                                    <div class="cp_price_cart">
                                        <p class="m-0">
                                            @if ($relatedProduct->original_price)
                                                <span class="cb_og_price">
                                                    {{ $relatedProduct->country->currency_symbol }}{{ number_format($relatedProduct['original_price'], 0) }}
                                                </span>&nbsp;
                                            @endif
                                            <span class="cb_price">
                                                {{ $relatedProduct->country->currency_symbol }}{{ number_format($relatedProduct['discounted_price'], 0) }}
                                            </span>
                                        </p>
                                        <a href="#" class="btn cb_add_cart add-to-cart-btn"
                                            data-slug="{{ $product->slug }}" data-qty="1"
                                            onclick="event.stopPropagation();">Add to cart</a>
                                    </div>
                                    <p class="cp_pieces m-0">{{ $relatedProduct->stock_quantity }} Pieces Available</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted my-4">No related products found.</p>
        @endif



    </div>
    <script>
        // Zoom functionality
        const mainImage = document.querySelector("#main-image");
        const detailsContainer = document.querySelector(".details");
        const thumbnails = document.querySelectorAll(".thumb-img");
        const thumbnailContainer = document.querySelector("#thumbnailContainer");
        const scrollUpBtn = document.querySelector("#scrollUpBtn");
        const scrollDownBtn = document.querySelector("#scrollDownBtn");

        detailsContainer.style.pointerEvents = "none";

        mainImage.addEventListener("mouseenter", () => {
            mainImage.style.cursor = "zoom-in";
            detailsContainer.style.pointerEvents = "auto";
            detailsContainer.style.border = "1px solid #f5f5f5";
        });

        mainImage.addEventListener("mouseleave", () => {
            mainImage.style.cursor = "default";
            detailsContainer.style.pointerEvents = "none";
            detailsContainer.style.border = "none";
        });

        let driftInstance = new Drift(mainImage, {
            paneContainer: detailsContainer,
            inlinePane: 769,
            inlineOffsetY: -85,
            containInline: true,
            hoverBoundingBox: true,
        });

        function checkScrollLimits() {
            scrollUpBtn.disabled = thumbnailContainer.scrollTop === 0;
            scrollDownBtn.disabled =
                thumbnailContainer.scrollTop + thumbnailContainer.clientHeight >=
                thumbnailContainer.scrollHeight;
        }

        function scrollUp() {
            thumbnailContainer.scrollBy({
                top: -80,
                behavior: "smooth",
            });
            setTimeout(checkScrollLimits, 300);
        }

        // Scroll Down Function
        function scrollDown() {
            thumbnailContainer.scrollBy({
                top: 80,
                behavior: "smooth",
            });
            setTimeout(checkScrollLimits, 300);
        }

        checkScrollLimits();

        thumbnails.forEach((thumbnail) => {
            thumbnail.addEventListener("click", (e) => {
                thumbnails.forEach((thumb) => thumb.classList.remove("active"));
                e.target.classList.add("active");

                mainImage.src = e.target.src;
                mainImage.setAttribute("data-zoom", e.target.dataset.zoom);

                driftInstance.destroy();
                driftInstance = new Drift(mainImage, {
                    paneContainer: document.querySelector(".details"),
                    inlinePane: 769,
                    inlineOffsetY: -85,
                    containInline: true,
                    hoverBoundingBox: true,
                });
            });
        });

        function changeQty(amount) {
            let qtyInput = document.getElementById("quantityInput");
            let hiddenQtyInput = document.getElementById("quantity");
            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + amount;

            if (newQty >= 1) {
                qtyInput.value = newQty;
                hiddenQtyInput.value = newQty;
            }
        }
    </script>
@endsection
