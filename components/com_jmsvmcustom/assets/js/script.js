jQuery( function($) {	
	jQuery(document).ready(function($) {
		
		$('.color-icon').click(function() {			
			var color_id = $(this).attr('rel');
			var pid = $(this).attr('data-pid');
			var imgs = $('#product' + pid + ' .product-image img');
			imgs.each(function(){
				$(this).css('opacity','0');
			});
			var cimgs = $('#product' + pid + ' .product-image .img' + color_id);
			cimgs.eq(0).css('opacity','1');
			var current_color = $('#product' + pid + ' .current_color');
			current_color.eq(0).val(color_id);
		});
		
		$('.product-image').mouseover(function() {			
			var current_color = $(this).children('.current_color');
			var color_id = current_color.val();
			var imgs = $(this).children('img');
			imgs.each(function(){
				$(this).css('opacity','0');
			});
			var cimgs = $(this).children('.img' + color_id);
			cimgs.eq(1).css('opacity','1');
		});
		$('.product-image').mouseleave(function() {			
			var current_color = $(this).children('.current_color');
			var color_id = current_color.val();
			var imgs = $(this).children('img');
			imgs.each(function(){
				$(this).css('opacity','0');
			});
			var cimgs = $(this).children('.img' + color_id);
			cimgs.eq(0).css('opacity','1');
		});
		
	});	
	
	
	
	
});