@extends('layouts.master')

@section('content')
    <div class="container py-lg-3">
        <div class="row m-0 mb-lg-5">
            <div class="col-12 mb-3">
                <div class="card p-3 cb_card_border my-3">
                    <div class="cb_address_summary">
                        <h5 class="fw-bold">Delivery Addresses</h5>
                    </div>

                    <div class="cb_address_data">
                        The Alexcier, 237 Alexandra Road, #04-10, Singapore-159929.
                    </div>
                </div>

                <div class="card p-3 cb_card_border mb-3">
                    <div class="row m-0">
                        <div class="col-md-6 col-12 cb_products_checkout">
                            <div class="cb_address_checkout">
                                <h5 class="fw-bold">Delivery Addresses</h5>
                            </div>
                            <h6 class="fw-bold">Premimum Shopping Box</h6>
                            <p>50cm x 50cm x 50cm </p>
                        </div>
                        <div class="col-md-6 col-12 cb_products_summary">
                            <div class="cb_product_values">
                                <p><span class="cb_og_price fw-bold">$999</span> $<span class="cb_dc_price">799</span>
                                    <span class="cb_off_percent">20% off</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-3 cb_card_border mb-3">
                    <div class="row m-0">
                        <div class="col-md-6 col-12 cb_products_checkout">
                            <div class="cb_address_checkout">
                                <h5 class="fw-bold">Payment Methods</h5>
                            </div>

                            <div class="row justify-content-center mt-3">
                                <div class="col-10 mb-3">
                                    <div class="card cb_card_border payment-option">
                                        <div class="d-flex align-items-center p-3 ">
                                            <input type="radio" name="payment_type" id="cash_on_delivery"
                                                value="cash_on_delivery" class="form-check-input"
                                                {{ old('payment_type') == 'cash_on_delivery' ? 'checked' : '' }}>
                                            <label for="cash_on_delivery" class="d-flex align-items-center m-0">
                                                <img src="{{ asset('assets/images/credit_card.jpeg') }}"
                                                    alt="Cash on Delivery" class="mx-3"
                                                    style="width: 24px; height: auto;">
                                                <span class="text-nowrap">Credit Card</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('payment_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center py-3"
                style="position: sticky; bottom: 0px; background: #fff;border-top: 1px solid #dcdcdc">
                <div class="d-flex justify-content-end align-items-center">
                    <h4>
                        Total Amount &nbsp;&nbsp;
                        <span id="original-price-strike" style="text-decoration: line-through; color:#c7c7c7"
                            class="subtotal">
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
                <span class="cb_checkout_btn">Place Order</span>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll(".payment-option").forEach(function(card) {
                    card.addEventListener("click", function() {
                        let radio = this.querySelector("input[type='radio']");
                        radio.checked = true;
                    });
                });
            });
        </script>
    @endsection
