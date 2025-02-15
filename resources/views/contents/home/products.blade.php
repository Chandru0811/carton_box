@php
    $products = [
        [
            'id' => 1,
            'name' => 'Large Storage Box : 39cm(L) x 39cm(W) x 26cm(H)',
            'price' => '199',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '7',
            'discount' => '16',
        ],
        [
            'id' => 2,
            'name' => 'Heavy-Duty Moving Box : 40cm(L) x 40cm(W) x 30cm(H)',
            'price' => '249',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '5',
            'discount' => '12',
        ],
        [
            'id' => 3,
            'name' => 'Corrugated Carton Box : 35cm(L) x 35cm(W) x 25cm(H)',
            'price' => '299',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '9',
            'discount' => '20',
        ],
        [
            'id' => 4,
            'name' => 'Eco-Friendly Packing Box : 38cm(L) x 38cm(W) x 28cm(H)',
            'price' => '279',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '10',
            'discount' => '18',
        ],
        [
            'id' => 5,
            'name' => 'Premium Shipping Box : 42cm(L) x 42cm(W) x 32cm(H)',
            'price' => '189',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '8',
            'discount' => '14',
        ],
        [
            'id' => 6,
            'name' => 'Multipurpose Storage Box : 37cm(L) x 37cm(W) x 29cm(H)',
            'price' => '100',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '6',
            'discount' => '10',
        ],
        [
            'id' => 7,
            'name' => 'Office File Storage Box : 45cm(L) x 35cm(W) x 27cm(H)',
            'price' => '149',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '4',
            'discount' => '8',
        ],
        [
            'id' => 8,
            'name' => 'Durable Packing Carton : 44cm(L) x 44cm(W) x 31cm(H)',
            'price' => '200',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '3',
            'discount' => '12',
        ],
        [
            'id' => 9,
            'name' => 'Compact Storage Carton : 36cm(L) x 36cm(W) x 26cm(H)',
            'price' => '299',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '2',
            'discount' => '14',
        ],
        [
            'id' => 10,
            'name' => 'Extra Large Packing Box : 50cm(L) x 50cm(W) x 35cm(H)',
            'price' => '379',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '8',
            'discount' => '10',
        ],
        [
            'id' => 11,
            'name' => 'Lightweight Shipping Box : 41cm(L) x 41cm(W) x 30cm(H)',
            'price' => '319',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '7',
            'discount' => '16',
        ],
        [
            'id' => 12,
            'name' => 'Sturdy Moving Carton : 39cm(L) x 39cm(W) x 27cm(H)',
            'price' => '269',
            'image' => asset('assets/images/deal_categories/hotpicks_dummy.jpg'),
            'pieces' => '6',
            'discount' => '12',
        ],
    ];
@endphp



<div class="container-fluid px-lg-5">
    <div class="row pb-4">
        @foreach ($products as $product)
            <div class="col-md-3 col-lg-3 col-12 mb-2 p-3 d-flex align-items-stretch justify-content-center">
                <a href="#" class="cb_products">
                    <div class="card h-100 position-relative cp_card">
                        <div class="cb_badge">{{ $product['discount'] }}% OFF</div>
                        <img src="{{ $product['image'] }}" class="card-img-top" alt="{{ $product['name'] }}">
                        <div class="cb_card_contents">
                            <h5 class="card-title">{{ $product['name'] }}</h5>
                            <div class="cp_price_cart">
                                <p class="m-0 cb_price">${{ $product['price'] }}</p>
                                <a href="#" class="btn cb_add_cart">Add to cart</a>
                            </div>
                            <p class="cp_pieces m-0">{{ $product['pieces'] }} Pieces Available</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
