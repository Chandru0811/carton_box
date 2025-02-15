<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @section('head_links')
        <title>Carton Box</title>
        <meta name="description"
            content="Save money with discount coupons and exclusive deals. Deals that you cannot find elsewhere. Special deals, festive deals, discount offers and more." />
        <link rel="canonical" href="https://dealsmachi.com/" />
        <link rel="icon" href="{{ asset('assets/images/home/favicon.ico') }}" />
        <meta name="google-site-verification" content="epg22d0eiryofP3td_QFU2i7_Vwj8O8CdWoICn1MpsQ" />

        <meta property="og:title" content="{{ $pagetitle ?? 'DealsMachi – Deals that matter in India' }}" />
        <meta property="og:description"
            content="{{ $pagedescription ?? 'Shop Big, Earn Big Save Big, DealsMachi – Deals that matters in India' }}" />
        <meta property="og:url" content="{{ $pageurl ?? 'https://dealsmachi.com' }}" />
        <meta property="og:site_name" content="DealsMachi" />
        <meta property="og:image" content="{{ asset($pageimage ?? 'assets/images/social/Dealslah_og.png') }}" />
        @php
            // Provide a fallback for $pageimage in case it's not set
$imageType = isset($pageimage) ? pathinfo($pageimage, PATHINFO_EXTENSION) : 'png'; // Default to 'png' if $pageimage is not set
        @endphp

        @if (in_array($imageType, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
            <meta property="og:image:type" content="image/{{ $imageType }}">
        @endif

        <meta property="og:image:alt" content="Get the best deals and discounts in India" />
        <meta property="og:image:width" content="256">
        <meta property="og:image:height" content="256">


        <!-- Twitter Meta Tags -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $pagetitle ?? 'DealsMachi - Deals that Matter in India !' }}" />
        <meta name="twitter:description"
            content="{{ $pagedescription ?? 'Shop Big, Earn Big Save Big, DealsMachi – Deals that matters in India' }}" />
        <meta name="twitter:site" content="@dealsMachi" />
        <meta name="twitter:image" content="{{ asset($pageimage ?? 'assets/images/social/Dealslah_twitter.png') }}" />
        <meta name="twitter:image:alt" content="Get the best deals and discounts in India" />


        <!-- Vendor CSS Files -->
        {{-- Boostrap css  --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
            rel="stylesheet">
        <!-- Main CSS File -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
        <link rel="stylesheet" href="https://unpkg.com/xzoom/dist/xzoom.css" />

        {{-- Custom Css  --}}
        <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

        <meta name="csrf-token" content="{{ csrf_token() }}">
    @show
</head>

<body>
    <section class="home-section">
        @include('nav.header')
        <div class="home-content">
            <div>
                @yield('content')
            </div>
            @include('nav.footer')
        </div>
    </section>

    <!-- ======= Script  ======= -->


    <!-- Vendor JS Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://kit.fontawesome.com/5b8838406b.js" crossorigin="anonymous"></script>

    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <!-- Page Scripts -->
    @yield('scripts')
</body>

</html>
