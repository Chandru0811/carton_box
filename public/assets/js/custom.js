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

    // Cart Remove Function
    $(".cart-remove")
        .off("click")
        .on("click", function (e) {
            e.preventDefault();
            const productId = $(this).data("product-id");
            const cartId = $(this).data("cart-id");

            $.ajax({
                url: "https://sgitjobs.com/carton_box/cart/remove",
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
                            "₹" +
                                response.updatedCart.grand_total.toLocaleString()
                        );
                        $(".quantity-value").text(
                            response.updatedCart.quantity
                        );
                    }

                    if (response.cartItemCount === 0) {
                        $(".cart-items-container").after(`
                         <div class="empty-cart col-12 text-center d-flex flex-column align-items-center justify-content-center mt-0">
                             <img src="assets/images/home/cart_empty.png" alt="Empty Cart"
                                 class="img-fluid empty_cart_img">
                             <p class="pt-5" style="color: #cd8245;font-size: 22px">Your Cart is Currently Empty</p>
                             <p class="" style="color: #6C6C6C;font-size: 16px">Looks Like You Have Not Added Anything To </br>
                                 Your Cart. Go Ahead & Explore Top Categories.</p>
                             <a href="/" class="btn showmoreBtn my-lg-4 my-2">Shop More</a>
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
                url: `http://127.0.0.1:8000/addtocart/${slug}`,
                type: "POST",
                data: {
                    quantity: 1,
                    saveoption: "add to cart",
                    cartnumber: cartnumber,
                },
                success: function (data, textStatus, jqXHR) {
                    if (textStatus === "success") {
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
                        alert("Working");
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

    // Function to Retrieve Cart Number from Local Storage or Cookie
    function getCartNumber() {
        if (isLocalStorageAvailable()) {
            return localStorage.getItem("cartnumber");
        } else {
            let match = document.cookie.match(/(^| )cartnumber=([^;]+)/);
            return match ? match[2] : null;
        }
    }

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
                                previousDefault.find(".badge_primary").remove();
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

                        var finddiv = $("#myAddressModal").find(".allAddress");
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
                                $(".defaultAddress .badge_infos").length === 0
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
                            var cartId = $("#moveToCheckout").data("cart-id");
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

    // Validation for Edit Address Form
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
                                previousDefault.find(".badge_primary").remove();
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

                        var finddiv = $("#myAddressModal").find(".allAddress");
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

    $(document).on("click", ".badge_edit", function () {
        const addressId = $(this)
            .closest(".row")
            .find("input[type='radio']")
            .val();

        $.ajax({
            url: `https://sgitjobs.com/carton_box/getAddress/${addressId}`, // Adjust the route as necessary
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

    var addressIdToDelete = null;

    $(".badge_del")
        .off("click")
        .on("click", function (e) {
            addressIdToDelete = $(this).data("address-id");
            $("#deleteAddressModal").modal("show");
        });

    $("#confirmDeleteBtn").click(function () {
        if (!addressIdToDelete) return;

        $.ajax({
            url: `https://sgitjobs.com/carton_box/address/${addressIdToDelete}`,
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

    function showMessage(message, type) {
        var textColor, icon;

        if (type === "success") {
            textColor = "#16A34A";
            icon =
                '<i class="fa-regular fa-cart-shopping" style="color: #16A34A"></i>';
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
        }, 5000);
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
            url: "https://sgitjobs.com/carton_box/selectaddress",
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
                    sessionStorage.setItem("selectedAddressId", selectedId);
                } else {
                    alert(response.message || "An error occurred.");
                }
            },
            error: function (xhr) {
                alert(xhr.responseJSON.message || "An error occurred.");
            },
        });
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

    // Remove error message when a radio button is selected
    $('input[name="selected_id"]').on("change", function () {
        if ($('input[name="selected_id"]:checked').val()) {
            $("#addressErrorMessage").remove();
        }
    });

    $(document).ready(function () {
        const dealType = parseInt($("#checkoutForm").data("deal-type"), 10);
        const $placeOrderSpinner = $("#placeOrderSpinner");
        const $checkoutForm = $("#checkoutForm");

        $.validator.addMethod(
            "emailPattern",
            function (value, element) {
                return (
                    this.optional(element) ||
                    /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(
                        value
                    )
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

function closePopup() {
    $("#successModal").modal("hide");
    $("#errorModal").modal("hide");
}

function showAddress(country) {
    // Hide all addresses
    var contents = document.getElementsByClassName("address-content");
    for (var i = 0; i < contents.length; i++) {
        contents[i].classList.remove("active-address");
    }

    // Show the selected address
    document.getElementById(country).classList.add("active-address");

    // Update active tab styling
    var tabs = document.getElementsByClassName("nav-link");
    for (var j = 0; j < tabs.length; j++) {
        tabs[j].classList.remove("active");
    }
    document.getElementById(country + "-tab").classList.add("active");

    // Change phone number and href based on the selected country
    var phoneLink = document.getElementById("phone-link");
    var phoneNumber = document.getElementById("phone-number");

    if (country === "india") {
        phoneLink.href = "tel:+9188941306";
        phoneNumber.innerHTML = "+91 8894 1306";
    } else if (country === "india") {
        phoneLink.href = "tel:+9188941306";
        phoneNumber.innerHTML = "+91 8894 1306";
    }
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

function checkAddressAndOpenModal() {
    fetch("/addresses")
        .then((response) => response.json())
        .then((data) => {
            if (data.length === 0) {
                $("#defaultAddressCheckbox").prop("checked", true);
                $("#defaultAddressCheckbox").prop("disabled", true);
            } else {
                $("#defaultAddressCheckbox").prop("checked", false);
                $("#defaultAddressCheckbox").prop("disabled", false);
            }
            $("#newAddressModal").modal("show");
        })
        .catch((error) => console.error("Error fetching address:", error));
}
