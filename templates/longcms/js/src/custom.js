/*start toplink*/
jQuery.fn.topLink = function(settings) {
	settings = jQuery.extend({
		min: 1,
		fadeSpeed: 400
	}, settings);
	return this.each(function() {
		//listen for scroll
		var el = $(this);
		el.hide(); //in case the user forgot

	$(window).scroll(function() {
	var bgg=$(document).height();
	var gbb = $(document).scrollTop();
	var pgg = $(window).height();
	var frr = bgg - pgg;
	var gbb = $(document).scrollTop();
	if(frr == gbb)
	{
		$("#top-link").css({ bottom: "140px"});
	}
	else
	{
		$("#top-link").css({ bottom: "140px"});
	};

	   			if($(window).scrollTop() >= settings.min)
			{

				el.fadeIn(settings.fadeSpeed);
			}
			else
			{
				el.fadeOut(settings.fadeSpeed);
			}
		});
	});
};
//usage w/ smoothscroll
$(document).ready(function() {
	$('#top-link').topLink({
		min: 200,
		fadeSpeed: 400
	});
	//smoothscroll
	$('#top-link').click(function(e) {
		e.preventDefault();
		$("html, body").animate({
			scrollTop: "0px"
		});
	});


	$('.fancybox').fancybox();



});


