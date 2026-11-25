$(window).on("load", function () {
    cartCount();
    $(".js-mega-menu").HSMegaMenu({
        event: "hover",
        direction: "horizontal",
        pageContainer: $(".container"),
        breakpoint: 767.98,
        hideTimeOut: 0,
    });

    // initialization of svg injector module
    $.HSCore.components.HSSVGIngector.init(".js-svg-injector");
});

$(document).on("ready", function () {
    // initialization of header
    $.HSCore.components.HSHeader.init($("#header"));

    // initialization of animation
    $.HSCore.components.HSOnScrollAnimation.init("[data-animation]");

    // initialization of unfold component
    $.HSCore.components.HSUnfold.init($("[data-unfold-target]"), {
        afterOpen: function () {
            $(this).find('input[type="search"]').focus();
        },
    });

    // initialization of popups
    $.HSCore.components.HSFancyBox.init(".js-fancybox");

    // initialization of countdowns
    var countdowns = $.HSCore.components.HSCountdown.init(".js-countdown", {
        yearsElSelector: ".js-cd-years",
        monthsElSelector: ".js-cd-months",
        daysElSelector: ".js-cd-days",
        hoursElSelector: ".js-cd-hours",
        minutesElSelector: ".js-cd-minutes",
        secondsElSelector: ".js-cd-seconds",
    });

    // initialization of malihu scrollbar
    $.HSCore.components.HSMalihuScrollBar.init($(".js-scrollbar"));

    // initialization of forms
    $.HSCore.components.HSFocusState.init();

    // initialization of form validation
    $.HSCore.components.HSValidation.init(".js-validate", {
        rules: {
            confirmPassword: {
                equalTo: "#signupPassword",
            },
        },
    });

    // initialization of show animations
    $.HSCore.components.HSShowAnimation.init(".js-animation-link");

    // initialization of fancybox
    $.HSCore.components.HSFancyBox.init(".js-fancybox");

    // initialization of slick carousel
    $.HSCore.components.HSSlickCarousel.init(".js-slick-carousel");

    // initialization of go to
    $.HSCore.components.HSGoTo.init(".js-go-to");

    // initialization of hamburgers
    $.HSCore.components.HSHamburgers.init("#hamburgerTrigger");

    // initialization of unfold component
    $.HSCore.components.HSUnfold.init($("[data-unfold-target]"), {
        beforeClose: function () {
            $("#hamburgerTrigger").removeClass("is-active");
        },
        afterClose: function () {
            $("#headerSidebarList .collapse.show").collapse("hide");
        },
    });

    $('#headerSidebarList [data-toggle="collapse"]').on("click", function (e) {
        e.preventDefault();

        var target = $(this).data("target");

        if ($(this).attr("aria-expanded") === "true") {
            $(target).collapse("hide");
        } else {
            $(target).collapse("show");
        }
    });

    // initialization of unfold component
    $.HSCore.components.HSUnfold.init($("[data-unfold-target]"));

    // initialization of select picker
    $.HSCore.components.HSSelectPicker.init(".js-select");
});

var swiper = new Swiper(".mySwiper", {
    slidesPerView: 3,
    spaceBetween: 30,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        // When window width is >= 320px
        320: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        // When window width is >= 640px
        640: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
        // When window width is >= 1024px
        1024: {
            slidesPerView: 3,
            spaceBetween: 30,
        },
        // When window width is >= 1440px
        1440: {
            slidesPerView: 4,
            spaceBetween: 30,
        },
    }
});


$(document).ready(function () {
    cartCount(); // Initial cart count update when the document is ready
});
// Function to update the cart count

$(document).ready(function () {
    // init_validator();
    // validator.defaults.alerts = false;

    $(".modal").modal({
        show: false,
        backdrop: "static",
        keyboard: false,
    });

    toastr.options.timeOut = 1000;
    $("#add-to-cart").on("submit", function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr("action"),
            method: $(this).attr("method"),
            data: new FormData(this),
            processData: false,
            dataType: "json",
            contentType: false,

            success: function (data) {
                if (data.status == 0) {
                    toastr.error(data.error.quantity);
                } else {
                    toastr.success(data.msg);
                    cartCount();
                    loadCartItem();
                }
            },
        });
    });
});
