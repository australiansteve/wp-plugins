
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

jQuery(document).on('mouseover', ".widget_austeve_gallery_widget", function() {
    jQuery(this).find(".layover").show(); 
    jQuery(this).find(".img").css('opacity', '0.5');     
});

jQuery(document).on('mouseout', ".widget_austeve_gallery_widget", function() {
    jQuery(this).find(".layover").hide();   
    jQuery(this).find(".img").css('opacity', '1');   
});