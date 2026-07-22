/**
*
* Plugin Name: floatton by Phpbits
* Plugin URI: https://phpbits.net/plugin/floatton/
*
*/

// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {

	var construct_floatton = {
		init: function( opts, elem ) {
			var self 			= this;

			self.elem 			= elem;
			self.$elem 			= $( elem );

			self.opts 			= $.extend( {}, $.fn.floatton.opts, opts );

			self.opts.id 		= self.$elem.attr('data-id');
			self.opts.page 		= self.$elem.attr('data-page');
			self.opts.target 	= self.$elem.attr('data-target');
			self.opts.BodyRegex = new RegExp("<body[^>]*>((.|\n|\r)*)</body>", "i");

			self.vars();
			// setTimeout(function(){
				self.display();
				self.buildResize();
				self.buildClick();
				self.buildRating();
				self.onFormSubmit();
				self.autoOpen();
				self.onTouchStart();
			// }, 1000);
			
		},
		vars: function(){
			var self 	= this;

			self.opts.pos 	= self.$elem.position();
			self.opts.h 	= self.$elem.innerHeight();
			self.opts.w 	= self.$elem.innerWidth();

			self.opts.wh 	= $(window).height();
			self.opts.ww 	= $(window).width();
			self.opts.open 	= self.$elem.attr('data-open');
			self.opts.openValue = self.$elem.attr('data-open-value');
			self.opts.openType 	= self.$elem.attr('data-open-type');

			if( self.opts.ww <= 600 ){
				self.opts.device = 'mobile';
			}else if( self.opts.ww > 600 &&  self.opts.ww < 769 ){
				self.opts.device = 'tablet';
			}else{
				self.opts.device = 'desktop';
			}
		},
		display: function(){
			var self 	= this;
			var l 		= self.$elem.attr('data-left');
			var r 		= self.$elem.attr('data-right');
			var t 		= self.$elem.attr('data-type');
			var css  	= { 'bottom' : self.opts.h + 28 };
			var diff 	= 0;
			var q 	 	= self.opts.ww / 2;
			var pos  	= '';
			// alert( self.opts.h );
			if( 'auto' != l ){
				l = parseFloat( l ) + t;
			}
			if( 'auto' != r ){
				r = parseFloat( r ) + t;
			}
			if( '%' == t ){
				l = self.opts.ww * ( parseFloat( l ) / 100 );
			}
			if( '%' == r ){
				r = self.opts.ww * ( parseFloat( r ) / 100 );
			}
			//button position
			self.$elem.css({ 'left' : l, 'right' : r }).animate({ 'margin-bottom' : 9 }, self.opts.animSpeed );
			
			self.opts.pos = self.$elem.position();
			if( 'auto' != l ){
				self.opts.pos.left = parseFloat( l );
			}

			$( self.opts.target ).find('label').removeAttr('for');
			$( self.opts.target + ' .floatton-inner' ).css({ 'max-height': (self.opts.wh - 170) + 'px', 'max-width': (self.opts.ww - 40) + 'px' });

			if( self.opts.device == 'mobile' ){
				var posL = self.opts.pos.left;
				if( 'auto' != l ){
					posL = self.opts.pos.left + 5;
					if( isNaN( posL ) ){
						posL = (self.opts.ww * ( parseFloat( r ) / 100 )) + self.opts.w + 32;
					}
				}
				$( self.opts.target + ' .floatton-pointer' ).css({ 'left' : posL });
				$( self.opts.target ).css({ 'max-width' : (self.opts.ww - 40) + 'px' });
			}else{
				$( self.opts.target + ' .floatton-pointer' ).removeAttr('style');
			}

			//content position
			if( typeof self.opts.pos.left != 'undefined' ){
				diff = self.opts.ww - self.opts.pos.left;
				if( diff < q ){
					pos = 'right';
				}else{
					pos = 'left';
				}
			}

			if( pos == 'right' ){
				if( diff < 1 ){ diff = 0 }
				css['right'] = ( diff - self.opts.w ) - 8; 
				css['left']  = 'auto'; 
				$( self.opts.target ).addClass('floatton-pos-right');
			}else if( pos == 'left' ){
				css['left']  = self.opts.pos.left - 8;
				css['right'] = 'auto'; 
				if( isNaN( css['left'] ) ){
					css['left']  = self.opts.ww * ( parseFloat( r ) / 100 );
					$( self.opts.target ).addClass('floatton-pos-right');
				}else{
					$( self.opts.target ).addClass('floatton-pos-left');
				}
			}
			//custom css for position
			$( self.opts.target ).css( css );
			
		},
		buildClick: function(){
			var self = this;
			var isWebkit = 'WebkitAppearance' in document.documentElement.style;
			$( self.$elem ).on('click', function(){
				if( !self.$elem.hasClass('floatton-opened') ){
					self.btnOpen('click');

					// console.log( self.opts.target + '-' + $( self.opts.target + ' .floatton-inner' ).prop('scrollHeight') + '-' + $( self.opts.target ).height() );
					//has scrollbar
					if(  (isWebkit) && $( self.opts.target + ' .floatton-inner' ).prop('scrollHeight') > $( self.opts.target ).height() ){
						$( self.opts.target + ' .floatton-inner' ).addClass('floatton-scroller');
					}
				}else{
					$( self.opts.target ).animate({ opacity : 0, 'margin-bottom' : -20 }, self.opts.animSpeed  ,function(){
						$(this).hide();
						$( self.opts.target + ' .floatton-success' ).hide();
					});
					self.$elem.removeClass('floatton-opened');
					$( self.opts.target ).removeClass('floatton-opened');
					$( '.floatton-btn' ).removeClass( 'floatton-touched' );
				}
			});

			$( document ).on( 'click', self.opts.target + ' .floatton-close, .floatton-open' , function(e){
				var target = $(this).attr('data-target');
				if( typeof target != 'undefined' ){
					$( target ).click();
				}
				e.preventDefault();
				e.stopPropagation();
				return false;
			} );
		},
		btnOpen: function( type ){
			var self = this;

			if( 'click' == type ){
				$('.floatton-container.floatton-opened').animate({ opacity : 0, 'margin-bottom' : -20 }, self.opts.animSpeed  ,function(){
					$(this).hide();
					$(this).removeClass('floatton-opened');
				});
			}
			$( self.opts.target ).show().animate({ opacity : 1, 'margin-bottom' : 0 }, self.opts.animSpeed );
			if( 'click' == type ){
				$('.floatton-btn').removeClass('floatton-opened');
			}
			$( self.opts.target ).addClass('floatton-opened');
			self.$elem.addClass('floatton-opened');
		},
		onTouchStart: function(){
			var self = this;
			$( self.opts.target + ' input, '+ self.opts.target + ' textarea' ).not(':input[type=button], :input[type=submit], :input[type=reset]').bind( 'touchstart', function(){
				$( '.floatton-btn' ).addClass( 'floatton-touched' );
			});
		},
		buildRating: function(){
			var self = this;
			$('.floatton-ratings').find('span').hover(
				function() {
					self.ratingAction( $(this), 'hover' );
				}, function() {
					var rating = $( self.opts.target ).find('input#rating').val();
					if (rating) {
						var list = $('.floatton-ratings span');
						list.removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
						list.slice(0, rating).removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
					}else{
						$( self.opts.target + ' .floatton-ratings span' ).removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
					}
				}
			);

			$( self.opts.target + ' .floatton-ratings span' ).on( 'click', function(e){
				self.ratingAction( $(this), 'click' );
				return false;
			} );
		},
		ratingAction: function( dis, action ){
			var self = this;
			dis.nextAll('span').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
			dis.prevAll('span').removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
			dis.removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
			if( 'click' == action ){
				$( self.opts.target ).find('input#rating').val( dis.attr('data-rating') );
			}
		},
		onFormSubmit: function(){
			var self 	= this;
			var req  	= [];
			var n 	 	= '';
			var isEmpty = '';
			var pos 	= 0;
			$( document ).on('submit', self.opts.target + ' form', function(e){
				if( $(this).find('.gform_body').length > 0 ){
					//do nothing for gravity forms
				}else{
					$( '.floatton-loading' ).show();
					var formurl		= $(this).attr('action');
					var formdata	= $(this).serialize();
					var first 		= '';
					$( self.opts.target + ' form' ).find( 'input, textarea' ).each(function(){
						if( $(this).prop('required') || $(this).attr('aria-required') ){
							n = $(this).attr('name');
							if( $(this).val().length === 0 ){
								req[n] = true;
								$(this).addClass('floatton-req');
								if( first.length == 0 ){
									first = $(this);
								}
							}else{
								$(this).removeClass('floatton-req');
								delete req[ n ];
							}
						}
					});
					isEmpty = $.isEmptyObject( req );
					
					if( isEmpty == true ){
						jQuery.ajax({
							type 	: 'post',
							url 	: formurl,
							data 	: formdata,
							success	: function( data, textStatus ){
								// console.log( textStatus );
								$( self.opts.target + ' .floatton-success' ).fadeIn( 150,function(){
									$( '.floatton-loading' ).hide();
								} );
							},
							error 	: function( data, textStatus, errorThrown ){
								data = data.responseText;
								var r = self.extractBody(data);
								if( typeof r[0] !== 'undefined' ){
									$( '.floatton-loading' ).hide();
									$('.floatton-errors').fadeOut(150,function(){
										$(this).find('.floatton-error').remove();
										$(this).append( '<div class="floatton-error">' + r[0].innerHTML + '</div>' ).fadeIn(100, function(){
											$( self.opts.target + ' .floatton-inner').scrollTop( $( self.opts.target + ' .floatton-inner')[0].scrollHeight );
										});
									});
								}
							}
						});
					}else{
						pos = $( self.opts.target + ' .floatton-inner').scrollTop() - $( self.opts.target + ' .floatton-inner').offset().top + first.offset().top;
						pos = pos - 15;
						$( self.opts.target + ' .floatton-inner').scrollTop( pos );
						// console.log( pos );
						first = '';
						$( '.floatton-loading' ).hide();
					}
					
					return false;
				}
			});
		},
		extractBody: function( html ){
			var self = this;
			try {
				return $( '<div class="floatton-error">'+ self.opts.BodyRegex.exec(html)[1] +'</div>' );
			} catch (e) {
				return false;
			}
		},
		buildScroll: function(){
			var doc = $(document).height();
			var win = window.innerHeight;
			// console.log( $(window).scrollTop() + '-' + doc )
			if( $('.floatton-onScroll').length > 0 ){
				$('.floatton-onScroll').each(function(){
					var val 	= $(this).attr('data-open-value');
					var prcnt 	= $(this).attr('data-open-type');
					if( '%' == prcnt ){
						val = ( doc - win ) * ( parseFloat( val ) / 100 );
					}
					if( !$(this).hasClass('floatton-scrolled') && $(window).scrollTop() >= val && !$(this).hasClass('floatton-opened') && !$(this).hasClass('floatton-touched') ){
						$(this).click();
						$(this).addClass('floatton-scrolled');
					}
				});
			}
		},
		buildScrollContent: function(){
			var el 	= $('.floatton-article');
			var t 	= ( el.offset().top + el.outerHeight() - ( window.innerHeight / 2 ) );
			if( $('.floatton-onScrollContent').length > 0 && el.length > 0 ){
				$('.floatton-onScrollContent').each(function(){
					if( $(window).scrollTop() >= t ) {
				        if( !$(this).hasClass('floatton-scrolled') && !$(this).hasClass('floatton-opened') && !$(this).hasClass('floatton-touched') ){
				        	$(this).click();
				        	$(this).addClass('floatton-scrolled')
				        }
				    }
				});
			}
		},
		buildScrollBottom: function(){
			var doc = $(document).height() - ( $(window).height() + 60 );
			if( $('.floatton-onScrollBottom').length > 0 ){
				$('.floatton-onScrollBottom').each(function(){
					if( $(window).scrollTop() >= doc ) {
				        if( !$(this).hasClass('floatton-scrolled') && !$(this).hasClass('floatton-opened') && !$(this).hasClass('floatton-touched') ){
				        	$(this).click();
				        	$(this).addClass('floatton-scrolled')
				        }
				    }
				});
			}
		},
		buildResize: function(e){
			var self = this;

			$(window).on('resize', function(event) {
				self.vars();
				self.display();
			});

		},
		autoOpen: function(){
			var self = this;

			if( typeof self.opts.open != 'undefined' ){
				switch( self.opts.open ){
					case 'onLoad':
						self.buildonLoad( self.opts.open );
					break;
					case 'onLoadOnce':
						if( !self.$elem.hasClass('floatton-loaded') ){
							self.buildonLoad( self.opts.open );
						}
						// console.log( floatton.onLoadOnce );
					break;
					case 'onScroll':
						if( self.opts.openValue.length > 0 ){
							$( window ).bind( 'scroll', self.buildScroll );
						}
					break;
					case 'onScrollContent':
						$( window ).bind( 'scroll', self.buildScrollContent );
					break;
					case 'onScrollBottom':
						$( window ).bind( 'scroll', self.buildScrollBottom );
					break;
					default:
					break;
				}
			}
		},
		buildonLoad: function( type ){
			var self = this;
			$(window).load(function(){
				setTimeout(function(){

					self.btnOpen( type );

					switch( type ){
						case 'onLoadOnce':
							jQuery.ajax({
								type 	 : 'post',
								dataType : 'json',
								url 	 : floatton.ajaxurl,
								data 	 : { action: 'floatton_ajax', type: type, id: self.opts.id },
								success	 : function( data, textStatus ){
									// console.log( data );
								}
							});
						break;
						default:
						break;
					}
				}, self.opts.animSpeed + 80 );
			});
		}
	};

	$.fn.floatton = function( opts ) {
		return this.each(function() {
			var floatton = Object.create( construct_floatton );
			
			floatton.init( opts, this );

			$.data( this, 'floatton', floatton );
		});
	};

	$.fn.floatton.opts = {
		container : '.floatton-container',
		animSpeed : 250
	};

	$( '.floatton-btn' ).floatton();

})( jQuery, window, document );