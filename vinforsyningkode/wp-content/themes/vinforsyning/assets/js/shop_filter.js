function serialize(form) {
	if (!form || form.nodeName !== "FORM") {
		return;
	}
	var i, j, q = [];
	for (i = form.elements.length - 1; i >= 0; i = i - 1) {
		if (form.elements[i].name === "" || form.elements[i].isDirty == false) {
			continue;
		}
		switch (form.elements[i].nodeName) {
		case 'INPUT':
			switch (form.elements[i].type) {
			case 'text':
			case 'hidden':
			case 'password':
			case 'button':
			case 'reset':
			case 'submit':
				q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
				break;
			case 'checkbox':
			case 'radio':
				if (form.elements[i].checked) {
					q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
					//console.log(form.elements[i].value);
				}
				break;
			case 'file':
				break;
			}
			break;
		case 'TEXTAREA':
			q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
			break;
		case 'SELECT':
			switch (form.elements[i].type) {
			case 'select-one':
				q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
				break;
			case 'select-multiple':
				for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
					if (form.elements[i].options[j].selected) {
						q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
					}
				}
				break;
			}
			break;
		case 'BUTTON':
			switch (form.elements[i].type) {
			case 'reset':
			case 'submit':
			case 'button':
				q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
				break;
			}
			break;
		}
	}
	return q.join("&");
}

let shopFilter = function(){

	const self = this;

	// element reference
	self.filter;
	self.form;
	self.fields;
	self.moreBtn;
	self.countEl;

	// state
	self.route = filter_data.route;
	self.token = filter_data.token;
	self.rest_token = filter_data.rest_token
	self.isLoading = false;

	var controller = null;

	self.init = function(){
		self.filter = document.getElementById('shop-filter');
		self.form =  document.getElementById('shop-filter-form');
		self.fields = self.filter.querySelectorAll('.field');
		self.moreBtn = document.querySelector('.more-btn');
		self.countEl = document.querySelector('#post-results .count');
		if(!self.filter){
			return;
		}
		self.setupFilterBoxes();
		self.form.addEventListener('submit', self.onFormSubmit);
		/* disabled history state untill i find out how to fill out form values efficiently
		window.addEventListener('popstate', self.onpopstate);
		self.pushState(false);
		*/
		$('#shop-filter-form').change(function(){
			self.form.querySelector('input[name="orderby"]').value = $('select[name="orderby"]').val();
			self.onFormSubmit();
			//remove_hide_from_input();

		})
		self.checkInitialSearch();


		document.querySelector('.woocommerce-ordering .orderby').addEventListener('change', self.onOrderbyChange);

		let orderChangers = document.querySelectorAll('#order-changer .order-changer-item')
		for (var i = 0; i < orderChangers.length; i++) {
			orderChangers[i].addEventListener('click', self.onOrderChange);
		}

		//self.form.querySelector('.reset').addEventListener('click',self.reset);


		if(self.moreBtn){
			self.moreBtn.addEventListener('click', self.onMoreClick);
		}

		window.addEventListener('getmoreposts', () => {
			self.getPosts(false, true);
		})

		let elements = self.form.elements;
		for (var i = 0; i < elements.length; i++) {
			elements[i].addEventListener('change', self.onFormElementChange);
		}
	}
	self.cleanFormElements = function(){
		for (var i = 0; i < self.form.elements.length; i++) {
			self.form.elements[i].isDirty = false;
		}
	}
	self.onFormElementChange = function(){
		this.isDirty = true;
	}

	self.reset = function(){
		let filterBoxes = self.form.querySelectorAll('.filter-box-wrap')
		for (var i = 0; i < filterBoxes.length; i++) {
			filterBoxes[i].classList.remove('green');
		}
		self.form.reset();
		self.getPosts();

	}
	self.onOrderbyChange = function(evt){
		evt.preventDefault();
		evt.stopImmediatePropagation();
		let val = evt.target.value;
		self.form.querySelector('input[name="orderby"]').value = val;
		self.onFormSubmit();
	}
	self.onOrderChange = function(evt){
		evt.preventDefault();
		evt.stopImmediatePropagation();
		if( this.classList.contains('active') ){
			return false;
		}
		let siblings = this.parentNode.children;
		for (var i = 0; i < siblings.length; i++) {
			siblings[i].classList.remove('active')
		}
		this.classList.add('active');
		let val = this.getAttribute('data-value');
		self.form.querySelector('input[name="order"]').value = val;
		// console.log(val, self.form.querySelector('input[name="order"]').value);
		self.onFormSubmit();
	}
	self.onMoreClick = function(evt){
		evt.preventDefault();
		//let scrolledY = window.pageYOffset;
		let pageInput =  self.form.querySelector('input[name="page"]');
		let page = pageInput.value;
		pageInput.value = parseInt(page) + 1;

		self.getPosts(false, true).then( () => {
			// window.scrollTo(0,scrolledY);
			// setTimeout(function(){
			// 	console.log(document.scrollTop);
			// }, 1500)
		});
	}
	self.checkInitialSearch = function(){
		let field = 'search';
		let url = window.location.href;
		if(url.indexOf('?' + field + '=') != -1) {
			if(url.indexOf('?' + field + '=') + field.length + 2 != url.length){
				self.getPosts();
			}
		} else if(url.indexOf('&' + field + '=') != -1){
			self.getPosts();
		}
		document.body.classList.remove('filter-search');

	}
	self.onpopstate = function(evt){
		if(evt.state ){
			for (var property in evt.state) {
			  	if (evt.state.hasOwnProperty(property)) {
			    	let input = self.form.querySelector('[name="'+property+'"]');
					if(input){
						input.value = evt.state[property];
						input.dispatchEvent(new Event('change'));
					}
			  	}
			}
			self.getPosts();
		}
	}
	self.pushState = function(data = true){
		let serialized = serialize(self.form);

	 	let toArray = serialized.split('&');
		let obj = {};
		for (var i = 0; i < toArray.length; i++) {
			let pair = toArray[i].split("=");
			if(obj[pair[0]] != null){
				if(!Array.isArray(obj[pair[0]])){
					obj[pair[0]] = new Array(obj[pair[0]]);
				}
				obj[pair[0]].push(pair[1]);
				continue;
			}
			obj[pair[0]] = pair[1];
		}
		let locationBase = window.location.href.split('?')[0];
		locationBase = locationBase.replace(/\/$/, "");
		if(serialized == window.location.search.substring(1)){
			return;
		}
		serialized = '?'+serialized;
		if(data == false){
			serialized = '';
		}
		window.history.pushState(obj, "null", locationBase+serialized);
	}
	self.getPosts = function(dataString = false, more = false){
		if(self.isLoading == true){
			//return false;
		}

		self.isLoading = true;
		if(more == false){
			document.querySelector('ul.products').innerHTML = '';
			self.form.querySelector('input[name="page"]').value = 1;
		}
		let data;
		if(dataString !== false){
			data = dataString;
		} else{
			data = serialize(self.form);
		}
		let scrolledY = window.pageYOffset;

		data = data+ '&token='+self.token;
		let response = self.fetch(self.route+'?'+data, more);
		self.addSpinner(more);
		return response.then(data => {
			if(!more){
				document.querySelector('ul.products').innerHTML = data.posts;
				if(self.countEl){
					self.countEl.innerHTML = data.found;
				}
			} else{
				document.querySelector('ul.products').innerHTML += data.posts;

			}
			self.removeSpinner();
			self.isLoading = false;
			if(data.end == true){
				self.moreBtn.classList.add('hidden');
			} else{
				self.moreBtn.classList.remove('hidden');
			}
			var bbh_terms = JSON.parse(data.terms)
			bbh_hide_terms(bbh_terms);


			document.dispatchEvent(new Event('ajaxComplete'))


			window.scrollTo(0, scrolledY)

		}).catch(function(err) {
            console.error(` Err: ${err}`);
        });


	}
	self.onFormSubmit = function(evt){
		evt && evt.preventDefault();
		if (controller != null) {
			controller.abort();
			self.removeSpinner();
			self.isLoading == false;
		}

		controller = new AbortController();
		signal = controller.signal;

		if(self.isLoading == true){
			//return false;
		}

		self.getPosts(false);
		//self.pushState();

	}
	self.addSpinner = function(){
		let scrolledY = window.pageYOffset;
		let outer = document.createElement('div')
		let inner = document.createElement('div');
		let products = document.querySelector('ul.products');
		outer.appendChild(inner);
		outer.classList.add('bbh-loader');
		outer.classList.add('filter-loader');
		inner.classList.add('spinner');
		inner.classList.add('round');
		products.parentNode.insertBefore(outer, products.nextSibling);
		window.scrollTo(0, scrolledY)
		$('.spinner').prepend('<lottie-player src="https://vinforsyning.dk/wp-content/themes/vinforsyning/assets/json/lottie-vinglas.json"  background="transparent"  speed="1"  style="width: 20px; margin:auto;"  loop autoplay></lottie-player>')

		//document.querySelector('ul.products').appendChild(outer);
	}
	self.removeSpinner = function(){
		let spinner = document.querySelector('.filter-loader');
		if(spinner){
			spinner.parentNode.removeChild(spinner);
		}
	}
	self.fetch = function(route, more){
		let obj = {
			method: 'GET',
			headers: {
				'X-WP-Nonce': self.rest_token, // This is required to check if user is logged in
				'cache-control': 'no-cache'
			},
		}
		if (!more) { obj[2] = {signal: signal} }

		return fetch(route, obj)
			.then((response) => {
				return response.json();
			})
	}
	self.setupFilterBoxes = function(){
		let boxes = self.filter.querySelectorAll('.filter-box-wrap');
		for (var i = 0; i < boxes.length; i++) {
			new shopFilterBox(boxes[i])
		}
	}

	self.init();
}

document.addEventListener('DOMContentLoaded', function(){
	new shopFilter();
})


let shopFilterBox = function(wrapElement){
	const self = this;

	// element reference
	self.wrap;
	self.trigger;

	// state
	self.isOpen = false;

	self.init = function(){
		self.wrap = wrapElement;
		self.trigger = wrapElement.querySelector('.trigger');

		if(self.wrap.classList.contains('search-box')){
			self.initListSearch();
		}

		self.trigger.addEventListener('click', self.toggleBox);

		window.addEventListener('click', self.onOutsideClick);

	}

	self.toggleBox = function(){
		let slider = self.wrap.querySelector('.slider-el');

		if(self.isOpen){
			self.wrap.classList.remove('open');
			self.isOpen = false;
			if(self.wrap.querySelector('input:checked')){
				self.wrap.classList.add('green');
			}else if(slider){
				//Price slider
				let startMin = parseInt(slider.getAttribute('data-from-start'));
				let startMax = parseInt(slider.getAttribute('data-to-start'));
				let Min = self.wrap.querySelector('input[name=price-from-val]');
				let Max = self.wrap.querySelector('input[name=price-to-val]');
				if(Min && Max){
					if(Min.value && Min.value > 0 || Max.value && Max.value < startMax){
						self.wrap.classList.add('green');
					}else{
						self.wrap.classList.remove('green');
					}
				}
				//Year slider
				let yearMin = self.wrap.querySelector('input#year-slider-from');
				let yearMax = self.wrap.querySelector('input#year-slider-to');
				let startYearMin = document.querySelector('input#bbh-year-from');
				let startYearMax = document.querySelector('input#bbh-year-to');
				if(yearMin && yearMax){
					if(yearMin.value && yearMin.value  > startYearMin.value ||  yearMax.value && yearMax.value < startYearMax.value){
						self.wrap.classList.add('green');
					}else{
						self.wrap.classList.remove('green');
					}
				}
			}
			else{
				self.wrap.classList.remove('green');
			}
		} else {
			self.wrap.classList.add('open');
			self.isOpen = true;

		}
	}


	self.onTriggerClick = function(){

	}

	self.onOutsideClick = function(evt){
		if(!self.isOpen){
			return;
		}
		if(self.wrap.contains(evt.target) || self.wrap.isSameNode(evt.target)){
			// clicked inside
		} else{
			// clicked outside

			self.toggleBox();
		}
	}

	self.onListSearchInput = function(e){
		let s = this.value;
		let resultList = self.wrap.querySelector('.input-list');
		if(s == '' || !s){
			for (let i = 0; i < resultList.children.length; i++) {
				resultList.children[i].classList.remove('search-hidden');
			}

		}

		for (let i = 0; i < resultList.children.length; i++) {
			let val = resultList.children[i].querySelector('.input-title').innerText;
			let re = new RegExp(s, 'gmi');
			let checked = resultList.children[i].querySelector('input:checked') || false;
			if(val.match(re) || checked){
				resultList.children[i].classList.remove('search-hidden');
			} else{
				resultList.children[i].classList.add('search-hidden');
			}
		}
	}
	self.initListSearch = function(){

		let input = self.wrap.querySelector('.filter-list-search-input');
		input.addEventListener('input', self.onListSearchInput);

	}

	self.init();

}


/*=============================================
          = Initiate sliders =
===============================================*/

let formatNumber = function(number, separator){
	return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator);
}
let initUIsliders = function(){
	if(typeof noUiSlider == undefined){
		return false;
	}
	const form =  document.getElementById('shop-filter-form');

	/* general sliders */
	let sliders = document.querySelectorAll('.filter-range-slider');
	for (var i = 0; i < sliders.length; i++) {
		let wrapper = sliders[i];
		let from = wrapper.querySelector('.slider-from');
		let to = wrapper.querySelector('.slider-to');
		let fromInput = wrapper.querySelector('.slider-from-val');
		let toInput = wrapper.querySelector('.slider-to-val');
		let slider = wrapper.querySelector('.slider-el');
		if(slider && from && to){
			let startMin = parseInt(slider.getAttribute('data-from-start'));
			let startMax = parseInt(slider.getAttribute('data-to-start'))
			let step = parseInt(slider.getAttribute('data-step'));

			let settings = {
				start: [startMin, startMax],
			    connect: true,
			    range: {
			        'min': startMin,
			        'max': startMax
			    },
				margin: 0,
				behaviour: 'drag',
				step: step
			}
			noUiSlider.create(slider, settings);

			slider.noUiSlider.on('update', function(values){
				let min = parseInt(values[0]);
				let max = parseInt(values[1]);
				to.value=max;
				from.value=min;

				if(min !== startMin || max !== startMax){
					fromInput.value = min;
					toInput.value = max;
					fromInput.isDirty = true;
					toInput.isDirty = true;
					$('#UI_output > .year-from > span').html("<span>" + fromInput.value + " - </span>");
					$('#UI_output > .year-to > span').html("<span>" + toInput.value + " / </span>");
					$('.wine_year').addClass("red-active");
				} else {
					fromInput.value = null;
					toInput.value = null;
					fromInput.isDirty = false;
					toInput.isDirty = false;
					$('.wine_year').removeClass("red-active");
				}
				if ($('.bbh-output-tags .bubble.wine_year').length == 0) {
					if ($('.filter-box-wrap.wine_year.red-active').length == 1) {
						$('.bbh-output-tags').append('<div class="bubble noselect wine_year " ><span class="between"><span class="year-from">' + min + '</span><span class="year-to"> - ' + max +'</span></span><span class="close">✖</span></div>')
					}
				}else if($('.bbh-output-tags .bubble.wine_year').length == 1){
					$('.bbh-output-tags .bubble.wine_year .between .year-from').text(min)
					$('.bbh-output-tags .bubble.wine_year .between .year-to').text(' - ' + max)
				}
				if(min == max){
					$('#UI_output .year-from span>span').remove();
					$('.bbh-output-tags .bubble.wine_year .between .year-to').addClass('hide')
				}else{
					$('.bbh-output-tags .bubble.wine_year .between .year-to').removeClass('hide')
				}


			})

			$(document).delegate('.bbh-output-tags .bubble.wine_year .close', 'click', function() {
				$('.filter-box-wrap.wine_year.red-active').removeClass('red-active')
				slider.noUiSlider.reset()
				$(this).parent('.bubble').remove()
				$('.submit-field button').trigger('click');
			});
			slider.noUiSlider.on('change', function(values){
				if ($('.filter-box-wrap.wine_year.red-active').length == 0) {
					$('.bbh-output-tags .bubble.wine_year').remove();
				}
			})

			from.addEventListener('change', function () {
			    slider.noUiSlider.set([parseInt(this.value), null]);
				// console.log((this.value));
			});
			to.addEventListener('change', function () {
			    slider.noUiSlider.set([null, parseInt(this.value)]);
				// console.log((this.value));
			});

			// handle form reset
			form.addEventListener('reset', function(){
				if(slider){
					slider.noUiSlider.reset();
				}
			})
		}




	}

	/*----------- Price slider -----------*/
	let priceSlider = document.getElementById('price-slider-el');
	let priceFrom = document.getElementById('price-slider-from').querySelector('bdi');
	let priceTo = document.getElementById('price-slider-to').querySelector('bdi');
	let priceFromInput = document.getElementById('price-slider-from-val');
	let priceToInput = document.getElementById('price-slider-to-val');
	if(priceSlider && priceFrom && priceTo && priceFromInput && priceToInput){
		let startMin = parseInt(priceSlider.getAttribute('data-from-start'));
		let startMax = parseInt(priceSlider.getAttribute('data-to-start'))
		let settings = {
			start: [startMin, startMax],
		    connect: true,
			behaviour: 'drag',
		    range: {
		        'min': startMin,
		        'max': startMax
		    },
			margin: 10,
			step: 10
		}

		noUiSlider.create(priceSlider, settings);


		priceSlider.noUiSlider.on('update', function(values){
			let min = parseInt(values[0]);
			let max = parseInt(values[1]);
			priceFrom.firstChild.nodeValue = formatNumber(min, '.') + ' ';
			priceTo.firstChild.nodeValue = formatNumber(max, '.') + ' ';
			if (max !== startMax) {
				$('#price-slider-to .plus').css('display','none')
			}else{
				$('#price-slider-to .plus').css('display','inline-block')
			}

				if ($('.bbh-output-tags .bubble.price').length == 0) {
					if ($('.filter-box-wrap.price.red-active').length == 1) {
						$('.bbh-output-tags').append('<div class="bubble noselect price" ><span class="between">' + min + " - " + max +'</span>&nbsp;DKK<span class="close">✖</span></div>')
					}
				}else if($('.bbh-output-tags .bubble.price').length == 1){
					$('.bbh-output-tags .bubble.price .between').text(min + " - " + max)
				}
				if (max == 500) {
					$('.bbh-output-tags .bubble.price .between').addClass('plus');
				}else{
					$('.bbh-output-tags .bubble.price .between').removeClass('plus');
				}

			if(min !== startMin || max !== startMax){
				priceFromInput.value = min;
				priceToInput.value = max;
				priceFromInput.isDirty = true;
				priceToInput.isDirty = true;
				$('#UI_output > .price-from > span').html("<span>" + priceFromInput.value + " - </span>");
				$('#UI_output > .price-to > span').html("<span>" + priceToInput.value + " / </span>");
				$('.price').addClass("red-active");
			} else {
				priceFromInput.value = null;
				priceToInput.value = null;
				priceFromInput.isDirty = false;
				priceToInput.isDirty = false;
				$('.price').removeClass("red-active");
				$('#UI_output .price-from span>span, #UI_output .price-to span>span').remove();
			}

		})
		priceFromInput.addEventListener('change', function () {
		    priceSlider.noUiSlider.set([parseInt(this.value), null]);
		});
		priceToInput.addEventListener('change', function () {
		    priceSlider.noUiSlider.set([null, parseInt(this.value)]);
		});

		$(document).delegate('.bbh-output-tags .bubble.price .close', 'click', function() {
			$('.filter-box-wrap.price.red-active').removeClass('red-active')
			priceSlider.noUiSlider.reset()
			$(this).parent('.bubble').remove()
			$('.submit-field button').trigger('click');
		});
		priceSlider.noUiSlider.on('change', function(values){
			if ($('.filter-box-wrap.price.red-active').length == 0) {
				$('.bbh-output-tags .bubble.price').remove();
			}
		})



		// handle form reset
		form.addEventListener('reset', function(){
			if(priceSlider){
				priceSlider.noUiSlider.reset();
			}
		})
	}



}

/* If page is shop - and input is checked - trigger Search */
$( document ).ready(function() {
    var currentLocation = window.location.pathname;
    if (currentLocation == "/shop/") {
        $('#shop-filter-box .filter-fields input:checked').each(function(){
            if ($(this).length > 0 ) {
                $('.submit-field button').trigger('click');

            }
        })
    }
})


////////////////////////select only one checkbox/////////////////////////////////////////
// $(document).ready(function(){
//     $(".tax-pa_pro_cat_att input[type='checkbox']").click(function() {
//         $(".tax-pa_pro_cat_att input[type='checkbox']").not(this).prop('checked', false);
//     });
// });
// $(document).ready(function(){
//     $(".tax-pa_country_att input[type='checkbox']").click(function() {
//         $(".tax-pa_country_att input[type='checkbox']").not(this).prop('checked', false);
//     });
// });
// $(document).ready(function(){
//     $(".tax-pa_product_group input[type='checkbox']").click(function() {
//         $(".tax-pa_product_group input[type='checkbox']").not(this).prop('checked', false);
//     });
// });
// $(document).ready(function(){
//     $(".tax-pa_grape_type input[type='checkbox']").click(function() {
//         $(".tax-pa_grape_type input[type='checkbox']").not(this).prop('checked', false);
//     });
// });
// $(document).ready(function(){
//     $(".tax-pa_grape_type input[type='checkbox']").click(function() {
//         $(".tax-pa_grape_type input[type='checkbox']").not(this).prop('checked', false);
//     });
// });
// $(document).ready(function(){
//     $(".tax-pa_size input[type='checkbox']").click(function() {
//         $(".tax-pa_size input[type='checkbox']").not(this).prop('checked', false);
//     });
// });
// $(document).ready(function(){
//     $(".tax-pa_distrikt_att input[type='checkbox']").click(function() {
//         $(".tax-pa_distrikt_att input[type='checkbox']").not(this).prop('checked', false);
//     });
// });
////////////////////////select only one checkbox/////////////////////////////////////////
// Slugify a string
	function slugify(str)
	{
	    str = str.replace(/^\s+|\s+$/g, '');

	    // Make the string lowercase
	    str = str.toLowerCase();

	    // Remove accents, swap ñ for n, etc
	    var from = "ÁÄÂÀÃÅČÇĆĎÉĚËÈÊẼĔȆÍÌÎÏŇÑÓÖÒÔÕØŘŔŠŤÚŮÜÙÛÝŸŽáäâàãåčçćďéěëèêẽĕȇíìîïňñóöòôõøðřŕšťúůüùûýÿžþÞĐđßÆa·/_,:;";
	    var to   = "AAAAAACCCDEEEEEEEEIIIINNOOOOOORRSTUUUUUYYZaaaaaacccdeeeeeeeeiiiinnooooooorrstuuuuuyyzbBDdBAa------";
	    for (var i=0, l=from.length ; i<l ; i++) {
	        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	    }

	    // Remove invalid chars
	    str = str.replace(/[^a-z0-9 -]/g, '')
	    // Collapse whitespace and replace by -
	    .replace(/\s+/g, '-')
	    // Collapse dashes
	    .replace(/-+/g, '-');

	    return str;
	}


////////////////////////Run sort functions/////////////////////////////////////////
// Sander
function bbh_hide_terms(bbh_terms){
	for (const term in bbh_terms) {
	  	$( `.input-list.tax-${term} .input-wrap input`).each(function() {
	  	  var value = $(this).val()
		  var this_term = `${bbh_terms[term]}`;
	  	if ( this_term.includes(value) ) {
	  		$(this).parents('.input-wrap').removeClass('hide');
	  	}else{
			$(this).parents('.filter-box-wrap:not(.red-active) .input-wrap').addClass('hide');
	  	}
	  	});
	}
}
	/*Append on change*/
	$('.filter-box-wrap .input-list .input-wrap input').change(function() {
	var title = $(this).siblings('.input-title').children('.input-title-el').attr('title')
	var slug = slugify(title);
	var classList = $(this).parents('.filter-box-wrap')[0].classList;
	var term = classList[1];
	// if (($(this).parents('.filter-box-wrap').hasClass('red-active'))) {
	// 	$(`.bbh-output-tags .${term}`).remove();
	// }
	  if ($(this).is(':checked')) {
		$('.bbh-output-tags').append('<div class="bubble noselect '+ slug + " " + term +'" ><span class="value">' + title + '</span><span class="close" title="' + title + '" term="' + term + '">✖</span></div>')
	  }else{
		$(`.bbh-output-tags .${slug}`).remove();
	  }
	  if ($('#shop-filter-form .filter-box-wrap .input-list input:checked').length == 0) {
		  $('.filter-box-wrap .input-list .input-wrap').each(function() {
			  $(this).removeClass('hide');
		  })
	  }
	});

	/*Append on load*/
	$( document ).ready(function() {
		$('.filter-box-wrap .input-list .input-wrap input').each(function() {
    	  if ($(this).is(':checked')) {
			var title = $(this).siblings('.input-title').children('.input-title-el').attr('title')
			var slug = slugify(title);
			var classList = $(this).parents('.filter-box-wrap')[0].classList;
			var term = classList[1];
			$(this).parents('.filter-box-wrap').addClass('red-active')
    	$('.bbh-output-tags').append('<div class="bubble noselect '+ slug + " " + term +'" ><span class="value">' + title + '</span><span class="close" title="' + title + '" term="' + term + '">✖</span></div>')
    	  }
    	});
	});

	//CLick add active-red (Green border)
	$(document).ready(function() {
		$(".filter-box-wrap .input-list .input-wrap input[type='checkbox']").click(function() {
			if ($(this).is(':checked')) {
				$(this).parents('.filter-box-wrap').addClass("red-active");
			}else{
				$(this).parents('.filter-box-wrap').removeClass("red-active");
			}
			if ($(this).parents('.filter-box-wrap').find('input:checkbox:checked').length > 0) {
				$(this).parents('.filter-box-wrap').addClass("red-active");
			}else{
				$(this).parents('.filter-box-wrap').removeClass("red-active");
			}
		});
	});

	$(document).delegate('.bbh-output-tags .bubble .close', 'click', function() {

		$(this).parent('.bubble').remove()
		var title = $(this).attr('title')
		var term = $(this).attr('term')
		$('.filter-box-wrap .input-list .input-wrap .input-title-el[title="' + title + '"]').click();
		// $('.filter-box-wrap .input-list .input-wrap .input-title-el[title="' + title + '"]').parents('.filter-box-wrap').removeClass('red-active')
		if ($(`.filter-box-wrap.${term} .input-wrap input:checked`).length == 0) {
			$(`.filter-box-wrap.${term}`).removeClass('red-active')
		}
	});

	/* ON Ready / Load - save orderby to hidden input field*/
	$(document).ready(function() {
		var order_val = $('input[name="order"]').val()
		if (order_val == 'ASC') {
			$('.order-changer-item.desc').removeClass('active')
			$('.order-changer-item.asc').addClass('active')
		}else if(order_val == 'DESC'){
			$('.order-changer-item.asc').removeClass('active')
			$('.order-changer-item.desc').addClass('active')
		}
	});

	$(".woocommerce-ordering .order-changer-item").click(function() {
		$('#shop-filter-form input[name="orderby"]').val($('select[name="orderby"]').val())
	});


//jon
//$( document ).ajaxComplete(function() {

	// /* COUNTRY */
	// var country = bbh_terms.pa_country_att;
	// $( ".input-list.tax-pa_country_att .input-wrap input" ).each(function() {
	//   var value = $(this).val()
	//   if ( country.some(r=> value.indexOf(r) >= 0) ) {
	// 	$(this).parents('.input-wrap').removeClass('hide');
	// }else{
	// 	$(this).parents('.input-wrap').addClass('hide');
	// }
	// });

	// /* COUNTRY */
	// var product_cat = bbh_terms.pa_pro_cat_att;
	// $( ".input-list.tax-pa_pro_cat_att .input-wrap input" ).each(function() {
	//   var value = $(this).val()
	//   if ( product_cat.some(r=> value.indexOf(r) >= 0) ) {
	// 	$(this).parents('.input-wrap').removeClass('hide');
	// }else{
	// 	$(this).parents('.input-wrap').addClass('hide');
	// }
	// });

	// product_cat_show_hidden_value_district();
	// product_cat_show_hidden_value_groupe();
	// product_cat_show_hidden_value_grape();
	// product_cat_show_hidden_value_size();
	//
	// country_show_hidden_value_product_cat();
	// country_show_hidden_value_grape();
	// country_show_hidden_value_district();
	// country_show_hidden_value_groupe();
	// country_show_hidden_value_size();
	//
	// groupe_show_hidden_value_product_cat();
	// groupe_show_hidden_value_grape();
	// groupe_show_hidden_value_district();
	// groupe_show_hidden_value_country();
	// groupe_show_hidden_value_size();
	//
	// grape_show_hidden_value_groupe();
	// grape_show_hidden_value_country();
	// grape_show_hidden_value_size();
	// grape_show_hidden_value_product_cat();
	// grape_show_hidden_value_district();
	//
	// district_hidden_value_groupe();
	// district_hidden_value_country();
	// district_hidden_value_size();
	// district_hidden_value_product_cat();
	// district_hidden_value_grape();
	// product_cat_show_hidden_value_country();


//});
/////////////////////////Run functions////////////////////////////////////////

////////////////////////Show hide functions for filter////////////////////////
////////////////////////Product cat filter first//////////////////////////////
// function product_cat_show_hidden_value_country() {
// 	let input_ids_string = $('.products input#bbh-country-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_country_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_country_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-countery > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-countery > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_country_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_country_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function product_cat_show_hidden_value_district() {
//     let input_ids_string = $('.products input#bbh-district-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")) {
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_distrikt_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_distrikt_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-district > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-district > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_distrikt_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_distrikt_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function product_cat_show_hidden_value_groupe() {
// 	let input_ids_string = $('.products input#bbh-pro-group').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_product_group .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_product_group .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-group > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-group > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_product_group .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_product_group input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function product_cat_show_hidden_value_grape() {
// 	let input_ids_string = $('.products input#bbh-grapes').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_grape_type .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_grape_type .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-grape > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-grape > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_grape_type .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_grape_type input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function product_cat_show_hidden_value_size() {
//     let input_ids_string = $('.products input#bbh-size').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_size .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-size > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-size > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_size input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_size input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// ////////////////////////Product cat filter first//////////////////////////////
//
// ////////////////////////Country filter first//////////////////////////////
// function country_show_hidden_value_product_cat() {
// 	let input_ids_string = $('.products input#bbh-pro-cat-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_country_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_pro_cat_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_pro_cat_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-product-cat > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-product-cat > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_pro_cat_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_pro_cat_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function country_show_hidden_value_grape() {
//     let input_ids_string = $('.products input#bbh-grapes').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_country_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_grape_type .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_grape_type .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-grape > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-grape > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_grape_type .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_grape_type input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function country_show_hidden_value_district() {
//     let input_ids_string = $('.products input#bbh-district-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_country_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_distrikt_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_distrikt_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-district > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-district > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_distrikt_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_distrikt_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function country_show_hidden_value_groupe() {
//     let input_ids_string = $('.products input#bbh-pro-group').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_country_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_product_group .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_product_group .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-group > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-group > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_product_group .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_product_group input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function country_show_hidden_value_size() {
//     let input_ids_string = $('.products input#bbh-size').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_country_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_size .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-size > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-size > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_size input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_size input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
//
//
// ////////////////////////Groupe filter first//////////////////////////////
// function groupe_show_hidden_value_product_cat() {
// 	let input_ids_string = $('.products input#bbh-pro-cat-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_product_group input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_pro_cat_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_pro_cat_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-product-cat > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-product-cat > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_pro_cat_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_pro_cat_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function groupe_show_hidden_value_grape() {
// 	let input_ids_string = $('.products input#bbh-grapes').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_product_group input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_grape_type .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_grape_type .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-grape > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-grape > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_grape_type .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_grape_type input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function groupe_show_hidden_value_country() {
// 	let input_ids_string = $('.products input#bbh-country-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_product_group input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_country_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_country_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-countery > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-countery > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_country_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_country_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function groupe_show_hidden_value_size() {
// 	let input_ids_string = $('.products input#bbh-size').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_product_group input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_size .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-size > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-size > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_size input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_size input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function groupe_show_hidden_value_district() {
// 	let input_ids_string = $('.products input#bbh-district-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_product_group input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_distrikt_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_distrikt_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-district > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-district > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_distrikt_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_distrikt_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
//
//
// ////////////////////////Grape filter first//////////////////////////////
// function grape_show_hidden_value_groupe() {
// 	let input_ids_string = $('.products input#bbh-pro-group').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_grape_type input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_product_group .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_product_group .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-group > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-group > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_product_group .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_product_group input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function grape_show_hidden_value_country() {
// 	let input_ids_string = $('.products input#bbh-country-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_grape_type input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_country_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_country_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-countery > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-countery > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_country_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_country_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function grape_show_hidden_value_size() {
// 	let input_ids_string = $('.products input#bbh-size').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_grape_type input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_size .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-size > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-size > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_size input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_size input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function grape_show_hidden_value_product_cat() {
// 	let input_ids_string = $('.products input#bbh-pro-cat-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_grape_type input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_pro_cat_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_pro_cat_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-product-cat > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-product-cat > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_pro_cat_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_pro_cat_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function grape_show_hidden_value_district() {
// 	let input_ids_string = $('.products input#bbh-district-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_grape_type input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_distrikt_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_distrikt_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-district > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-district > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_distrikt_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_distrikt_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
//
//
// ///////////////////District filter first//////////////////////////////
// function district_hidden_value_groupe() {
// 	let input_ids_string = $('.products input#bbh-pro-group').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_product_group .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_product_group .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-group > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-group > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_product_group .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_product_group input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_product_group input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function district_hidden_value_country() {
// 	let input_ids_string = $('.products input#bbh-country-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_country_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_country_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-countery > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-countery > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_country_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_country_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_country_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function district_hidden_value_size() {
// 	let input_ids_string = $('.products input#bbh-size').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_size .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-size > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-size > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_size .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_size input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_size input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function district_hidden_value_product_cat() {
// 	let input_ids_string = $('.products input#bbh-pro-cat-att').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_pro_cat_att .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_pro_cat_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-product-cat > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-product-cat > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_pro_cat_att .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_pro_cat_att input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_pro_cat_att input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
// function district_hidden_value_grape() {
// 	let input_ids_string = $('.products input#bbh-grapes').val()
//
// 	//If there are no values in the sub, hide alle the options
// 	if (typeof input_ids_string === 'undefined' && $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")) {input_ids_string
// 		console.log("pro groupe is length < 0 = ", input_ids_string);
// 		$('.tax-pa_grape_type .input-wrap').each(function() {
// 			$(this).addClass('hide')
// 		})
// 	}
//     else if (input_ids_string != null &&  $(".tax-pa_distrikt_att input[type='checkbox']").is(":checked")){
//         //Make array for all counterys in selected pro_cat_att
//         var input_ids_array = input_ids_string.replace("[", "").replace("]", "").split(',');
//
//         //Save all countery values to string then obj then array
//         $('.tax-pa_grape_type .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
//             $('#UI_input_ids > .array-string-grape > span').append(values + ' ');
//         })
//
// 		//convert the info to arry that we can use later
//         var all_ids_string = $('#UI_input_ids > .array-string-grape > span').text();
//         var all_ids_obj = all_ids_string.trim().split(' ');
//         var all_ids_array = $.map(all_ids_obj, function(value, index) {
//             return [value];
//         });
//
//         //Compare both array and find overlap, then hide
//         $('.tax-pa_grape_type .input-wrap').each(function() {
//             var input = $(this).find('input');
//             var values = input.val();
// 			var current_checkbox = $(this).find("input[type='checkbox']");
//             if (!all_ids_array.indexOf(input_ids_array[i]) > -1) {
// 				if($(".tax-pa_grape_type input[type='checkbox']").is(":checked")){
//
// 					$(".tax-pa_grape_type input[type='checkbox']").click(function(){
// 						if($(this).is(":checked")){
// 						   $(this).removeClass('hide')
// 						}
// 						else if(!$(this).is(":checked")){
// 							$(this).addClass('hide')
// 						}
// 					});
// 				} else {
// 					if($(this).is(":checked")){
// 					   $(this).removeClass('hide')
// 				   	}
// 					else if (!input_ids_array.includes(values)) {
// 	                    $(this).addClass('hide')
// 	                }
// 					else if (input_ids_string == null){
// 						$(this).addClass('hide')
// 					}
// 					else {
// 		                $(this).removeClass('hide')
// 		            }
// 				}
//             }
//         })
//     }
// }
//
// //////////////Add and remove Green color and filter text////////////////
// //Product cat
// $(document).ready(function() {
// 	$(".pro_cat_att input[type='checkbox']").click(function() {
// 		console.log("has been clicked")
// 		var value = $(this).next().text();
//
// 		if ($(this).is(":checked")) {
// 			$('#UI_output > .product-cat > span').html("<span>" + value + " / </span>");
// 			//$('#UI_output > .product-cat > span').html("<span>" + value + " / </span>");
// 			console.log("this is checked")
// 		}
// 		else {
// 			$("#UI_output > .product-cat > span").html($("#UI_output > .product-cat > span").html().replace(value + " / ", ""));
// 			console.log("hide check pro ccat")
// 		}
// 		if ($(".pro_cat_att input:checkbox:checked").length > 0) {
// 			$('.pro_cat_att').addClass("red-active");
// 			$(this).removeClass('hide')
// 		}
// 		else {
// 			$('.pro_cat_att').removeClass("red-active");
// 		}
// 	});
// });
// //Country type
// $(document).ready(function() {
// 	$(".country_att input[type='checkbox']").click(function() {
// 		var value = $(this).next().text();
// 		if ($(this).is(":checked")) {
// 			//if($(".product_cat input[type='radio']").is(":checked")){
// 				//console.log("countery, product cat and country search checked")
// 				$('#UI_output > .countries > span').html("<span>" + value + " / </span>");
// 			//} else{
// 				//$('#UI_output > .countries > span').html("<span>" + value + " / </span>");
// 				//console.log("product cat not checked")
// 			//}
// 		}
// 		else {
// 			$("#UI_output > .countries > span").html($("#UI_output > .countries > span").html().replace(value + " / ", ""));
// 			console.log("hide check countery")
// 		}
// 		if ($(".country_att input:checkbox:checked").length > 0) {
// 			$('.country_att').addClass("red-active");
// 			$(this).removeClass('hide')
// 		}
// 		else {
// 			$('.country_att').removeClass("red-active");
// 		}
// 	});
// });
// //district type
// $(document).ready(function() {
// 	$(".distrikt_att input[type='checkbox']").click(function() {
// 		var value = $(this).next().text();
// 		if ($(this).is(":checked")) {
// 			//if($(".product_cat input[type='radio']").is(":checked")){
// 				//console.log("countery, product cat and country search checked")
// 				$('#UI_output > .district > span').html("<span>" + value + " / </span>");
// 			//} else{
// 				//$('#UI_output > .district > span').html("<span>" + value + " / </span>");
// 				//console.log("product cat not checked")
// 			//}
// 		}
// 		else {
// 			$("#UI_output > .district > span").html($("#UI_output > .district > span").html().replace(value + " / ", ""));
// 			console.log("hide check countery")
// 		}
// 		if ($(".distrikt_att input:checkbox:checked").length > 0) {
// 			$('.distrikt_att').addClass("red-active");
// 			$(this).removeClass('hide')
// 		}
// 		else {
// 			$('.distrikt_att').removeClass("red-active");
// 		}
// 	});
// });
// //Grape type
// $(document).ready(function() {
// 	$(".grape_type input[type='checkbox']").click(function() {
// 		var value = $(this).next().text();
// 		if ($(this).is(":checked")) {
// 			$('#UI_output > .grape > span').html("<span>" + value + " / </span>");
// 		}
// 		else {
// 			$("#UI_output > .grape > span").html($("#UI_output > .grape > span").html().replace(value + " / ", value));
// 		}
// 		if ($(".grape_type input:checkbox:checked").length > 0) {
// 			$('.grape_type').addClass("red-active");
// 		}
// 		else {
// 			$('.grape_type').removeClass("red-active");
// 		}
// 	});
// });
// //Cl size
// $(document).ready(function() {
//   $(".wine_size input[type='checkbox']").click(function() {
// 	  var value = $(this).next().text();
// 	  if ($(this).is(":checked")) {
// 		  $('#UI_output > .cl-size > span').html("<span>" + value + " / </span>");
// 	  }
// 	  else {
// 		  $("#UI_output > .cl-size > span").html($("#UI_output > .cl-size > span").html().replace(value + " / ", value));
// 	  }
// 	  if ($(".wine_size input:checkbox:checked").length > 0) {
// 		  $('.wine_size').addClass("red-active");
// 	  }
// 	  else {
// 		  $('.wine_size').removeClass("red-active");
// 	  }
//   });
// });
// //Product groupe
// $(document).ready(function() {
//   $(".product_group input[type='checkbox']").click(function() {
// 	  var value = $(this).next().text();
// 	  if ($(this).is(":checked")) {
// 		  $('#UI_output > .product-group > span').html("<span>" + value + " / </span>");
// 	  }
// 	  else {
// 		  $("#UI_output > .product-group > span").html($("#UI_output > .product-group > span").html().replace(value + " / ", value));
// 	  }
// 	  if ($(".product_group input:checkbox:checked").length > 0) {
// 		  $('.product_group').addClass("red-active");
// 	  }
// 	  else {
// 		  $('.product_group').removeClass("red-active");
// 	  }
//   });
// });
//
// //Pro cat on load
// $(window).on("load", function() {
// 	var value = $(".pro_cat_att input:checkbox:checked").next().text();
// 	if ($(".pro_cat_att input:checkbox:checked").length > 0) {
// 		$('#UI_output > .product-cat > span').html("<span>" + value + " / </span>");
// 	}
// 	if ($(".pro_cat_att input:checkbox:checked").length > 0) {
// 		$('.pro_cat_att').addClass("red-active");
// 		$(".pro_cat_att input[type='checkbox']").removeClass('hide')
// 	}
// 	$('.pro_cat_att .input-wrap').each(function() {
// 		var current_checkbox = $(this).find("input[type='checkbox']");
// 		if($(current_checkbox).is(":checked")){
// 			$(this).removeClass('hide')
// 			console.log("pro cad is still checked!")
// 		}
// 	})
// });
// //Country cat on load
// $(window).on("load", function() {
// 	var value_country = $(".country_att input:checkbox:checked").next().text();
// 	if ($(".country_att input:checkbox:checked").length > 0) {
// 		$('#UI_output > .countries > span').html("<span>" + value_country + " / </span>");
// 		$(".country_att input:checkbox:checked").removeClass('hide')
// 	}
// 	if ($(".country_att input:checkbox:checked").length > 0) {
// 		$('.country_att').addClass("red-active");
// 		$(".country_att input[type='checkbox']").removeClass('hide')
// 	}
// 	$('.country_att .input-wrap').each(function() {
// 		var current_checkbox = $(this).find("input[type='checkbox']");
// 		if($(current_checkbox).is(":checked")){
// 			$(this).removeClass('hide')
// 			console.log("country is still checked!")
// 		}
// 	})
// });
// $(window).on("load", function() {
// 	//Wine size cat on load
// 	var value_wine_size = $(".wine_size input:checkbox:checked").next().text();
// 	if ($(".wine_size input:checkbox:checked").length > 0) {
// 		$('#UI_output > .cl-size > span').html("<span>" + value_wine_size + " / </span>");
// 		$(".wine_size input:checkbox:checked").removeClass('hide')
// 	}
// 	if ($(".wine_size input:checkbox:checked").length > 0) {
// 		$('.wine_size').addClass("red-active");
// 		$(".wine_size input[type='checkbox']").removeClass('hide')
// 	}
// 	$('.wine_size .input-wrap').each(function() {
// 		var current_checkbox = $(this).find("input[type='checkbox']");
// 		if($(current_checkbox).is(":checked")){
// 			$(this).removeClass('hide')
// 		}
// 	})
// });
// //Product groupe cat on load
// $(window).on("load", function() {
// 	var value_product_group = $(".product_group input:checkbox:checked").next().text();
// 	if ($(".product_group input:checkbox:checked").length > 0) {
// 		$('#UI_output > .product-group > span').html("<span>" + value_product_group + " / </span>");
// 		$(".product_group input:checkbox:checked").removeClass('hide')
// 	}
// 	if ($(".product_group input:checkbox:checked").length > 0) {
// 		$('.product_group').addClass("red-active");
// 		$(".product_group input[type='checkbox']").removeClass('hide')
// 	}
// 	$('.product_group .input-wrap').each(function() {
// 		var current_checkbox = $(this).find("input[type='checkbox']");
// 		if($(current_checkbox).is(":checked")){
// 			$(this).removeClass('hide')
// 		}
// 	})
// });
// $(window).on("load", function() {
// 	//Grapes cat on load
// 	var value_product_group = $(".grape_type input:checkbox:checked").next().text();
// 	if ($(".grape_type input:checkbox:checked").length > 0) {
// 		$('#UI_output > .grape > span').html("<span>" + value_product_group + " / </span>");
// 		$(".grape_type input:checkbox:checked").removeClass('hide')
// 	}
// 	if ($(".grape_type input:checkbox:checked").length > 0) {
// 		$('.grape_type').addClass("red-active");
// 		$(".grape_type input[type='checkbox']").removeClass('hide')
// 	}
// 	$('.grape_type .input-wrap').each(function() {
// 		var input = $(this).find('input');
// 		var values = input.val();
// 		var current_checkbox = $(this).find("input[type='checkbox']");
// 		if($(current_checkbox).is(":checked")){
// 			$(this).removeClass('hide')
// 			console.log("is it checked!")
// 		}
// 	})
// });
// //Grapes cat on load
// $(window).on("load", function() {
// 	var value_product_group = $(".distrikt_att input:checkbox:checked").next().text();
// 	if ($(".distrikt_att input:checkbox:checked").length > 0) {
// 		$('#UI_output > .district > span').html("<span>" + value_product_group + " / </span>");
// 		$(".distrikt_att input:checkbox:checked").removeClass('hide')
// 	}
// 	if ($(".distrikt_att input:checkbox:checked").length > 0) {
// 		$('.distrikt_att').addClass("red-active");
// 		$(".distrikt_att input[type='checkbox']").removeClass('hide')
// 	}
// 	$('.distrikt_att .input-wrap').each(function() {
// 		var current_checkbox = $(this).find("input[type='checkbox']");
// 		if($(current_checkbox).is(":checked")){
// 			$(this).removeClass('hide')
// 		}
// 	})
// });


//$(".wine_size .input-title-el").append(" cl");

$('#shop-filter-box .reset').on('click', function(e) {
    // $('#UI_output > .product-cat > span').empty();
	// $('#UI_output > .countries > span').empty();
	// $('#UI_output > .grape > span').empty();
	// $('#UI_output > .cl-size > span').empty();
	// $('#UI_output > .product-group > span').empty();
	// $('#UI_output > .price-to > span').empty();
	// $('#UI_output > .price-from > span').empty();
	// $('#UI_output > .year-from > span').empty();
	// $('#UI_output > .year-to > span').empty();
	// $('.product_group').removeClass("red-active");
	// $('.product_cat').removeClass("red-active");
	// $('.country_search').removeClass("red-active");
	// $('.grape_type').removeClass("red-active");
	// $('.wine_size').removeClass("red-active");
	 //location.reload();
	 $('.filter-box-wrap').each(function(){
		 $(this).removeClass('red-active')
	 })
	 // $('.filter-box-wrap input').each(function(){
		//  $(this).prop( "checked", false );
	 // })
	 $('.filter-box-wrap .input-list .input-wrap').each(function(){
		$(this).removeClass('hide')
	})
	 $('.bbh-output-tags .bubble').each(function(){
		 $(this).remove()
	 })
	 $('#shop-filter-form')[0].reset()
		$('#shop-filter-form input[name="orderby"]').val($('select[name="orderby"]').val())
	 // $('.woocommerce-ordering')[0].reset()
	 $('.submit-field button').trigger('click');

});


$('.woocommerce-ordering select[name="orderby"] option[value="date"]').attr("selected",false);
// $('.woocommerce-ordering select[name="orderby"] option[value="stock"]').attr("selected",true);




document.addEventListener('DOMContentLoaded', initUIsliders);
