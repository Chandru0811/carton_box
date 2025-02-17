@extends('layouts.master')
@section('content')
    @php
        $products = [
            [
                'id' => 1,
                'name' => 'Large Storage Box : 39cm(L) x 39cm(W) x 26cm(H)',
                'original_price' => '249',
                'price' => '199',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '7',
                'discount' => '16',
            ],
            [
                'id' => 2,
                'name' => 'Heavy-Duty Moving Box : 40cm(L) x 40cm(W) x 30cm(H)',
                'original_price' => '299',
                'price' => '249',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '5',
                'discount' => '12',
            ],
            [
                'id' => 3,
                'name' => 'Corrugated Carton Box : 35cm(L) x 35cm(W) x 25cm(H)',
                'original_price' => '279',
                'price' => '229',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '7',
                'discount' => '14',
            ],
            [
                'id' => 4,
                'name' => 'Eco-Friendly Packing Box : 38cm(L) x 38cm(W) x 28cm(H)',
                'original_price' => '299',
                'price' => '249',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '6',
                'discount' => '12',
            ],
            [
                'id' => 5,
                'name' => 'Premium Shipping Box : 42cm(L) x 42cm(W) x 32cm(H)',
                'original_price' => '219',
                'price' => '179',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '7',
                'discount' => '13',
            ],
            [
                'id' => 6,
                'name' => 'Multipurpose Storage Box : 37cm(L) x 37cm(W) x 29cm(H)',
                'original_price' => '199',
                'price' => '149',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '6',
                'discount' => '10',
            ],
            [
                'id' => 7,
                'name' => 'Office File Storage Box : 45cm(L) x 35cm(W) x 27cm(H)',
                'original_price' => '269',
                'price' => '229',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '6',
                'discount' => '14',
            ],
            [
                'id' => 8,
                'name' => 'Durable Packing Carton : 44cm(L) x 44cm(W) x 31cm(H)',
                'original_price' => '239',
                'price' => '200',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '3',
                'discount' => '12',
            ],
            [
                'id' => 9,
                'name' => 'Compact Storage Carton : 36cm(L) x 36cm(W) x 26cm(H)',
                'original_price' => '229',
                'price' => '199',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '4',
                'discount' => '11',
            ],
            [
                'id' => 10,
                'name' => 'Extra Large Packing Box : 50cm(L) x 50cm(W) x 35cm(H)',
                'original_price' => '399',
                'price' => '359',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '9',
                'discount' => '14',
            ],
            [
                'id' => 11,
                'name' => 'Lightweight Shipping Box : 41cm(L) x 41cm(W) x 30cm(H)',
                'original_price' => '239',
                'price' => '199',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '8',
                'discount' => '11',
            ],
            [
                'id' => 12,
                'name' => 'Sturdy Moving Carton : 39cm(L) x 39cm(W) x 27cm(H)',
                'original_price' => '249',
                'price' => '209',
                'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
                'pieces' => '7',
                'discount' => '13',
            ],
        ];
    @endphp
    <div class="container">
        {{-- Breadcrumb navigate  --}}
        <div class="my-3 ps-lg-3">
            <ol class="breadcrumb cb_breadcrumb">
                <li class="breadcrumb-item"><a class="cb_breadcrumb_link" href="/">Home</a></li>
                &nbsp;
                <span className="breadcrumb-separator"> &gt; </span>&nbsp;&nbsp;
                <li class="breadcrumb-item"><a class="cb_breadcrumb_link" href="#">Product</a>
                </li>&nbsp;
                <span className="breadcrumb-separator"> &gt; </span>&nbsp;&nbsp;
                <li class="breadcrumb-item active"><span class="cb_breadcrumb_link">Large Storage Box</span></li>
            </ol>
        </div>
        {{-- product start  --}}
        <div class="row m-0 p-2">
            <div class="col-md-6 column">
                <div class="row m-0" style="position:sticky; top:100px">
                    <div class="col-md-2 col-2 pe-md-0 image_slider_vw">
                        <div class="text-center arrow-button mb-2">
                            <button type="button" style="border:none; background-color: #eaeaea; font-size:10px"
                                id="scrollUpBtn" title="Scroll up" aria-label="Scroll up" onclick="scrollUp()">
                                <i class="fa fa-angle-up"></i>
                            </button>
                        </div>

                        <div class="thumbnail" id="thumbnailContainer">
                            <div>
                                <img class="thumb-img" data-zoom="{{ asset('assets/images/home/secondaryImg.jpg') }}"
                                    src="{{ asset('assets/images/home/secondaryImg.jpg') }}" alt="Image" />
                            </div>
                            <div>
                                <img class="thumb-img" data-zoom="{{ asset('assets/images/home/secondaryImg.jpg') }}"
                                    src="{{ asset('assets/images/home/secondaryImg.jpg') }}" alt="Image" />
                            </div>
                            <div>
                                <img class="thumb-img" data-zoom="{{ asset('assets/images/home/secondaryImg.jpg') }}"
                                    src="{{ asset('assets/images/home/secondaryImg.jpg') }}" alt="Image" />
                            </div>
                            <div>
                                <img class="thumb-img" data-zoom="{{ asset('assets/images/home/secondaryImg.jpg') }}"
                                    src="{{ asset('assets/images/home/secondaryImg.jpg') }}" alt="Image" />
                            </div>
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
                            <img id="main-image" alt="Product Image" class="drift-demo-trigger image-fluid"
                                data-zoom="{{ asset('assets/images/home/secondaryImg.jpg') }}"
                                src="{{ asset('assets/images/home/secondaryImg.jpg') }}" />
                        </div>
                    </div>
                    <div class="col-md-2 col-3  d-none d-md-block"></div>
                    <div class="col-md-10 col-9 mt-3">
                        <div class="cb_add_cart_btns">
                            <button class="btn cb_cart_btn text-nowrap add-to-cart-btn">
                                <i class="fa-solid fa-cart-shopping"></i>&nbsp;&nbsp;Add to Cart
                            </button>

                            <form>
                                @csrf
                                <input type="hidden" name="saveoption" id="saveoption" value="buy now">
                                <button type="submit" class="cb_Buy_btn text-nowrap">
                                    <i class="fa-solid fa-cart-shopping"></i>&nbsp;&nbsp;Buy Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 ps-4 mt-3 column">
                <span class="details" style="position:fixed; top:100px"></span>
                <h5 class="cb_product_name">Large Storage Box : 39cm(L) x 39cm(W) x 26cm(H) (Pack of 16) | Free Home
                    Delivery</h5>
                <p class="cb_pd_price"><del class="text-secondary fw-bold">$179</del>&nbsp; <span
                        class="fw-bold">$149</span></p>
                <p class="cb_sku">SKU : CBG546251785</p>
                <div class="cb_stock">
                    <p class="text-nowrap fw-semibold">Availability : <span>20 in stock</span></p>
                    <div class="cb_qty text-nowrap">
                        <p class="fw-semibold">QTY :</p>
                        <p>
                        <div class="cb_quantity_container">
                            <button class="qty-btn" onclick="changeQty(-1)">-</button>
                            <input type="text" id="productQty" value="1" readonly>
                            <button class="qty-btn" onclick="changeQty(1)">+</button>
                        </div>
                        </p>
                    </div>
                </div>
                <div class="text-start">
                    <h6>Description</h6>
                </div>
                <p class="cb_prouduct_desc">
                    Efficiently move and ship your belongings with our 🎉💥Moving/Shipping Carton Boxes! Made with sturdy
                    double wall construction and a 50cm x 50cm x 50cm size, each pack of 20🔖 provides reliable protection
                    for your items. Get yours now for only $100 and enjoy 🆓🚚 free delivery.
                </p>
                <div class="text-start">
                    <h6>Specification</h6>
                </div>
                <p class="cb_prouduct_desc">
                    Efficiently move and ship your belongings with our 🎉💥Moving/Shipping Carton Boxes! Made with sturdy
                    double wall construction and a 50cm x 50cm x 50cm size, each pack of 20🔖 provides reliable protection
                    for your items. Get yours now for only $100 and enjoy 🆓🚚 free delivery.
                </p>
                <div class="pt-2">
                    <p class="fw-semibold">Share on Social Media</p>
                    <div class="card cb_card_border cb_link_conents p-2" style="width: fit-content">
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

        <h5 class="text-center py-3">
            Related Products
        </h5>
        <div class="card cb_card_border p-3 mb-lg-5 cb_related_cards owl-carousel owl-theme">
            @foreach ($products as $product)
                <div class="item">
                    <div class="card h-100 position-relative cp_card mb-2">
                        <div class="cb_badge">{{ $product['discount'] }}% OFF</div>
                        <img src="{{ $product['image'] }}" class="card-img-top" alt="{{ $product['name'] }}">
                        <div class="cb_card_contents">
                            <h5 class="card-title">{{ $product['name'] }}</h5>
                            <div class="cp_price_cart">
                                <p class="m-0">
                                    <span class="cb_og_price">${{ $product['original_price'] }}</span>&nbsp;
                                    <span class="cb_price">${{ $product['price'] }}</span>
                                </p>
                                <a href="#" class="btn cb_add_cart">Add to cart</a>
                            </div>
                            <p class="cp_pieces m-0">{{ $product['pieces'] }} Pieces Available</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

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
            detailsContainer.style.pointerEvents = "auto";
            detailsContainer.style.border = "1px solid #f5f5f5";
        });


        mainImage.addEventListener("mouseleave", () => {
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
            let qtyInput = document.getElementById("productQty");
            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + amount;

            if (newQty >= 1) {
                qtyInput.value = newQty;
            }
        }
    </script>
@endsection
