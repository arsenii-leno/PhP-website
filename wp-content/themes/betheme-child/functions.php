<?php
/**
 * Betheme Child Theme functions and definitions.
 *
 * @package Betheme_Child
 * @author  Muffin Group / Arsen Kozak
 * @link    https://muffingroup.com
 */

// Prevent direct access to the file for security reasons.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Child Theme constants.
 * Toggle Betheme's white-label mode.
 */
if ( ! defined( 'WHITE_LABEL' ) ) {
    define( 'WHITE_LABEL', true );
}

if ( ! function_exists( 'mfnch_enqueue_styles' ) ) {
    /**
     * Enqueue Child Theme styles.
     *
     * Handles RTL stylesheet loading and forces the child theme's style.css
     * to load last (priority 101). Cache busting is handled automatically via filemtime.
     *
     * @return void
     */
    function mfnch_enqueue_styles() {
        if ( is_rtl() ) {
            $rtl_path = get_template_directory() . '/rtl.css';

            wp_enqueue_style(
                'mfn-rtl',
                get_template_directory_uri() . '/rtl.css',
                array(),
                file_exists( $rtl_path ) ? filemtime( $rtl_path ) : null
            );
        }

        // Dequeue default handle to prevent duplicate or incorrect load order.
        wp_dequeue_style( 'style' );

        $style_path = get_stylesheet_directory() . '/style.css';

        wp_enqueue_style(
            'betheme-child-style',
            get_stylesheet_directory_uri() . '/style.css',
            array(),
            file_exists( $style_path ) ? filemtime( $style_path ) : null
        );
    }
}
add_action( 'wp_enqueue_scripts', 'mfnch_enqueue_styles', 101 );

if ( ! function_exists( 'mfnch_textdomain' ) ) {
    /**
     * Load Child Theme textdomains for translations.
     *
     * Hooked to 'after_setup_theme' to ensure translation assets are available
     * at the correct state of the WordPress bootstrap sequence.
     *
     * @return void
     */
    function mfnch_textdomain() {
        load_child_theme_textdomain( 'betheme', get_stylesheet_directory() . '/languages' );
        load_child_theme_textdomain( 'mfn-opts', get_stylesheet_directory() . '/languages' );
    }
}
add_action( 'after_setup_theme', 'mfnch_textdomain' );

if ( ! function_exists( 'med_add_continue_shopping_button' ) ) {
    /**
     * Inject a custom "Continue Shopping" action button into the cart page layout.
     *
     * @return void
     */
    function med_add_continue_shopping_button() {
        $shop_page_url = wc_get_page_permalink( 'shop' );
        
        // Fail-safe check if the shop page is unassigned in WooCommerce settings.
        if ( ! $shop_page_url ) {
            return; 
        }
        
        printf(
            '<a href="%1$s" class="button med-continue-shopping" style="margin-right: 10px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>%2$s</span>
            </a>',
            esc_url( $shop_page_url ),
            esc_html__( 'Продовжити покупки', 'woocommerce' )
        );
    }
}
add_action( 'woocommerce_cart_actions', 'med_add_continue_shopping_button', 20 );

if ( ! function_exists( 'betheme_child_enqueue_custom_scripts' ) ) {
    /**
     * Enqueue custom theme scripts.
     *
     * Registers and enqueues the child theme's JavaScript bundle.
     * Uses automated file modification time for dynamic asset cache-busting.
     *
     * @return void
     */
    function betheme_child_enqueue_custom_scripts() {
        $script_path = get_stylesheet_directory() . '/custom-scripts.js';
        $version     = file_exists( $script_path ) ? filemtime( $script_path ) : '1.0.0';

        wp_enqueue_script(
            'betheme-child-custom-js',
            get_stylesheet_directory_uri() . '/custom-scripts.js',
            array( 'jquery' ),
            $version,
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'betheme_child_enqueue_custom_scripts' );

// Відключаємо застарілий smoothscroll.js для усунення попереджень у консолі
add_action( 'wp_enqueue_scripts', 'dequeue_smooth_scroll', 100 );
function dequeue_smooth_scroll() {
    wp_dequeue_script( 'smoothscroll' ); // або назва хендлу, який використовує тема
    wp_deregister_script( 'smoothscroll' );
}

// functions.php у Betheme Child Theme
function my_child_theme_enqueue_styles() {
    // Основний стиль батьківської теми (Betheme)
    wp_enqueue_style( 'betheme-style', get_template_directory_uri() . '/style.css' );
    
    // Стилі дочірньої теми з автоматичним версіонуванням
    $child_css_version = filemtime( get_stylesheet_directory() . '/style.css' );
    wp_enqueue_style( 'child-style', 
        get_stylesheet_directory_uri() . '/style.css', 
        array('betheme-style'), 
        $child_css_version 
    );
}
add_action( 'wp_enqueue_scripts', 'my_child_theme_enqueue_styles', 100 );
