<?php
/*
 * Create Custom Post Type
 * and other post related functions
 */

if( !class_exists( 'FLOATTON_TYPES' ) ){
	class FLOATTON_TYPES
	{
		/*
		 * For easier overriding we declared the keys
		 * here as well as our tabs array which is populated
		 * when registering settings
		 */
		private $post_type 				= 'floatton';

		function __construct(){
			add_action( 'init', array( &$this, 'custom_post_type' ), 0 );
			add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
			add_action( 'save_post', array( &$this, 'save_meta_box_data' ) );
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue') );

			add_filter( 'post_updated_messages', array($this, 'update_messages') );
		}

		function enqueue(){
			global $post_type;
			if( $post_type == $this->post_type ){
				wp_enqueue_style( 'wp-color-picker' );
	        	wp_enqueue_script('wp-color-picker');
				wp_enqueue_style( 'floatton-admin-css', plugins_url( 'assets/css/floatton-admin.css' , dirname(__FILE__) ) , array(), null );

				wp_register_script(
					'admin-floatton',
					plugins_url( 'assets/js/jquery.floatton-admin.js' , dirname(__FILE__) ),
					array( 'jquery' ),
					'',
					true
				);

				wp_enqueue_script('admin-floatton');
			}
		}

		function custom_post_type() {
			$labels = array(
				'name'                => __( 'Floatton', 'floatton' ),
				'singular_name'       => __( 'Floatton', 'floatton' ),
				'menu_name'           => __( 'Floatton', 'floatton' ),
				'name_admin_bar'      => __( 'Floatton', 'floatton' ),
				'parent_item_colon'   => __( 'Parent Item:', 'floatton' ),
				'all_items'           => __( 'Floating Buttons', 'floatton' ),
				'add_new_item'        => __( 'Add New Floating Action Button', 'floatton' ),
				'add_new'             => __( 'Create Button', 'floatton' ),
				'new_item'            => __( 'New Button', 'floatton' ),
				'edit_item'           => __( 'Edit Button Details', 'floatton' ),
				'update_item'         => __( 'Update Button Details', 'floatton' ),
				'view_item'           => __( 'View Button', 'floatton' ),
				'search_items'        => __( 'Search Buttons', 'floatton' ),
				'not_found'           => sprintf( __( 'Whoops, looks like you haven\'t created any floating action button yet. <a href="%s">Create one now</a>!', 'floatton' ), admin_url( 'post-new.php?post_type=floatton' ) ),
				'not_found_in_trash'  => __( 'No items found in Trash.', 'floatton' ),
			);
			//add option to override
			$labels = apply_filters( 'floatton_post_type_labels', $labels );
			$args = array(
				'label'               => __( 'Floating Action Buttons', 'floatton' ),
				'description'         => __( 'Floating Action Buttons', 'floatton' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 54,
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
				'menu_icon'			  => 'dashicons-format-chat',
			);
			//add option to override
			$args = apply_filters( 'floatton_post_type_args', $args );

			register_post_type( $this->post_type, $args );

		}

		/**
		 * Adds the meta box container.
		 */
		public function add_meta_box( $post_type ) {

			//add button details metabox
			add_meta_box(
				'floatton_buttonmeta'
				, apply_filters( 'floatton-buttonmeta-title', __( 'Floating Button Details', 'floatton' ) )
				,array( &$this, 'metabox_button' )
				,$this->post_type
				,'normal'
				,'high'
			);

            //add content details metabox
            add_meta_box(
				'floatton_contentmeta'
				, apply_filters( 'floatton-contentmeta-title', __( 'Pop-up Content Details', 'floatton' ) )
				,array( &$this, 'metabox_content' )
				,$this->post_type
				,'normal'
				,'high'
			);

			//add success message metabox
			add_meta_box(
				'floatton_successmeta'
				, apply_filters( 'floatton-successmeta-title', __( 'Custom Success Message', 'floatton' ) )
				,array( &$this, 'metabox_success' )
				,$this->post_type
				,'normal'
				,'high'
			);

			//add styling metabox
			add_meta_box(
				'floatton_stylingmeta'
				, apply_filters( 'floatton-stylingmeta-title', __( 'Appearance & Styling', 'floatton' ) )
				,array( &$this, 'metabox_styling' )
				,$this->post_type
				,'normal'
				,'high'
			);

			//add visibility metabox
            add_meta_box(
				'floatton_visibilitymeta'
				, apply_filters( 'floatton-visibilitymeta-title', __( 'Visibility', 'floatton' ) )
				,array( &$this, 'metabox_visibility' )
				,$this->post_type
				,'side'
				,'default'
			);
		}

		/**
		 * Render Meta Box content.
		 *
		 * @param WP_Post $post The post object.
		 */
		function metabox_button( $post ){
			$dashicons = apply_filters('floatton_dashicons',array()); // REMOVE LATER
			$dashicons = apply_filters('floatton/dashicons',$dashicons);

			$btn_text 			= get_post_meta( $post->ID, '_floatton-btn-text', true );
			$btn_icon 			= get_post_meta( $post->ID, '_floatton-btn-icon', true );
			$btn_pos_left 		= get_post_meta( $post->ID, '_floatton-btn-pos-left', true );
			$btn_pos_right 		= get_post_meta( $post->ID, '_floatton-btn-pos-right', true );
			$btn_pos_type 		= get_post_meta( $post->ID, '_floatton-btn-pos-type', true );

			wp_nonce_field( 'floatton-content-metabox', 'floatton-dashboard-nonce' );
			?>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="floatton-btn-label"><?php _e( 'Label/Text','floatton' );?></label></th>
						<td>
							<input type="text" class="widefat" id="floatton-btn-label" name="floatton[btn-text]" value="<?php echo $btn_text;?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="floatton-btn-position"><?php _e( 'Position','floatton' );?></label></th>
						<td>
							<strong><?php _e( 'Left','floatton' );?></strong>:<input type="text" size="4" id="floatton-btn-label" name="floatton[btn-pos-left]" value="<?php echo $btn_pos_left;?>" />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<strong><?php _e( 'Right','floatton' );?></strong>:<input type="text" size="4" id="floatton-btn-label" name="floatton[btn-pos-right]" value="<?php echo $btn_pos_right;?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<strong><?php _e( 'Type','floatton' );?></strong>:
							<select name="floatton[btn-pos-type]">
								<option value="px"><?php _e( 'Pixels','floatton' );?></option>
								<option value="%" <?php if( '%' == $btn_pos_type ){ echo 'selected="selected"'; }?> ><?php _e( 'Percentage','floatton' );?></option>
							</select>
							<br />
							<small><?php _e( 'use <strong>auto</strong> or leave the field empty if your adjusting on one side only.','floatton' );?></small>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="floatton-btn-icons"><?php _e( 'Icon','floatton' );?></label></th>
						<td>
							<div class="floatton-icon-selection">
								<input type="radio" name="floatton[btn-icon]" id="floatton-no-icon" value="no-icon" <?php if( $btn_icon == 'no-icon' ){ echo 'checked="checked"'; }?>/>
								<label for="floatton-no-icon"><span class="dashicons floatton-no-icon <?php if( $btn_icon == 'no-icon' ){ echo 'floatton-selected-icon'; }?>"><?php _e( 'None','floatton' );?></span></label>
								<?php foreach ( $dashicons as $k => $v) { ?>
									<input type="radio" name="floatton[btn-icon]" id="floatton-<?php echo $v;?>" value="<?php echo $v;?>" <?php if( $btn_icon == $v ){ echo 'checked="checked"'; }?>/>
									<label for="floatton-<?php echo $v;?>"><span class="dashicons <?php echo $v;?> <?php if( $btn_icon == $v ){ echo 'floatton-selected-icon'; }?>"></span></label>
								<?php }?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		<?php }

		function metabox_content( $post ){
			$content_type 		= get_post_meta( $post->ID, '_floatton-content-type', true );
			$auto_open 			= get_post_meta( $post->ID, '_floatton-auto-open', true );
			$auto_open_value 	= get_post_meta( $post->ID, '_floatton-auto-open-value', true );
			$auto_open_type 	= get_post_meta( $post->ID, '_floatton-auto-open-type', true );
			$content_title 		= get_post_meta( $post->ID, '_floatton-content-title', true );
			$content_btn_text 	= get_post_meta( $post->ID, '_floatton-content-btn-text', true );
			$content 	 		= get_post_meta( $post->ID, '_floatton-content_custom', true );
			$notes_before 		= get_post_meta( $post->ID, '_floatton-content-notes-before', true );
			$notes_after 		= get_post_meta( $post->ID, '_floatton-content-notes-after', true );
			$target_url 		= get_post_meta( $post->ID, '_floatton-target-url', true );
			$target_new 		= get_post_meta( $post->ID, '_floatton-new-tab', true );
			?>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="floatton-content-type"><?php _e( 'Content Type','floatton' );?></label></th>
						<td colspan="2">
							<select class="widefat floatton-content-select" id="floatton-content-type" name="floatton[content-type]">
								<option value="comment" <?php echo ( $content_type == 'comment' ) ? 'selected="selected"' : '';?>><?php _e( 'Comment Form','floatton' );?></option>
								<option value="woo-rating" <?php echo ( $content_type == 'woo-rating' ) ? 'selected="selected"' : '';?>><?php _e( 'WooCommerce Product Rating Form','floatton' );?></option>
								<option value="custom" <?php echo ( $content_type == 'custom' ) ? 'selected="selected"' : '';?>><?php _e( 'Custom Content','floatton' );?></option>
								<option value="link" <?php echo ( $content_type == 'link' ) ? 'selected="selected"' : '';?>><?php _e( 'Custom Link','floatton' );?></option>
							</select>
						</td>
					</tr>
					<tr valign="top" class="floatton-open-row" <?php echo ( $content_type == 'link' ) ? 'style="display:none;"' : '';?> >
						<th scope="row"><label for="floatton-content-open"><?php _e( 'Automatically Open','floatton' );?></label></th>
						<td>
							<select class="widefat floatton-content-open" id="floatton-content-open" name="floatton[auto-open]">
								<option value="disabled" <?php echo ( $auto_open == 'disabled' ) ? 'selected="selected"' : '';?>><?php _e( 'Disabled','floatton' );?></option>
								<option value="onLoad" <?php echo ( $auto_open == 'onLoad' ) ? 'selected="selected"' : '';?>><?php _e( 'On Page Load','floatton' );?></option>
								<option value="onLoadOnce" <?php echo ( $auto_open == 'onLoadOnce' ) ? 'selected="selected"' : '';?>><?php _e( 'Only Once on Page Load via cookie','floatton' );?></option>
								<option value="onScroll" <?php echo ( $auto_open == 'onScroll' ) ? 'selected="selected"' : '';?>><?php _e( 'On User Scroll','floatton' );?></option>
								<option value="onScrollContent" <?php echo ( $auto_open == 'onScrollContent' ) ? 'selected="selected"' : '';?>><?php _e( 'User Finish Reading Post/Post Types','floatton' );?></option>
								<option value="onScrollBottom" <?php echo ( $auto_open == 'onScrollBottom' ) ? 'selected="selected"' : '';?>><?php _e( 'User Scrolls to Bottom of Page','floatton' );?></option>
							</select>
						</td>
						<td>
							<div class="floatton-content-open-opts <?php echo ( $auto_open == 'onScroll' ) ? 'floatton-show' : '';?>">
								<input type="text" size="5" name="floatton[auto-open-value]" value="<?php echo $auto_open_value;?>" />
								<select name="floatton[auto-open-type]" >
									<option value="px"><?php _e( 'Pixels','floatton' );?></option>
									<option value="%" <?php if( '%' == $auto_open_type ){ echo 'selected="selected"'; }?> ><?php _e( 'Percentage','floatton' );?></option>
								</select>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="floatton-content-default <?php echo ( $content_type == 'custom' || $content_type == 'link' ) ? 'floatton-hide' : 'floatton-show';?>">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="floatton-comment-title"><?php _e( 'Title','floatton' );?></label></th>
						<td>
							<input type="text" class="widefat" id="floatton-comment-title" name="floatton[content-title]" value="<?php echo $content_title;?>" placeholder="<?php _e( 'Default', 'floatton' );?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="floatton-comment-btn-text"><?php _e( 'Submit Button Text','floatton' );?></label></th>
						<td>
							<input type="text" class="widefat" id="floatton-comment-btn-text" name="floatton[content-btn-text]" value="<?php echo $content_btn_text;?>" placeholder="<?php _e( 'Default', 'floatton' );?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="floatton-comment-notes-before"><?php _e( 'Notes Before Fields','floatton' );?></label></th>
						<td>
							<textarea class="widefat" rows="4" id="floatton-comment-notes-before" name="floatton[content-notes-before]"><?php echo $notes_before;?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="floatton-comment-notes-after"><?php _e( 'Notes After Fields','floatton' );?></label></th>
						<td>
							<textarea class="widefat" rows="4" id="floatton-comment-notes-after" name="floatton[content-notes-after]"><?php echo $notes_after;?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			</div>
			<div class="floatton-custom-content <?php echo ( $content_type == 'custom' ) ? 'floatton-show' : '';?>">
				<?php
					$settings = array(
					    'textarea_name' =>	'floatton[content_custom]',
					    'media_buttons' =>	true,
					    'teeny'			=>	false,
					    'editor_height' => 	260
					);
					wp_editor( html_entity_decode(stripcslashes($content)) , 'floatton_content_custom', $settings );
				?>
			</div>

			<div class="floatton-content-link <?php echo ( $content_type == 'link' ) ? 'floatton-show' : 'floatton-hide';?>">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label for="floatton-link-url"><?php _e( 'Target URL','floatton' );?></label></th>
							<td>
								<input type="text" class="widefat" id="floatton-link-url" name="floatton[target-url]" value="<?php echo $target_url;?>" placeholder="<?php _e( 'http://', 'floatton' );?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="floatton-link-target"><?php _e( 'Open to new tab','floatton' );?></label></th>
							<td>
								<input type="checkbox" id="floatton-link-target" name="floatton[new-tab]" value="1" <?php echo ( $target_new == '1' ) ? 'checked="checked"' : '';?> />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php }

		function metabox_visibility( $post ){
			$fields = $this->visibility_fields();

			if( isset( $fields['post_types'] ) && !empty( $fields['post_types'] ) ){ ?>
				<table class="form-table floatton-visibility-meta">
					<tbody>
					<tr valign="top">
						<th scope="row" colspan="2"><strong><?php _e( 'Post Types', 'floatton' );?></strong></th>
					</tr>
				<?php foreach ( $fields['post_types'] as $post_type ) {
					if( $post_type->name == 'attachment' ){ continue; }
					$post_meta 	= get_post_meta( $post->ID, '_floatton-types-' . $post_type->name, true );
				?>
						<tr valign="top">
							<th scope="row"><label for="floatton-ptype-<?php echo $post_type->name;?>"><?php echo $post_type->label;?></label></th>
							<td>
								<input type="checkbox" name="floatton_types[type-<?php echo $post_type->name;?>]" id="floatton-ptype-<?php echo $post_type->name;?>" value="1" <?php echo ( $post_meta == '1' ) ? 'checked="checked"' : ''; ?> />
							</td>
						</tr>
					<?php
				} ?>
					</tbody>
				</table>
				<br />
			<?php }

			$post_ids = get_post_meta( $post->ID ,'_floatton-post-ids', true );
			?>
			<table class="form-table floatton-visibility-meta">
				<tbody>
					<tr valign="top">
						<th scope="row" colspan="2"><strong><?php _e( 'Post IDs', 'floatton' );?></strong></th>
					</tr>
					<tr valign="top">
						<td colspan="2">
							<input type="text" class="widefat" name="floatton_ids" value="<?php echo $post_ids;?>" /><br />
							<small><?php _e( 'Comma separated post, pages or post types ID you want the button to be displayed.', 'floatton' );?></small><br />
							<small><?php _e( '<strong>Note: </strong>You have to check post type above which the ID belong.', 'floatton' );?></small>
						</td>
					</tr>
				</tbody>
			</table><br />

			<?php if( isset( $fields['taxonomies'] ) && !empty( $fields['taxonomies'] ) ){ ?>
				<table class="form-table floatton-visibility-meta">
					<tbody>
						<tr valign="top">
							<th scope="row" colspan="2"><strong><?php _e( 'Taxonomies Archive Pages', 'floatton' );?></strong></th>
						</tr>
						<?php foreach ( $fields['taxonomies'] as $taxonomy ) {
							$tax_meta 	= get_post_meta( $post->ID, '_floatton-tax-' . $taxonomy->name, true ); ?>
							<tr valign="top">
								<th scope="row"><label for="floatton-tax-<?php echo $taxonomy->name;?>"><?php echo $taxonomy->label;?></label> <?php if( isset( $taxonomy->object_type ) && isset( $taxonomy->object_type[0] ) ){ echo ' <small>- '. $taxonomy->object_type[0] .'</small>'; } ?> </th>
								<td>
									<input type="checkbox" name="floatton_taxonomy[type-<?php echo $taxonomy->name;?>]" id="floatton-tax-<?php echo $taxonomy->name;?>" value="1" <?php echo ( $tax_meta == '1' ) ? 'checked="checked"' : ''; ?> />
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table><br />
			<?php }

			if( isset( $fields['misc'] ) && !empty( $fields['misc'] ) ){ ?>
				<table class="form-table floatton-visibility-meta">
					<tbody>
						<tr valign="top">
							<th scope="row" colspan="2"><strong><?php _e( 'Miscellaneous', 'floatton' );?></strong></th>
						</tr>
						<?php foreach ( $fields['misc'] as $m_key => $m_value ) {
							$misc_meta 	= get_post_meta( $post->ID, '_floatton-misc-' . $m_key, true ); ?>
							<tr valign="top">
								<th scope="row"><label for="floatton-misc-<?php echo $m_key;?>"><?php echo $m_value;?></label> </th>
								<td>
									<input type="checkbox" name="floatton_misc[misc-<?php echo $m_key;?>]" id="floatton-misc-<?php echo $m_key;?>" value="1" <?php echo ( $misc_meta == '1' ) ? 'checked="checked"' : ''; ?> />
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php }

			if( isset( $fields['permission'] ) && !empty( $fields['permission'] ) ){ ?>
				<table class="form-table floatton-visibility-meta">
					<tbody>
						<tr valign="top">
							<th scope="row" colspan="2">
								<strong><?php _e( 'Hide On Users', 'floatton' );?></strong><br >
								<small><?php _e( 'By Default the Floating Buttons will be visibile to any users, check the option below to hide it on guests or logged in users.', 'floatton' );?></small>
							</th>
						</tr>
						<?php foreach ( $fields['permission'] as $p_key => $p_value ) {
							$p_meta 	= get_post_meta( $post->ID, '_floatton-permission-' . $p_key, true ); ?>
							<tr valign="top">
								<th scope="row"><label for="floatton-permission-<?php echo $p_key;?>"><?php echo $p_value;?></label> </th>
								<td>
									<input type="checkbox" name="floatton_permission[permission-<?php echo $p_key;?>]" id="floatton-permission-<?php echo $p_key;?>" value="1" <?php echo ( $p_meta == '1' ) ? 'checked="checked"' : ''; ?> />
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php }

			if( isset( $fields['devices'] ) && !empty( $fields['devices'] ) ){ ?>
				<table class="form-table floatton-visibility-meta">
					<tbody>
						<tr valign="top">
							<th scope="row" colspan="2">
								<strong><?php _e( 'Hide On Devices', 'floatton' );?></strong><br >
								<small><?php _e( 'By Default the Floating Buttons will be visibile all devices. Use option below to hide them.', 'floatton' );?></small>
							</th>
						</tr>
						<?php foreach ( $fields['devices'] as $d_key => $d_value ) {
							$p_meta 	= get_post_meta( $post->ID, '_floatton-devices-' . $d_key, true ); ?>
							<tr valign="top">
								<th scope="row"><label for="floatton-devices-<?php echo $d_key;?>"><?php echo $d_value;?></label> </th>
								<td>
									<input type="checkbox" name="floatton_devices[devices-<?php echo $d_key;?>]" id="floatton-devices-<?php echo $d_key;?>" value="1" <?php echo ( $p_meta == '1' ) ? 'checked="checked"' : ''; ?> />
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php }
		}

		function metabox_success( $post ){
			$content  = get_post_meta( $post->ID, '_floatton-success_message', true );
			$settings = array(
					    'textarea_name' =>	'floatton[success_message]',
					    'media_buttons' =>	true,
					    'teeny'			=>	false,
					    'editor_height' => 	260
					);
			wp_editor( html_entity_decode(stripcslashes($content)) , 'floatton_success_msg', $settings );
			_e('<p><strong>Note:</strong> Custom Success Message will not work with gravity forms since it already has custom message after submission. Thanks!</p>');
		}

		function metabox_styling( $post ){
			$btn = array(
					'button' 	=> array(
										'label' 	=> __( 'Background Color', 'floatton' ),
										'regular' 	=> get_post_meta( $post->ID, '_floatton-styling-button-regular', true ),
										'hover' 	=> get_post_meta( $post->ID, '_floatton-styling-button-hover', true ),
									),
					'text' 		=> array(
										'label' 	=> __( 'Text Color', 'floatton' ),
										'regular' 	=> get_post_meta( $post->ID, '_floatton-styling-text-regular', true ),
										'hover' 	=> get_post_meta( $post->ID, '_floatton-styling-text-hover', true ),
									),
					'icon' 		=> array(
										'label' 	=> __( 'Icon Color', 'floatton' ),
										'regular' 	=> get_post_meta( $post->ID, '_floatton-styling-icon-regular', true ),
										'hover' 	=> get_post_meta( $post->ID, '_floatton-styling-icon-hover', true ),
									),
				);

			$popup = array(
					'border' 	=> array(
										'label' 	=> __( 'Border Color', 'floatton' ),
										'value' 	=> get_post_meta( $post->ID, '_floatton-styling-popup-border', true )
									),
					'background' 	=> array(
										'label' 	=> __( 'Background Color', 'floatton' ),
										'value' 	=> get_post_meta( $post->ID, '_floatton-styling-popup-background', true )
									),
					'text' 			=> array(
										'label' 	=> __( 'Text Color', 'floatton' ),
										'value' 	=> get_post_meta( $post->ID, '_floatton-styling-popup-text', true )
									),
					'link' 			=> array(
										'label' 	=> __( 'Link Color', 'floatton' ),
										'value' 	=> get_post_meta( $post->ID, '_floatton-styling-popup-link', true )
									),
					'star' 			=> array(
										'label' 	=> __( 'Star Rating Color', 'floatton' ),
										'value' 	=> get_post_meta( $post->ID, '_floatton-styling-popup-star', true )
									)
					);

			$form = array(
					'fields-color' 	=> array(
										'label' 	=> __( 'Input/Textarea Text Color', 'floatton' ),
										'regular' 	=> get_post_meta( $post->ID, '_floatton-styling-fields-color-regular', true ),
										'hover' 	=> get_post_meta( $post->ID, '_floatton-styling-fields-color-hover', true ),
									),
					'fields' 	=> array(
										'label' 	=> __( 'Input/Textarea Border Color', 'floatton' ),
										'regular' 	=> get_post_meta( $post->ID, '_floatton-styling-fields-regular', true ),
										'hover' 	=> get_post_meta( $post->ID, '_floatton-styling-fields-hover', true ),
									),
					'submit' 	=> array(
										'label' 	=> __( 'Submit Button Text Color', 'floatton' ),
										'regular' 	=> get_post_meta( $post->ID, '_floatton-styling-submit-regular', true ),
										'hover' 	=> get_post_meta( $post->ID, '_floatton-styling-submit-hover', true ),
									),
					'submitbg' 	=> array(
										'label' 	=> __( 'Submit Button Background Color', 'floatton' ),
										'regular' 	=> get_post_meta( $post->ID, '_floatton-styling-submitbg-regular', true ),
										'hover' 	=> get_post_meta( $post->ID, '_floatton-styling-submitbg-hover', true ),
									)
				);
			$success = array(
					'background' 	=> array(
										'label' 	=> __( 'Background Color', 'floatton' ),
										'value' 	=> get_post_meta( $post->ID, '_floatton-styling-success-background', true )
									),
					'text' 			=> array(
										'label' 	=> __( 'Text Color', 'floatton' ),
										'value' 	=> get_post_meta( $post->ID, '_floatton-styling-success-text', true )
									),
					'link' 			=> array(
										'label' 	=> __( 'Link Color', 'floatton' ),
										'value' 	=> get_post_meta( $post->ID, '_floatton-styling-success-link', true )
									)
					);

			?>
			<table class="form-table floatton-styling-meta">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<strong><?php _e( 'Floating Button', 'floatton' );?></strong>
						</th>
						<td><?php _e( 'Regular', 'floatton' );?></td>
						<td><?php _e( 'Hover/Mouseover', 'floatton' );?></td>
					</tr>
					<?php foreach ( $btn as $key => $value ) { ?>
						<tr valign="top">
							<th scope="row"><label for="floatton-styling-btn-regular"><?php echo $value['label'];?></label> </th>
							<td>
								<input type="text" name="floatton_styling[<?php echo $key;?>-regular]" class="floatton_colorpicker" id="floatton-styling-btn-<?php echo $key;?>-regular" value="<?php echo $value['regular'];?>" />
							</td>
							<td>
								<input type="text" name="floatton_styling[<?php echo $key;?>-hover]" class="floatton_colorpicker" id="floatton-styling-btn-<?php echo $key;?>-hover" value="<?php echo $value['hover'];?>" />
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<hr />
			<h3><?php _e( 'Pop-up Content', 'floatton' );?></h3>
			<table class="form-table floatton-styling-meta">
				<tbody>
					<?php foreach ( $popup as $k => $v ) { ?>
						<tr valign="top">
							<th scope="row"><label for="floatton-styling-popup-<?php echo $k;?>"><?php echo $v['label'];?></label> </th>
							<td>
								<input type="text" name="floatton_styling[popup-<?php echo $k;?>]" class="floatton_colorpicker" id="floatton-styling-<?php echo $k;?>" value="<?php echo $v['value'];?>" />
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<table class="form-table floatton-styling-meta">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<strong><?php _e( 'Form Elements', 'floatton' );?></strong>
						</th>
						<td><?php _e( 'Regular', 'floatton' );?></td>
						<td><?php _e( 'Hover/Focus', 'floatton' );?></td>
					</tr>
					<?php foreach ( $form as $fk => $fv ) { ?>
						<tr valign="top">
							<th scope="row"><label for="floatton-styling-form-regular"><?php echo $fv['label'];?></label> </th>
							<td>
								<input type="text" name="floatton_styling[<?php echo $fk;?>-regular]" class="floatton_colorpicker" id="floatton-styling-form-<?php echo $fk;?>-regular" value="<?php echo $fv['regular'];?>" />
							</td>
							<td>
								<input type="text" name="floatton_styling[<?php echo $fk;?>-hover]" class="floatton_colorpicker" id="floatton-styling-form-<?php echo $fk;?>-hover" value="<?php echo $fv['hover'];?>" />
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<h3><?php _e( 'Form Success Content', 'floatton' );?></h3>
			<table class="form-table floatton-styling-meta">
				<tbody>
					<?php foreach ( $success as $s_k => $s_v ) { ?>
						<tr valign="top">
							<th scope="row"><label for="floatton-styling-success-<?php echo $s_k;?>"><?php echo $s_v['label'];?></label> </th>
							<td>
								<input type="text" name="floatton_styling[success-<?php echo $s_k;?>]" class="floatton_colorpicker" id="floatton-styling-<?php echo $s_k;?>" value="<?php echo $s_v['value'];?>" />
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php }

		function update_messages( $messages ){
			$messages['floatton'][1] = __( 'Floating Action Button Successfully Updated.', 'floatton' );
			$messages['floatton'][6] = __( 'Floating Action Button Successfully Created.', 'floatton' );
			return $messages;
		}

		/**
		 * When the post is saved, saves our custom data.
		 *
		 * @param int $post_id The ID of the post being saved.
		 */
		function save_meta_box_data( $post_id ){
			/*
			 * We need to verify this came from our screen and with proper authorization,
			 * because the save_post action can be triggered at other times.
			 */


			// Check if our nonce is set.
			if ( ! isset( $_POST['floatton-dashboard-nonce'] ) ) {
				return;
			}

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['floatton-dashboard-nonce'], 'floatton-content-metabox' ) ) {
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Check the user's permissions.
			if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}

			} else {

				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}

			/* OK, it's safe for us to save the data now. */

			// Make sure that it is set.
			if ( isset( $_POST['floatton'] ) ) {
				$floatton 	= $_POST['floatton'];
				if( is_array( $floatton ) && !empty( $floatton ) ){
					foreach ( $floatton  as $key => $value ) {
						update_post_meta( $post_id, '_floatton-' . $key, $value );
					}

					if( !isset( $floatton['new-tab'] ) ){
						update_post_meta( $post_id, '_floatton-new-tab', '' );
					}
				}
			}

			$fields = $this->visibility_fields();

			//save post types visibility
			$visibility 	= ( isset( $_POST['floatton_types'] ) ) ? $_POST['floatton_types'] : array();
			if( isset( $fields['post_types'] ) && !empty( $fields['post_types'] ) ){
				foreach ( $fields['post_types'] as $post_type ) {
					if( isset( $visibility[ 'type-' . $post_type->name ] ) && $visibility[ 'type-' . $post_type->name ] == '1' ){
						update_post_meta( $post_id, '_floatton-types-' . $post_type->name , 1 );
					}else{
						delete_post_meta( $post_id, '_floatton-types-' . $post_type->name  );
					}
				}
			}

			//save taxonomy visibility
			$tax_visibility 	= ( isset( $_POST['floatton_taxonomy'] ) ) ? $_POST['floatton_taxonomy'] : array();
			if( isset( $fields['taxonomies'] ) && !empty( $fields['taxonomies'] ) ){
				foreach ( $fields['taxonomies'] as $taxonomy ) {
					if( isset( $tax_visibility[ 'type-' . $taxonomy->name ] ) && $tax_visibility[ 'type-' . $taxonomy->name ] == '1' ){
						update_post_meta( $post_id, '_floatton-tax-' . $taxonomy->name , 1 );
					}else{
						delete_post_meta( $post_id, '_floatton-tax-' . $taxonomy->name  );
					}
				}
			}

			//save misc pages
			$misc_visibility 	= ( isset( $_POST['floatton_misc'] ) ) ? $_POST['floatton_misc'] : array();
			if( isset( $fields['misc'] ) && !empty( $fields['misc'] ) ){
				foreach ( $fields['misc'] as $m_key => $m_value ) {
					if( isset( $misc_visibility[ 'misc-' . $m_key ] ) && $misc_visibility[ 'misc-' . $m_key ] == '1' ){
						update_post_meta( $post_id, '_floatton-misc-' . $m_key , 1 );
					}else{
						delete_post_meta( $post_id, '_floatton-misc-' . $m_key  );
					}
				}
			}

			//save permission
			$misc_permission 	= ( isset( $_POST['floatton_permission'] ) ) ? $_POST['floatton_permission'] : array();
			if( isset( $fields['permission'] ) && !empty( $fields['permission'] ) ){
				foreach ( $fields['permission'] as $p_key => $p_value ) {
					if( isset( $misc_permission[ 'permission-' . $p_key ] ) && $misc_permission[ 'permission-' . $p_key ] == '1' ){
						update_post_meta( $post_id, '_floatton-permission-' . $p_key , 1 );
					}else{
						delete_post_meta( $post_id, '_floatton-permission-' . $p_key  );
					}
				}
			}

			//save device visibility
			$devices 	= ( isset( $_POST['floatton_devices'] ) ) ? $_POST['floatton_devices'] : array();
			if( isset( $fields['devices'] ) && !empty( $fields['devices'] ) ){
				foreach ( $fields['devices'] as $d_key => $d_value ) {
					if( isset( $devices[ 'devices-' . $d_key ] ) && $devices[ 'devices-' . $d_key ] == '1' ){
						update_post_meta( $post_id, '_floatton-devices-' . $d_key , 1 );
					}else{
						delete_post_meta( $post_id, '_floatton-devices-' . $d_key  );
					}
				}
			}

			//styling
			if ( isset( $_POST['floatton_styling'] ) ) {
				$styling 	= $_POST['floatton_styling'];
				if( is_array( $styling ) && !empty( $styling ) ){
					foreach ( $styling  as $s_key => $s_value ) {
						update_post_meta( $post_id, '_floatton-styling-' . $s_key, $s_value );
					}
				}
			}

			//save post IDs
			if( isset( $_POST['floatton_ids'] ) && !empty( $_POST['floatton_ids'] ) ){
				$postids = sanitize_text_field( $_POST['floatton_ids'] );
				update_post_meta( $post_id, '_floatton-post-ids', $postids );
			}

			//remove transient saved
			delete_transient( 'floatton_meta_' . $post_id );
			delete_transient( 'floatton_styling_' . $post_id );

		}

		function visibility_fields(){
			$fields = array();

			//declare miscellaneous pages - wordpress default pages
	        $fields['misc']   = array(
                        'home'      =>  __( 'Home/Front', 'floatton' ),
                        'blog'      =>  __( 'Blog', 'floatton' ),
                        'archives'  =>  __( 'Archives', 'floatton' ),
                        '404'       =>  __( '404', 'floatton' ),
                        'search'    =>  __( 'Search', 'floatton' )
                    );

	        $fields['permission']   = array(
                        'guest'      	=>  __( 'Guest Users', 'floatton' ),
                        'loggedin'      =>  __( 'Logged-in Users', 'floatton' ),
                    );

	        $fields['devices']   = array(
                        'mobile'      	=>  __( 'Mobile', 'floatton' ),
                        'tablet'      	=>  __( 'Tablet', 'floatton' ),
                        'desktop'      	=>  __( 'Desktop', 'floatton' ),
                    );

			//get available post types
			$get_cpt_args = array(
				'public'   => true,
				// '_builtin' => false
			);
			$fields['post_types'] = get_post_types( $get_cpt_args, 'objects', 'and');

			//get all taxonomies
			$tax_args = array(
              'public'   => true
            );
            $tax_output 	= 'objects'; // or objects
            $tax_operator 	= 'and'; // 'and' or 'or'
            $taxonomies 	= get_taxonomies( $tax_args, $tax_output, $tax_operator );
            unset( $taxonomies['post_format'] );
            $fields['taxonomies'] = $taxonomies;

			return $fields;
		}

	}
	new FLOATTON_TYPES();
}
?>
