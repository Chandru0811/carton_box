<div class="container-fluid px-lg-5">
    <div class="row pb-4">
        @foreach ($products as $product)
            <div class="col-sm-6 col-md-4 col-lg-3 col-12 mb-2 p-3 d-flex align-items-stretch justify-content-center">
                <a href="{{ url('/deal/' . $product->id) }}" class="cb_products">
                    <div class="card h-100 position-relative cp_card">
                        <div class="cb_badge">{{ number_format($product['discount_percentage'], 0) }}% OFF</div>
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
                                <a href="#" class="btn cb_add_cart add-to-cart-btn"
                                    data-slug="{{ $product->slug }}" data-qty="1"
                                    onclick="event.stopPropagation();">Add to cart</a>
                            </div>
                            <p class="cp_pieces m-0">{{ $product['sku'] }} Pieces Available</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
