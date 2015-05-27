
jQuery(document).ready(function(){
	slickInit();
});

function slickInit() {
	jQuery('.austeve-gallery-images').slick({
		dots: true,
		infinite: true,
		speed: 500,
		fade: true,
		cssEase: 'linear',
		adaptiveHeight: true
	});
}