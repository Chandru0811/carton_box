@extends('layouts.master')

@section('content')
    {{-- @if (session('status'))
        <div class="alert alert-dismissible fade show toast-success" role="alert"
            style="position: fixed; top: 70px; right: 40px; z-index: 1050;">
            <div class="toast-content">
                <div class="toast-icon">
                    <i class="fa-solid fa-check-circle" style="color: #16A34A"></i>
                </div>
                <span class="toast-text"> {!! nl2br(e(session('status'))) !!}</span>&nbsp;&nbsp;
                <button class="toast-close-btn" data-bs-dismiss="alert" aria-label="Close">
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
                <button class="toast-close-btn" data-bs-dismiss="alert" aria-label="Close">
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
                <button class="toast-close-btn" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa-solid fa-xmark" style="color: #EF4444"></i>
                </button>
            </div>
        </div>
    @endif --}}
    @php
        $selectedAddressId = session('selectedId');
        $default_address =
            $addresses->firstWhere('id', $selectedAddressId) ?? ($addresses->firstWhere('default', true) ?? null);
    @endphp
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
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="text-center mb-0" style="visibility: hidden">p</p>

                    <div class="card p-3 mb-3">
                        <div class="d-flex align-items-center">
                            <h5 class="fw-bold mb-0">Delivery Addresses</h5> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="change-address-btn defaultAddress">
                                @if ($default_address)
                                    <span class="badge badge_infos py-1" data-bs-toggle="modal"
                                        data-bs-target="#myAddressModal">Change</span>
                                @else
                                    <button type="button" class="btn primary_new_btn" style="font-size: 11px"
                                        data-bs-toggle="modal" data-bs-target="#newAddressModal"
                                        onclick="checkAddressAndOpenModal()">
                                        <i class="fa-light fa-plus"></i> Add New Address
                                    </button>
                                @endif
                            </span>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 selected-address">
                                @if ($default_address)
                                    <p>
                                        <strong>{{ $default_address->first_name ?? '' }}
                                            {{ $default_address->last_name ?? '' }} (+91)
                                            {{ $default_address->phone ?? '' }}</strong>&nbsp;&nbsp;<br>
                                        {{ $default_address->address ?? '' }},
                                        {{ $default_address->city ?? '' }},
                                        {{ $default_address->state ?? '' }} -
                                        {{ $default_address->postalcode ?? '' }}
                                        <span>
                                            @if ($default_address->default)
                                                <span class="badge badge_danger py-1">Default</span>&nbsp;&nbsp;
                                            @endif
                                        </span>
                                    </p>
                                @else
                                    <p class="font-weight-bold">Uh-oh! It looks like you don't have a default
                                        address set yet. Add one now to speed up your checkout process!</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        @foreach ($carts->items as $item)
                            @php
                                $product = $item->product;
                                $image = isset($product->productMedia)
                                    ? $product->productMedia->where('order', 1)->where('type', 'image')->first()
                                    : null;
                            @endphp
                            <div class="row px-4 pt-4">
                                <div class="col-md-3 d-flex flex-column justify-content-center align-items-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <img src="{{ $image ? asset($image->resize_path) : asset('assets/images/home/noImage.webp') }}"
                                            style="max-width: 100%; max-height: 100%;" alt="{{ $product->name }}" />
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h5>{{ $product->name }}</h5>
                                    <h6 class="truncated-description">{{ $product->description }}</h6>
                                    @php
                                        $currentDate = Carbon::now();

                                        $deliveryDays = is_numeric($product->delivery_days)
                                            ? (int) $product->delivery_days
                                            : 0;

                                        $deliveryDate =
                                            $deliveryDays > 0
                                                ? $currentDate->addDays($deliveryDays)->format('d-m-Y')
                                                : null;
                                    @endphp
                                    @if ($product->deal_type == 1)
                                        <div class="rating my-2">
                                            <img src="{{ asset('assets/images/home/icon_delivery.svg') }}" alt="icon"
                                                class="img-fluid" /> &nbsp;
                                            <span>Delivery Date :</span><span class="stars">
                                                <span>
                                                    {{ $deliveryDays > 0 ? $deliveryDate : 'No delivery date available' }}
                                                </span>
                                            </span>
                                        </div>
                                    @else
                                        <div class="rating mt-3 mb-3">
                                            <span style="color: #22cb00">Currently Services are free through
                                                DealsMachi</span>
                                        </div>
                                    @endif

                                    <p>Seller : {{ $product->shop->legal_name ?? '' }}</p>

                                    <div>
                                        @if ($product->deal_type == 2)
                                            <span class="ms-1" style="font-size:22px;color:#cd8245">
                                                {{ formatIndianCurrency($product->discounted_price) }}
                                            </span>
                                        @else
                                            <span style="text-decoration: line-through; color:#c7c7c7">
                                                {{ formatIndianCurrency($product->original_price) }}
                                            </span>
                                            <span class="ms-1" style="font-size:22px;color:#cd8245">
                                                {{ formatIndianCurrency($product->discounted_price) }}
                                            </span>

                                            <span class="ms-1" style="font-size:12px; color:#2d8937">
                                                {{ round($product->discount_percentage) }}% off
                                            </span>&nbsp; &nbsp; &nbsp;
                                        @endif
                                        @if ($item->deal_type == 1)
                                            <span>Quantity : {{ $item->quantity ?? 1 }}</span>
                                        @else
                                            <span>Date: {{ $item->service_date ?? '--' }}, Time:
                                                {{ $item->service_time ? \Carbon\Carbon::createFromFormat('H:i:s', $item->service_time)->format('h:i A') : '--' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="mt-2">
                            </div>
                        @endforeach
                    </div>
                    <div class="card mb-4">
                        <div class="card-header m-0 p-2">
                            <p class="mb-0">Order Summary</p>
                        </div>
                        <div class="card-body m-0 p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <p>Subtotal (x{{ $carts->quantity }})</p>
                                <p>{{ formatIndianCurrency($carts->total) }}</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="color: #2d8937;">
                                <p>Discount (x{{ $carts->quantity }})</p>
                                <p>{{ formatIndianCurrency($carts->discount) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-4 mt-4"
                        style="position: sticky; bottom: 0px; background: #fff;border-top: 1px solid #dcdcdc">
                        <div class="d-flex justify-content-end align-items-center">
                            <h4>Total Amount (x{{ $carts->quantity }}) &nbsp;&nbsp;
                                <span style="text-decoration: line-through; color:#c7c7c7" class="subtotal">
                                    {{ formatIndianCurrency($carts->total) }}
                                </span>
                                &nbsp;&nbsp;
                                <span class="total ms-1" style="color:#000">
                                    {{ formatIndianCurrency($carts->grand_total) }}</span>
                                &nbsp;&nbsp;
                                <span class="ms-1" style="font-size:12px; color:#2d8937;white-space: nowrap;">
                                    Congrats, You saved 
                                    &nbsp;<span class="discount">- {{ formatIndianCurrency($carts->discount) }}</span>
                                </span>
                            </h4>
                        </div>
                        <div class="d-flex justify-content-end align-items-center p-3"
                            style="position: sticky; bottom: 0px; background: #fff">
                            @if ($default_address)
                                <form action="{{ route('checkout.cart') }}" method="POST" id="checkoutForm">
                                    @csrf
                                    <input type="hidden" name="cart_id" value="{{ $carts->id }}">
                                    <input type="hidden" name="address_id" id="addressID"
                                        value="{{ $default_address->id }}">
                                    <button type="submit" class="btn cb_checkout_btn">
                                        Checkout
                                    </button>
                                </form>
                            @else
                                <a href="#" class="btn cb_checkout_btn" data-bs-toggle="modal" id="moveCartToCheckout"
                                    data-bs-target="#newAddressModal" data-cart-id="{{ $carts->id }}"
                                    onclick="checkAddressAndOpenModal()">
                                    Checkout
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
