@extends('layouts.master')

@section('content')
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
    <div class="container categoryIcons p-3">
        <div class="d-flex justify-content-between mb-3">
            <h3 style="color: #cd8245">
                My Orders
                @if ($orders->isNotEmpty())
                    ({{ $orders->sum(function ($order) {return $order->items->count();}) }})
                @endif
            </h3>
            <a href="/" class="text-decoration-none">
                <button type="button" class="btn showmoreBtn">
                    Shop more
                </button>
            </a>
        </div>
        @if ($orders->isNotEmpty())
            @foreach ($orders as $order)
                @foreach ($order->items as $item)
                    <a class="text-decoration-none"
                        href="{{ url('order', ['id' => $order->id, 'product_id' => $item->product_id]) }}">
                        <div class="card orderCard p-3 mb-3">
                            <div class="coupon_code_align pb-lg-2">
                                <div class="coupon_code_font">
                                    <div class="coupon_code_font">
                                        {{-- <p class="text-nowrap mb-1">Order Id: {{ $order->order_number ?? 'N/A' }},</p> --}}
                                        <p class="text-nowrap mb-0">Order Id : {{ $item->item_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="batch_code mt-1">
                                        <p class="text-nowrap mb-1"><span
                                                class="badge_payment">{{ $order->status === '1'
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
                                                                            : 'Unknown Status')))))) }}</span>
                                        </p>
                                        {{-- <p class="text-nowrap mb-1"><span class="badge_warning">
                                                {{ $item->deal_type == 1 ? 'Product' : ($item->deal_type == 2 ? 'Service' : '') }}
                                            </span></p> --}}
                                    </div>
                                </div>
                                <div class="text-start">
                                    <p class="date_align mb-1">
                                        Date :
                                        <span>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="row mt-3">
                                    <div class="col-lg-3 col-md-3 col-12">
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
                                    <div class="col-lg-9 col-md-9 col-12">
                                        {{-- <a href="{{ url(path: '/deal/' . $item->product_id) }}" style="color: #000;"
                                            onclick="clickCount('{{ $item->product_id }}')">
                                            <p class="mb-1" style="font-size: 24px;">
                                                {{ $item->item_description ?? 'No Name Available' }}
                                            </p>
                                        </a> --}}
                                        <p class="mb-1" style="font-size: 24px;">
                                            {{ $item->item_description ?? 'No Name Available' }}
                                        </p>
                                        <p class="mb-1 truncated-description">
                                            {{ $item->product->description ?? 'No Description Available' }}
                                        </p>
                                        @if ($item->deal_type === '1' || $item->deal_type === 'Product')
                                            <?php
                                            
                                            $currentDate = Carbon::now();
                                            
                                            try {
                                                if (isset($item->product->delivery_days, $order->created_at) && is_numeric($item->product->delivery_days)) {
                                                    $deliveryDays = (int) $item->product->delivery_days;
                                            
                                                    $deliveryDate = $deliveryDays > 0 ? Carbon::parse($order->created_at)->addDays($deliveryDays)->format('d-m-Y') : null;
                                                } else {
                                                    $deliveryDays = 0;
                                                    $deliveryDate = null;
                                                }
                                            } catch (\Exception $e) {
                                                $deliveryDays = 0;
                                                $deliveryDate = null;
                                            }
                                            ?>

                                            <div>
                                                <div class="d-flex mt-3 mb-3">
                                                    <p class="mt-1 mb-0">Quantity : {{ $item->quantity }}</p>
                                                    &nbsp;&nbsp;&nbsp;<div class="">
                                                        <img src="{{ asset('assets/images/home/icon_delivery.svg') }}"
                                                            alt="icon" class="img-fluid" />
                                                    </div> &nbsp;&nbsp;
                                                    <p class="mt-1 mb-0">Delivery Date:
                                                        {{ $deliveryDays > 0 ? $deliveryDate : 'No delivery date available' }}
                                                    </p>
                                                </div>
                                                <p>
                                                    <del>
                                                        {{-- ₹{{ number_format($item->unit_price * $item->quantity, 0) }} --}}
                                                        {{ formatIndianCurrency($item->unit_price * $item->quantity) }}
                                                    </del>
                                                    &nbsp;
                                                    <span style="color: #cd8245; font-size:24px">
                                                        {{-- ₹{{ number_format($item->discount * $item->quantity, 0) }} --}}
                                                        {{ formatIndianCurrency($item->discount * $item->quantity) }}
                                                    </span> &nbsp;
                                                    <span class="badge_payment">
                                                        {{-- {{ number_format($item->discount_percent, 0) }}% --}}
                                                        {{ formatIndianCurrency($item->discount_percent) }}%
                                                        saved</span>
                                                </p>
                                            </div>
                                        @else
                                            <div>
                                                <div class="rating mt-3 mb-3">
                                                    <span style="color: #22cb00">Currently Services are free through
                                                        cartonBoxGuru</span>
                                                </div>
                                                <p>
                                                    <span style="color: #cd8245; font-size:24px">
                                                        {{-- ₹{{ number_format($item->discount * $item->quantity, 0) }} --}}
                                                        {{ formatIndianCurrency($item->discount * $item->quantity) }}
                                                    </span> &nbsp;
                                                </p>
                                                <div class="d-flex justify-content-start align-items-center">
                                                    @if ($item->deal_type === '2' || $item->deal_type === 'Product')
                                                        <p class="mt-1 mb-0">Service Date : {{ $item->service_date }}
                                                        </p>
                                                    @endif
                                                    &nbsp;&nbsp;
                                                    @if ($item->deal_type === '2' || $item->deal_type === 'Product')
                                                        <p class="mt-1 mb-0">Service Time :
                                                            {{ $item->service_time ? \Carbon\Carbon::parse($item->service_time)->format('h:i A') : 'N/A' }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end align-items-center">
                                <span
                                    class="badge_warning">{{ ucfirst(str_replace('_', ' ', $order->payment_type ?? 'N/A')) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endforeach
        @else
            <p>No orders available.</p>
        @endif
    </div>
@endsection
