$(document).ready(function () {
    // Set up CSRF token globally for all AJAX requests
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Owl Carousel for Main Slider
    $(".carousel_slider").owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsive: {
            0: { items: 1 },
            600: { items: 1 },
            1000: { items: 1 },
        },
        navText: ["&#10094;", "&#10095;"],
    });

    // Owl Carousel for Related Cards
    var owl = $(".cb_related_cards");
    var itemCount = owl.children().length;

    owl.owlCarousel({
        loop: itemCount > 5,
        margin: 15,
        nav: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 3000,
        responsive: {
            0: { items: 1 },
            600: { items: 2 },
            1000: { items: 4 },
        },
    });

    // Add to Cart Functionality
    $(".add-to-cart-btn")
        .off("click")
        .on("click", function (e) {
            e.preventDefault();
            // alert("Add to cart button clicked");

            let slug = $(this).data("slug");
            let cartnumber = localStorage.getItem("cartnumber") || null;
            console.log("Cartnumber in localStorage:", cartnumber);

            if (cartnumber == null) {
                cartnumber = getCartNumber();
                console.log(cartnumber);
            }

            $.ajax({
                url: `/addtocart/${slug}`,
                type: "POST",
                data: {
                    quantity: 1,
                    saveoption: "add to cart",
                    cartnumber: cartnumber,
                },
                success: function (data, textStatus, jqXHR) {
                    if (textStatus === "success") {
                        console.log(data.cartItems);
                        let currentCount =
                            parseInt($("#cart-count").text()) || 0;
                        let newCount = currentCount + 1;

                        $("#cart-count").text(newCount).addClass("cart-border");

                        if (newCount > 0 && newCount <= 6) {
                            updateCartUI(data.cartItems);
                        } else {
                            $(".cart_items").append(`
                            <div class="text-end mb-2">
                                <a style="font-size: 13px" class="cart-screen">View All</a>
                            </div>
                        `);
                        }
                        localStorage.setItem("cartnumber", data.cart_number);
                        saveCartNumber(data.cart_number);
                        showMessage(
                            data.status || "Deal added to cart!",
                            "success"
                        );
                    } else {
                        showMessage("Something went wrong!", "error");
                    }
                },
                error: function (xhr) {
                    const errorMessage =
                        xhr.responseJSON?.error || "Something went wrong!";
                    showMessage(errorMessage, "error");
                },
            });
        });

    // Automatically Add CSRF Token to All AJAX Requests
    $(document).ajaxSend(function (event, jqxhr, settings) {
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (csrfToken) {
            jqxhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
        }
    });
});

// Function to Update Cart UI
function updateCartUI(cartItems) {
    $(".cartEmpty").hide();

    const imagePath =
        cartItems.product.product_media.length > 0 &&
        cartItems.product.product_media[0].type === "image" &&
        cartItems.product.product_media[0].order === 1
            ? cartItems.product.product_media[0].resize_path
            : "assets/images/home/noImage.webp";

    const productName =
        cartItems.product.name.length > 20
            ? cartItems.product.name.substring(0, 20) + "..."
            : cartItems.product.name;

    $(".cart_items").append(`
        <div class="d-flex">
            <img src="http://127.0.0.1:8000/${imagePath}" class="img-fluid dropdown_img" alt="${
        cartItems.product.name
    }" />
            <div class="text-start">
                <p class="text-start px-1 text-wrap m-0 p-0" style="font-size: 12px; white-space: normal;">
                    ${productName}
                </p>
                <p class="px-1 text_size" style="color: #cd8245">
                    ₹ ${cartItems.discount.toLocaleString()}
                </p>
            </div>
        </div>
    `);
}

// Function to Check if Local Storage is Available
function isLocalStorageAvailable() {
    try {
        localStorage.setItem("test", "test");
        localStorage.removeItem("test");
        return true;
    } catch (e) {
        return false; // Local Storage is disabled (Incognito Mode)
    }
}

// Function to Save Cart Number in Local Storage or Cookie
function saveCartNumber(cartNumber) {
    if (isLocalStorageAvailable()) {
        localStorage.setItem("cartnumber", cartNumber);
    } else {
        document.cookie = `cartnumber=${cartNumber}; path=/; max-age=86400`; // Fallback to cookies (1 day)
    }
}

// Function to Retrieve Cart Number from Local Storage or Cookie
function getCartNumber() {
    if (isLocalStorageAvailable()) {
        return localStorage.getItem("cartnumber");
    } else {
        let match = document.cookie.match(/(^| )cartnumber=([^;]+)/);
        return match ? match[2] : null;
    }
}

// Function to Show Messages
function showMessage(message, type) {
    let alertClass = type === "success" ? "alert-success" : "alert-danger";
    $(".alert-box").html(`
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
}
