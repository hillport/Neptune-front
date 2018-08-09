"use strict";
var $ = jQuery.noConflict();
var owl;

var charge_diapo_deja_lance = false;

init_owl(false);
function init_owl(loop)
{
	if(typeof(loop) == typeof(undefined))
	{
		loop = false;
	}


	if($.fn.owlCarousel)
	{
		owl = 
		$('.content-diapo').owlCarousel({
			slideSpeed : 300,
			paginationSpeed : 400,
			items : 1, 
			dots: true,
			nav: false,
			autoplay: loop,
			animateOut: 'fadeOut',
			autoplayTimeout: 6000,
			loop:loop,
			mouseDrag:false,


		});



	}

}

