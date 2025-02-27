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

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    initializeEventListeners();

    // Save Later Add Function
    $(document).on("click", ".save-for-later-btn", function (e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        const productId = $(this).data("product-id");
        let cartnumber = localStorage.getItem("cartnumber") || null;

        $.ajax({
            url: "/saveforlater/add",
            type: "POST",
            data: {
                product_id: productId,
                cartnumber: cartnumber,
            },
            success: function (response) {
                if (response.cartItemCount !== undefined) {
                    const cartCountElement = $("#cart-count");
                    if (response.cartItemCount > 0) {
                        cartCountElement.text(response.cartItemCount);
                        cartCountElement.css("display", "inline");
                    } else {
                        cartCountElement.attr(
                            "style",
                            "display: none !important;"
                        );
                    }
                }

                $(`.cart-item[data-product-id="${productId}"]`).remove();

                if (response.updatedCart) {
                    $(".subtotal").text(
                        "₹" + response.updatedCart.subtotal.toLocaleString()
                    );
                    $(".discount").text(
                        "₹" + response.updatedCart.discount.toLocaleString()
                    );
                    $(".total").text(
                        "₹" + response.updatedCart.grand_total.toLocaleString()
                    );
                    $(".quantity-value").text(response.updatedCart.quantity);
                }

                if (response.cartItemCount === 0) {
                    $(".cart-items-container").after(`
                         <div class="empty-cart col-12 text-center d-flex flex-column align-items-center justify-content-center mt-0">
                             <img src="assets/images/home/cart_empty.webp" alt="Empty Cart"
                                 class="img-fluid empty_cart_img">
                             <p class="pt-5" style="color: #ff0060;font-size: 22px">Your Cart is Currently Empty</p>
                             <p class="" style="color: #6C6C6C;font-size: 16px">Looks Like You Have Not Added Anything To </br>
                                 Your Cart. Go Ahead & Explore Top Categories.</p>
                             <a href="/" class="btn showmoreBtn mt-2">Shop More</a>
                         </div>
                    `);
                    $(".cart-items-container").hide();
                } else {
                    $(".item_count").text(response.cartItemCount);
                }

                if (response.deal) {
                    $(".empty-savedItems").hide();

                    const imagePath =
                        response.deal.product_media.length > 0
                            ? response.deal.product_media.find(
                                  (media) =>
                                      media.order === 1 &&
                                      media.type === "image"
                              )?.resize_path
                            : "assets/images/home/noImage.webp";

                    const deliveryDate =
                        response.deal.deal_type === 1
                            ? response.deal.delivery_days &&
                              response.deal.delivery_days > 0
                                ? (() => {
                                      const currentDate = new Date();
                                      currentDate.setTime(
                                          currentDate.getTime() +
                                              response.deal.delivery_days *
                                                  24 *
                                                  60 *
                                                  60 *
                                                  1000
                                      );
                                      const day = String(
                                          currentDate.getDate()
                                      ).padStart(2, "0");
                                      const month = String(
                                          currentDate.getMonth() + 1
                                      ).padStart(2, "0");
                                      const year = currentDate.getFullYear();

                                      return `${day}-${month}-${year}`;
                                  })()
                                : "No delivery date available"
                            : '<span style="color: #22cb00">Currently Services are free through DealsMachi</span>';

                    const discountPercentage = Math.round(
                        response.deal.discount_percentage
                    );

                    const savedItemHtml = `
                        <div class="saved-item" data-product-id="${
                            response.deal.id
                        }">
                            <div class="row p-4">
                                <div class="col-md-3 d-flex flex-column justify-content-center align-items-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <img src="${imagePath}" style="max-width: 100%; max-height: 100%;"
                                            alt="${response.deal.name}" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <a href="/deal/${
                                        response.deal.id
                                    }" style="color: #000;"
                                        onclick="clickCount('${
                                            response.deal.id
                                        }')">
                                        <p style="font-size: 18px;font-weight:500">${
                                            response.deal.name
                                        }</p>
                                    </a>
                                    <p class="truncated-description" style="font-size: 16px">${
                                        response.deal.description
                                    }</p>
                                    ${
                                        response.deal.deal_type === 1
                                            ? `
                                        <div class="rating my-2">
                                            <span>Delivery Date :</span><span class="stars">
                                                <span>${deliveryDate}</span>
                                            </span>
                                        </div>
                                    `
                                            : `
                                        <div class="rating mt-3 mb-3">
                                            <span style="color: #22cb00">Currently Services are free through DealsMachi</span>
                                        </div>
                                    `
                                    }
                                    <p style="color: #AAAAAA;font-size:14px;">Seller : ${
                                        response.deal.shop.legal_name
                                    }</p>
                                    <div class="ms-0">
                                        <span style="font-size:15px;text-decoration: line-through; color:#c7c7c7">
                                            ₹${Math.round(
                                                response.deal.original_price
                                            ).toLocaleString("en-IN", {
                                                maximumFractionDigits: 0,
                                            })}
                                        </span>
                                        <span class="ms-1" style="font-size:18px;font-weight:500;color:#ff0060">
                                             ₹${Math.round(
                                                 response.deal.discounted_price
                                             ).toLocaleString("en-IN", {
                                                 maximumFractionDigits: 0,
                                             })}
                                        </span>
                                        <span class="ms-1" style="font-size:18px;font-weight:500; color:#28A745">
                                             ${discountPercentage}% Off
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex flex-column justify-content-end align-items-end mb-3">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn remove-cart-btn removeSaveLater"
                                            style="color: #ff0060;border: none" data-product-id="${
                                                response.deal.id
                                            }">
                                            <div class="d-inline-flex align-items-center gap-2-2">
                                                <div>
                                                    <img src="assets/images/home/icon_delete.svg" alt="icon" class="img-fluid" />
                                                </div>
                                                <div class="d-inline-flex align-items-center gap-2">
                                                    <span class="loader spinner-border spinner-border-sm" style="display: none"></span>
                                                    Remove
                                                </div>
                                            </div>
                                        </button>
                                        <button type="button" class="btn cancel-btn moveToCart"
                                            style="color: #ff0060;border: none" data-product-id="${
                                                response.deal.id
                                            }">
                                            <div class="d-inline-flex align-items-center gap-2">
                                                <div>
                                                    <img src="assets/images/home/icon_delivery.svg" alt="icon" class="img-fluid" />
                                                </div>
                                                <div class="d-inline-flex align-items-center gap-2">
                                                    <span class="loader spinner-border spinner-border-sm me-2" style="display: none"></span>
                                                    Move to Cart
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                                <hr class="mt-3">
                            </div>
                        </div>`;

                    $(".saved-item-container").append(savedItemHtml);
                }

                //fetchCartDropdown();
                showMessage(
                    response.status || "Item moved to Buy Later!",
                    "success"
                );
            },
            error: function (xhr) {
                const errorMessage =
                    xhr.responseJSON?.error ||
                    "Failed to move item to Buy Later!";
                showMessage(errorMessage, "error");
            },
        });
    });

    // Cart Remove Function
    $(document).on("click", ".cart-remove", function (e) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        e.preventDefault();
        const productId = $(this).data("product-id");
        const cartId = $(this).data("cart-id");

        $.ajax({
            url: "/cart/remove",
            type: "POST",
            data: { product_id: productId, cart_id: cartId },
            success: function (response) {
                if (response.cartItemCount !== undefined) {
                    const cartCountElement = $("#cart-count");
                    if (response.cartItemCount > 0) {
                        cartCountElement.text(response.cartItemCount);
                        cartCountElement.css("display", "inline");
                    } else {
                        cartCountElement.attr(
                            "style",
                            "display: none !important;"
                        );
                    }
                }

                const cartItemElement = $(
                    `.cart-item[data-product-id="${productId}"]`
                );
                if (cartItemElement.length) {
                    cartItemElement.remove();
                }

                if (response.updatedCart) {
                    $(".subtotal").text(
                        "₹" + response.updatedCart.subtotal.toLocaleString()
                    );
                    $(".discount").text(
                        "₹" + response.updatedCart.discount.toLocaleString()
                    );
                    $(".total").text(
                        "₹" + response.updatedCart.grand_total.toLocaleString()
                    );
                    $(".quantity-value").text(response.updatedCart.quantity);
                }

                if (response.cartItemCount === 0) {
                    $(".cart-items-container").after(`
                         <div class="empty-cart col-12 text-center d-flex flex-column align-items-center justify-content-center mt-0">
                             <img src="assets/images/home/cart_empty.webp" alt="Empty Cart"
                                 class="img-fluid empty_cart_img">
                             <p class="pt-5" style="color: #ff0060;font-size: 22px">Your Cart is Currently Empty</p>
                             <p class="" style="color: #6C6C6C;font-size: 16px">Looks Like You Have Not Added Anything To </br>
                                 Your Cart. Go Ahead & Explore Top Categories.</p>
                             <a href="/" class="btn showmoreBtn mt-2">Shop More</a>
                         </div>
                    `);
                    $(".cart-items-container").hide();
                } else {
                    $(".item_count").text(response.cartItemCount);
                }

                // fetchCartDropdown();
                showMessage(
                    response.status || "Item moved to Buy Later!",
                    "success"
                );
            },
            error: function (xhr) {
                const errorMessage =
                    xhr.responseJSON?.error ||
                    "Failed to move item to Buy Later!";
                showMessage(errorMessage, "error");
            },
        });
    });

    // Move To Cart Function in Cart Blade
    $(document).on("click", ".moveToCart", function (e) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        e.preventDefault();
        const productId = $(this).data("product-id");
        let cartnumber = localStorage.getItem("cartnumber") || null;
        $.ajax({
            url: "/saveforlater/toCart",
            type: "POST",
            data: {
                product_id: productId,
                cartnumber: cartnumber,
            },
            success: function (response) {
                if (response.cartItemCount !== undefined) {
                    const cartCountElement = $("#cart-count");
                    if (response.cartItemCount > 0) {
                        cartCountElement.text(response.cartItemCount);
                        cartCountElement.css("display", "inline");
                    } else {
                        cartCountElement.attr(
                            "style",
                            "display: none !important;"
                        );
                    }
                }

                $(`.saved-item[data-product-id="${productId}"]`).remove();

                if ($(".saved-item").length === 0) {
                    $(".saved-items").hide();
                    $(".empty-savedItems").show();
                    $(".saved-items").after(`
                        <div class="text-center mb-4 empty-savedItems">
                                <img src='/assets/images/home/empty_savedItems.png' alt="Empty Cart" class="img-fluid mb-2" style="width: 300px;" />
                                <h4 style="color: #ff0060;">Your Saved Wishlists are awaiting your selection!</h4>
                            </div>
                        `);
                }

                $(".item_count").text(response.cartItemCount);

                if (response.updatedCart) {
                    $(".subtotal").text(
                        "₹" + response.updatedCart.subtotal.toLocaleString()
                    );
                    $(".discount").text(
                        "₹" + response.updatedCart.discount.toLocaleString()
                    );
                    $(".total").text(
                        "₹" + response.updatedCart.grand_total.toLocaleString()
                    );
                    $(".quantity-value").text(response.updatedCart.quantity);
                }

                if (response.item) {
                    $(".empty-cart").attr("style", "display: none !important;");

                    $(".cart-items-container").css("display", "block");

                    const image =
                        response.item.product.product_media.length > 0
                            ? response.item.product.product_media.find(
                                  (media) =>
                                      media.order === 1 &&
                                      media.type === "image"
                              )?.resize_path
                            : "assets/images/home/noImage.webp";

                    const deliveryDate =
                        response.item.product.deal_type === 1 &&
                        response.item.product.delivery_days
                            ? (() => {
                                  const date = new Date();
                                  date.setDate(
                                      date.getDate() +
                                          parseInt(
                                              response.item.product
                                                  .delivery_days
                                          )
                                  );
                                  return (
                                      date
                                          .getDate()
                                          .toString()
                                          .padStart(2, "0") +
                                      "-" +
                                      (date.getMonth() + 1)
                                          .toString()
                                          .padStart(2, "0") +
                                      "-" +
                                      date.getFullYear()
                                  );
                              })()
                            : "No delivery date available";

                    const discountPercentage = Math.round(
                        response.item.product.discount_percentage
                    );

                    const cartItemHtml = `
                            <div class="cart-item" data-product-id="${
                                response.item.product_id
                            }">
    <div class="row p-4">
        <div class="col-md-4 mb-3">
            <div class="d-flex justify-content-center align-items-center">
                <img src="${image}" style="max-width: 100%; max-height: 100%;" alt="${
                        response.item.item_description
                    }" />
            </div>
        </div>
        <div class="col-md-8">
            <a href="/deal/${
                response.item.product_id
            }" style="color: #000;" onclick="clickCount('${
                        response.item.product.id
                    }')">
                <p style="font-size: 18px;">${response.item.product.name}</p>
            </a>
            <p class="truncated-description" style="font-size: 16px">${
                response.item.product.description
            }</p>
            <p style="color: #AAAAAA;font-size:14px;">Seller : ${
                response.item.product.shop.legal_name
            }</p>
            ${
                response.item.product.deal_type === 2
                    ? `
            <div class="rating mt-3 mb-3">
                <span style="color: #22cb00">Currently Services are free through
                    DealsMachi
                </span>
            </div>
            <span class="ms-1" style="font-size:18px;font-weight:500;color:#ff0060">
                ₹${Math.round(
                    response.item.product.discounted_price
                ).toLocaleString("en-IN", { maximumFractionDigits: 0 })}
            </span>
            `
                    : `
            <div class="d-flex">
                <div class="">
                    <img src="assets/images/home/icon_delivery.svg" alt="icon"
                        class="img-fluid" />
                </div> &nbsp;&nbsp;
                <div class="">
                    <p style="font-size: 16px;">
                        Delivery Date : ${deliveryDate}
                    </p>
                </div>
            </div>
            <div class="ms-0">
                <span style="font-size:15px;text-decoration: line-through; color:#c7c7c7">
                    ₹${Math.round(
                        response.item.product.original_price
                    ).toLocaleString("en-IN", { maximumFractionDigits: 0 })}
                </span>
                <span class="ms-1" style="font-size:18px;font-weight:500;color:#ff0060">
                    ₹${Math.round(
                        response.item.product.discounted_price
                    ).toLocaleString("en-IN", { maximumFractionDigits: 0 })}
                </span>
                <span class="ms-1" style="font-size:18px;font-weight:500;color:#28A745">
                     ${discountPercentage}% Off
                </span>
            </div>
            `
            }
        </div>
    </div>
    <div class="row d-flex align-items-center">
        <div class="col-md-6">
            ${
                response.item.product.deal_type == 2
                    ? `<div class="d-flex align-items-start my-1 mb-3" style="padding-left:24px">
                <div class="d-flex flex-column ms-0" style="width: 30%">
                    <label for="service_date" class="form-label">Service Date</label>
                    <input type="date" id="service_date" name="service_date"
                        class="form-control form-control-sm service-date" value="${response.item.service_date}"
                        data-cart-id="${response.item.cart_id}" data-product-id="${response.item.product.id}">
                </div>&nbsp;&nbsp;
                <div class="d-flex flex-column" style="width: 30%;">
                    <label for="service_time" class="form-label">Service Time</label>
                    <input type="time" id="service_time" name="service_time"
                        class="form-control form-control-sm service-time"
                        value="${response.item.service_time}" data-cart-id="${response.item.cart_id}"
                        data-product-id="${response.item.product.id}">
                </div>
            </div>`
                    : `<div class="d-flex align-items-center my-1 mb-3" style="padding-left: 24px;">
                <span>Qty</span> &nbsp;&nbsp;
                <button class="btn rounded btn-sm decrease-btn1" data-cart-id="${response.item.cart_id}"
                    data-product-id="${response.item.product.id}">-</button>
                <input type="text" class="form-control form-control-sm mx-2 text-center quantity-input"
                    style="width: 50px;background-color:#F9F9F9;border-radius:2px"
                    value="${response.item.quantity}" readonly>
                <button class="btn rounded btn-sm increase-btn1" data-cart-id="${response.item.cart_id}"
                    data-product-id="${response.item.product.id}">+</button>
            </div>`
            }
        </div>
        <div class="col-md-6 d-flex justify-content-md-end" style="padding-left: 24px">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="submit" class="btn save-for-later-btn"
                    style="color: #ff0060; border: none;" data-product-id="${
                        response.item.product.id
                    }">
                    <div class="d-inline-flex align-items-center gap-2 buy_later">
                        <div>
                            <img src="/assets/images/home/icon_save_later.svg" alt="icon" class="img-fluid" />
                        </div>
                        <div class="d-inline-flex align-items-center gap-2 buy-for-later-btn">
                            <span class="loader spinner-border spinner-border-sm" style="display: none;"></span>
                            <span>Buy Later</span>
                        </div>
                    </div>
                </button>
                &nbsp;&nbsp;
                <button type="submit" class="btn cancel-btn cart-remove" style="color: #ff0060;border: none"
                    data-product-id="${
                        response.item.product.id
                    }" data-cart-id="${response.item.cart_id}">
                    <div class="d-inline-flex align-items-center gap-2">
                        <div>
                            <img src="/assets/images/home/icon_delete.svg" alt="icon" class="img-fluid" />
                        </div>
                        <div class="d-inline-flex align-items-center gap-2">
                            <span class="loader spinner-border spinner-border-sm me-2" style="display: none"></span>
                            Remove
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
    <hr>
</div>
                        `;

                    $(".cart-items").append(cartItemHtml);
                    $(".decrease-btn1, .increase-btn1").on(
                        "click",
                        function () {
                            const cartId = $(this).data("cart-id");
                            const productId = $(this).data("product-id");
                            const quantityInput = $(this)
                                .closest(".cart-item")
                                .find(".quantity-input");
                            let quantity = parseInt(quantityInput.val());

                            if (
                                $(this).hasClass("decrease-btn1") &&
                                quantity > 1
                            ) {
                                quantity -= 1;
                            } else if (
                                $(this).hasClass("increase-btn1") &&
                                quantity < 10
                            ) {
                                quantity += 1;
                            }
                            quantityInput.val(quantity);
                            updateCart(cartId, productId, quantity);
                        }
                    );
                    $(".service-date, .service-time").on("change", function () {
                        const cartId = $(this).data("cart-id");
                        const productId = $(this).data("product-id");
                        const serviceDate = $(
                            `.service-date[data-product-id="${productId}"]`
                        ).val();
                        const serviceTime = $(
                            `.service-time[data-product-id="${productId}"]`
                        ).val();
                        updateCart(
                            cartId,
                            productId,
                            null,
                            serviceDate,
                            serviceTime
                        );
                    });
                    function updateCart(
                        cartId,
                        productId,
                        quantity,
                        serviceDate = null,
                        serviceTime = null
                    ) {
                        const csrfToken = $('meta[name="csrf-token"]').attr(
                            "content"
                        );
                        const data = {
                            cart_id: cartId,
                            product_id: productId,
                            quantity: quantity,
                            service_date: serviceDate,
                            service_time: serviceTime,
                            _token: csrfToken,
                        };

                        $.ajax({
                            url: "/cart/update",
                            type: "POST",
                            contentType: "application/json",
                            data: JSON.stringify(data),
                            success: function (data) {
                                if (data.status === "success") {
                                    const indianCurrencyFormatter =
                                        new Intl.NumberFormat("en-IN", {
                                            style: "currency",
                                            currency: "INR",
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0,
                                        });

                                    $(".quantity-value").each(function () {
                                        $(this).text(data.updatedCart.quantity);
                                    });

                                    $(".subtotal").each(function () {
                                        $(this).text(
                                            indianCurrencyFormatter.format(
                                                data.updatedCart.subtotal
                                            )
                                        );
                                    });

                                    $(".discount").each(function () {
                                        let discountValue =
                                            data.updatedCart.discount;
                                        if (discountValue < 0) {
                                            $(this).text(
                                                `- ${indianCurrencyFormatter.format(
                                                    Math.abs(discountValue)
                                                )}`
                                            );
                                        } else {
                                            $(this).text(
                                                `- ${indianCurrencyFormatter.format(
                                                    discountValue
                                                )}`
                                            );
                                        }
                                    });

                                    $(".total").each(function () {
                                        $(this).text(
                                            indianCurrencyFormatter.format(
                                                data.updatedCart.grand_total
                                            )
                                        );
                                    });
                                } else {
                                    alert(data.message);
                                }
                            },
                            error: function (error) {
                                console.error("Error updating cart:", error);
                            },
                        });
                    }

                    const today = new Date();
                    const currentDate = today.toISOString().split("T")[0]; // Format 'YYYY-MM-DD'

                    // Calculate the next date
                    const nextDate = new Date(today);
                    nextDate.setDate(today.getDate() + 1);
                    const nextDateString = nextDate.toISOString().split("T")[0];

                    $(".service-date").each(function () {
                        // Set the minimum date to the day after tomorrow
                        const restrictedMinDate = new Date(nextDate);
                        restrictedMinDate.setDate(nextDate.getDate() + 1);

                        $(this).attr(
                            "min",
                            restrictedMinDate.toISOString().split("T")[0]
                        );

                        // Handle user-typed values (if they bypass the UI)
                        $(this).on("change", function () {
                            const selectedDate = $(this).val();
                            if (
                                selectedDate === currentDate ||
                                selectedDate === nextDateString
                            ) {
                                $(this).val(""); // Clear invalid date
                            }
                        });
                    });

                    $(".service-date, .service-time").on("input", function () {
                        $(this)
                            .closest(".d-flex")
                            .find(".error-message")
                            .remove();
                    });
                }

                // fetchCartDropdown();
                showMessage(
                    response.status || "Item moved to cart!",
                    "success"
                );
            },
            error: function (xhr) {
                showMessage(
                    xhr.responseJSON?.error || "Failed to move item to cart!",
                    "error"
                );
            },
        });
    });

    // Save Later Remove Function
    $(document).on("click", ".removeSaveLater", function (e) {
        e.preventDefault();
        const productId = $(this).data("product-id");

        $.ajax({
            url: "/saveforlater/remove",
            type: "POST",
            data: { product_id: productId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $(`.saved-item[data-product-id="${productId}"]`).remove();

                if ($(".saved-item").length === 0) {
                    $(".saved-items").hide();
                    $(".empty-savedItems").show();
                    $(".saved-items").after(`
                        <div class="text-center mb-4 empty-savedItems">
                                <img src='/assets/images/home/empty_savedItems.png' alt="Empty Cart" class="img-fluid mb-2" style="width: 300px;" />
                                <h4 style="color: #ff0060;">Your Saved Wishlists are awaiting your selection!</h4>
                            </div>
                        `);
                }

                showMessage(
                    response.status || "Save for Later Item Removed!",
                    "success"
                );
            },
            error: function (xhr) {
                showMessage(
                    xhr.responseJSON?.error ||
                        "Failed to remove item from Save for Later!",
                    "error"
                );
            },
        });
    });

    // Move To Cart Function in Save Later Blade
    $(document).on("click", ".saveLaterToCart", function (e) {
        e.preventDefault();
        const productId = $(this).data("product-id");

        $.ajax({
            url: "/saveforlater/toCart",
            type: "POST",
            data: { product_id: productId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.cartItemCount !== undefined) {
                    const cartCountElement = $("#cart-count");
                    if (response.cartItemCount > 0) {
                        cartCountElement.text(response.cartItemCount);
                        cartCountElement.css("display", "inline");
                    } else {
                        cartCountElement.attr(
                            "style",
                            "display: none !important;"
                        );
                    }
                }

                $(`.saved-item[data-product-id="${productId}"]`).remove();

                if ($(".saved-item").length === 0) {
                    $(".saved-items").hide();
                    $(".saved-items").after(`
                        <div class="text-center mb-4 empty-savedItems">
                                <img src='/assets/images/home/empty_savedItems.png' alt="Empty Cart" class="img-fluid mb-2" style="width: 300px;" />
                                <h4 style="color: #ff0060;">Your Saved Wishlists are awaiting your selection!</h4>
                            </div>
                        `);
                }

                //fetchCartDropdown();
                showMessage(
                    response.status || "Save for Later Item Removed!",
                    "success"
                );
            },
            error: function (xhr) {
                showMessage(
                    xhr.responseJSON?.error ||
                        "Failed to remove item from Save for Later!",
                    "error"
                );
            },
        });
    });

    $(document).on("click", ".saveItemToCart", function (e) {
        e.preventDefault();
        const productId = $(this).data("product-id");

        $.ajax({
            url: "/saveforlater/toCart",
            type: "POST",
            data: { product_id: productId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.cartItemCount !== undefined) {
                    const cartCountElement = $("#cart-count");
                    if (response.cartItemCount > 0) {
                        cartCountElement.text(response.cartItemCount);
                        cartCountElement.css("display", "inline");
                    } else {
                        cartCountElement.attr(
                            "style",
                            "display: none !important;"
                        );
                    }
                }

                $(`.saved-item[data-product-id="${productId}"]`).remove();

                if ($(".saved-item").length === 0) {
                    $(".saved-items").hide();
                    $(".saved-items").after(`
                        <div class="text-center">
                            <p class="text-muted">No items found in the Saved list.</p>
                        </div>
                    `);
                }

                if (response.item) {
                    $("#no_items").hide();
                    $(".no_items").hide();
                    $(".cart-item-container").css("display", "block");
                    $("#get_cartItems").css("display", "block");

                    const image =
                        response.item.product.product_media.length > 0
                            ? response.item.product.product_media.find(
                                  (media) =>
                                      media.order === 1 &&
                                      media.type === "image"
                              )?.resize_path
                            : "assets/images/home/noImage.webp";

                    const cartItemHtml = `
                           <div id="cart_items" class="cart-item">
                            <div class="row d-flex align-items-center mb-3 mt-2"
                                id="cart_item_${response.item.product_id}">
                                <div class="col-1">
                                    <input type="checkbox" class="cartItem_check" value="${response.item.product_id}"
                                        class="me-1" />
                                </div>
                                <div class="col-3">
                                    <img src="/${image}"
                                        class="img-fluid card_img_cont" alt="${response.item.product.name}" />
                                </div>
                                <div class="col-8">
                                    <div class="d-flex flex-column justify-content-start">
                                        <a href="/deal/${response.item.product_id}" style="color: #000;"
                                            onclick="clickCount('${response.item.product_id}')">
                                            <h5 class="mb-1 fs_common text-truncate" style="max-width: 100%;">
                                            ${response.item.product.name}
                                            </h5>
                                        </a>
                                        <p class="mb-0 text-muted fs_common text-truncate" style="max-width: 100%;">
                                        ${response.item.product.description}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;

                    $(".cart-items").append(cartItemHtml);
                }

                // fetchCartDropdown();
                showMessage(
                    response.status || "Save for Later Item Removed!",
                    "success"
                );
            },
            error: function (xhr) {
                showMessage(
                    xhr.responseJSON?.error ||
                        "Failed to remove item from Save for Later!",
                    "error"
                );
            },
        });
    });

    // function fetchCartDropdown() {
    //     $.ajax({
    //         url: "/cart/dropdown",
    //         type: "GET",
    //         success: function (response) {
    //             if (response.html) {
    //                 $(".dropdown_cart").html(response.html);
    //             }
    //         },
    //         error: function () {
    //             showMessage("Failed to update cart dropdown!", "error");
    //         },
    //     });
    // }

    function showMessage(message, type) {
        var textColor = type === "success" ? "#16A34A" : "#EF4444";
        var icon =
            type === "success"
                ? '<i class="fa-regular fa-cart-shopping" style="color: #16A34A"></i>'
                : '<i class="fa-solid fa-triangle-exclamation" style="color: #EF4444"></i>';
        var alertClass = type === "success" ? "toast-success" : "toast-danger";

        var alertHtml = `
          <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 70px; right: 40px; z-index: 1050; color: ${textColor};">
            <div class="toast-content">
                <div class="toast-icon">${icon}</div>
                <span class="toast-text">${message}</span>&nbsp;&nbsp;
                <button class="toast-close-btn" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa-solid fa-times" style="color: ${textColor}; font-size: 14px;"></i>
                </button>
            </div>
          </div>
        `;

        $("body").append(alertHtml);
        setTimeout(function () {
            $(".alert").alert("close");
        }, 5000);
    }

    // fetchCartDropdown();
});

$(document).ready(function () {
    // Validation for Main Form
    $(".social-button .fab.fa-twitter")
        .removeClass("fa-twitter")
        .addClass("fa-x-twitter");

    $("#enquiryFormMain").validate({
        rules: {
            name: {
                required: true,
            },
            phone: {
                required: true,
                digits: true,
                maxlength: 10,
            },
        },
        messages: {
            name: {
                required: "Please enter your name",
            },
            phone: {
                required: "Please enter your phone number",
                digits: "Phone number must be numeric",
                maxlength: "Phone number can't exceed 10 digits",
            },
        },
        errorPlacement: function (error, element) {
            error.addClass("text-danger mt-1");
            error.insertAfter(element);
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
        submitHandler: function (form) {
            submitEnquiryForm(form);
        },
    });

    $("#contactForm").validate({
        rules: {
            first_name: {
                required: true,
                minlength: 2,
            },
            email: {
                required: true,
                email: true,
            },
            mobile: {
                required: true,
                number: true,
                maxlength: 10,
            },
            description_info: {
                required: true,
            },
        },
        messages: {
            first_name: {
                required: "Please enter your first name*",
                minlength: "Your name must be at least 2 characters long",
            },
            email: {
                required: "Please enter your email*",
                email: "Please enter a valid email address",
            },
            mobile: {
                required: "Please enter your phone number*",
                number: "Please enter a valid phone number",
                maxlength: "Your phone number must be at most 10 digits long",
            },
            description_info: {
                required: "Please enter your message*",
            },
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.next(".error"));
        },
        submitHandler: function (form) {
            var payload = {
                first_name: $("#first_name").val(),
                last_name: $("#last_name").val(),
                email: $("#email").val(),
                phone: $("#mobile").val(),
                company_id: 40,
                company: "DealsMachi",
                lead_status: "PENDING",
                description_info: $("#description_info").val(),
                lead_source: "Contact Us",
                country_code: "65",
                createdBy: $("#first_name").val(),
            };

            // console.log("Form data:", $("#description_info").val());

            // AJAX call to the newClient API
            $.ajax({
                url: "https://crmlah.com/ecscrm/api/newClient",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(payload),
                success: function (response) {
                    console.log("API response:", response);
                    $("#successModal").modal("show");
                    $(form).trigger("reset"); // Reset form after successful submission
                },
                error: function (xhr, status, error) {
                    console.error("API call failed:", error);
                    $("#errorModal").modal("show");
                },
            });
        },
    });

    // Validation for Modal Form
    $("#enquiryFormModal").validate({
        rules: {
            name: {
                required: true,
            },
            phone: {
                required: true,
                digits: true,
                maxlength: 10,
            },
        },
        messages: {
            name: {
                required: "Please enter your name",
            },
            phone: {
                required: "Please enter your phone number",
                digits: "Phone number must be numeric",
                maxlength: "Phone number can't exceed 10 digits",
            },
        },
        errorPlacement: function (error, element) {
            error.addClass("text-danger mt-1");
            error.insertAfter(element);
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
        submitHandler: function (form) {
            submitEnquiryForm(form);
        },
    });

    function submitEnquiryForm(form) {
        var $currentForm = $(form);
        var dealId = $currentForm.data("deal-id");

        var payload = {
            name: $currentForm.find("[name='name']").val(),
            email: $currentForm.find("[name='email']").val(),
            phone: $currentForm.find("[name='phone']").val(),
            company_id: 40,
            company: "ECSCloudInfotech",
            lead_status: "PENDING",
            lead_source: "Product Page",
            country_code: "65",
        };

        var laravelRequest = $.ajax({
            url: "https://dealsmachi.com/deals/count/enquire",
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            contentType: "application/json",
            data: JSON.stringify({ id: dealId }),
        });

        var crmlahRequest = $.ajax({
            url: "https://crmlah.com/ecscrm/api/newClient",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(payload),
        });

        $.when(laravelRequest, crmlahRequest)
            .done(function (laravelResponse, crmlahResponse) {
                console.log(
                    "Both APIs succeeded:",
                    laravelResponse,
                    crmlahResponse
                );
                $("#successModal").modal("show");
                $currentForm[0].reset();
                $("#enquiryModal").modal("hide");
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error(
                    "One or both API calls failed:",
                    textStatus,
                    errorThrown
                );
                $("#errorModal").modal("show");
                $currentForm[0].reset();
                $("#enquiryModal").modal("hide");
            });
    }
    // Validation for Profile
    $("#profileFormModal").validate({
        rules: {
            name: {
                required: true,
            },
            email: {
                required: true,
            },
        },
        messages: {
            name: {
                required: "Please enter your name",
            },
            email: {
                required: "Please enter your email",
            },
        },
        errorPlacement: function (error, element) {
            error.addClass("text-danger mt-1");
            error.insertAfter(element);
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
        submitHandler: function (form) {
            form.submit();
        },
    });

    //Address Modal
    $(document).ready(function () {
        // Validation for New Address Form
        $("#addressNewForm").validate({
            rules: {
                first_name: {
                    required: true,
                    maxlength: 200,
                },
                last_name: {
                    maxlength: 200,
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 200,
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10,
                },
                postalcode: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 6,
                },
                address: {
                    required: true,
                    maxlength: 200,
                },
                state: {
                    required: true,
                    maxlength: 200,
                },
                city: {
                    required: true,
                    maxlength: 200,
                },
                type: {
                    required: true,
                },
                unit: {
                    maxlength: 255,
                },
            },
            messages: {
                first_name: {
                    required: "Please provide your first name.",
                    maxlength: "First name may not exceed 200 characters.",
                },
                last_name: {
                    maxlength: "Last name may not exceed 200 characters.",
                },
                email: {
                    required: "Please provide an email address.",
                    email: "Please provide a valid email address.",
                    maxlength: "Email may not exceed 200 characters.",
                },
                phone: {
                    required: "Please provide a phone number.",
                    digits: "Phone number must be exactly 10 digits.",
                    minlength: "Phone number must be exactly 10 digits.",
                    maxlength: "Phone number must be exactly 10 digits.",
                },
                postalcode: {
                    required: "Please provide a postal code.",
                    digits: "Postal code must be exactly 6 digits.",
                    minlength: "Postal code must be exactly 6 digits.",
                    maxlength: "Postal code must be exactly 6 digits.",
                },
                address: {
                    required: "Please provide an address.",
                    maxlength: "Address may not exceed 200 characters.",
                },
                state: {
                    required: "Please provide your State.",
                    maxlength: "State may not exceed 200 characters.",
                },
                city: {
                    required: "Please provide your City.",
                    maxlength: "City may not exceed 200 characters.",
                },
                unit: {
                    maxlength: "Additional Info may not exceed 200 characters.",
                },
            },
            errorPlacement: function (error, element) {
                error.addClass("text-danger mt-1");
                error.insertAfter(element);
            },
            highlight: function (element) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid");
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                var isDefault = $("#defaultAddressCheckbox").prop("checked")
                    ? 1
                    : 0;
                formData.append("default", isDefault);

                $.ajax({
                    url: $(form).attr("action"),
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $("#newAddressModal").modal("hide");
                            $("#addressNewForm")[0].reset();

                            if (response.address.default === "1") {
                                $("#addressID").val(response.address.id);
                            }

                            if (response.address.default === "1") {
                                var previousDefault = $(
                                    ".allAddress .badge_primary"
                                ).closest(".row");
                                if (previousDefault.length > 0) {
                                    previousDefault
                                        .find(".badge_primary")
                                        .remove();
                                    var oldAddressId = previousDefault
                                        .find("input[type=radio]")
                                        .val();
                                    previousDefault.find(".delBadge").append(`
                                        <button type="button" class="badge_del" data-bs-toggle="modal"
                                            data-bs-target="#deleteAddressModal" data-address-id="${oldAddressId}">
                                            Delete
                                        </button>
                                    `);
                                }
                            }

                            var finddiv =
                                $("#myAddressModal").find(".allAddress");
                            finddiv.append(`
                                <div class="row p-2">
                                    <div class="col-10">
                                        <div class="d-flex text-start">
                                            <div class="px-1">
                                                <input type="radio" name="selected_id"
                                                    id="selected_id_${
                                                        response.address.id
                                                    }"
                                                    value="${
                                                        response.address.id
                                                    }"
                                                    ${
                                                        response.address
                                                            .default === "1"
                                                            ? "checked"
                                                            : ""
                                                    } />
                                            </div>
                                            <p class="text-turncate fs_common">
                                                <span class="px-2">
                                                    ${
                                                        response.address
                                                            .first_name
                                                    } ${
                                response.address.last_name ?? ""
                            } |
                                                    <span style="color: #c7c7c7;">&nbsp;+91
                                                        ${
                                                            response.address
                                                                .phone
                                                        }
                                                    </span>
                                                </span><br>
                                                <span class="px-2" style="color: #c7c7c7">
                                                    ${
                                                        response.address.address
                                                    }, ${
                                response.address.city
                            }, ${response.address.state} - ${
                                response.address.postalcode
                            }.
                                                </span>
                                                <br>
                                                ${
                                                    response.address.default ===
                                                    "1"
                                                        ? '<span class="badge badge_primary">Default</span>'
                                                        : ""
                                                }
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="d-flex gap-2 delBadge">
                                                <button type="button" class="badge_edit" data-bs-toggle="modal"
                                                    data-address-id="${
                                                        response.address.id
                                                    }" data-bs-target="#editAddressModal">
                                                    Edit
                                                </button>
                                                ${
                                                    response.address.default ===
                                                    "0"
                                                        ? `
                                                    <button type="button" class="badge_del"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteAddressModal"
                                                        data-address-id="${response.address.id}">
                                                        Delete
                                                    </button>`
                                                        : ""
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                            if (response.address.default === "1") {
                                $(
                                    '.modal-body p strong:contains("Phone :")'
                                ).parent().html(`
                                    <strong>Phone :</strong> (+91) ${
                                        response.address.phone || "--"
                                    }
                                `);
                                var profileAddress = `
                                    <p>
                                        <strong>${
                                            response.address.first_name
                                        } ${
                                    response.address.last_name ?? ""
                                } (+91)
                                            ${
                                                response.address.phone
                                            }</strong>&nbsp;&nbsp;<br>
                                        ${response.address.address}, ${
                                    response.address.city
                                }, ${response.address.state} - ${
                                    response.address.postalcode
                                }
                                        <span>
                                            <span class="badge badge_danger py-1">Default</span>&nbsp;&nbsp;
                                        </span>
                                    </p>
                                `;
                                $(".selected-address").html(profileAddress);
                                $(".defaultAddress .primary_new_btn").hide();
                                if (
                                    $(".defaultAddress .badge_infos").length ===
                                    0
                                ) {
                                    $(".defaultAddress").append(`
                                        <span class="badge badge_infos py-1" data-bs-toggle="modal" data-bs-target="#myAddressModal">Change</span>
                                    `);
                                }
                            }
                            $("#myAddressModal").modal("show");

                            if ($("#cartCheckoutForm").length === 0) {
                                var cartId = $("#moveCartToCheckout").data(
                                    "cart-id"
                                );
                                if ($("#moveCartToCheckout").length) {
                                    $("#moveCartToCheckout").hide();
                                    $("#moveCartToCheckout").after(`
                                        <form action="/cartCheckout" method="POST" id="cartCheckoutForm">
                                            <input type="hidden" name="_token" value="${$(
                                                'meta[name="csrf-token"]'
                                            ).attr("content")}">
                                            <input type="hidden" name="cart_id"  value="${cartId}">
                                            <input type="hidden" name="address_id" id="addressID" value="${
                                                response.address.id
                                            }">
                                            <button type="submit" class="btn check_out_btn">
                                                Checkout
                                            </button>
                                        </form>
                                    `);
                                }
                            }

                            if ($("#summaryCheckoutForm").length === 0) {
                                var cartId =
                                    $("#moveToCheckout").data("cart-id");
                                var productsId =
                                    $("#moveToCheckout").data("products-id");
                                if ($("#moveToCheckout").length) {
                                    $("#moveToCheckout").hide();
                                    $(".summary_checkout_button").css(
                                        "display",
                                        "block"
                                    );
                                }
                            }

                            showMessage(response.message, "success");
                        } else {
                            showMessage(response.message, "error");
                        }
                    },
                    error: function (xhr, status, error) {
                        showMessage(
                            "There was an issue with the request. Please try again.",
                            "error"
                        );
                    },
                });
            },
        });

        $("#newAddressModal").on("hidden.bs.modal", function () {
            $("#addressNewForm")[0].reset();
            $("#addressNewForm").find(".is-invalid").removeClass("is-invalid");
            $("#addressNewForm").find("label.error").remove();
        });

        $("#editAddressModal").on("hidden.bs.modal", function () {
            $("#addressEditForm")[0].reset();
            $("#addressEditForm").find(".is-invalid").removeClass("is-invalid");
            $("#addressEditForm").find("label.error").remove();
        });

        $(".btn-close").on("click", function () {
            $("#addressNewForm")[0].reset();
            $("#addressNewForm").find(".is-invalid").removeClass("is-invalid");
            $("#addressNewForm").find("label.error").remove();
            $("#addressEditForm")[0].reset();
            $("#addressEditForm").find(".is-invalid").removeClass("is-invalid");
            $("#addressEditForm").find("label.error").remove();
        });

        // Validation for New Address Form
        $("#addressEditForm").validate({
            rules: {
                first_name: {
                    required: true,
                    maxlength: 200,
                },
                last_name: {
                    maxlength: 200,
                },
                email: {
                    required: true,
                    email: true,
                    maxlength: 200,
                },
                phone: {
                    required: true,
                    digits: true,
                    maxlength: 10,
                },
                postalcode: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 6,
                },
                address: {
                    required: true,
                    maxlength: 200,
                },
                state: {
                    required: true,
                    maxlength: 200,
                },
                city: {
                    required: true,
                    maxlength: 200,
                },
                type: {
                    required: true,
                },
                unit: {
                    maxlength: 200,
                },
            },
            messages: {
                first_name: {
                    required: "Please provide your first name.",
                    maxlength: "First name may not exceed 200 characters.",
                },
                last_name: {
                    maxlength: "Last name may not exceed 200 characters.",
                },
                email: {
                    required: "Please provide an email address.",
                    email: "Please provide a valid email address.",
                    maxlength: "Email may not exceed 200 characters.",
                },
                phone: {
                    required: "Please provide a phone number.",
                    digits: "Phone number must be exactly 8 digits.",
                    maxlength: "Phone number must be exactly 10 digits.",
                },
                postalcode: {
                    required: "Please provide a postal code.",
                    digits: "Postal code must be exactly 6 digits.",
                    minlength: "Postal code must be exactly 6 digits.",
                    maxlength: "Postal code must be exactly 6 digits.",
                },
                address: {
                    required: "Please provide an address.",
                    maxlength: "Address may not exceed 200 characters.",
                },
                type: {
                    required: "Please provide the address type.",
                },
                state: {
                    required: "Please provide your State.",
                    maxlength: "State may not exceed 200 characters.",
                },
                city: {
                    required: "Please provide your City.",
                    maxlength: "City may not exceed 200 characters.",
                },
                unit: {
                    maxlength: "Additional Info may not exceed 200 characters.",
                },
            },
            errorPlacement: function (error, element) {
                error.addClass("text-danger mt-1");
                error.insertAfter(element);
            },
            highlight: function (element) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid");
            },
            submitHandler: function (form) {
                var formData = new FormData(form);
                var isDefault = $("#default_address").prop("checked") ? 1 : 0;
                formData.append("default", isDefault);

                $.ajax({
                    url: $(form).attr("action"),
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $("#editAddressModal").modal("hide");
                            $("#addressEditForm")[0].reset();

                            if (response.address.default === "1") {
                                var previousDefault = $(
                                    ".allAddress .badge_primary"
                                ).closest(".row");
                                if (previousDefault.length > 0) {
                                    previousDefault
                                        .find(".badge_primary")
                                        .remove();
                                    var oldAddressId = previousDefault
                                        .find("input[type=radio]")
                                        .val();
                                    previousDefault.find(".delBadge").append(`
                                        <button type="button" class="badge_del" data-bs-toggle="modal"
                                            data-bs-target="#deleteAddressModal" data-address-id="${oldAddressId}">
                                            Delete
                                        </button>
                                    `);
                                }
                            }

                            var finddiv =
                                $("#myAddressModal").find(".allAddress");
                            finddiv
                                .find(`#selected_id_${response.address.id}`)
                                .closest(".row")
                                .remove();
                            finddiv.append(`
                                <div class="row p-2">
                                    <div class="col-10">
                                        <div class="d-flex text-start">
                                            <div class="px-1">
                                                <input type="radio" name="selected_id"
                                                    id="selected_id_${
                                                        response.address.id
                                                    }"
                                                    value="${
                                                        response.address.id
                                                    }"
                                                    ${
                                                        response.address
                                                            .default === "1"
                                                            ? "checked"
                                                            : ""
                                                    } />
                                            </div>
                                            <p class="text-turncate fs_common">
                                                <span class="px-2">
                                                    ${
                                                        response.address
                                                            .first_name
                                                    } ${
                                response.address.last_name ?? ""
                            } |
                                                    <span style="color: #c7c7c7;">&nbsp;+91 ${
                                                        response.address.phone
                                                    }</span>
                                                </span><br>
                                                <span class="px-2"
                                                    style="color: #c7c7c7">${
                                                        response.address.address
                                                    }, ${
                                response.address.city
                            }, ${response.address.state} - ${
                                response.address.postalcode
                            }.
                                                </span>
                                                <br>
                                                ${
                                                    response.address.default ===
                                                    "1"
                                                        ? '<span class="badge badge_primary">Default</span>'
                                                        : ""
                                                }
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="d-flex gap-2 delBadge">
                                                <button type="button" class="badge_edit" data-bs-toggle="modal"
                                                    data-address-id="${
                                                        response.address.id
                                                    }" data-bs-target="#editAddressModal">
                                                    Edit
                                                </button>
                                                ${
                                                    response.address.default ===
                                                    "0"
                                                        ? `
                                                    <button type="button" class="badge_del"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteAddressModal"
                                                        data-address-id="${response.address.id}">
                                                        Delete
                                                    </button>`
                                                        : ""
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);

                            if (response.address.default === "1") {
                                $(
                                    '.modal-body p strong:contains("Phone :")'
                                ).parent().html(`
                                    <strong>Phone :</strong> (+91) ${
                                        response.address.phone || "--"
                                    }
                                `);
                                $(".selected-address").html(`
                                    <p>
                                        <strong>${
                                            response.address.first_name
                                        } ${
                                    response.address.last_name ?? ""
                                } (+91)
                                            ${
                                                response.address.phone
                                            }</strong>&nbsp;&nbsp;<br>
                                        ${response.address.address}, ${
                                    response.address.city
                                }, ${response.address.state} - ${
                                    response.address.postalcode
                                }
                                        <span>
                                            <span class="badge badge_danger py-1">Default</span>&nbsp;&nbsp;
                                        </span>
                                    </p>
                                `);
                            }

                            // Show the updated address modal
                            $("#myAddressModal").modal("show");
                            showMessage(response.message, "success");
                        } else {
                            showMessage(response.message, "error");
                        }
                    },
                    error: function () {
                        showMessage(
                            "There was an issue with the request. Please try again.",
                            "error"
                        );
                    },
                });
            },
        });

        $(document).on("click", ".badge_edit", function () {
            const addressId = $(this)
                .closest(".row")
                .find("input[type='radio']")
                .val();

            $.ajax({
                url: `/getAddress/${addressId}`, // Adjust the route as necessary
                type: "GET",
                success: function (address) {
                    populateAddressModal(address);
                },
                error: function () {
                    showMessage(
                        "Failed to fetch address details. Please try again.",
                        "error"
                    );
                },
            });
        });

        function populateAddressModal(address) {
            // Populate form fields
            $("#first_name").val(address.first_name);
            $("#last_name").val(address.last_name);
            $("#phone").val(address.phone);
            $("#email").val(address.email);
            $("#postalcode").val(address.postalcode);
            $("#address").val(address.address);
            $("#unit").val(address.unit ?? "");
            $("#address_id").val(address.id ?? "");
            $("#state").val(address.state ?? "");
            $("#city").val(address.city ?? "");

            // Set Address Type
            if (address.type === "home_mode") {
                $("#home_mode").prop("checked", true);
            } else if (address.type === "work_mode") {
                $("#work_mode").prop("checked", true);
            }

            // Set default checkbox
            const defaultCheckbox = $("#default_address");
            if (address.default === 1) {
                defaultCheckbox.prop("checked", true);
                defaultCheckbox.prop("disabled", true);
            } else {
                defaultCheckbox.prop("checked", false);
                defaultCheckbox.prop("disabled", false);
            }
        }

        // Reset radio selection when modal is closed
        $("#myAddressModal").on("hidden.bs.modal", function () {
            let storedSelectedId = sessionStorage.getItem("selectedAddressId"); // Get last selected ID (if set)
            let selectedAddressId = "{{ $selectedAddressId }}"; // PHP session value
            let defaultAddressId =
                "{{ $default_address ? $default_address->id : '' }}"; // PHP default value

            $('input[name="selected_id"]').each(function () {
                if (storedSelectedId && $(this).val() === storedSelectedId) {
                    $(this).prop("checked", true);
                } else if ($(this).val() === selectedAddressId) {
                    $(this).prop("checked", true);
                } else if (
                    !selectedAddressId &&
                    $(this).val() === defaultAddressId
                ) {
                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", false);
                }
            });
        });

        $(document).ready(function () {
            var addressIdToDelete = null;

            $(document).on("click", ".badge_del", function () {
                addressIdToDelete = $(this).data("address-id");
                $("#deleteAddressModal").modal("show");
            });

            $("#confirmDeleteBtn").click(function () {
                if (!addressIdToDelete) return;

                $.ajax({
                    url: `/address/${addressIdToDelete}`,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        if (response.success) {
                            $("#deleteAddressModal").modal("hide");
                            $(`#selected_id_${addressIdToDelete}`)
                                .closest(".row")
                                .remove();
                            $("#myAddressModal").modal("show");
                            showMessage(response.message, "success");
                            addressIdToDelete = null;

                            // Check if the deleted address was the selected address and update
                            updateSelectedAddressAfterDelete();
                        } else {
                            showMessage(response.message, "error");
                        }
                    },
                    error: function (xhr, status, error) {
                        showMessage(
                            "There was an issue with the request. Please try again.",
                            "error"
                        );
                    },
                });
            });

            // Handle confirm address button click
            $("#confirmAddressBtn").on("click", function (e) {
                e.preventDefault();

                let selectedId = $('input[name="selected_id"]:checked').val();
                $("#addressErrorMessage").remove();

                if (!selectedId) {
                    $("#myAddressModal .modal-body").prepend(
                        '<div id="addressErrorMessage" class="text-danger">Please select an address.</div>'
                    );
                    return;
                }

                $.ajax({
                    url: "/selectaddress",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        selected_id: selectedId,
                    },
                    success: function (response) {
                        if (response.success) {
                            $("#myAddressModal").modal("hide");
                            updateSelectedAddress(response.selectedAddress);

                            // Store selected address ID in session storage (temporary, until page refresh)
                            sessionStorage.setItem(
                                "selectedAddressId",
                                selectedId
                            );
                        } else {
                            alert(response.message || "An error occurred.");
                        }
                    },
                    error: function (xhr) {
                        alert(xhr.responseJSON.message || "An error occurred.");
                    },
                });
            });

            // Remove error message when a radio button is selected
            $('input[name="selected_id"]').on("change", function () {
                if ($('input[name="selected_id"]:checked').val()) {
                    $("#addressErrorMessage").remove();
                }
            });

            // Function to update the selected address UI
            function updateSelectedAddress(address) {
                if (address) {
                    const addressHtml = `
                            <strong>${address.first_name} ${
                        address.last_name ?? ""
                    } (+91) ${address.phone}</strong><br>
                            ${address.address}, ${address.city}, ${
                        address.state
                    } - ${address.postalcode}
                            ${
                                address.default
                                    ? '<span class="badge badge_danger py-1">Default</span>'
                                    : ""
                            }
                        `;
                    $("#addressID").val(address.id);
                    $(".selected-address").html(addressHtml);

                    const changeBtnHtml = `<span class="badge badge_infos py-1" data-bs-toggle="modal" data-bs-target="#myAddressModal">Change</span>`;
                    $(".change-address-btn").html(changeBtnHtml);
                }
            }

            function updateSelectedAddressAfterDelete() {
                $.get("/addresses", function (addresses) {
                    const defaultAddress = addresses.find(
                        (address) => address.default === 1
                    );

                    if (defaultAddress) {
                        updateSelectedAddress(defaultAddress);
                    }
                });
            }
        });

        function showMessage(message, type) {
            var textColor, icon;

            if (type === "success") {
                textColor = "#16A34A";
                icon =
                    '<i class="fa-solid fa-check-circle" style="color: #16A34A"></i>';
                var alertClass = "toast-success";
            } else {
                textColor = "#EF4444";
                icon =
                    '<i class="fa-solid fa-triangle-exclamation" style="color: #EF4444"></i>';
                var alertClass = "toast-danger";
            }

            var alertHtml = `
              <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 70px; right: 40px; z-index: 1050; color: ${textColor};">
                <div class="toast-content">
                    <div class="toast-icon">
                        ${icon}
                    </div>
                    <span class="toast-text">${message}</span>&nbsp;&nbsp;
                    <button class="toast-close-btn" data-bs-dismiss="alert" aria-label="Close">
                        <i class="fa-solid fa-times" style="color: ${textColor}; font-size: 14px;"></i>
                    </button>
                </div>
              </div>
            `;

            $("body").append(alertHtml);
            setTimeout(function () {
                $(".alert").alert("close");
            }, 1000);
        }
    });
});

function closePopup() {
    $("#successModal").modal("hide");
    $("#errorModal").modal("hide");
}

function selectPaymentOption(optionId) {
    document.querySelectorAll(".card.payment-option").forEach((card) => {
        card.classList.remove("selected");
    });

    const selectedCard = document.getElementById(optionId).closest(".card");
    selectedCard.classList.add("selected");

    document.getElementById(optionId).checked = true;

    $("#checkoutForm")
        .validate()
        .element("#" + optionId);
}

$(document).ready(function () {
    const dealType = parseInt($("#checkoutForm").data("deal-type"), 10);
    const $placeOrderSpinner = $("#placeOrderSpinner");
    const $checkoutForm = $("#checkoutForm");

    $.validator.addMethod(
        "emailPattern",
        function (value, element) {
            return (
                this.optional(element) ||
                /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value)
            );
        },
        "Please enter a valid email address"
    );

    $checkoutForm.validate({
        rules: {
            first_name: { required: true },
            email: { required: true, email: true, emailPattern: true },
            mobile: {
                required: true,
                digits: true,
                maxlength: 10,
            },
            street: { required: true },
            city: { required: true },
            state: { required: true },
            country: { required: true },
            zipCode: {
                required: true,
                digits: true,
                minlength: 6,
                maxlength: 6,
            },
            payment_type: { required: true },
            service_date: {
                required: function () {
                    return dealType === 2;
                },
            },
            service_time: {
                required: function () {
                    return dealType === 2;
                },
            },
        },
        messages: {
            first_name: "First name is required",
            email: {
                required: "Email is required",
                email: "Please enter a valid email address",
            },
            mobile: {
                required: "Mobile number is required",
                digits: "Please enter a valid mobile number",
                maxlength: "Mobile number must be 10 digits",
            },
            street: "Street is required",
            city: "City is required",
            state: "State is required",
            country: "Country is required",
            zipCode: {
                required: "Zip Code is required",
                digits: "Zip Code should contain only numbers",
                minlength: "Mobile number must be 6 digits",
                maxlength: "Mobile number must be 6 digits",
            },
            payment_type: "Please select a payment method",
            service_date: {
                required: "Service date is required",
            },
            service_time: {
                required: "Service time is required",
            },
        },
        errorPlacement: function (error, element) {
            error.addClass("text-danger mt-1");
            if (element.attr("name") === "payment_type") {
                error.insertAfter(".payment-option:first");
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
    });

    $checkoutForm.on("submit", function (e) {
        e.preventDefault();

        const isValid = $checkoutForm.valid();

        if (isValid) {
            $placeOrderSpinner.removeClass("d-none");
            $placeOrderSpinner.addClass("show");

            this.submit();
        }
    });
});
