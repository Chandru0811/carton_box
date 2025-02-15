<nav class="navbar navbar-expand-lg bg-body-tertiary cb_bg_header">
    <div class="container-fluid">
        <a class="navbar-brand active mx-lg-5" href="/"><img src="{{ asset('assets/images/cb_logo.png') }}"
                alt="" class="img-fluid cb_logo"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-lg-auto gap-lg-5 mx-lg-5 mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link cb_nav_items" href="#">New Cartons</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link cb_nav_items" href="#">Used Cartons</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link cb_nav_items" href="#">Packing Materials</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link cb_nav_items" href="#">Bulk Purchase</a>
                </li>
            </ul>
            <form class="d-flex pe-lg-3" role="search">
                <div class="position-relative cb_serch_header">
                    <input class="form-control ps-5 me-2 text-secondary" type="search" placeholder="Search"
                        aria-label="Search">
                    <i class="fa fa-search position-absolute text-secondary"
                        style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                </div>
            </form>
        </div>
    </div>
</nav>
