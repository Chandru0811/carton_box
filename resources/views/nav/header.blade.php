<nav class="navbar navbar-expand-lg bg-body-tertiary cb_bg_header">
    <div class="container-fluid">
        <a class="navbar-brand active mx-lg-3" href="/"><img src="{{ asset('assets/images/cb_logo1.png') }}"
                alt="" class="img-fluid cb_logo"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-lg-auto gap-lg-3 mx-lg-5 mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link cb_nav_items dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        New Cartons
                    </a>
                    <ul class="dropdown-menu cb_sub_menu">
                        <li><a class="dropdown-item" href="#">All Sizes New Carton Box</a></li>
                        <li><a class="dropdown-item" href="#">House Moving Carton Box</a></li>
                        <li><a class="dropdown-item" href="#">Postal | Shipping Carton Box</a></li>
                        <li><a class="dropdown-item" href="#">E-Commerce Carton Box</a></li>
                        <li><a class="dropdown-item" href="#">Cake Box</a></li>
                        <li><a class="dropdown-item" href="#">Gift Boxes</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link cb_nav_items dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Used Cartons
                    </a>
                    <ul class="dropdown-menu cb_sub_menu">
                        <li><a class="dropdown-item" href="#">All Sizes Used Carton Box</a></li>
                        <li><a class="dropdown-item" href="#">Assorted Boxes</a></li>
                        <li><a class="dropdown-item" href="#">TV Carton Box</a></li>
                        <li><a class="dropdown-item" href="#">Buy Back Boxes</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link cb_nav_items dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Packing Materials
                    </a>
                    <ul class="dropdown-menu cb_sub_menu">
                        <li><a class="dropdown-item" href="#">Bubble Wrap</a></li>
                        <li><a class="dropdown-item" href="#">Tape</a></li>
                        <li><a class="dropdown-item" href="#">All Packaging Products</a></li>
                        <li><a class="dropdown-item" href="#">Stationeries</a></li>
                        <li><a class="dropdown-item" href="#">Home Essentials</a></li>
                        <li><a class="dropdown-item" href="#">Gift Packaging</a></li>
                    </ul>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link cb_nav_items" href="#">Bulk Purchase</a>
                </li> --}}
            </ul>
            <form class="d-flex pe-lg-5" role="search">
                <div class="position-relative cb_serch_header">
                    <input id="searchInput" class="form-control ps-5 me-2 text-dark" type="search" placeholder="Search"
                        aria-label="Search">
                    <i class="fa fa-search position-absolute text-dark"
                        style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                </div>
            </form>
            {{-- whatsapp icon  --}}
            <div class="d-flex justify-content-center align-items-center text-center gap-3 pe-lg-3 cp_icons_head">
                <a href="https://wa.me/6588941306" target="blank" class="cb_social_media_icons cb_title"
                    data-title="Whatsapp">
                    <i class="fab fa-whatsapp" style="font-size: 28px"></i>
                </a>
                <a href="#" class="cb_social_media_icons" data-title="Shopping Cart">
                    <i class="fa-thin fa-cart-shopping" style="font-size: 26px"></i>
                </a>
                <a href="/login" class="cb_social_media_icons cb_title" data-title="User">
                    <i class="fa-regular fa-circle-user fa-xl" style="font-size: 26px"></i>
                </a>
            </div>

        </div>
    </div>
</nav>
<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        if (this.value.length > 0) {
            window.location.href = '/search';
        }
    });
</script>
