(function($) {

	// Change quantity for ajax add to cart button
	$(document.body).on('click input', 'input.qty', function() {
		$(this).parent().parent().find('a.ajax_add_to_cart').attr('data-quantity', $(this).val());
		// (optional) Removing other previous "view cart" buttons
		$(".added_to_cart").remove();
	});


	let customVideoPlayer = function(){
		let self = this;
		self.wrap = document.querySelector('.video-container');
		self.init = function(){
			if(!self.wrap){
				return false;
			}
			// assign props
			self.play = self.wrap.querySelector('.play-btn');
			self.video = self.wrap.querySelector('video');
			self.mute = self.wrap.querySelector('.mute-btn');

			self.play.addEventListener('click', self.onPlayClick);
			self.video.addEventListener('ended', self.onVideoEnd);
			self.video.addEventListener('click', self.onVideoClick);
			self.mute.addEventListener('click', self.toggleMute);

			self.play.addEventListener('mouseenter', self.addHoverClass);
			self.play.addEventListener('mouseleave', self.removeHoverClass);


			self.play.addEventListener('touchstart', self.onVideoTouch);
		}
		self.isVideoPlaying = function(){
			return !!(self.video.currentTime > 0 && !self.video.paused && !self.video.ended && self.video.readyState > 2);
		}
		self.onVideoClick = function(){
			if(self.isVideoPlaying()){
				self.wrap.classList.remove('playing');
				self.video.pause();
			}

		}
		self.onVideoRun = function(){

		}
		self.onVideoEnd = function(){
			setTimeout(function(){
				self.wrap.classList.remove('playing');
				self.wrap.classList.remove('show-video');
				setTimeout(function(){
					self.video.currentTime = 0;
				}, 200)
			}, 500)
		}
		self.onPlayClick = function(){
			if(self.isVideoPlaying()){
				self.wrap.classList.remove('playing');
				self.video.pause();
			} else{
				self.wrap.classList.add('playing');
				self.wrap.classList.add('show-video')
				self.video.play();
			}

		}
		self.onVideoTouch = function(){
			if( window.innerWidth  <= 768 ){
				self.addHoverClass();
				setTimeout(function(){
					self.removeHoverClass();
				}, 700);
			}
		}
		self.addHoverClass = function(){
			self.wrap.classList.add('is-hover');
		}
		self.removeHoverClass = function(){
			self.wrap.classList.remove('is-hover')
		}
		self.toggleMute = function(){
			if(self.video.muted){
				self.wrap.classList.remove('muted');
			} else{
				self.wrap.classList.add('muted');
			}
			self.video.muted = !self.video.muted;

		}
		self.init();
	}
	document.addEventListener('DOMContentLoaded', customVideoPlayer);


	/*=============================================
	          = Sticky header =
	===============================================*/
	let customStickyHeader = function(){
		let self = this;

		self.header = document.getElementById('fixed-header');
		self.headerHeight = 0;
		self.isStuck = false;
		self.construct = function(){

			// add placeholder element to fill space
			self.addPlaceholderHeader();

			// do sticky check on scroll
			window.addEventListener('scroll', self.onWindowScroll);

			// trigger resize checks and add resizing events
			self.onResize();
			window.addEventListener('resize', self.onResize);
			window.addEventListener('load', self.onResize);

			// trigger inital scroll to see if page is scrolled on page load
			self.onWindowScroll();
		}

		self.addPlaceholderHeader = function(){
			let newNode = document.createElement('div');
			newNode.classList.add('header-placeholder');
			self.header.parentNode.insertBefore(newNode, self.header);
			self.placeholder = newNode;
		}

		self.onResize = function(){
			self.headerHeight = self.header.offsetHeight;
			self.placeholder.style.height = self.header.offsetHeight+'px';
		}


		self.onWindowScroll = function(){
			window.scrollY;
			let breakpoint = 350
			let placeholderPos = self.placeholder.offsetTop;
			if (window.scrollY >= breakpoint && self.isStuck !== true) {
				self.isStuck = true;
				document.documentElement.classList.add('stuck-header');
				self.placeholder.classList.add('show');
				self.header.classList.add('is-stuck');
				self.header.classList.add('animated');
				self.header.classList.add('fadeIn');
			} else if(( window.scrollY < placeholderPos || window.scrollY == 0) && self.isStuck == true){
				self.isStuck = false;
				document.documentElement.classList.remove('stuck-header');
				self.placeholder.classList.remove('show');
				self.header.classList.remove('is-stuck');
				self.header.classList.remove('animated');
				self.header.classList.remove('fadeIn');
			}
		}
		// initalize function
		self.construct();
	}
	document.addEventListener('DOMContentLoaded', function(){
		new customStickyHeader();
	})


	/*=============================================
			  = Enter-view animations =
	===============================================*/
	let bbhEnterView = function(){
		// make sure enterView is available
		if(!window.enterView || typeof enterView !== 'function'){
			return;
		}
		$('[data-animation]').each(function(){
			let offset = this.getAttribute('data-animation-offset') || 0.1;
			let once = this.getAttribute('data-animation-once') || true;
			let delay = this.getAttribute('data-animation-delay') || 0;
			enterView({
				selector: [this],
				enter: function enter(el) {
					setTimeout(function(){
						let animationClass = el.getAttribute("data-animation");
						el.classList.add('animated');
						el.classList.add(animationClass); //change this class to change animation
					}, delay);

				},
				exit: function exit(el) {
					let animationClass = el.getAttribute("data-animation");
					el.classList.remove('animated');
					el.classList.remove(animationClass); //change this class to change animation
				},
				offset: parseFloat(offset),
				once: once
			});
		})
	}
	$(document).ready(function(){
		new bbhEnterView();
	})




	/*=============================================
	          = WooCommerce quantity buttons =
	===============================================*/
	$(document).on('ready ajaxComplete', function(){
		function plusClick(evt){
			evt.preventDefault();
			let input = $(this).siblings('.qty');
			let max = input.attr('max') || Number.MAX_SAFE_INTEGER;
			let newVal = parseInt(input.val()) + 1;
			if(newVal > max){
				return;
			}
			input.val( newVal );
			input.trigger('change');
		}
		function minusClick(evt){
			evt.preventDefault();
			let input = $(this).siblings('.qty');
			let min = input.attr('min') || 0;
			let newVal = parseInt(input.val()) - 1;
			if(newVal < min){
				return;
			}
			input.val( newVal );
			input.trigger('change');
		}
		// append buttons to quantity inputs, that doesn't already have them
		$('input.qty:not(.has-buttons)').each(function(){
			let input = $(this);

			input.after('<button class="plus">+</button>');
			input.before('<button class="minus">&minus;</button>')

			// register events
			input.siblings('.plus').on('click', plusClick);
			input.siblings('.minus').on('click', minusClick);

			input.addClass('has-buttons');
			input.parent().addClass('has-buttons');

		})

	})

	/*=============================================
	          = Video modal player =
	===============================================*/
	$( function() {
		registerModal();
	});
	function registerModal(){
		let modal = $('#bbh-modal');
		let overlay = $('#bbh-modal--overlay');
		let iframe = modal.find('.modal-frame');
		let animation = 300;


		function openIframeModal(){
			let src = $(this).attr('data-iframe-src');
			iframe.attr('src', src);
			modal.addClass('open');
			modal.fadeIn(animation);
			modal.isOpen = true;
		}
		function closeModal(){
			modal.fadeOut(animation, function(){
				modal.removeClass('open');
				iframe.attr('src',"");
				modal.isOpen = false;
			})
		}
		function onModalKeyDown(evt){
			if (evt.keyCode == 27 || evt.key === 'Escape') {
				if(modal.isOpen == true){
					closeModal();
				}
			}
		}
		// Register events
		$('[data-iframe-src]').on('click', openIframeModal)
		overlay.on('click', closeModal);
		modal.find('.close').on('click', closeModal);
		$(window).on('keydown', onModalKeyDown);

	}

	/*=============================================
	          = flexible content slider =
	===============================================*/
	$(document).on('ready', initFlexContentSlider);
	function initFlexContentSlider(){
		// Make sure slick is available
		if(typeof $.fn.slick !== 'function'){
			return;
		}
		$('.c2-content-slider .content-slider').each(function(){
			let slider = $(this);
			$(this).slick({
				rows: 0,
				slidesToShow: 1,
				slidesToScroll: 1,
				fade: true,
				dots: true,
				arrows: false,
				adaptiveHeight: true,
				speed: 800
			})
			// Fix slider sizing when lazyloading images
			setTimeout(function(){
				slider.slick('setPosition')
			}, 50);
			// set position after every lazyloaded image.
			$(slider).find('.lazyload').on('lazyloaded', function(){
				slider.slick('setPosition')
			})
		})
	}



	/*=============================================
	          	= Custom Login popup =
	===============================================*/
	let LoginFormPopup = function(){
		const self = this;

		// element references
		self.popup;
		self.popupOverlay;
		self.form;

		// state
		self.isOpen = false;

		self.init = function(){
			let popup = document.getElementById('login-popup');
			if(!popup){
				return false;
			}

			self.popup = popup;
			self.form = popup.querySelector('form');
			self.popupOverlay = document.getElementById('login-popup-overlay');
			// Check if form is open from page load due to submission data error.
			self.checkInitalOpen();
			//self.setFormPlaceholders(); // This was only needed when we used the native wp form.
			self.registerEvents();
		}
		self.registerEvents = function(){
			let close = self.popup.querySelector('.close');
			if(close){
				close.addEventListener('click', self.closePopup);
			}
			self.popupOverlay.addEventListener('click', self.closePopup);

			// register custom events to work with popup
			document.addEventListener('openLogin', self.openPopup);
			document.addEventListener('closeLogin', self.closePopup);

			document.addEventListener('keydown', self.onKeyDown);
		}
		self.removeURLParameter = function(url, parameter) {
		    //prefer to use l.search if you have a location/link object
		    var urlparts = url.split('?');
		    if (urlparts.length >= 2) {

		        var prefix = encodeURIComponent(parameter) + '=';
		        var pars = urlparts[1].split(/[&;]/g);

		        //reverse iteration as may be destructive
		        for (var i = pars.length; i-- > 0;) {
		            //idiom for string.startsWith
		            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
		                pars.splice(i, 1);
		            }
		        }

		        return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
		    }
		    return url;
		}
		self.checkInitalOpen = function(){
			const query = window.location.search;
			if( query.indexOf('login=failed') != -1 ){
				self.openPopup();
				if('history' in window){
					window.history.replaceState({}, document.title, self.removeURLParameter(window.location.href, 'login'));
				}
			}
		};
		// check for escape keypress when open
		self.onKeyDown = function(evt){
			if (evt.keyCode == 27 || evt.key === 'Escape') {
				if(self.isOpen == true){
					self.closePopup();
				}
			}
		}
		self.openPopup = function(){
			self.isOpen = true;
			self.popup.classList.add('open');
			self.popupOverlay.classList.add('open');
		}
		self.closePopup = function(){
			self.isOpen = false;
			self.popup.classList.remove('open');
			self.popupOverlay.classList.remove('open');
		}
		// used for native wp login form which has no placeholders
		self.setFormPlaceholders = function(){
			let inputs = self.form.querySelectorAll('.input');
			for (var i = 0; i < inputs.length; i++) {
				let label = inputs[i].parentNode.querySelector('label');
				if(label){
					inputs[i].setAttribute('placeholder', label.textContent);
				}
			}

		}
		self.init();
	}

	new LoginFormPopup();

	// register login button click
	document.addEventListener('DOMContentLoaded', function(){
		let loginBtn = document.querySelectorAll('.login-btn');
		for (let i = 0; i < loginBtn.length; i++) {
			loginBtn[i].addEventListener('click', function(e){
				e.preventDefault();
				document.dispatchEvent( new Event('openLogin') )
			})
		}
	})


	/*=============================================
	          = Typewriter effect banner =
	===============================================*/
	var TxtRotate = function(el, toRotate, period) {
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
		ref.parentNode.style.minHeight = this.height + 'px';
		window.addEventListener('resize', function(){
			ref.height = 0;
			ref.parentNode.style.minHeight = ref.height + 'px';
		})
	};

	TxtRotate.prototype.tick = function() {
		var i = this.loopNum % this.toRotate.length;
		var fullTxt = this.toRotate[i];

		if (this.isDeleting) {
			this.txt = fullTxt.substring(0, this.txt.length - 1);
		} else {
			this.txt = fullTxt.substring(0, this.txt.length + 1);
		}
		this.el.classList.add('writing');
		this.el.innerHTML = '<span class="wrap">'+this.txt+'</span>';

		var that = this;
		var delta = Math.random() * (150 - 80) + 80;

		//var delta = 300 - Math.random() * 100;

		if (this.isDeleting) { delta /= 2; }

		if (!this.isDeleting && this.txt === fullTxt) {
			delta = this.period;
			this.isDeleting = true;
			this.el.classList.remove('writing');
			this.height = this.height < this.parentNode.clientHeight ? this.parentNode.clientHeight : this.height;
			this.parentNode.style.minHeight = this.height + 'px';

		} else if (this.isDeleting && this.txt === '') {
			this.isDeleting = false;
			this.el.classList.remove('writing');
			this.loopNum++;
			delta = 500;
		}
		setTimeout(function() {
			that.tick();
		}, delta);
	};

	window.onload = function() {
		var elements = document.getElementsByClassName('txt-rotate');
		for (var i=0; i<elements.length; i++) {
			var toRotate = elements[i].getAttribute('data-rotate');
			var period = elements[i].getAttribute('data-period');
			if (toRotate) {
				new TxtRotate(elements[i], JSON.parse(toRotate), period);
			}
		}

	};


	/*=============================================
	          = Single product gallery =
	===============================================*/
	$(document).on('ready', initSingleProductGallerySlider);
	function initSingleProductGallerySlider(){
		if(typeof $.fn.slick !== 'function'){
			return;
		}
		$('.product-gallery .gallery-slider').each(function(){
			let dotsNav = $(this).find('.dots-nav')
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
			})
			setTimeout(function(){
				slider.slick('setPosition')
			}, 50);
			$(slider).find('.lazyload').on('lazyloaded', function(){
				slider.slick('setPosition')
			})

		})

		let variationForm = document.querySelector('form.variations_form');
		if(variationForm){
			jQuery(variationForm).on('found_variation', function(evt, data){
				setTimeout(function(){
					if('image_id' in data){
						let index = $('.product-gallery .gallery-slider .slick-slide[data-image-id="'+data.image_id+'"]').index();
						$('.product-gallery .gallery-slider').slick('slickGoTo', index );
					}
				},0)
			})
			jQuery(variationForm).on('reset_image', function(){
				setTimeout(function(){
					$('.product-gallery .gallery-slider').slick('slickGoTo', 0);
				}, 0)
			})
		}
	}


	/*=============================================
	          = single post gallery =
	===============================================*/
	$(document).on('ready', initSinglePostGallerySlider);
	function initSinglePostGallerySlider(){
		if(typeof $.fn.slick !== 'function'){
			return;
		}
		$('.article-section .gallery').each(function(){
			let slider = $(this);
			let count = slider.data('cols') || 1;
			let rows = slider.data('rows') || 0;
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
				responsive: [
					{
						breakpoint: 768,
						settings: {
							rows: 1,
							slidesToShow: 1,
							slidesToScroll: 1
						}
					},

				]
			})
			setTimeout(function(){
				slider.slick('setPosition')
			}, 50);
			$(slider).find('.lazyload').on('lazyloaded', function(){
				slider.slick('setPosition')
			})

		})
	}

	/* CONTACT - COLLAPSIBLE - EMPLOYEES*/

	    $('.contact-employees-collapsible .info .headline').click(function(){
	       //Add or remove active class from clicked element
	       if ($(this).hasClass('active')) {
	          $(this).removeClass('active');
	       } else {
	          $(this).addClass('active');
	       }
	       //Slide content that belong to clicked element
	       $(this).next('.collapsible-content').slideToggle();
	       //SlideUp all other content
	       $(this).parent('.info').siblings('.info').children('.collapsible-content').slideUp();
	       //Remove active class from all other content boxes
	       $(this).parent('.info').siblings('.info').children('.headline').removeClass('active');
	    })


})( jQuery )
lazySizes.init(); //fallback if img is above-the-fold
