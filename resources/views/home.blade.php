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
        <button class="toast-close-btn" data-bs-dismiss="alert" aria-label="Close">
            <i class="fa-thin fa-xmark" style="color: #16A34A"></i>
        </button>
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
