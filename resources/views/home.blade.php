@extends('layouts.master')

@section('content')

@if (session('status'))
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast align-items-center cb_toast_succ border-0 show" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-check-circle me-2"></i> {!! nl2br(e(session('status'))) !!}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if ($errors->any())
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast align-items-center cb_toast_err border-0 show" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if (session('error'))
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast align-items-center cb_toast_err border-0 show" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if (session('status1'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var orderSuccessModal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
        orderSuccessModal.show();
    });
</script>
<div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-labelledby="orderSuccessModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3" style="border-radius: 24px !important">
            <div class="modal-body">
                <div class="d-flex justify-content-end align-items-center">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mb-1 d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/images/home/check.webp') }}" class="img-fluid card-img-top1" />
                </div>
                <div class="d-flex justify-content-center align-items-center mb-1">
                    <p style="font-size: 20px">{{ session('status1')['order'] ?? '' }}</p>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <p style="font-size: 20px">{{ session('status1')['delivery'] ?? '' }}</p>
                </div>
                <div class="d-flex justify-content-center align-items-center text-center">
                    <p style="font-size: 18px; color: rgb(179, 184, 184)">
                        {{ session('status1')['address'] ?? '' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

    <div class="container-lg">
        <section class="categoryIcons">
            @include('contents.home.slider')
        </section>

        <div class="products-container">
            <div id="products-wrapper">
                <section>
                    @include('contents.home.hotpicks')
                    @include('contents.home.products')
                </section>
            </div>
        </div>
    </div>
@endsection
