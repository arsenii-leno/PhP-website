<?php

/*
  Plugin Name: Floatton by Phpbits
  Plugin URI: http://floatton.com/
  Description: Floating Action Button with Pop-up Contents for WordPress.
  Author: Phpbits Creative Studio
  Version: 2.0
  Author URI: https://phpbits.net/

  Text Domain: floatton
 */

//avoid direct calls to this file

if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
define( 'FLOATTON_VERSION', '2.0' );

/*##################################
  REQUIRE
################################## */
require_once( dirname( __FILE__ ) . '/core/functions.dashicons.php' );
require_once( dirname( __FILE__ ) . '/core/functions.display.php' );
require_once( dirname( __FILE__ ) . '/core/functions.post-type.php' );
require_once( dirname( __FILE__ ) . '/core/functions.screen.php' );

?>
