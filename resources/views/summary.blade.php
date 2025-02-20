@extends('layouts.master')

@section('content')
    <div class="container py-lg-3">
        <div class="row m-0 mb-lg-5">
            <div class="col-lg-8 col-md-8 col-12">
                <div class="card p-3 cb_card_border my-3">
                    <div class="cb_address_summary">
                        <h5 class="fw-bold">Delivery Addresses</h5>
                        <span class="cb_badge_1">Change</span>
                    </div>

                    <div class="cb_address_data">
                        The Alexcier, <br>
                        237 Alexandra Road, #04-10, <br>
                        Singapore-159929.
                    </div>
                </div>

                <div class="card p-3 cb_card_border">
                    <div class="row m-0">
                        <div class="col-md-6 col-12 cb_products_summary">
                            <img src="{{ asset('assets/images/home/secondaryImg.jpg') }}" alt="product img"
                                class="img-fluid">
                        </div>
                        <div class="col-md-6 col-12 cb_products_summary">
                            <div class="cb_product_values">
                                <h6 class="fw-bold">Premimum Shopping Box</h6>
                                <p>50cm x 50cm x 50cm </p>
                                <p>Seller Name: <span> Carton Box Guru</span></p>
                                <p><i class="fa-thin fa-cart-shopping cb_shop_icon" style="font-size: 16px"></i> Delivery
                                    Date:
                                    <span>2025-12-31</span>
                                </p>
                                <p><span class="cb_og_price fw-bold">$999</span> $<span class="cb_dc_price">799</span>
                                    <span class="cb_off_percent">20% off</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-12">
                <div class="d-flex gap-2 text-start my-3">
                    <i class="fa-thin fa-cart-shopping cb_shop_icon" style="font-size: 26px"></i>
                    <div>
                        <p class="fw-bold">You have this item in your cart!</p>
                        <p>No item found in cart</p>
                    </div>
                </div>
                <div class="d-flex gap-2 text-start">
                    <div class="mx-lg-4 px-lg-3">
                        <p class="fw-bold">Saved items</p>
                        <p>No item found in the saved list</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center py-3"
            style="position: sticky; bottom: 0px; background: #fff;border-top: 1px solid #dcdcdc">
            <div class="d-flex justify-content-end align-items-center">
                <h4>
                    Total Amount &nbsp;&nbsp;
                    <span id="original-price-strike" style="text-decoration: line-through; color:#c7c7c7" class="subtotal">
                        $999
                    </span>
                    &nbsp;&nbsp;
                    <span id="discounted-price" style="color:#000;white-space:nowrap">
                        $799
                    </span>
                    <span class="cb_summary_total ms-1" id="deal-discount">
                        Congrats, You Saved
                        &nbsp;<span class="discount">₹200</span>
                    </span>
                </h4>
            </div>
            <a href="/directCheckout" class="text-decoration-none">
                <div class="cb_checkout_btn">CheckOut</div>
            </a>
        </div>
    </div>
@endsection
