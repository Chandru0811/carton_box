@extends('layouts.master')

@section('content')
    <?php
    
    use Carbon\Carbon;
    
    $currentDate = Carbon::now();
    
    try {
        if (isset($order->items[0]->product->delivery_days, $order->created_at) && is_numeric($order->items[0]->product->delivery_days)) {
            $deliveryDays = (int) $order->items[0]->product->delivery_days;
    
            $deliveryDate = $deliveryDays > 0 ? Carbon::parse($order->created_at)->addDays($deliveryDays)->format('d-m-Y') : null;
        } else {
            $deliveryDays = 0;
            $deliveryDate = null;
        }
    } catch (\Exception $e) {
        $deliveryDays = 0;
        $deliveryDate = null;
    }
    
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
    ?>

@if (session('status'))
<div class="alert alert-dismissible fade show toast-success" role="alert"
    style="position: fixed; top: 100px; right: 40px; z-index: 1050;">
    <div class="toast-content">
        <div class="toast-icon">
            <i class="fa-solid fa-check-circle" style="color: #16A34A"></i>
        </div>
        <span class="toast-text"> {!! nl2br(e(session('status'))) !!}</span>&nbsp;&nbsp;
        <button class="toast-close-btn"data-bs-dismiss="alert" aria-label="Close">
            <i class="fa-solid fa-times" style="color: #16A34A"></i>
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
    @if ($order)
        <div class="container categoryIcons p-3">
            <div>
                <div class="container">
                    <div class="order_heading">
                        <div class="order_align gap-2">
                            <div>
                                <h4 class="text-dark order_id mb-0 me-2 text-nowrap">
                                    Order Item ID: <span>{{ $order->items[0]->item_number ?? 'N/A' }}</span>
                                </h4>
                            </div>
                            <div class="d-flex gap-2 ">
                                <p class="text-nowrap">
                                    <span class="badge_warning me-2">
                                        {{ $order->payment_status === '1'
                                            ? 'Not Paid'
                                            : ($order->payment_status === '2'
                                                ? 'Pending'
                                                : ($order->payment_status === '3'
                                                    ? 'Paid'
                                                    : ($order->payment_status === '4'
                                                        ? 'Refund Initiated'
                                                        : ($order->payment_status === '5'
                                                            ? 'Refunded'
                                                            : ($order->payment_status === '6'
                                                                ? 'Refund Error'
                                                                : 'Unknown Status'))))) }}
                                    </span>
                                </p>
                                <p class="text-nowrap">
                                    <span
                                        class="{{ $order->order_type === 'service' ? 'badge_default' : 'badge_payment' }}">
                                        {{ $order->items[0]->deal_type == 1 ? 'Product' : ($order->items[0]->deal_type == 2 ? 'Service' : '') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        @if (isset($order->items[0]->product) &&
                                !($order->items[0]->product->active == 0 || $order->items[0]->product->deleted_at != null))
                            @if ($order->items[0]->shop->deleted_at == null)
                                <div class="col-12 col-md-auto d-flex flex-wrap gap-2">
                                    @if (isset($order->items[0]->product->slug) && $order->items[0]->product->slug)
                                        <form action="{{ route('cart.add', ['slug' => $order->items[0]->product->slug]) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="saveoption" id="saveoption" value="buy now">
                                            {{-- <button type="submit" class="btn showmoreBtn sm_screen_btn">Order
                                                again</button> --}}
                                        </form>
                                    @endif
                                    <div>

                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        {{-- Order Item --}}
                        <div class="card mb-4">
                            <div
                                class="card-header cb_card_hd m-0 p-2 d-flex gap-2 align-items-start justify-content-between">
                                <div class="order_align gap-2">
                                    <div>
                                        <p class="mb-0">Order Item</p>
                                    </div>
                                    <div class="d-flex gap-2 mt-1">
                                        <p class="badge_danger">
                                            {{ $order->status === '1'
                                                ? 'Created'
                                                : ($order->status === '2'
                                                    ? 'Payment Error'
                                                    : ($order->status === '3'
                                                        ? 'Confirmed'
                                                        : ($order->status === '4'
                                                            ? 'Awaiting Delivery'
                                                            : ($order->status === '5'
                                                                ? 'Delivered'
                                                                : ($order->status === '6'
                                                                    ? 'Returned'
                                                                    : ($order->status === '7'
                                                                        ? 'Cancelled'
                                                                        : 'Unknown Status')))))) }}
                                        </p>
                                        @if ($order->items[0]->coupon_code == null)
                                            <p class="badge_payment">No Coupon Code</p>
                                        @else
                                            <p class="badge_payment">{{ $order->items[0]->coupon_code }}</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="date_align mb-1">
                                    <span>
                                        Date :
                                        <span>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</span>
                                    </span>
                                </p>
                            </div>
                            <div class="card-body m-0 p-4">
                                @foreach ($order->items as $item)
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-3">
                                            @php
                                                $image = isset($item->product->productMedia)
                                                    ? $item->product->productMedia
                                                        ->where('order', 1)
                                                        ->where('type', 'image')
                                                        ->first()
                                                    : null;
                                            @endphp
                                            <img src="{{ $image ? asset($image->resize_path) : asset('assets/images/home/noImage.webp') }}"
                                                class="img-fluid" alt="{{ $item->item_description }}" />
                                        </div>
                                        <div class="col">
                                            @if ($item->order_id)
                                                @if (isset($order->items[0]->product) &&
                                                        !($order->items[0]->product->active == 0 || $order->items[0]->product->deleted_at != null))
                                                    @if ($order->items[0]->shop->deleted_at == null)
                                                        <a href="{{ url(path: '/deal/' . $order->items[0]->product_id) }}"
                                                            style="color: #000;"
                                                            onclick="clickCount('{{ $order->items[0]->deal_id }}')">
                                                            <p style="font-size: 24px;">
                                                                {{ $item->item_description }}
                                                            </p>
                                                        </a>
                                                    @else
                                                        <p style="font-size: 24px; color: #000; text-decoration: none;">
                                                            {{ $item->item_description }}
                                                        </p>
                                                    @endif
                                                @else
                                                    <p style="font-size: 24px; color: #000; text-decoration: none;">
                                                        {{ $item->item_description }}
                                                    </p>
                                                @endif
                                                <p class="truncated-description mb-3">
                                                    {{ $item->product->description ?? 'No Description Found' }}
                                                </p>
                                                @if ($item->deal_type === '1')
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset('assets/images/home/icon_delivery.svg') }}"
                                                            alt="icon" class="img-fluid"
                                                            style="width:3%; height:3%;" />&nbsp;
                                                        Delivery Date:
                                                        {{ $deliveryDays > 0 ? $deliveryDate : 'No delivery date available' }}

                                                    </div>
                                                @else
                                                    <div class="rating mb-4 mt-4">
                                                        <span style="color: #22cb00">Currently Services are free through
                                                            DealsMachi</span>
                                                    </div>
                                                @endif
                                                <div class="mb-3">
                                                    <span> Seller Company Name: {{ $item->shop->legal_name }}</span>
                                                </div>
                                                <p class="mb-0">
                                                    @if ($item->deal_type == 2)
                                                        <span style="color:#ff0060; font-size:24px">
                                                            {{-- ₹{{ number_format($item->discount, 0) }} --}}
                                                            {{ formatIndianCurrency($item->discount) }}
                                                        </span>
                                                        &nbsp;&nbsp;
                                                        &nbsp;&nbsp;
                                                    @else
                                                        <del>
                                                            {{-- ₹{{ number_format($item->unit_price, 0) }} --}}
                                                            {{ formatIndianCurrency($item->unit_price) }}
                                                        </del>
                                                        &nbsp;&nbsp;
                                                        <span style="color:#ff0060; font-size:24px">
                                                            {{-- ₹{{ number_format($item->discount, 0) }} --}}
                                                            {{ formatIndianCurrency($item->discount) }}
                                                        </span>
                                                        &nbsp;&nbsp;
                                                        <span
                                                            class="badge_danger">{{ number_format($item->discount_percent, 0) }}%
                                                            saved
                                                        </span>
                                                    @endif
                                                </p>
                                            @else
                                                <div class="nodata">No Product Data Available!</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-9">
                                        @foreach ($order->items as $item)
                                            @if ($item->deal_type === '2')
                                                <div class="d-flex gap-4">
                                                    <p>Service Date: {{ $item->service_date ?? 'N/A' }}</p>
                                                    <p>Service Time:
                                                        {{ \Carbon\Carbon::parse($item->service_time)->format('h:i A') ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            @else
                                                <div class="d-flex gap-4">
                                                    <p>Quantity: {{ $item->quantity ?? ' ' }}</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Shop Details --}}
                        {{-- <div class="card mb-4">
                            <div class="card-header cb_card_hd m-0 p-2 d-flex gap-2 align-items-center" >
                                <p class="mb-0">Shop Details</p>
                            </div>
                            <div class="card-body m-0 p-4">
                                @if ($order->items->isNotEmpty() && $order->items->first()->shop)
                                    <p>Company Name : {{ $order->items->first()->shop->name ?? 'N/A' }}</p>
                                    <p>Company Email : {{ $order->items->first()->shop->email ?? 'N/A' }}</p>
                                    <p>Company Mobile : {{ $order->items->first()->shop->mobile ?? 'N/A' }}</p>
                                    <p>Description : {{ $order->items->first()->shop->description ?? 'N/A' }}</p>
                                    <p>
                                        Address:
                                        @php
                                            $addressParts = [];

                                            if ($order->items->first()->shop->street) {
                                                $addressParts[] = $order->items->first()->shop->street;
                                            }
                                            if ($order->items->first()->shop->street2) {
                                                $addressParts[] = $order->items->first()->shop->street2;
                                            }
                                            if ($order->items->first()->shop->city) {
                                                $addressParts[] = $order->items->first()->shop->city;
                                            }
                                            if ($order->items->first()->shop->zip_code) {
                                                $addressParts[] = $order->items->first()->shop->zip_code;
                                            }
                                        @endphp

                                        @if (!empty($addressParts))
                                            {{ implode(', ', $addressParts) }}
                                        @endif
                                        {{-- @if ($order->items->first()->shop->street)
                                            {{ $order->items->first()->shop->street }}
                                        @endif
                                        @if ($order->items->first()->shop->street2)
                                            ,{{ $order->items->first()->shop->street2 }}
                                        @endif
                                        @if ($order->items->first()->shop->city)
                                            , {{ $order->items->first()->shop->city }}
                                        @endif
                                        @if ($order->items->first()->shop->zip_code)
                                            , {{ $order->items->first()->shop->zip_code }}
                                        @endif --}}
                        {{-- </p>
                                @else
                                    <p>No Shop Details Available</p>
                                @endif
                            </div>
                        </div> --}}
                        {{-- Order Summary --}}
                        <div class="card mb-4">
                            <div class="card-header cb_card_hd m-0 p-2 d-flex justify-content-between align-items-center">
                                <p class="mb-0">Order Summary</p>
                                <p>
                                    <span
                                        class="{{ $order->payment_type === 'online_payment' ? 'badge_default' : 'badge_payment' }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_type ?? 'Pending')) }}
                                    </span>&nbsp;
                                    <span
                                        class="badge_warning">{{ $order->payment_status === '1'
                                            ? 'Not Paid'
                                            : ($order->payment_status === '2'
                                                ? 'Pending'
                                                : ($order->payment_status === '3'
                                                    ? 'Paid'
                                                    : ($order->payment_status === '4'
                                                        ? 'Refund Initiated'
                                                        : ($order->payment_status === '5'
                                                            ? 'Refunded'
                                                            : ($order->payment_status === '6'
                                                                ? 'Refund Error'
                                                                : 'Unknown Status'))))) }}</span>
                                </p>
                            </div>
                            <div class="card-body m-0 p-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal
                                        @if ($item->quantity != 1)
                                            (x{{ $item->quantity }})
                                        @endif
                                    </span>
                                    <span id="subtotal"></span>
                                </div>
                                <div class="d-flex justify-content-between" style="color: #00DD21;">
                                    <span>Discount
                                        @if ($item->quantity != 1)
                                            (x{{ $item->quantity }})
                                        @endif
                                    </span>
                                    <span id="discount"></span>
                                </div>
                                <hr />
                                <div class="d-flex justify-content-between pb-3">
                                    <span>Total
                                        @if ($item->quantity != 1)
                                            (x{{ $item->quantity }})
                                        @endif
                                    </span>
                                    <span id="total"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Right Column: Notes, Customer Info, Contact, and Address --}}
                    <div class="col-md-4">
                        {{-- Notes {{--
                      {{--  <div class="card mb-4">
                            <div class="card-header cb_card_hd m-0 p-2" >
                                <p class="mb-0">Notes</p>
                            </div>
                            <div class="card-body m-0 p-4">
                                <p>{{ $order->items->first()?->item_description ?? 'No notes available' }}</p>
            </div>
        </div> --}}
                        {{-- Contact Information --}}
                        <div class="card mb-4">
                            <div class="card-header cb_card_hd m-0 p-2">
                                <p class="mb-0">Contact Information</p>
                            </div>

                            <div class="card-body m-0 p-4">
                                <?php
                                $deliveryAddress = json_decode($order->delivery_address, true);
                                ?>
                                <p>Name :
                                    {{ ($deliveryAddress['first_name'] ?? '') . ' ' . ($deliveryAddress['last_name'] ?? '') }}
                                </p>
                                <p>Email : {{ $deliveryAddress['email'] ?? '' }}</p>
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <?php
                        $deliveryAddress = json_decode($order->delivery_address, true);
                        ?>
                        <div class="card mb-4">
                            <div class="card-header cb_card_hd m-0 p-2">
                                <p class="mb-0">Address</p>
                            </div>
                            <div class="card-body m-0 p-4">

                                <p>Phone : {{ $deliveryAddress['phone'] ?? '' }}</p>
                                <p>Address:
                                    @if (!empty($deliveryAddress['address']))
                                        {{ $deliveryAddress['address'] }}
                                    @endif
                                    @if (!empty($deliveryAddress['city']))
                                        , {{ $deliveryAddress['city'] }}
                                    @endif
                                    @if (!empty($deliveryAddress['state']))
                                        , {{ $deliveryAddress['state'] }}
                                    @endif
                                    @if (!empty($deliveryAddress['postalcode']))
                                        , {{ $deliveryAddress['postalcode'] }}
                                    @endif
                                    @if (!empty($deliveryAddress['unit']))
                                        - {{ $deliveryAddress['unit'] }}
                                    @endif

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-12 d-flex justify-content-center align-items-center text-center" style="min-height: 75vh;">
            <div class="col-12 col-md-12" style="color: rgb(128, 128, 128);">
                <h4>Oh no! We couldn't find the order you're looking for.</h4>
                <p style="margin-top: 10px; font-size: 14px;">Please check the order ID and try again.</p>
                <a href="{{ url('/') }}" style="color: #007BFF; text-decoration: underline;">Back to
                    Home</a>
            </div>
        </div>
    @endif



    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stars = document.querySelectorAll('#starRating .star');
            const starRatingInput = document.getElementById('rating');
            let selectedRating = 0;

            stars.forEach((star) => {
                // Click event for selecting a rating
                star.addEventListener('click', function() {
                    selectedRating = parseInt(this.dataset.value, 10); // Update selected rating
                    starRatingInput.value = selectedRating; // Set the hidden input value
                    updateStars(selectedRating);
                });

                // Mouseover event for previewing rating
                star.addEventListener('mouseover', function() {
                    const hoverValue = parseInt(this.dataset.value, 10);
                    updateStars(hoverValue);
                });

                // Mouseout event to reset stars to the selected rating
                star.addEventListener('mouseout', function() {
                    updateStars(selectedRating);
                });
            });

            // Function to update the stars
            function updateStars(value) {
                stars.forEach((s, index) => {
                    const icon = s.querySelector('i');
                    if (index < value) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        icon.style.color = '#fdbf46'; // Filled star color
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        icon.style.color = '#ccc'; // Unfilled star color
                    }
                });
            }

            // Initialize stars to the default state (unselected)
            updateStars(selectedRating);
        });



        @if ($order)
            const orderItems = @json($order->items);

            let subtotal = orderItems.reduce((sum, item) => sum + (parseFloat(item.unit_price) * item.quantity), 0);
            let total = orderItems.reduce((sum, item) => sum + (parseFloat(item.discount) * item.quantity), 0);
            let discount = subtotal - total;

            const formattedSubtotal =
                `{{ formatIndianCurrency($order->items->reduce(fn($sum, $item) => $sum + $item['unit_price'] * $item['quantity'], 0)) }}`;
            const formattedTotal =
                `{{ formatIndianCurrency($order->items->reduce(fn($sum, $item) => $sum + $item['discount'] * $item['quantity'], 0)) }}`;
            // `₹{{ number_format($order->items->reduce(fn($sum, $item) => $sum + $item['discount'] * $item['quantity'], 0), 0) }}`;
            const formattedDiscount =
                `-{{ formatIndianCurrency($order->items->reduce(fn($sum, $item) => $sum + $item['unit_price'] * $item['quantity'], 0) - $order->items->reduce(fn($sum, $item) => $sum + $item['discount'] * $item['quantity'], 0)) }}`;
            // `-₹{{ number_format($order->items->reduce(fn($sum, $item) => $sum + $item['unit_price'] * $item['quantity'], 0) - $order->items->reduce(fn($sum, $item) => $sum + $item['discount'] * $item['quantity'], 0), 0) }}`;

            document.getElementById('subtotal').innerText = formattedSubtotal;
            document.getElementById('discount').innerText = formattedDiscount;
            document.getElementById('total').innerText = formattedTotal;
        @endif
    </script>

@endsection
