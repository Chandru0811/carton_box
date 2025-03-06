@extends('layouts.master')

@section('content')

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
    @php
        use Carbon\Carbon;

        function formatIndianCurrency($num)
        {
            $num = intval($num);
            $lastThree = substr($num, -3);
            $rest = substr($num, 0, -3);
            if ($rest != '') {
                $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest) . ',';
            }
            return '₹' . $rest . $lastThree;
        }
    @endphp
    <section>
        <div class="container" style="margin-top: 100px">
            @php
                $subtotal = 0;
                $total_discount = 0;
            @endphp
            <!-- Check if carts or cart->items are empty -->
            @if (!$cart || $cart->items->isEmpty())
                <div
                    class="empty-cart col-12 text-center d-flex flex-column align-items-center justify-content-center mt-0">
                    <img src="{{ asset('assets/images/home/cart_empty.png') }}" alt="Empty Cart"
                        class="img-fluid empty_cart_img">
                    <p class="pt-5" style="color: #cd8245;font-size: 22px">Your Cart is Currently Empty</p>
                    <p class="" style="color: #6C6C6C;font-size: 16px">Looks Like You Have Not Added Anything To </br>
                        Your Cart. Go Ahead & Explore Top Categories.</p>
                    <a href="/" class="btn showmoreBtn my-lg-4 my-2">Shop More</a>
                </div>
            @endif
            @if ($cart && !$cart->items->isEmpty())
                <div class="cart-items-container">
                    @if ($cart)
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Your Cart <span style="color: #cd8245">(<span
                                        class="item_count">{{ $cart->items->count() }}</span>)</span></h5>
                            <a href="/" class="text-decoration-none">
                                <button type="button" class="btn showmoreBtn">
                                    Shop more
                                </button>
                            </a>
                        </div>
                        <div class="cart-items">
                            @foreach ($cart->items as $item)
                                <div class="cart-item" data-product-id="{{ $item->product_id }}">
                                    @php
                                        $product = $item->product;
                                        $subtotal += $product->original_price * $item->quantity;
                                        $total_discount +=
                                            ($product->original_price - $product->discounted_price) * $item->quantity;
                                    @endphp
                                    @php
                                        $currentDate = Carbon::now();

                                        $deliveryDays = is_numeric($item->product->delivery_days)
                                            ? (int) $item->product->delivery_days
                                            : 0;

                                        $deliveryDate =
                                            $deliveryDays > 0
                                                ? $currentDate->addDays($deliveryDays)->format('d-m-Y')
                                                : null;
                                    @endphp
                                    <div class="row p-4">
                                        <div class="col-md-4 mb-3">
                                            <div class="d-flex justify-content-center align-items-center">
                                                @php
                                                    $image = isset($product->productMedia)
                                                        ? $product->productMedia
                                                            ->where('order', 1)
                                                            ->where('type', 'image')
                                                            ->first()
                                                        : null;
                                                @endphp
                                                <img src="{{ $image ? asset($image->resize_path) : asset('assets/images/home/noImage.webp') }}"
                                                    style="max-width: 100%; max-height: 100%;" alt="{{ $product->name }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <a href="{{ $cart->items->count() > 1 ? url('/deal/' . $product->id) : '#' }}"
                                                style="color: #000;" class="dynamic-link"
                                                data-product-id="{{ $product->id }}">
                                                <p style="font-size: 18px;">
                                                    {{ $product->name }}
                                                </p>
                                            </a>
                                            <p style="font-size: 16px">
                                                {{ $product->description }}
                                            </p>
                                            {{-- <p style="color: #AAAAAA;font-size:14px;">Seller :
                                                {{ $product->shop->legal_name ?? '' }}
                                            </p> --}}
                                            @if ($product->deal_type == 2)
                                                <div class="rating mt-3 mb-3">
                                                    <span style="color: #22cb00">Currently Services are free through
                                                        cartonBoxGuru</span>
                                                </div>
                                                <span class="ms-1" style="font-size:18px;font-weight:500;color:#cd8245">
                                                    {{ formatIndianCurrency($product->discounted_price) }}
                                                </span>
                                            @else
                                                <div class="d-flex">
                                                    <div class="">
                                                        <img src="{{ asset('assets/images/home/icon_delivery.svg') }}"
                                                            alt="icon" class="img-fluid" />
                                                    </div> &nbsp;&nbsp;
                                                    <div class="">
                                                        <p style="font-size: 16px;">
                                                            Delivery Date :
                                                            @if ($product->deal_type == 1)
                                                                {{ $deliveryDays > 0 ? $deliveryDate : 'No delivery date available' }}
                                                            @else
                                                                No delivery date available
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="ms-0">
                                                    <span
                                                        style="font-size:15px;text-decoration: line-through; color:#c7c7c7">
                                                        {{ formatIndianCurrency($product->original_price) }}
                                                    </span>
                                                    <span class="ms-1"
                                                        style="font-size:18px;font-weight:500;color:#cd8245">
                                                        {{ formatIndianCurrency($product->discounted_price) }}
                                                    </span>
                                                    <span class="ms-1"
                                                        style="font-size:18px;font-weight:500; color:#28A745">
                                                        - {{ round($product->discount_percentage) }}% Off
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-md-6">
                                            @if ($product->deal_type === 2)
                                                <div class="d-flex align-items-start my-1 mb-3" style="padding-left:24px">
                                                    <div class="d-flex flex-column ms-0" style="width: 30%">
                                                        <label for="service_date" class="form-label">Service Date</label>
                                                        <input type="date" id="service_date" name="service_date"
                                                            class="form-control form-control-sm service-date"
                                                            value="{{ $item->service_date }}"
                                                            data-cart-id="{{ $cart->id }}"
                                                            data-product-id="{{ $product->id }}">
                                                    </div>
                                                    &nbsp;&nbsp;
                                                    <div class="d-flex flex-column" style="width: 30%;">
                                                        <label for="service_time" class="form-label">Service Time</label>
                                                        <input type="time" id="service_time" name="service_time"
                                                            class="form-control form-control-sm service-time"
                                                            value="{{ $item->service_time }}"
                                                            data-cart-id="{{ $cart->id }}"
                                                            data-product-id="{{ $product->id }}">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center my-1 mb-3"
                                                    style="padding-left: 24px;">
                                                    <span>Qty</span> &nbsp;&nbsp;
                                                    <button class="btn rounded btn-sm decrease-btn"
                                                        data-cart-id="{{ $cart->id }}"
                                                        data-product-id="{{ $product->id }}">-</button>
                                                    <input type="text"
                                                        class="form-control form-control-sm mx-2 text-center quantity-input"
                                                        style="width: 50px;background-color:#F9F9F9;border-radius:2px"
                                                        value="{{ $item->quantity }}" readonly>
                                                    <button class="btn rounded btn-sm increase-btn"
                                                        data-cart-id="{{ $cart->id }}"
                                                        data-product-id="{{ $product->id }}">+</button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-md-end" style="padding-left: 24px">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                {{-- <button type="submit" class="btn save-for-later-btn"
                                                    style="color: #cd8245; border: none;"
                                                    data-product-id="{{ $product->id }}">
                                                    <div class="d-inline-flex align-items-center gap-2 buy_later">
                                                        <div>
                                                            <img src="{{ asset('assets/images/home/icon_save_later.svg') }}"
                                                                alt="icon" class="img-fluid" />
                                                        </div>
                                                        <div
                                                            class="d-inline-flex align-items-center gap-2 buy-for-later-btn">
                                                            <span class="loader spinner-border spinner-border-sm"
                                                                style="display: none;"></span>
                                                            <span>Buy Later</span>
                                                        </div>
                                                    </div>
                                                </button>
                                                &nbsp;&nbsp; --}}
                                                <button type="submit" class="btn cancel-btn cart-remove"
                                                    style="color: #cd8245;border: none"
                                                    data-product-id="{{ $product->id }}"
                                                    data-cart-id="{{ $cart->id }}">
                                                    <div class="d-inline-flex align-items-center gap-2">
                                                        <div>
                                                            <img src="{{ asset('assets/images/home/icon_delete.svg') }}"
                                                                alt="icon" class="img-fluid" />
                                                        </div>
                                                        <div class="d-inline-flex align-items-center gap-2">
                                                            <span class="loader spinner-border spinner-border-sm me-2"
                                                                style="display: none"></span>
                                                            Remove
                                                        </div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            @endforeach
                        </div>
                        <div class="card mb-4 order_summary_card p-3">
                            <div class="card-header m-0 py-3" style="background: #fff">
                                <p class="mb-0" style="font-size:20px;font-weight:400">Order Summary</p>
                            </div>
                            <div class="card-body m-0 p-4 order-summary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p style="color: #AAAAAA;font-size:16px">Subtotal (x<span
                                            class="quantity-value">{{ $cart->quantity }}</span>)</p>
                                    <p class="subtotal">₹{{ number_format($subtotal, 0) }}</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center" style="color: #28A745;">
                                    <p style="font-size:16px">Discount (x<span
                                            class="quantity-value">{{ $cart->quantity }}</span>)</p>
                                    <p class="discount">-₹{{ number_format($total_discount, 0) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-4 mt-4"
                            style="position: sticky; bottom: 0px; background: #fff;border-top: 1px solid #dcdcdc">
                            <div class="d-flex justify-content-end align-items-center">
                                <h4>
                                    Total Amount (x<span class="quantity-value">{{ $cart->quantity }}</span>)&nbsp;&nbsp;
                                    <span style="text-decoration: line-through; color:#c7c7c7" class="subtotal">
                                        {{ formatIndianCurrency($subtotal) }}
                                    </span>
                                    &nbsp;&nbsp;
                                    <span class="total mx-1" style="color:#000">
                                        {{ formatIndianCurrency($subtotal - $total_discount) }}
                                    </span>
                                    <span style="font-size:12px; color:#28A745; white-space: nowrap;">
                                        Congrats, You saved
                                        &nbsp;<span class="discount">- {{ formatIndianCurrency($total_discount) }}</span>
                                    </span>
                                </h4>
                            </div>

                            <div class="d-flex justify-content-end align-items-center"
                                style="position: sticky; bottom: 0px; background: #fff">
                                <a href="{{ url('/cartSummary/' . $cart->id) }}" class="btn cb_checkout_btn">
                                    Checkout
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="cart-items-container" style="display: none;">
                    @if ($cart)
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Your Cart <span style="color: #cd8245">(<span
                                        class="item_count">{{ $cart->items->count() }}</span>)</span></h5>
                            <a href="/" class="text-decoration-none">
                                <button type="button" class="btn showmoreBtn">
                                    Shop more
                                </button>
                            </a>
                        </div>
                        <div class="cart-items">
                            @foreach ($cart->items as $item)
                                <div class="cart-item" data-product-id="{{ $item->product_id }}">
                                    @php
                                        $product = $item->product;
                                        $subtotal += $product->original_price * $item->quantity;
                                        $total_discount +=
                                            ($product->original_price - $product->discounted_price) * $item->quantity;
                                    @endphp
                                    @php
                                        $currentDate = Carbon::now();

                                        $deliveryDays = is_numeric($item->product->delivery_days)
                                            ? (int) $item->product->delivery_days
                                            : 0;

                                        $deliveryDate =
                                            $deliveryDays > 0
                                                ? $currentDate->addDays($deliveryDays)->format('d-m-Y')
                                                : null;
                                    @endphp
                                    <div class="row p-4">
                                        <div class="col-md-4 mb-3">
                                            <div class="d-flex justify-content-center align-items-center">
                                                @php
                                                    $image = isset($product->productMedia)
                                                        ? $product->productMedia
                                                            ->where('order', 1)
                                                            ->where('type', 'image')
                                                            ->first()
                                                        : null;
                                                @endphp
                                                <img src="{{ $image ? asset($image->resize_path) : asset('assets/images/home/noImage.webp') }}"
                                                    style="max-width: 100%; max-height: 100%;"
                                                    alt="{{ $product->name }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <a href="{{ url(path: '/deal/' . $product->id) }}" style="color: #000;"
                                                onclick="clickCount('{{ $product->id }}')">
                                                <p style="font-size: 18px;">
                                                    {{ $product->name }}
                                                </p>
                                            </a>
                                            <p style="font-size: 16px">
                                                {{ $product->description }}
                                            </p>
                                            {{-- <p style="color: #AAAAAA;font-size:14px;">Seller :
                                                {{ $product->shop->legal_name ?? '' }}
                                            </p> --}}
                                            @if ($product->deal_type == 2)
                                                <div class="rating mt-3 mb-3">
                                                    <span style="color: #22cb00">Currently Services are free through
                                                        cartonBoxGuru</span>
                                                </div>
                                                <span class="ms-1" style="font-size:18px;font-weight:500;color:#cd8245">
                                                    ₹{{ number_format($product->discounted_price, 0) }}
                                                </span>
                                            @else
                                                <div class="d-flex">
                                                    <div class="">
                                                        <img src="{{ asset('assets/images/home/icon_delivery.svg') }}"
                                                            alt="icon" class="img-fluid" />
                                                    </div> &nbsp;&nbsp;
                                                    <div class="">
                                                        <p style="font-size: 16px;">
                                                            Delivery Date :
                                                            @if ($product->deal_type == 1)
                                                                {{ $deliveryDays > 0 ? $deliveryDate : 'No delivery date available' }}
                                                            @else
                                                                No delivery date available
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="ms-0">
                                                    <span
                                                        style="font-size:15px;text-decoration: line-through; color:#c7c7c7">
                                                        ₹{{ number_format($product->original_price, 0) }}
                                                    </span>
                                                    <span class="ms-1"
                                                        style="font-size:18px;font-weight:500;color:#cd8245">
                                                        ₹{{ number_format($product->discounted_price, 0) }}
                                                    </span>
                                                    <span class="ms-1"
                                                        style="font-size:18px;font-weight:500; color:#28A745">
                                                        - {{ round($product->discount_percentage) }}% Off
                                                    </span>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-md-6">
                                            @if ($product->deal_type === 2)
                                                <div class="d-flex align-items-start my-1 mb-3" style="padding-left:24px">
                                                    <div class="d-flex flex-column ms-0" style="width: 30%">
                                                        <label for="service_date" class="form-label">Service Date</label>
                                                        <input type="date" id="service_date" name="service_date"
                                                            class="form-control form-control-sm service-date"
                                                            value="{{ $item->service_date }}"
                                                            data-cart-id="{{ $cart->id }}"
                                                            data-product-id="{{ $product->id }}">
                                                    </div>
                                                    &nbsp;&nbsp;
                                                    <div class="d-flex flex-column" style="width: 30%;">
                                                        <label for="service_time" class="form-label">Service Time</label>
                                                        <input type="time" id="service_time" name="service_time"
                                                            class="form-control form-control-sm service-time"
                                                            value="{{ $item->service_time }}"
                                                            data-cart-id="{{ $cart->id }}"
                                                            data-product-id="{{ $product->id }}">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center my-1 mb-3"
                                                    style="padding-left: 24px;">
                                                    <span>Qty</span> &nbsp;&nbsp;
                                                    <button class="btn rounded btn-sm decrease-btn"
                                                        data-cart-id="{{ $cart->id }}"
                                                        data-product-id="{{ $product->id }}">-</button>
                                                    <input type="text"
                                                        class="form-control form-control-sm mx-2 text-center quantity-input"
                                                        style="width: 50px;background-color:#F9F9F9;border-radius:2px"
                                                        value="{{ $item->quantity }}" readonly>
                                                    <button class="btn rounded btn-sm increase-btn"
                                                        data-cart-id="{{ $cart->id }}"
                                                        data-product-id="{{ $product->id }}">+</button>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-md-end" style="padding-left: 24px">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                {{-- <button type="submit" class="btn save-for-later-btn"
                                                    style="color: #cd8245; border: none;"
                                                    data-product-id="{{ $product->id }}">
                                                    <div class="d-inline-flex align-items-center gap-2 buy_later">
                                                        <div>
                                                            <img src="{{ asset('assets/images/home/icon_save_later.svg') }}"
                                                                alt="icon" class="img-fluid" />
                                                        </div>
                                                        <div
                                                            class="d-inline-flex align-items-center gap-2 buy-for-later-btn">
                                                            <span class="loader spinner-border spinner-border-sm"
                                                                style="display: none;"></span>
                                                            <span>Buy Later</span>
                                                        </div>
                                                    </div>
                                                </button>
                                                &nbsp;&nbsp; --}}
                                                <button type="submit" class="btn cancel-btn cart-remove"
                                                    style="color: #cd8245;border: none"
                                                    data-product-id="{{ $product->id }}"
                                                    data-cart-id="{{ $cart->id }}">
                                                    <div class="d-inline-flex align-items-center gap-2">
                                                        <div>
                                                            <img src="{{ asset('assets/images/home/icon_delete.svg') }}"
                                                                alt="icon" class="img-fluid" />
                                                        </div>
                                                        <div class="d-inline-flex align-items-center gap-2">
                                                            <span class="loader spinner-border spinner-border-sm me-2"
                                                                style="display: none"></span>
                                                            Remove
                                                        </div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            @endforeach
                        </div>
                        <div class="card mb-4 order_summary_card p-3">
                            <div class="card-header m-0 py-3" style="background: #fff">
                                <p class="mb-0" style="font-size:20px;font-weight:400">Order Summary</p>
                            </div>
                            <div class="card-body m-0 p-4 order-summary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p style="color: #AAAAAA;font-size:16px">Subtotal (x<span
                                            class="quantity-value">{{ $cart->quantity }}</span>)</p>
                                    <p class="subtotal">₹{{ number_format($subtotal, 0) }}</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center" style="color: #28A745;">
                                    <p style="font-size:16px">Discount (x<span
                                            class="quantity-value">{{ $cart->quantity }}</span>)</p>
                                    <p class="discount">-₹{{ number_format($total_discount, 0) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-4 mt-4"
                            style="position: sticky; bottom: 0px; background: #fff;border-top: 1px solid #dcdcdc">
                            <div class="d-flex justify-content-end align-items-center">
                                <h4>
                                    Total Amount (x<span class="quantity-value">{{ $cart->quantity }}</span>)&nbsp;&nbsp;
                                    <span style="text-decoration: line-through; color:#c7c7c7" class="subtotal">
                                        {{ formatIndianCurrency($subtotal) }}
                                    </span>
                                    &nbsp;&nbsp;
                                    <span class="total ms-1" style="color:#000">
                                        {{ formatIndianCurrency($subtotal - $total_discount) }}
                                    </span>
                                    &nbsp;&nbsp;
                                    <span class="ms-1" style="font-size:12px; color:#28A745; white-space: nowrap;">
                                        Congrats, You saved
                                        &nbsp;<span class="discount">- {{ formatIndianCurrency($total_discount) }}</span>
                                    </span>
                                </h4>
                            </div>

                            <div class="d-flex justify-content-end align-items-center"
                                style="position: sticky; bottom: 0px; background: #fff">
                                <a href="{{ url('/cartSummary/' . $cart->id) }} class="btn cb_checkout_btn">
                                    Checkout
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        {{-- <div class="container mt-5">
            <hr>
            <div class="d-flex">
                <div class="my-4"> <img src="{{ asset('assets/images/home/icon_save_later.svg') }}" alt="icon"
                        class="img-fluid" />
                </div> &nbsp;&nbsp;
                <div class="my-4">
                    <p style="font-size:20px;font-weight:400">Buy Later</p>
                </div>
            </div>
        </div> --}}
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const orderNowButton = document.querySelector('.btn[href*="/cartSummary"]');

            orderNowButton.addEventListener('click', (event) => {
                let valid = true;
                let firstInvalidField = null;
                const currentDate = new Date();
                const currentHours = currentDate.getHours();
                const currentMinutes = currentDate.getMinutes();

                // Clear existing error messages
                document.querySelectorAll('.error-message').forEach(error => error.remove());

                // Loop through all products with deal_type = 2
                document.querySelectorAll('.service-date').forEach((serviceDateField) => {
                    const productId = serviceDateField.getAttribute('data-product-id');
                    const serviceTimeField = document.querySelector(
                        `.service-time[data-product-id="${productId}"]`);

                    // Validate service date
                    if (!serviceDateField.value) {
                        valid = false;
                        displayError(serviceDateField, 'Service Date is required');
                        if (!firstInvalidField) firstInvalidField = serviceDateField;
                    }

                    // Validate service time
                    if (!serviceTimeField.value) {
                        valid = false;
                        displayError(serviceTimeField, 'Service Time is required');
                        if (!firstInvalidField) firstInvalidField = serviceTimeField;
                    } else if (serviceDateField.value) {
                        const selectedDate = new Date(serviceDateField.value);
                        if (selectedDate.toDateString() === currentDate.toDateString()) {
                            const [inputHours, inputMinutes] = serviceTimeField.value.split(':')
                                .map(Number);
                            if (inputHours < currentHours || (inputHours === currentHours &&
                                    inputMinutes < currentMinutes)) {
                                valid = false;
                                displayError(serviceTimeField,
                                    'Service Time must be in the future');
                                if (!firstInvalidField) firstInvalidField = serviceTimeField;
                            }
                        }
                    }
                });

                if (!valid) {
                    event.preventDefault();
                    if (firstInvalidField) firstInvalidField.focus();
                } else {
                    // Update cart for all valid service dates and times
                    document.querySelectorAll('.service-date').forEach((serviceDateField) => {
                        const cartId = serviceDateField.getAttribute('data-cart-id');
                        const productId = serviceDateField.getAttribute('data-product-id');
                        const serviceDate = serviceDateField.value;
                        const serviceTime = document.querySelector(
                            `.service-time[data-product-id="${productId}"]`).value;

                        updateCart(cartId, productId, null, serviceDate, serviceTime);
                    });
                }
            });

            // Function to remove error messages when input changes
            document.querySelectorAll('.service-date, .service-time').forEach((input) => {
                input.addEventListener('input', function() {
                    const error = this.closest('.d-flex').querySelector('.error-message');
                    if (error) error.remove();
                });
            });

            // Function to display error messages
            function displayError(field, message) {
                const fieldContainer = field.closest('.d-flex');
                const errorMessage = document.createElement('span');
                errorMessage.textContent = message;
                errorMessage.style.color = 'red';
                errorMessage.style.fontSize = '12px';
                errorMessage.classList.add('error-message');
                fieldContainer.appendChild(errorMessage);
            }
        });

        document.querySelectorAll('.decrease-btn, .increase-btn').forEach((btn) => {
            btn.addEventListener('click', function() {
                const cartId = this.getAttribute('data-cart-id');
                const productId = this.getAttribute('data-product-id');
                const quantityInput = this.parentElement.querySelector('.quantity-input');
                let quantity = parseInt(quantityInput.value);

                if (this.classList.contains('decrease-btn') && quantity > 1) {
                    quantity -= 1;
                } else if (this.classList.contains('increase-btn') && quantity < 10) {
                    quantity += 1;
                }
                quantityInput.value = quantity;
                updateCart(cartId, productId, quantity);
            });
        });

        document.querySelectorAll('.service-date, .service-time').forEach((input) => {
            input.addEventListener('change', function() {
                const cartId = this.getAttribute('data-cart-id');
                const productId = this.getAttribute('data-product-id');
                const serviceDate = document.querySelector(`.service-date[data-product-id="${productId}"]`)
                    .value;
                const serviceTime = document.querySelector(`.service-time[data-product-id="${productId}"]`)
                    .value;
                updateCart(cartId, productId, null, serviceDate, serviceTime);
            });
        });

        function updateCart(cartId, productId, quantity, serviceDate = null, serviceTime = null) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const data = {
                cart_id: cartId,
                product_id: productId,
                quantity: quantity,
                service_date: serviceDate,
                service_time: serviceTime,
                _token: csrfToken,
            };
            fetch("{{ route('cart.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 'success') {
                        const indianCurrencyFormatter = new Intl.NumberFormat('en-IN', {
                            style: 'currency',
                            currency: 'INR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        });

                        document.querySelectorAll('.quantity-value').forEach((element) => {
                            element.textContent = data.updatedCart.quantity;
                        });

                        document.querySelectorAll('.subtotal').forEach((element) => {
                            element.textContent = indianCurrencyFormatter.format(data.updatedCart.subtotal);
                        });

                        document.querySelectorAll('.discount').forEach((element) => {
                            let discountValue = data.updatedCart.discount;
                            if (discountValue < 0) {
                                element.textContent =
                                    `- ${indianCurrencyFormatter.format(Math.abs(discountValue))}`;
                            } else {
                                element.textContent = `- ${indianCurrencyFormatter.format(discountValue)}`;
                            }
                        });
                        document.querySelectorAll('.total').forEach((element) => {
                            element.textContent = indianCurrencyFormatter.format(data.updatedCart.grand_total);
                        });
                    } else {
                        alert(data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error updating cart:', error);
                });
        }

        function showLoader(form) {
            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;

            // Add loader element
            const loader = document.createElement('span');
            loader.className = 'custom-loader';
            button.appendChild(loader);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const currentDate = today.toISOString().split('T')[0]; // Format 'YYYY-MM-DD'

            // Calculate the next date
            const nextDate = new Date(today);
            nextDate.setDate(today.getDate() + 1);
            const nextDateString = nextDate.toISOString().split('T')[0];

            document.querySelectorAll('.service-date').forEach((serviceDateField) => {
                // Set the minimum date to the day after tomorrow
                const restrictedMinDate = new Date(nextDate);
                restrictedMinDate.setDate(nextDate.getDate() + 1);

                serviceDateField.setAttribute('min', restrictedMinDate.toISOString().split('T')[0]);

                // Handle user-typed values (if they bypass the UI)
                serviceDateField.addEventListener('change', () => {
                    const selectedDate = serviceDateField.value;
                    if (selectedDate === currentDate || selectedDate === nextDateString) {
                        serviceDateField.value = ''; // Clear invalid date
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Attach event listeners to all dynamic links
            document.querySelectorAll('.dynamic-link').forEach(link => {
                link.addEventListener('click', function(event) {
                    // Check if the cart has more than one item
                    const cartItemCount = {{ $cart->items->count() }};
                    if (cartItemCount <= 1) {
                        // Prevent default behavior (navigation) if cart has 1 or fewer items
                        event.preventDefault();
                        return;
                    }

                    // If cart has more than one item, handle navigation dynamically
                    const productId = this.getAttribute('data-product-id');
                    const url = `/deal/${productId}`;

                    // Use Fetch API or any other method to load content dynamically
                    fetch(url)
                        .then(response => response.text())
                        .then(data => {
                            // Update the page content dynamically
                            document.getElementById('content').innerHTML =
                                data; // Replace 'content' with your target element ID
                        })
                        .catch(error => {
                            console.error('Error loading content:', error);
                        });

                    // Prevent default navigation
                    event.preventDefault();
                });
            });
        });
    </script>
@endsection
