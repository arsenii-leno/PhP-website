<?php
/*
 * Set Display on the frontend
 */

if( !class_exists( 'FLOATTON_FE' ) ){
	class FLOATTON_FE{
		/*
		 * For easier overriding we declared the keys
		 * here as well as our tabs array which is populated
		 * when registering settings
		 */
		private $post_type 	= 'floatton';
		private $limit 		= 50;

		public function __construct() {
			add_action( 'wp_enqueue_scripts', array($this, 'enqueue'), 999 );
			add_action( 'wp_footer', array( $this, 'build_display' ) );
			add_filter( 'post_class', array( $this, 'post_class' ) );
			add_action( 'wp_ajax_floatton_ajax', array( $this, 'floatton_ajax' ));
			add_action( 'wp_ajax_nopriv_floatton_ajax', array( $this, 'floatton_ajax' ));
		}

		function enqueue(){
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'css-floatton', plugins_url( 'assets/css/floatton.css' , dirname(__FILE__) ) , array(), null );
			wp_register_script(
				'jquery-floatton',
				plugins_url( 'assets/js/jquery.floatton.js' , dirname(__FILE__) ),
				array( 'jquery' ),
				'',
				true
			);

			wp_enqueue_script( 'jquery-floatton' );

			$params = array(
						'ajaxurl' 		=>  admin_url( 'admin-ajax.php' )
					);

			wp_localize_script( 'jquery-floatton', 'floatton', $params);
		}

		function post_class( $classes ){
			$classes[] = 'floatton-article';
			return $classes;
		}

		function build_display(){
			$floatton = '';
			$args 	 = array(
							'post_type' 		=>	$this->post_type,
							'post_status' 		=>	'publish',
							'posts_per_page' 	=>	$this->limit
						);
			$custom  = array(
			                'key'     => '_floatton-content-type',
			                'value'   => array( 'custom', 'link' ),
			                'compare' => 'IN',
			            );
			$page 	= '';
			if( ( is_home() && is_front_page() ) || is_front_page() ){
				//home page
				$page = 'home';
				$args['meta_query'] = array(
			            'relation'  => 'AND',
			            array(
			                'key'     => '_floatton-misc-home',
			                'value'   => array( '1' ),
			                'compare' => 'IN',
			            ), $custom
			        );
				//run query
				$floatton = get_posts( $args );
			}else if( is_home() ){
				//blog page
				$page = 'blog';
				$args['meta_query'] = array(
			            'relation'  => 'AND',
			            array(
			                'key'     => '_floatton-misc-blog',
			                'value'   => array( '1' ),
			                'compare' => 'IN',
			            ), $custom
			        );
				//run query
				$floatton = get_posts( $args );

			}else if( is_single() || is_page() ){
				global $post;
				$page = $post->ID;
				$args[ 'meta_key' ] 	= '_floatton-types-'. $post->post_type;
				$args[ 'meta_value' ] 	= 1;

				//run query
				$floatton = get_posts( $args );
			}else if( is_archive() ){
				$object = get_queried_object();

				if( isset( $object->taxonomy ) ){
					$page = $object->taxonomy;
					$args['meta_query'] = array(
				            'relation'  => 'AND',
				            array(
				                'key'     => '_floatton-tax-'. $object->taxonomy,
				                'value'   => array( '1' ),
				                'compare' => 'IN',
				            ), $custom
				        );
					//run query
					$floatton = get_posts( $args );
				}
			}else if ( is_404() ) {
				//404 page
				$page = '404';
				$args['meta_query'] = array(
			            'relation'  => 'AND',
			            array(
			                'key'     => '_floatton-misc-404',
			                'value'   => array( '1' ),
			                'compare' => 'IN',
			            ), $custom
			        );
				//run query
				$floatton = get_posts( $args );
			}else if ( is_search() ) {
				//home page
				$page = 'search';
				$args['meta_query'] = array(
			            'relation'  => 'AND',
			            array(
			                'key'     => '_floatton-misc-search',
			                'value'   => array( '1' ),
			                'compare' => 'IN',
			            ), $custom
			        );
				//run query
				$floatton = get_posts( $args );
			}


			if( !empty( $floatton ) ){
				foreach ( $floatton as $postdata ) {
					// setup_postdata( $postdata );
					$id = $postdata->ID;
					/*
			         * get meta values
			         * Check for transient. If none, then execute Query
			         */
			        if ( false === ( $meta = get_transient( 'floatton_meta_'. $id  ) ) ) {

			            $meta = get_post_meta( $id );

			          // Put the results in a transient. Expire after 4 weeks.
			          set_transient( 'floatton_meta_'. $id , $meta, 4 * WEEK_IN_SECONDS );
			        }
			        // print_r( $meta );
					if( isset( $meta['_floatton-post-ids'] ) && isset( $meta['_floatton-post-ids'][0] ) && !empty( $meta['_floatton-post-ids'][0] ) ){
						$post_ids = explode( ',', $meta['_floatton-post-ids'][0] );

						if( is_array( $post_ids ) && !in_array( $page, $post_ids ) ){
							continue;
						}
					}
			        $this->build_content( $id, $meta, $page );

				}
				wp_reset_postdata();
			}
		}

		function build_content( $id, $meta, $page ){

			//run user permission first
			if( is_user_logged_in() && isset( $meta['_floatton-permission-loggedin'] ) && isset( $meta['_floatton-permission-loggedin'][0] ) && '1' == $meta['_floatton-permission-loggedin'][0] ){
				return false;
			}else if( !is_user_logged_in() && isset( $meta['_floatton-permission-guest'] ) && isset( $meta['_floatton-permission-guest'][0] ) && '1' == $meta['_floatton-permission-guest'][0] ){
				return false;
			}
			//add action before running contents
			do_action( 'floatton_before_display', $id, $page );

			$filter_return = apply_filters( 'floatton_before_display_filter', true );
			if( $filter_return ){
				//continue
			}else{
				return false;
			}

			$class = 'floatton-btn-'. $id;
			$defaults = array(
						'_floatton-btn-text' 		 => '',
						'_floatton-content-title' 	 => __( 'Leave Comment', 'floatton' ),
						'_floatton-content-btn-text' => __( 'Submit', 'floatton' ),
						'_floatton-btn-pos-left' 	 => 'auto',
						'_floatton-btn-pos-right' 	 => 'auto',
						'_floatton-btn-pos-type' 	 => 'px',
						'_floatton-devices-mobile' 	 => '',
						'_floatton-devices-tablet' 	 => '',
						'_floatton-devices-desktop'  => '',
						'_floatton-auto-open' 		 => 'disabled',
						'_floatton-auto-open-value'  => '',
						'_floatton-auto-open-type' 	 => 'px',
						'_floatton-target-url' 	 	 => '',
						'_floatton-new-tab' 	 	 => '',
				);
			foreach ( $defaults as $key => $value ) {
				if( !isset( $meta[ $key ] ) || !isset( $meta[ $key ][0] ) || empty( $meta[ $key ][0] ) ){
					$meta[ $key ][0] = $value;
				}
			}
			$icon  		= '';
			$message 	= '';
			if( isset( $meta['_floatton-btn-icon'] ) && isset( $meta['_floatton-btn-icon'][0] ) && !empty( $meta['_floatton-btn-icon'][0]  ) ){
				$icon = '<span class="dashicons '.  $meta['_floatton-btn-icon'][0] .'"></span> ';
				if( 'no-icon' == $meta['_floatton-btn-icon'][0] ){
					$class .= ' floatton-btn-no-icon';
				}
			}else{
				$class .= ' floatton-btn-no-icon';
			}
			if( isset( $meta['_floatton-success_message'] ) && isset( $meta['_floatton-success_message'][0] ) && !empty( $meta['_floatton-success_message'][0] ) ){
				$message = $meta['_floatton-success_message'][0];
			}
			if( empty( $meta['_floatton-btn-text'][0] ) ){
				$class .= ' floatton-btn-icon-only';
			}
			if( !empty( $meta['_floatton-devices-mobile'][0] ) ){
				$class .= ' floatton-hide-sm';
			}
			if( !empty( $meta['_floatton-devices-tablet'][0] ) ){
				$class .= ' floatton-hide-md';
			}
			if( !empty( $meta['_floatton-devices-desktop'][0] ) ){
				$class .= ' floatton-hide-lg';
			}
			if( isset( $_COOKIE['floatton_'. $id ] ) ){
				$class .= ' floatton-loaded';
			}
			if( isset( $meta['_floatton-auto-open'][0] ) ){
				$class .= ' floatton-'. $meta['_floatton-auto-open'][0];
			}

			if( isset( $meta['_floatton-content-type'][0] ) ){
				$content_type = $meta['_floatton-content-type'][0];
			}

			$linked = '';
			if( ( 'link' == $content_type && !isset( $meta['_floatton-new-tab'] ) )
		 		|| ( 'link' == $content_type && isset( $meta['_floatton-new-tab'] ) && empty( $meta['_floatton-new-tab'][0] ) )
			){
				$linked = 'onClick="window.location.href=\''. $meta['_floatton-target-url'][0] .'\'"';
			}elseif( 'link' == $content_type && isset( $meta['_floatton-new-tab'] ) && '1' == $meta['_floatton-new-tab'][0] ){
				$linked = 'onClick="window.open( \''. $meta['_floatton-target-url'][0] .'\',\'_blank\' )"';
			}
			?>
			<button class="floatton-btn <?php echo $class;?>" <?php echo $linked;?> data-id="<?php echo $id;?>" <?php if( !empty( $message ) ) { echo 'data-message="true"'; }?> data-type="<?php echo $meta['_floatton-btn-pos-type'][0];?>" data-left="<?php echo $meta['_floatton-btn-pos-left'][0];?>" data-right="<?php echo $meta['_floatton-btn-pos-right'][0];?>" data-target=".floatton-container-<?php echo $id;?>" data-open="<?php echo $meta['_floatton-auto-open'][0];?>" data-open-value="<?php echo $meta['_floatton-auto-open-value'][0];?>" data-open-type="<?php echo $meta['_floatton-auto-open-type'][0];?>" data-page="<?php echo $page;?>" ><?php echo $icon;?><span class="floatton-btn-label"><?php echo $meta['_floatton-btn-text'][0];?></span></button>
			<?php if( 'link' != $content_type ){ ?>
				<div class="floatton-container floatton-container-<?php echo $id;?> <?php if( empty( $message  ) ){ echo 'floatton-no-message'; }?>" data-id="<?php echo $id;?>" >
					<span class="floatton-close dashicons dashicons-no-alt" data-target=".floatton-btn-<?php echo $id;?>"></span>
					<div class="floatton-success">
						<div class="floatton-success-inner">
							<div class="floatton-success-content">
								<?php if( !empty( $message ) ){
									echo $message;
								}else{
									echo '<span class="dashicons floatton-success-icons dashicons-yes"></span>';
									_e( 'Thanks!', 'floatton' );
								} ?>
							</div>
						</div>
					</div>
					<div class="floatton-inner">
						<?php
						do_action( 'floatton_before_content', $id, $page );
						switch ( $meta['_floatton-content-type'][0] ) {
							case 'comment':
							case 'woo-rating':
								$form_args = array(
												'title_reply' 	=> $meta['_floatton-content-title'][0],
												'label_submit' 	=> $meta['_floatton-content-btn-text'][0],
											);

								if( isset( $meta['_floatton-content-notes-before'] ) && isset( $meta['_floatton-content-notes-before'][0] ) && !empty( $meta['_floatton-content-notes-before'][0] ) ){
									$form_args['comment_notes_before'] = wpautop( $meta['_floatton-content-notes-before'][0] );
								}

								if( isset( $meta['_floatton-content-notes-after'] ) && isset( $meta['_floatton-content-notes-after'][0] ) && !empty( $meta['_floatton-content-notes-after'][0] ) ){
									$form_args['comment_notes_after'] = wpautop( $meta['_floatton-content-notes-after'][0] );
								}
								if( $meta['_floatton-content-type'][0] == 'woo-rating' && is_singular('product') ){
									if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
										$form_args['must_log_in'] = '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'floatton' ), esc_url( $account_page_url ) ) . '</p>';
									}

									if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
										$form_args['comment_field'] = '<p class="comment-form-rating">
										<input type="hidden" name="rating" id="rating" aria-required="true" required />
										<span class="floatton-ratings">
											<span class="dashicons dashicons-star-empty" data-rating="1"></span>
											<span class="dashicons dashicons-star-empty" data-rating="2"></span>
											<span class="dashicons dashicons-star-empty" data-rating="3"></span>
											<span class="dashicons dashicons-star-empty" data-rating="4"></span>
											<span class="dashicons dashicons-star-empty" data-rating="5"></span>
										</span></p>';
									}
									$form_args['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'floatton' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea></p>';

									comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $form_args ) );
								}else{
									comment_form( $form_args );
								}
								break;
							case 'custom':
								if( isset( $meta['_floatton-content_custom'] ) && isset( $meta['_floatton-content_custom'][0] ) && !empty( $meta['_floatton-content_custom'][0] ) ){
									echo apply_filters( 'floatton_custom_content', do_shortcode( wpautop( $meta['_floatton-content_custom'][0] ) ) );
								}
							break;
							default:

								break;
						}
						do_action( 'floatton_after_content', $id, $page );
						?>
						<div class="floatton-errors"></div>
					</div>
					<div class="floatton-loading">
						<span class="floatton-dot-1"></span>
						<span class="floatton-dot-2"></span>
						<span class="floatton-dot-3"></span>
					</div>
					<span class="floatton-pointer"></span>
				</div>
		<?php
			}
			echo $this->custom_styling( $id, $meta );
		}

		function custom_styling( $id, $meta ){
			$styling = '';

			/*
	         * get meta values
	         * Check for transient. If none, then execute Query
	         */
	        if ( false === ( $styling = get_transient( 'floatton_styling_'. $id  ) ) ) {
	        	$btn 	 = 'body button.floatton-btn-'. $id;
	        	$pop 	 = 'body .floatton-container-'. $id;
	        	$success = 'body .floatton-container-'. $id .' .floatton-success';

				$fields = array(
						'button' 	=> array(
											'container'  =>  $btn,
											'hcontainer' => $btn . ':hover, ' . $btn . ':focus',
											'regular' 	 => array(
															'_floatton-styling-button-regular' 	=> 'background-color: ##val##;',
															'_floatton-styling-text-regular' 	=> 'color: ##val##;',
														),
											'hover' 	 => array(
															'_floatton-styling-button-hover' 	=> 'background-color: ##val##;',
															'_floatton-styling-text-hover' 		=> 'color: ##val##;'
														)
										),
						'icon' 		=> array(
											'container'  =>  $btn . ' .dashicons',
											'hcontainer' => $btn . ':hover .dashicons',
											'regular' 	 => array(
															'_floatton-styling-icon-regular' 	=> 'color: ##val##;'
														),
											'hover' 	 => array(
															'_floatton-styling-icon-hover' 		=> 'color: ##val##;'
														)
										),
						'fields' 	=> array(
											'container'  =>  $pop . ' .floatton-inner input, '. $pop .' .floatton-inner textarea',
											'hcontainer' => $pop . ' .floatton-inner input:focus, '. $pop .' .floatton-inner textarea:focus',
											'regular' 	 => array(
															'_floatton-styling-fields-color-regular' 	=> 'color: ##val## !important;',
															'_floatton-styling-fields-regular' 			=> 'border-color: ##val## !important;',
														),
											'hover' 	 => array(
															'_floatton-styling-fields-color-hover' 		=> 'color: ##val## !important;',
															'_floatton-styling-fields-hover' 			=> 'border-color: ##val## !important;'
														)
										),
						'submit' 	=> array(
											'container'  =>  $pop . ' .floatton-inner input[type="submit"], ' . $pop . ' .floatton-inner button[type="submit"]',
											'hcontainer' => $pop . ' .floatton-inner input[type="submit"]:hover, ' .$pop . ' .floatton-inner button[type="submit"]:hover',
											'regular' 	 => array(
															'_floatton-styling-submit-regular' 	 => 'color: ##val## !important;',
															'_floatton-styling-submitbg-regular' => 'background-color: ##val## !important;'
														),
											'hover' 	 => array(
															'_floatton-styling-submit-hover' 	 => 'color: ##val## !important;',
															'_floatton-styling-submitbg-hover' 	 => 'background-color: ##val## !important;'
														)
										),
						'success' 	=> array(
											'container'  =>  $success,
											'regular' 	 => array(
															'_floatton-styling-success-background' 	=> 'background-color: ##val##;',
															'_floatton-styling-success-text' 		=> 'color: ##val##;',
														)
										),
					);

				$other 	= array(
		        			'_floatton-styling-popup-border' 		=> $pop . '{ border-color: ##val##;}' . $pop . ' .floatton-pointer{ border-top-color: ##val##; }'. $pop . ' .floatton-close{ background-color: ##val##; }' . $pop . ' .floatton-loading{ color: ##val##; }',
		        			'_floatton-styling-popup-background' 	=> $pop . ', '. $pop .' .floatton-inner label{ background-color: ##val##;}',
		        			'_floatton-styling-popup-text' 			=> $pop . ' .floatton-inner, '. $pop .' .floatton-inner label{ color: ##val##;}',
		        			'_floatton-styling-popup-link' 			=> $pop . ' .floatton-inner a{ color: ##val##;}',
		        			'_floatton-styling-popup-star' 			=> $pop . ' .floatton-inner .floatton-ratings .dashicons{ color: ##val##;}',
		        			'_floatton-styling-success-link' 		=> $success . ' a{ color: ##val##;}',
		        		);
	            $styling = '<style type="text/css">';

	            //use separate foreach to lessen/optimize text sizes for css codes

	            foreach ( $fields as $key => $value ) {
	            	//regular element styling
	            	if( isset( $value['regular'] ) ){
	            		$styling .= $value['container'] . '{';
	            		foreach ( $value['regular'] as $k => $v ) {
	            			if( isset( $meta[ $k ] ) && isset( $meta[ $k ][0] ) && !empty( $meta[ $k ][0] ) ){
	            				$styling .= str_replace( '##val##', $meta[ $k ][0], $v );
	            			}
	            		}
	            		$styling .= '}';
	            	}
	            	//on element hover
	            	if( isset( $value['hover'] ) ){
	            		$styling .= $value['hcontainer'] . '{';
	            		foreach ( $value['hover'] as $hk => $hv ) {
	            			if( isset( $meta[ $hk ] ) && isset( $meta[ $hk ][0] ) && !empty( $meta[ $hk ][0] ) ){
	            				$styling .= str_replace( '##val##', $meta[ $hk ][0], $hv );
	            			}
	            		}
	            		$styling .= '}';
	            	}
	            }
	            foreach ( $other as $ok => $ov ) {
	            	if( isset( $meta[ $ok ] ) && isset( $meta[ $ok ][0] ) && !empty( $meta[ $ok ][0] ) ){
	            		$styling .= str_replace( '##val##', $meta[ $ok ][0], $ov );
	            	}
	            }
	            $styling .= '</style>';

	          // Put the results in a transient. Expire after 4 weeks.
	          set_transient( 'floatton_styling_'. $id , $styling, 4 * WEEK_IN_SECONDS );
	        }

	        return $styling;
		}

		//create ajax request for cookies and fE-BE interaction
		function floatton_ajax(){
			if( !isset( $_POST['type'] ) || !isset( $_POST['id'] ) ) die();
			$type 	= $_POST['type'];
			$id 	= $_POST['id'];
			// $page 	= $_POST['page'];

			$loaded = ( isset( $_COOKIE['floatton_'. $id ] ) ) ? $_COOKIE['floatton_'. $id ] : '';

			switch ( $type ) {
				case 'onLoadOnce':
					if( empty( $loaded ) ){
						setcookie( 'floatton_'. $id , 1 , time()+(10*365*24*60*60),'/');
						echo $loaded;
					}
					break;

				default:
					# code...
					break;
			}

			die();
		}
	}
	new FLOATTON_FE();
}
?>
