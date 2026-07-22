<?php
/*
 * Create Welcome Screen
 */

if( !class_exists( 'FLOATTON_SCREEN' ) ){
	class FLOATTON_SCREEN{

		public function __construct() {
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue') );
			add_action( 'admin_menu', array($this, 'screen_page') );
			add_action('activated_plugin', array($this, 'redirect'));
			add_action('admin_head', array($this, 'remove_menu'));
			// add_filter( 'admin_footer_text', array( $this, 'admin_footer'   ), 1, 2 );
		}

		function enqueue(){
			if ( !isset( $_GET['page'] ) || 'floatton-getting-started' != $_GET['page'] )
			return;
			
			wp_enqueue_style( 'floatton-welcome', plugins_url( 'assets/css/welcome.css' , dirname(__FILE__) ) , array(), null );
		}

		function screen_page(){
			add_dashboard_page(
				__( 'Getting started with Floatton', 'floatton' ),
				__( 'Getting started with Floatton', 'floatton' ),
				apply_filters( 'floatton_welcome_cap', 'manage_options' ),
				'floatton-getting-started',
				array( $this, 'welcome_content' )
			);
		}

		function welcome_head(){
			$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'floatton-getting-started';
			?>
			<h1><?php _e( 'Welcome to Floatton', 'floatton' ); ?></h1>
			<div class="about-text">
				<?php _e( 'Thank you for choosing Floatton - your all in one plugin on creating Sticky Floating Action Buttons with Custom Pop up Contents.', 'floatton' ); ?>
			</div>
			<div class="floatton-badge">
				<span class="dashicons dashicons-format-chat"></span>
				<span class="version"><?php _e( 'Version', 'floatton' );?> <?php echo FLOATTON_VERSION; ?></span>
			</div>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php echo $selected == 'floatton-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'floatton-getting-started' ), 'index.php' ) ) ); ?>">
					<?php _e( 'Getting Started', 'floatton' ); ?>
				</a>
			</h2>
			<?php
		}

		function welcome_content(){ ?>
			<div class="wrap about-wrap">
				<?php $this->welcome_head(); ?>
				<p class="about-description">
					<?php _e( 'Use the tips below to get started then you will be up and running in no time. ', 'floatton' ); ?>
				</p>

				<div class="feature-section two-col">	
					<div class="col">
						<h3><?php _e( 'Creating Your First Floating Action Button' , 'floatton' ); ?></h3>
						<p><?php printf( __( 'Floatton makes it very easy for you to create floating action buttons for your WordPress site. You can follow the video tutorial on the right or read our how to <a href="%s" target="_blank">create your first floating action button guide</a>.', 'floatton' ), 'http://floatton.com/create-first-wordpress-floating-action-button/' ); ?>
						<p><?php printf( __( 'But in reality, the process is so intuitive that you can just start by going to <a href="%s">Floatton &#x2192; Create Button</a>.', 'floatton' ), admin_url( 'post-new.php?post_type=floatton' ) ); ?>
					</div>
					<div class="col">
						<div class="feature-video">
							<iframe width="495" height="278" src="https://www.youtube-nocookie.com/embed/zBq_wKo1P4s?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
						</div>
					</div>
				</div>

				<div class="feature-section two-col">	
					<div class="col">
						<h3><?php _e( 'See all Floatton Features', 'floatton' ); ?></h3>
						<p><?php _e( 'Floatton is both easy to use and extremely powerful. We have tons of helpful features that will give you additional function on your websites by adding floating action button with slick pop-up contents.', 'floatton' ); ?></p>
						<p><a href="http://floatton.com/" target="_blank" class="floatton-features-button button button-primary"><?php _e( 'See all Features', 'floatton' ); ?></a></p>
					</div>
					<div class="col floatton-features">
						<img src="<?php echo plugins_url('/assets/images/welcome.png', dirname(__FILE__) )?>" title="<?php _e( 'floatton Features Banner', 'floatton' );?>" />
					</div>
				</div>
			</div>
		<?php }

		function redirect($plugin){
			if($plugin=='floatton/plugin.php') {
				wp_redirect(admin_url('index.php?page=floatton-getting-started'));
				die();
			}
		}
		function remove_menu(){
		    remove_submenu_page( 'index.php', 'floatton-getting-started' );
		}

		function admin_footer( $text ) {

			global $current_screen;
			if ( !empty( $current_screen->id ) && strpos( $current_screen->id, 'floatton' ) !== false ) {
				$url  = 'https://codecanyon.net/downloads/';
				$text = sprintf( __( 'Please rate <strong>Floatton</strong> <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%s" target="_blank">Codecanyon.net</a> to help us spread the word. Thank you from Phpbits Creative Studio!', 'floatton' ), $url, $url );
			}
			return $text;
		}
	}

	new FLOATTON_SCREEN();
}
?>