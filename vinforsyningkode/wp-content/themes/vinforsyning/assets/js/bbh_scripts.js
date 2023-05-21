"use strict";

(function($) {
    let timeout;
    $(".woocommerce").on("change", "input.qty", function() {
        if (timeout !== undefined) {
            clearTimeout(timeout);
        }
        timeout = setTimeout(function() {
            $("[name='update_cart']").trigger("click"); // trigger cart update
        }, 1000); // 1 second delay, half a second (500) seems comfortable too
    });

    /*===============================================
        =          BBH Page Load Spinner           =
        ===============================================*/
    // $( document ).ready(function() {
    //     $('.global-page-spinner').fadeOut(300, 'linear');
    // });
    //
    //
    // window.onbeforeunload = function () {
    //     $('.global-page-spinner').fadeIn(300, 'linear');
    // }
    //
    // document.onreadystatechange = () => {
    //     $('.global-page-spinner').fadeIn(300, 'linear');
    //   if (document.readyState === 'complete') {
    //     $('.global-page-spinner').fadeOut(300, 'linear');
    //   }
    // };

    /*===============================================
        =          OUTLET ORDERING           =
        ===============================================*/
    $(document).ready(function() {
        if (window.location.href.indexOf("/min-konto/outlet") > -1) {
            const urlSearchParams = new URLSearchParams(window.location.search);
            const params = Object.fromEntries(urlSearchParams.entries());
            if (params && params.hasOwnProperty("orderby")) {
                var orderby = params["orderby"];
                if (orderby != "stock") {
                    $(
                        '.woocommerce-ordering select.orderby option[selected="selected"]'
                    ).removeAttr("selected");
                    $(
                        `.woocommerce-ordering select.orderby option[value="${orderby}"]`
                    ).attr("selected", "selected");
                }
            }
            console.log(
                $(".woocommerce-pagination ul.page-numbers li a.prev").length
            );
            if ($(".woocommerce-pagination ul.page-numbers li a.prev").length == 0) {
                $(".woocommerce-pagination ul.page-numbers").addClass("missing-left");
            } else if (
                $(".woocommerce-pagination ul.page-numbers li a.next").length == 0
            ) {
                $(".woocommerce-pagination ul.page-numbers").addClass("missing-right");
            }
        }
    });

    // Change quantity for ajax add to cart button
    $(document.body).on("click input", "input.qty", function() {
        $(this)
            .parent()
            .parent()
            .find("a.ajax_add_to_cart")
            .attr("data-quantity", $(this).val()); // (optional) Removing other previous "view cart" buttons

        $(".added_to_cart").remove();
    });

    let customVideoPlayer = function customVideoPlayer() {
        let self = this;
        self.wrap = document.querySelector(".video-container");

        self.init = function() {
            if (!self.wrap) {
                return false;
            } // assign props

            self.play = self.wrap.querySelector(".play-btn");
            self.video = self.wrap.querySelector("video");
            self.mute = self.wrap.querySelector(".mute-btn");
            self.play.addEventListener("click", self.onPlayClick);
            self.video.addEventListener("ended", self.onVideoEnd);
            self.video.addEventListener("click", self.onVideoClick);
            self.mute.addEventListener("click", self.toggleMute);
            self.play.addEventListener("mouseenter", self.addHoverClass);
            self.play.addEventListener("mouseleave", self.removeHoverClass);
            self.play.addEventListener("touchstart", self.onVideoTouch);
        };

        self.isVideoPlaying = function() {
            return !!(
                self.video.currentTime > 0 &&
                !self.video.paused &&
                !self.video.ended &&
                self.video.readyState > 2
            );
        };

        self.onVideoClick = function() {
            if (self.isVideoPlaying()) {
                self.wrap.classList.remove("playing");
                self.video.pause();
            }
        };

        self.onVideoRun = function() {};

        self.onVideoEnd = function() {
            setTimeout(function() {
                self.wrap.classList.remove("playing");
                self.wrap.classList.remove("show-video");
                setTimeout(function() {
                    self.video.currentTime = 0;
                }, 200);
            }, 500);
        };

        self.onPlayClick = function() {
            if (self.isVideoPlaying()) {
                self.wrap.classList.remove("playing");
                self.video.pause();
            } else {
                self.wrap.classList.add("playing");
                self.wrap.classList.add("show-video");
                self.video.play();
            }
        };

        self.onVideoTouch = function() {
            if (window.innerWidth <= 768) {
                self.addHoverClass();
                setTimeout(function() {
                    self.removeHoverClass();
                }, 700);
            }
        };

        self.addHoverClass = function() {
            self.wrap.classList.add("is-hover");
        };

        self.removeHoverClass = function() {
            self.wrap.classList.remove("is-hover");
        };

        self.toggleMute = function() {
            if (self.video.muted) {
                self.wrap.classList.remove("muted");
            } else {
                self.wrap.classList.add("muted");
            }

            self.video.muted = !self.video.muted;
        };

        self.init();
    };

    document.addEventListener("DOMContentLoaded", customVideoPlayer);
    /*=============================================
        = Sticky header =
        ===============================================*/

    let customStickyHeader = function customStickyHeader() {
        let self = this;
        self.header = document.getElementById("fixed-header");
        self.headerHeight = 0;
        self.isStuck = false;

        self.construct = function() {
            // add placeholder element to fill space
            self.addPlaceholderHeader(); // do sticky check on scroll

            window.addEventListener("scroll", self.onWindowScroll); // trigger resize checks and add resizing events

            self.onResize();
            window.addEventListener("resize", self.onResize);
            window.addEventListener("load", self.onResize); // trigger inital scroll to see if page is scrolled on page load

            self.onWindowScroll();
        };

        self.addPlaceholderHeader = function() {
            let newNode = document.createElement("div");
            newNode.classList.add("header-placeholder");
            self.header.parentNode.insertBefore(newNode, self.header);
            self.placeholder = newNode;
        };

        self.onResize = function() {
            self.headerHeight = self.header.offsetHeight;
            self.placeholder.style.height = self.header.offsetHeight + "px";
        };

        self.onWindowScroll = function() {
            window.scrollY;
            let breakpoint = 350;
            let placeholderPos = self.placeholder.offsetTop;

            if (window.scrollY >= breakpoint && self.isStuck !== true) {
                self.isStuck = true;
                document.documentElement.classList.add("stuck-header");
                self.placeholder.classList.add("show");
                self.header.classList.add("is-stuck");
                self.header.classList.add("animated");
                self.header.classList.add("fadeIn");
            } else if (
                (window.scrollY < placeholderPos || window.scrollY == 0) &&
                self.isStuck == true
            ) {
                self.isStuck = false;
                document.documentElement.classList.remove("stuck-header");
                self.placeholder.classList.remove("show");
                self.header.classList.remove("is-stuck");
                self.header.classList.remove("animated");
                self.header.classList.remove("fadeIn");
            }
        }; // initalize function

        self.construct();
    };

    document.addEventListener("DOMContentLoaded", function() {
        new customStickyHeader();
    });
    /*=============================================
        = Enter-view animations =
        ===============================================*/

    let bbhEnterView = function bbhEnterView() {
        // make sure enterView is available
        if (!window.enterView || typeof enterView !== "function") {
            return;
        }

        $("[data-animation]").each(function() {
            let offset = this.getAttribute("data-animation-offset") || 0.1;
            let once = this.getAttribute("data-animation-once") || true;
            let delay = this.getAttribute("data-animation-delay") || 0;
            enterView({
                selector: [this],
                enter: function enter(el) {
                    setTimeout(function() {
                        let animationClass = el.getAttribute("data-animation");
                        el.classList.add("animated");
                        el.classList.add(animationClass); //change this class to change animation
                    }, delay);
                },
                exit: function exit(el) {
                    let animationClass = el.getAttribute("data-animation");
                    el.classList.remove("animated");
                    el.classList.remove(animationClass); //change this class to change animation
                },
                offset: parseFloat(offset),
                once: once,
            });
        });
    };

    /* Cart - Change adresse link - https://vinforsyning.dk/min-konto/cart/ */
    $(document).ready(function() {
        $(".shipping-calculator-button").attr("href", "/min-konto/edit-address/");
    });

    $(document).ready(function() {
        new bbhEnterView();
    });
    /*=============================================
        = WooCommerce quantity buttons =
        ===============================================*/

    $(document).on("ready ajaxComplete", function() {
        function plusClick(evt) {
            evt.preventDefault();
            let input = $(this).siblings(".qty");
            let max = input.attr("max") || Number.MAX_SAFE_INTEGER;
            let step =
                input.attr("step") && input.attr("step") != 0.01 ?
                input.attr("step") :
                1;
            let newVal = parseInt(input.val()) + parseInt(step);
            //let shit = input.attr('step');
            //let newVal = shit;

            if (newVal > max) {
                newVal = max;
            }

            if (input.hasClass("kolli-qty")) {
                let kolli = input.data("kolli");
                let real_qty = $(this)
                    .parents(".cart_item")
                    .find(".product-quantity input.qty");
                real_qty.val(newVal * kolli);
                real_qty.trigger("change");
            }

            input.val(newVal);
            input.trigger("change");
        }

        function minusClick(evt) {
            evt.preventDefault();
            let input = $(this).siblings(".qty");
            let min = input.attr("min") || 0;
            let step =
                input.attr("step") && input.attr("step") != 0.01 ?
                input.attr("step") :
                1;
            let newVal = parseInt(input.val()) - parseInt(step);

            if (newVal < min) {
                newVal = min;
            }

            if (input.hasClass("kolli-qty")) {
                let kolli = input.data("kolli");
                let real_qty = $(this)
                    .parents(".cart_item")
                    .find(".product-quantity input.qty");
                real_qty.val(newVal * kolli);
                real_qty.trigger("change");
            }

            input.val(newVal);
            input.trigger("change");
        } // append buttons to quantity inputs, that doesn't already have them

        $("input.qty:not(.has-buttons)").each(function() {
            let input = $(this);
            input.after('<button class="plus">+</button>');
            input.before('<button class="minus">&minus;</button>'); // register events
            input.siblings(".plus").on("click", plusClick);
            input.siblings(".minus").on("click", minusClick);
            input.addClass("has-buttons");
            input.parent().addClass("has-buttons");

            input.not(".kolli-qty").change(function() {
                let value = $(this).val();
                let kolli_qty = $(this)
                    .parents(".cart_item")
                    .find(".product-kolli-qty input.qty");
                let kolli = kolli_qty.data("kolli");
                let newVal = Number(value / kolli).toFixed(2);
                if (newVal.includes(".00")) {
                    newVal = newVal.replace(".00", "");
                }
                kolli_qty.val(newVal);
                kolli_qty.trigger("change");
            });
        });
    });

    /*=============================================
        = Video modal player =
        ===============================================*/

    $(function() {
        registerModal();
    });

    function registerModal() {
        let modal = $("#bbh-modal");
        let overlay = $("#bbh-modal--overlay");
        let iframe = modal.find(".modal-frame");
        let animation = 300;

        function openIframeModal() {
            let src = $(this).attr("data-iframe-src");
            iframe.attr("src", src);
            modal.addClass("open");
            modal.fadeIn(animation);
            modal.isOpen = true;
        }

        function closeModal() {
            modal.fadeOut(animation, function() {
                modal.removeClass("open");
                iframe.attr("src", "");
                modal.isOpen = false;
            });
        }

        function onModalKeyDown(evt) {
            if (evt.keyCode == 27 || evt.key === "Escape") {
                if (modal.isOpen == true) {
                    closeModal();
                }
            }
        } // Register events

        $("[data-iframe-src]").on("click", openIframeModal);
        overlay.on("click", closeModal);
        modal.find(".close").on("click", closeModal);
        $(window).on("keydown", onModalKeyDown);
    }
    /*=============================================
        = flexible content slider =
        ===============================================*/

    $(document).on("ready", initFlexContentSlider);

    function initFlexContentSlider() {
        // Make sure slick is available
        if (typeof $.fn.slick !== "function") {
            return;
        }

        $(".c2-content-slider .content-slider").each(function() {
            let slider = $(this);
            $(this).slick({
                rows: 0,
                slidesToShow: 1,
                slidesToScroll: 1,
                fade: true,
                dots: true,
                arrows: false,
                adaptiveHeight: true,
                speed: 800,
            }); // Fix slider sizing when lazyloading images

            setTimeout(function() {
                slider.slick("setPosition");
            }, 50); // set position after every lazyloaded image.

            $(slider)
                .find(".lazyload")
                .on("lazyloaded", function() {
                    slider.slick("setPosition");
                });
        });
    }
    /*=============================================
        = Custom Login popup =
        ===============================================*/

    let LoginFormPopup = function LoginFormPopup() {
        const self = this; // element references

        self.popup;
        self.popupOverlay;
        self.form; // state

        self.isOpen = false;

        self.init = function() {
            let popup = document.getElementById("login-popup");

            if (!popup) {
                return false;
            }

            self.popup = popup;
            self.form = popup.querySelector("form");
            self.popupOverlay = document.getElementById("login-popup-overlay"); // Check if form is open from page load due to submission data error.

            self.checkInitalOpen(); //self.setFormPlaceholders(); // This was only needed when we used the native wp form.

            self.registerEvents();
        };

        self.registerEvents = function() {
            let close = self.popup.querySelector(".close");

            if (close) {
                close.addEventListener("click", self.closePopup);
            }

            self.popupOverlay.addEventListener("click", self.closePopup); // register custom events to work with popup

            document.addEventListener("openLogin", self.openPopup);
            document.addEventListener("closeLogin", self.closePopup);
            document.addEventListener("keydown", self.onKeyDown);
        };

        self.removeURLParameter = function(url, parameter) {
            //prefer to use l.search if you have a location/link object
            var urlparts = url.split("?");

            if (urlparts.length >= 2) {
                var prefix = encodeURIComponent(parameter) + "=";
                var pars = urlparts[1].split(/[&;]/g); //reverse iteration as may be destructive

                for (var i = pars.length; i-- > 0;) {
                    //idiom for string.startsWith
                    if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                        pars.splice(i, 1);
                    }
                }

                return urlparts[0] + (pars.length > 0 ? "?" + pars.join("&") : "");
            }

            return url;
        };

        self.checkInitalOpen = function() {
            const query = window.location.search;

            if (query.indexOf("login=failed") != -1) {
                self.openPopup();

                if ("history" in window) {
                    window.history.replaceState({},
                        document.title,
                        self.removeURLParameter(window.location.href, "login")
                    );
                }
            }
        }; // check for escape keypress when open

        self.onKeyDown = function(evt) {
            if (evt.keyCode == 27 || evt.key === "Escape") {
                if (self.isOpen == true) {
                    self.closePopup();
                }
            }
        };

        self.openPopup = function() {
            self.isOpen = true;
            self.popup.classList.add("open");
            self.popupOverlay.classList.add("open");
        };

        self.closePopup = function() {
            self.isOpen = false;
            self.popup.classList.remove("open");
            self.popupOverlay.classList.remove("open");
        }; // used for native wp login form which has no placeholders

        self.setFormPlaceholders = function() {
            let inputs = self.form.querySelectorAll(".input");

            for (var i = 0; i < inputs.length; i++) {
                let label = inputs[i].parentNode.querySelector("label");

                if (label) {
                    inputs[i].setAttribute("placeholder", label.textContent);
                }
            }
        };

        self.init();
    };

    new LoginFormPopup(); // register login button click

    document.addEventListener("DOMContentLoaded", function() {
        let loginBtn = document.querySelectorAll(".login-btn");

        for (let i = 0; i < loginBtn.length; i++) {
            loginBtn[i].addEventListener("click", function(e) {
                e.preventDefault();
                document.dispatchEvent(new Event("openLogin"));
            });
        }
    });
    /*=============================================
        = Typewriter effect banner =
        ===============================================*/

    var TxtRotate = function TxtRotate(el, toRotate, period) {
        let ref = this;
        this.toRotate = toRotate;
        this.el = el;
        this.parentNode = this.el.parentNode;
        this.loopNum = 0;
        this.period = parseInt(period, 10) || 2000;
        this.txt = el.textContent;
        this.tick();
        this.isDeleting = true;
        this.height = this.parentNode.clientHeight;
        ref.parentNode.style.minHeight = this.height + "px";
        window.addEventListener("resize", function() {
            ref.height = 0;
            ref.parentNode.style.minHeight = ref.height + "px";
        });
    };

    TxtRotate.prototype.tick = function() {
        var i = this.loopNum % this.toRotate.length;
        var fullTxt = this.toRotate[i];

        if (this.isDeleting) {
            this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
            this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.el.classList.add("writing");
        this.el.innerHTML = '<span class="wrap">' + this.txt + "</span>";
        var that = this;
        var delta = Math.random() * (150 - 80) + 80; //var delta = 300 - Math.random() * 100;

        if (this.isDeleting) {
            delta /= 2;
        }

        if (!this.isDeleting && this.txt === fullTxt) {
            delta = this.period;
            this.isDeleting = true;
            this.el.classList.remove("writing");
            this.height =
                this.height < this.parentNode.clientHeight ?
                this.parentNode.clientHeight :
                this.height;
            this.parentNode.style.minHeight = this.height + "px";
        } else if (this.isDeleting && this.txt === "") {
            this.isDeleting = false;
            this.el.classList.remove("writing");
            this.loopNum++;
            delta = 500;
        }

        setTimeout(function() {
            that.tick();
        }, delta);
    };

    window.onload = function() {
        var elements = document.getElementsByClassName("txt-rotate");

        for (var i = 0; i < elements.length; i++) {
            var toRotate = elements[i].getAttribute("data-rotate");
            var period = elements[i].getAttribute("data-period");

            if (toRotate) {
                new TxtRotate(elements[i], JSON.parse(toRotate), period);
            }
        }
    };
    /*=============================================
        = Single product gallery =
        ===============================================*/

    $(document).on("ready", initSingleProductGallerySlider);

    function initSingleProductGallerySlider() {
        if (typeof $.fn.slick !== "function") {
            return;
        }

        $(".product-gallery .gallery-slider").each(function() {
            let dotsNav = $(this).find(".dots-nav");
            let slider = $(this);
            $(this).slick({
                rows: 0,
                slidesToShow: 1,
                slidesToScroll: 1,
                fade: true,
                arrows: true,
                adaptiveHeight: true,
                speed: 800,
                waitForAnimate: false,
                prevArrow: '<span class="arrow prev"></span>',
                nextArrow: '<span class="arrow next"></span>',
            });
            setTimeout(function() {
                slider.slick("setPosition");
            }, 50);
            $(slider)
                .find(".lazyload")
                .on("lazyloaded", function() {
                    slider.slick("setPosition");
                });
        });
        let variationForm = document.querySelector("form.variations_form");

        if (variationForm) {
            jQuery(variationForm).on("found_variation", function(evt, data) {
                setTimeout(function() {
                    if ("image_id" in data) {
                        let index = $(
                            '.product-gallery .gallery-slider .slick-slide[data-image-id="' +
                            data.image_id +
                            '"]'
                        ).index();
                        $(".product-gallery .gallery-slider").slick("slickGoTo", index);
                    }
                }, 0);
            });
            jQuery(variationForm).on("reset_image", function() {
                setTimeout(function() {
                    $(".product-gallery .gallery-slider").slick("slickGoTo", 0);
                }, 0);
            });
        }
    }
    /*===============================================
      =          logo slider slickSliderIntialize           =
      ===============================================*/
    const slick = () => {
        $(".logo-slider").each(function() {
            const $slider = $(this);
            $slider.slick({
                slidesToShow: 6,
                slidesToScroll: 1,
                dots: true,
                arrows: false,
                autoplay: true,
                autoplaySpeed: 0,
                speed: 4000,
                pauseOnHover: false,
                cssEase: "linear",
                responsive: [{
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 4,
                        },
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 3,
                        },
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 2,
                        },
                    },
                ],
            });
            $slider.on("setPosition", function(event, slick) {
                // hide dots when all slides are visible
                if (slick.slideCount <= slick.options.slidesToShow) {
                    $slider.find(".slick-dots").hide();
                } else {
                    $slider.find(".slick-dots").show();
                }
            });
        });
    };

    $(document).ready(() => {
        slick();
    });

    /*=============================================
        = single post gallery =
        ===============================================*/

    $(document).on("ready", initSinglePostGallerySlider);

    function initSinglePostGallerySlider() {
        if (typeof $.fn.slick !== "function") {
            return;
        }

        $(".article-section .gallery").each(function() {
            let slider = $(this);
            let count = slider.data("cols") || 1;
            let rows = slider.data("rows") || 0;
            $(this).slick({
                rows: rows,
                slidesToShow: count,
                slidesToScroll: 1,
                fade: false,
                arrows: false,
                adaptiveHeight: false,
                speed: 800,
                dots: true,
                infinite: false,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        rows: 1,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    },
                }, ],
            });
            setTimeout(function() {
                slider.slick("setPosition");
            }, 50);
            $(slider)
                .find(".lazyload")
                .on("lazyloaded", function() {
                    slider.slick("setPosition");
                });
        });
    }
    /* CONTACT - COLLAPSIBLE - EMPLOYEES*/

    $(".contact-employees-collapsible .info .headline").click(function() {
        //Add or remove active class from clicked element
        if ($(this).hasClass("active")) {
            $(this).removeClass("active");
        } else {
            $(this).addClass("active");
        } //Slide content that belong to clicked element

        $(this).next(".collapsible-content").slideToggle(); //SlideUp all other content

        $(this)
            .parent(".info")
            .siblings(".info")
            .children(".collapsible-content")
            .slideUp(); //Remove active class from all other content boxes

        $(this)
            .parent(".info")
            .siblings(".info")
            .children(".headline")
            .removeClass("active");
    });
    /*=============================================
        = Checkout datepicker =
        ===============================================*/
    var labels = document.getElementsByTagName("LABEL");
    for (var i = 0; i < labels.length; i++) {
        if (labels[i].htmlFor != "") {
            var elem = document.getElementById(labels[i].htmlFor);
            if (elem) elem.label = labels[i];
        }
    }
    var delivery_days = document.getElementById("bbh_delivery_datepicker");

    if (typeof delivery_days != "undefined" && delivery_days != null) {
        delivery_days = delivery_days.label.className;
        delivery_days ? (delivery_days = delivery_days) : (delivery_days = 1);

        // var unavailableDates = ["27/5/2021", "28/5/2021"];
        var holidays_test = $("#holidays_dates").text();
        var holidays_test_trim = holidays_test.replace(/\s+/g, "").trim();
        var array_test = holidays_test_trim.split(",");
        var bankHoliDays = array_test;

        function disableDates(date) {
            var dt = $.datepicker.formatDate("dd/mm/yy", date);
            var noWeekend = jQuery.datepicker.noWeekends(date);
            return noWeekend[0] ?
                $.inArray(dt, bankHoliDays) < 0 ? [true] : [false] :
                noWeekend;
        }

        var sVal = delivery_days;
        var count = parseInt(sVal);

        //var count = 7;
        //Add the 2 days of weekend in numer of days .
        var d = $.datepicker.formatDate("dd/mm/yy", new Date());
        console.log("d ", d);
        // var today_date = $.datepicker.formatDate('dd/mm/yy', new Date());
        // console.log("new date d", d);
        count = count + parseInt(count / 5) * 2;
        console.log("count", count);

        // d.setDate(d.getDate() +count);
        //
        //
        // var startDate = today_date; //YYYY-MM-DD
        // var endDate = d; //YYYY-MM-DD
        //
        // var getDateArray = function(start, end) {
        //     var arr = new Array();
        //     var dt = new Date(start);
        //     while (dt <= end) {
        //         arr.push(new Date(dt));
        //         dt.setDate(dt.getDate() + 1);
        //     }
        //     return arr;
        // }
        //
        // var dateArr = getDateArray(startDate, endDate);
        // console.log('test', dateArr)
        // //var dateArr = $.datepicker.formatDate('dd/mm/yy');
        // // Output
        //
        //
        // //suppose its ending on weekend day then increment them manually
        // if(d.getDay()>5) {  d.setDate(d.getDate()+ (d.getDay()-5)) ; }

        $("#bbh_delivery_datepicker").datepicker({
            //beforeShowDay: $.datepicker.noWeekends, //disable weekends
            beforeShowDay: disableDates,
            minDate: count, //cant choose days in the past
            //minDate : d, //cant choose days in the past
            maxDate: 120, //can only go X days into the future
            // setDate: '09-08-2021',
            //translation
            dayNamesMin: ["Sø", "Ma", "Ti", "On", "To", "Fr", "Lø"],
            monthNames: [
                "Januar",
                "Februar",
                "Marts",
                "April",
                "Maj",
                "Juni",
                "Juli",
                "August",
                "September",
                "Oktober",
                "November",
                "December",
            ],
            firstDay: 1,
            weekHeader: "Uge",
            dateFormat: "dd/mm/yy",
        });
        //$( "#bbh_delivery_datepicker" ).datepicker( "show")

        $(document).ready(function() {
            $("#ui-datepicker-div").addClass("hide-this");
            $("#bbh_delivery_datepicker").datepicker("show");
            var day = $(".ui-datepicker-calendar td[data-handler=selectDay]")
                .first()
                .children("a")
                .text();
            var month = $(".ui-datepicker-calendar td[data-handler=selectDay]")
                .first()
                .data("month");
            month++;
            var year = $(".ui-datepicker-calendar td[data-handler=selectDay]")
                .first()
                .data("year");
            var firstDate = day + "/" + month + "/" + year;
            $("#bbh_delivery_datepicker").val(firstDate);
            $("#bbh_delivery_datepicker").datepicker("hide");

            $("#bbh_delivery_datepicker").on("click", function() {
                console.log("click");
                $("#ui-datepicker-div").removeClass("hide-this");
                $("#bbh_delivery_datepicker").datepicker("show");
            });
        });

        // console.log("datepicker ", firstDay)
        // console.log('number of days', delivery_days);
        // console.log('acf input', holidays_test_trim);
        // console.log('array output', bankHoliDays);

        //console.log("disabledates ", beforeShowDay: disableDates);
    }
    //set first weekday to monday
    //var firstDay = $('#bbh_delivery_datepicker').datepicker('option', 'firstDay');
    //$('#bbh_delivery_datepicker').datepicker('option', 'firstDay', 1);

    /*===============================================
        =          Checkout - Remember shipping           =
        ===============================================*/
    if ($("#bbh_remember_shipping_field:checked").length > 0) {
        $("#ship-to-different-address-checkbox").prop("checked", true);
    }

    /*===============================================
        =          my account previous order           =
        ===============================================*/
    // var element =  document.querySelectorAll('.prev-quantity');
    // if (typeof(element) != 'undefined' && element != null){
    //     console.log('elementet findes')
    // }

    /*===============================================
        =          Invoice - Table sorting         =
        ===============================================*/

    $(document).on("click", ".invoices th:not(.pdf)", function() {
        var table = $(this).parents("table").eq(0);
        var rows = table
            .find("tbody tr")
            .toArray()
            .sort(comparer($(this).index()));
        this.asc = !this.asc;
        if (!this.asc) {
            rows = rows.reverse();
        }
        for (var i = 0; i < rows.length; i++) {
            table.append(rows[i]);
        }
    });

    function comparer(index) {
        return function(a, b) {
            var valA = getCellValue(a, index),
                valB = getCellValue(b, index);
            return $.isNumeric(valA) && $.isNumeric(valB) ?
                valA - valB :
                valA.toString().localeCompare(valB);
        };
    }

    function getCellValue(row, index) {
        return $(row).children("td").eq(index).text();
    }

    // Invoice - show/hide paid invoices
    $("#paid-invoice").change(function() {
        if ($(this).is(":checked")) {
            $(".woocommerce .invoices table tbody tr.paid").each(function() {
                $(this).addClass("hide");
            });
        } else {
            $(".woocommerce .invoices table tbody tr").each(function() {
                $(this).removeClass("hide");
            });
        }
    });

    /*========================================
=           ecology ajax                  =
========================================*/
    function runAjax() {
        var filter = $("#filter"); // Get form
        var wrapper = $("#response"); // Get markup grid container
        $.ajax({
            url: filter.attr("action"), // Get form action
            data: filter.serialize(), // Get form data
            type: filter.attr("method"), // Get form method
            beforeSend: function() {
                wrapper.animate({
                        opacity: 0,
                    },
                    50
                ); // Fade markup out
            },
            success: function(data) {
                if (data.ecology) {
                    wrapper.html(data.ecology); // insert data from output buffer in archives-ecology.php
                    let ecology_percent = $("#ajax-ecology-percentage").val();
                    filter.find("#ecology-value").text(ecology_percent);
                }
            },
            complete: function() {
                wrapper.delay(100).animate({
                        opacity: 1,
                    },
                    200
                ); // Fade markup in
            },
            error: function(error) {
                wrapper.css("opacity", 1); // Fade markup in
                console.log(error); // Console log error
            },
        });
    }
    $("#ecology-reset").on("click", function() {
        setTimeout(function() {
            runAjax();
        }, 50);
    });
    //Run ajax on form change
    $("#filter").change(function() {
        runAjax();
        return false;
    });
    //Reset when 'all' is pressed
    $("#resetform").click(function() {
        $("#filter input").each(function() {
            $(this).attr("checked", false);
        });
        $(this).attr("checked", true);
        runAjax();
    });
    //Label click = input click
    $("#filter label, #filter-tags label").click(function() {
        $(this).siblings("input").click();
        $(this).parent().siblings().children("input").attr("checked", false);
    });

    // Ecology - Copy btn
    $(".ecology-flex-container #copy-btn").on("click", function() {
        SelectContent();
        $(this).text("Kopieret");
        $(this).addClass("copied");
        setTimeout(function() {
            $(".ecology-flex-container #copy-btn").text(
                "Kopier hele tabel til udklipsholder"
            );
            $(".ecology-flex-container #copy-btn").removeClass("copied");
        }, 3000);
    });

    function SelectContent() {
        var elemToSelect = document.getElementById("ecology-table");

        if (window.getSelection) {
            // all browsers, except IE before version 9
            var selection = window.getSelection();
            var rangeToSelect = document.createRange();
            rangeToSelect.selectNodeContents(elemToSelect);

            selection.removeAllRanges();
            selection.addRange(rangeToSelect);
        } // Internet Explorer before version 9
        else if (document.body.createTextRange) {
            // Internet Explorer
            var rangeToSelect = document.body.createTextRange();
            rangeToSelect.moveToElementText(elemToSelect);
            rangeToSelect.select();
        } else if (document.createRange && window.getSelection) {
            range = document.createRange();
            range.selectNodeContents(el);
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
        document.execCommand("copy");
    }

    $(document).ready(function() {
        var currentLocation = window.location.pathname;
        if (
            currentLocation == "/min-konto/invoices/" ||
            currentLocation == "/min-konto/ecology/"
        ) {
            $(".invoices .th-invoice-date").trigger("click");
            $(".invoices .th-invoice-date").trigger("click");
        }
    });

    $(document).on("ready ajaxComplete", function() {
        var currentLocation = window.location.pathname;
        if (currentLocation == "/min-konto/ecology/") {
            $(".invoices .th-invoice-date").trigger("click");
            $(".invoices .th-invoice-date").trigger("click");
        }
    });

    $(document).ready(function() {
        let url = window.location.href;
        $("#account-nav-select option").each(function() {
            //console.log($(this).val())
            if ($(this).val() == url) {
                //$(this).css("background-color", "yellow")
            }
        });
    });

    /*===============================================
=          Rephurchase - Tidligere køb           =
===============================================*/
    if ($(".repurchase").length > 0) {
        $(".item").each(function() {
            let qty = $(this).children(".prev-quantity").data("qty");
            $(this).find(".input-text.qty").val(qty);
        });
    }

    /*===============================================
=          Cookie GDPR - Disallow/Decline           =
===============================================*/
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    $(document).ready(function() {
        $(document).delegate(".bbh-gdpr-disallow", "click", function() {
            var disallow = JSON.stringify({
                strict: "0",
                thirdparty: "0",
                advanced: "0",
            });
            setCookie("bbh_gdpr_popup", encodeURIComponent(disallow));
            $("#bbh_gdpr_cookie_info_bar").addClass("hide");
        });
    });

    /*===============================================
=          Make badge for discounts           =
===============================================*/

    if ($(".quantity-discount-price").length > 0) {
        if ($("body").hasClass("archive")) {
            $(".quantity-discount-price").each(function() {
                $(this).parents("li.product").addClass("quantity-discount-product");
                $(this)
                    .parents("li.product")
                    .prepend(
                        '<div class="badge quantity-discount">Rabat ved køb af flere flasker</div>'
                    );
            });
        }
    }
    $(document).on("ajaxComplete", function() {
        if ($(".quantity-discount-price").length > 0) {
            $(".quantity-discount-price").each(function() {
                $(this).parents("li.product").addClass("quantity-discount-product");
                $(this)
                    .parents("li.product")
                    .prepend(
                        '<div class="badge quantity-discount">Rabat ved køb af flere flasker</div>'
                    );
            });
        }
    });

    // if ($('.quantity-gift-price').length > 0) {
    //     if ($('body').hasClass('archive')) {
    //         $('.quantity-gift-price').each(function(){
    //             var gift_amount = $(this).data('gift-amount')
    //             $(this).parents('li.product').addClass('quantity-gift-product')
    //             $(this).parents('li.product').prepend('<div class="badge quantity-gift">Gave ved køb af min. ' + gift_amount + ' flasker</div>')
    //         })
    //     }else{
    //
    //         var gift_amount = $('.quantity-gift-price').data('gift-amount')
    //         var gift_name = $('.quantity-gift-price').data('gift-name')
    //         var gift_link = $('.quantity-gift-price').data('gift-link')
    //         $('.quantity-gift-price').parents('.single-product-sidebar').append('<div class="single-badge quantity-gift"><span class="icon-gave"></span><h3>Få en gave ved køb af minimum ' + gift_amount + ' flasker</h3><p>Gave:<br><span><a target="_blank" rel="noopener noreferrer" href="' + gift_link + '">' + gift_name + '</span></a></p></div>')
    //     }
    // }
    // $(document).on('ajaxComplete', function () {
    //     if ($('.quantity-gift-price').length > 0) {
    //         $('.quantity-gift-price').each(function(){
    //             var gift_amount = $(this).data('gift-amount')
    //             $(this).parents('li.product').addClass('quantity-gift-product')
    //             $(this).parents('li.product').prepend('<div class="badge quantity-gift">Gave ved køb af min. ' + gift_amount + ' flasker</div>')
    //         })
    //     }
    // });

    /*========================================
=           Cases ajax                  =
========================================*/
    function runAjax() {
        var filter = $(".bbh-filter-down-form"); // Get form
        var wrapper = $("#response"); // Get markup grid container
        $.ajax({
            url: filter.attr("action"), // Get form action
            data: filter.serialize(), // Get form data
            type: filter.attr("method"), // Get form method
            beforeSend: function() {
                wrapper.animate({
                        opacity: 0,
                    },
                    100
                ); // Fade markup out
            },
            success: function(data) {
                if (data.bbh_posts) {
                    wrapper.html(data.bbh_posts); // insert data from output buffer in archives-cases.php
                }
            },
            complete: function() {
                wrapper.delay(200).animate({
                        opacity: 1,
                    },
                    200
                ); // Fade markup in
            },
            error: function(error) {
                wrapper.css("opacity", 1); // Fade markup in
                console.log(error); // Console log error
            },
        });
    }

    //Run ajax on form change
    $(".bbh-filter-down-form").change(function() {
        runAjax();
        return false;
    });
    //Reset when 'all' is pressed
    $("#resetform").click(function() {
        $("#filter input").each(function() {
            $(this).attr("checked", false);
        });
        $(this).attr("checked", true);
        runAjax();
    });
    //Label click = input click
    $("#filter label, #filter-tags label").click(function() {
        $(this).siblings("input").click();
        $(this).parent().siblings().children("input").attr("checked", false);
    });

    /*===============================================
      =          call to action popups
      made by Mikkel Christiansen         =
      ===============================================*/
    jQuery(document).ready(function($) {
        $("#call-content").hide();
        $("#customer-content").show();

        $("#customer-popup-btn").click(function() {
            $("#customer-popup-btn").addClass("active");
            $("#call-popup-btn").removeClass("active");
            $("#call-content").hide();
            $("#customer-content").show();
            // show hide video and images
            $("#image-or-video-costumer").show();
            $("#image-or-video-call").hide();
        });

        $("#call-popup-btn").click(function() {
            $("#customer-popup-btn").removeClass("active");
            $("#call-popup-btn").addClass("active");
            $("#customer-content").hide();
            $("#call-content").show();
            // show hide video and images
            $("#image-or-video-costumer").hide();
            $("#image-or-video-call").show();
        });

        $("#customer-popup-btn").click(); // Trigger click event on page load

        // add link #hoer-mere
        $('a[href="#hoer-mere"]').click(function(event) {
            event.preventDefault();
            $("body").addClass("no-scroll");
            $("#call-to-action-popup").show();
            $("#call-popup-btn").trigger("click");
            history.pushState("", document.title, window.location.pathname);
        });

        // add link #bliv-kunde
        $('a[href="#bliv-kunde"]').click(function(event) {
            event.preventDefault();
            $("body").addClass("no-scroll");
            $("#call-to-action-popup").show();
            $("#customer-popup-btn").trigger("click");
            history.pushState("", document.title, window.location.pathname);
        });

        $("#close-btn").on("click", function() {
            $("body").removeClass("no-scroll");
            $("#call-to-action-popup").hide();
            history.pushState("", document.title, window.location.pathname);
        });
    });
    // Close popup when clicking outside of it
    $("#call-to-action-popup").on("click", function(event) {
        if (event.target == this) {
            $("#call-to-action-popup").hide();
            $("body").removeClass("no-scroll");
            history.pushState("", document.title, window.location.pathname);
        }
    });

    $(document).ready(function() {
        var container = $(".popups-wrapper");
        if (container.get(0).scrollHeight > container.innerHeight()) {
            container.addClass("overflow");
        }
    });

    /*===============================================
    =          video in article           =
    ===============================================*/
    jQuery(document).ready(function($) {
        $("#video-hide-article").hide();

        $("#video-btn-article").click(function() {
            $("#video-hide-article").show();
            $("body").addClass("no-scroll");
            $('#popup-video').trigger('play');
        });

        // Close popup when clicking outside of it
        $("#video-hide-article").on("click", function(event) {
            if (event.target == this) {
                $("#video-hide-article").hide();
                $("body").removeClass("no-scroll");
                $('#popup-video').trigger('pause');
            }
        });
        $("#close-btn").on("click", function() {
            $("body").removeClass("no-scroll");
            $("#video-hide-article").hide();
            $('#popup-video').trigger('pause');
        });
    });


})(jQuery);

lazySizes.init(); //fallback if img is above-the-fold