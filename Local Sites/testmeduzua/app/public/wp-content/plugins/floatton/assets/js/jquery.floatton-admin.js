/*
	Admin Settings Page Scripts
*/
(function($) {
	$('.floatton_colorpicker').wpColorPicker();
	$( document ).on( 'change', '.floatton-content-select', function(){
		if( this.value == 'custom' ){
			$('.floatton-content-default, .floatton-content-link').fadeOut( 150,function(){
				$('.floatton-custom-content').addClass('floatton-show');
				$('.floatton-custom-content, .floatton-open-row').fadeIn( 150 );
			} );
		}else if( this.value == 'link' ){
			$('.floatton-content-default, .floatton-custom-content, .floatton-open-row').fadeOut( 150,function(){
				$('.floatton-content-link').fadeIn( 150 );
			} );
		}else{
			$('.floatton-custom-content, .floatton-content-link').hide().removeClass('floatton-show');
			$('.floatton-content-default, .floatton-open-row').fadeIn( 150 );
		}
	} );
	$( document ).on( 'change', '.floatton-content-open', function(){
		if( this.value == 'onScroll' ){
			$('.floatton-content-open-opts').addClass('floatton-show');
		}else{
			$('.floatton-content-open-opts').removeClass('floatton-show');
		}
	} );

})(jQuery);
