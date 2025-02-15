@extends('layouts.master')

@section('content')
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
@endsection
