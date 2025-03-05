<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @section('head_links')
        <title>CartonBox Guru | Cartons and Boxes for Every Moving and Packing Needs in Singapore</title>
        <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" />

        @php
            $imageType = isset($pageimage) ? pathinfo($pageimage, PATHINFO_EXTENSION) : 'png';
        @endphp

        @if (in_array($imageType, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
            <meta property="og:image:type" content="image/{{ $imageType }}">
        @endif

        <meta property="og:image" content="{{ asset($pageimage ?? 'assets/images/CartonBoxGuru_og.png') }}" />

        <meta name="twitter:image" content="{{ asset($pageimage ?? 'assets/images/CartonBoxGuru_twitter.png') }}" />


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
        <script src="https://awik.io/demo/webshop-zoom/Drift.min.js"></script>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://kit.fontawesome.com/5b8838406b.js" crossorigin="anonymous"></script>

    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <!-- Page Scripts -->

    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            let newCartNumber = urlParams.get('cartnumber');

            if (newCartNumber) {
                localStorage.setItem("cartnumber", newCartNumber);
            }
            var cartNumber = localStorage.getItem('cartnumber');
            getcartdetails(cartNumber);
            const dropdownBtn = $("#toggleDropdown");
            const dropdownMenu = $(".dropdown-menu");

            dropdownBtn.on("click", function(event) {
                event.stopPropagation();
                dropdownMenu.toggleClass("show");
            });

            $(document).on("click", function(event) {
                if (!dropdownMenu.is(event.target) && !dropdownBtn.is(event.target) && dropdownMenu.has(
                        event.target).length === 0) {
                    dropdownMenu.removeClass("show");
                }
            });

            $('#cartButton').on('click', function(event) {
                const dropdownMenu = $('.dropdown_cart');
                var cartNumber = localStorage.getItem('cartnumber');
                if (!dropdownMenu.hasClass('show')) {
                    window.location.href = "{{ route('cart.index') }}" + '?cbg=' + cartNumber;
                }
            });


            $('.cart-screen').on('click', function() {
                var cartNumber = localStorage.getItem('cartnumber');
                window.location.href = "{{ route('cart.index') }}" + '?cbg=' + cartNumber;
            });

            function getcartdetails(cartnumber) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('cart.details') }}",
                    data: {
                        'cartnumber': cartnumber
                    },
                    success: function(data, textStatus, jqXHR) {
                        //console.log(data);
                        if (data.cartcount == 0) {
                            $('#cart-count').css('display', 'none');
                            $('#cart-count').css('border', 'none');
                        } else {
                            $('#cart-count').addClass('cart-border');
                            $('#cart-count').html(data.cartcount);
                            $('.cartDrop').html(data.html);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        // alert('Fail to Submit' + " " + errorThrown);
                        console.error(errorThrown);
                    }
                });
            }

        });
    </script>
    @yield('scripts')
</body>

</html>
