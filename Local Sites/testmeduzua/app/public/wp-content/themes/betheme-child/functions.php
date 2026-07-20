<?php
/**
 * Betheme Child Theme functions and definitions.
 *
 * @package Betheme_Child
 * @author  Muffin Group / Arsen Kozak
 * @link    https://muffingroup.com
 */

// Prevent direct access to this file for security reasons.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Child Theme constants.
 *
 * WHITE_LABEL toggles Betheme's white-label mode.
 */
if ( ! defined( 'WHITE_LABEL' ) ) {
	define( 'WHITE_LABEL', true );
}

if ( ! function_exists( 'mfnch_enqueue_styles' ) ) {
	/**
	 * Enqueue Child Theme styles.
	 *
	 * Loads the RTL stylesheet when required and forces the child theme's
	 * style.css to load last (priority 101). Cache busting is handled
	 * automatically via the file modification time, so browsers always
	 * receive a fresh copy after each deploy without manual version bumps.
	 *
	 * @return void
	 */
	function mfnch_enqueue_styles() {
		// Load the parent RTL stylesheet only for right-to-left locales.
		if ( is_rtl() ) {
			$rtl_path = get_template_directory() . '/rtl.css';

			wp_enqueue_style(
				'mfn-rtl',
				get_template_directory_uri() . '/rtl.css',
				array(),
				file_exists( $rtl_path ) ? filemtime( $rtl_path ) : null
			);
		}

		// Dequeue the default handle to avoid duplicate/incorrect load order.
		wp_dequeue_style( 'style' );

		// Enqueue the child theme stylesheet with automatic cache busting.
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
	 * Hooked to 'after_setup_theme' so translation files are loaded at the
	 * correct point in the WordPress bootstrap sequence.
	 *
	 * @return void
	 */
	function mfnch_textdomain() {
		load_child_theme_textdomain( 'betheme', get_stylesheet_directory() . '/languages' );
		load_child_theme_textdomain( 'mfn-opts', get_stylesheet_directory() . '/languages' );
	}
}
add_action( 'after_setup_theme', 'mfnch_textdomain' );
