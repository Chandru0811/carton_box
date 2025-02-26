<div class="child cartDrop">
    <p class="text_size" style="color: #cbcbcb">Recently Added Products</p>
    <div class="cart_items">
        @if (isset($cartItems) && $cartItems->count() > 0)
            @foreach ($cartItems->take(6) as $item)
                <div class="d-flex  ">
                    @php
                        $image = isset($item->product->productMedia)
                            ? $item->product->productMedia->where('order', 1)->where('type', 'image')->first()
                            : null;
                    @endphp
                    <img src="{{ $image ? asset($image->resize_path) : asset('assets/images/home/noImage.webp') }}"
                        class="img-fluid dropdown_img" alt="{{ $item->product->name }}" />
                    <div class="text-start">
                        <p class="text-start px-1 text-wrap m-0 p-0" style="font-size: 12px; white-space: normal;">
                            {{ \Illuminate\Support\Str::limit($item->product->name, 20) }}
                        </p>
                        <p class="px-1 cb_text_clr" style="font-size: 12px;">
                            ₹ {{ number_format($item->discount, 0) }}
                        </p>
                    </div>
                </div>
            @endforeach
            @if ($cartItems->count() > 6)
                <div class="text-end mb-2">
                    <a style="font-size: 13px; cursor: pointer;" class="cart-screen">View All</a>
                </div>
            @endif
        @else
            <div class="text-center cartEmpty">
                <img src="{{ asset('assets/images/home/empty_cart.webp') }}" alt="Empty Cart" class="img-fluid"
                    width="75">
                <p class="text_size" style="color: #cbcbcb">Your cart is empty</p>
            </div>
        @endif
    </div>
    <div class="dropdown_cart_view d-flex justify-content-end">
        <a class="text_size text-decoration-none d-none d-xl-inline cart-screen" style="text-decoration: none;">View My
            Shopping Cart</a>
    </div>
</div>
