<?php
/*
Plugin Name: HotelsPro Integration
Plugin URI: https://github.com/Ashik72
Description: HotelsPro Integration Plugin
Version: 0.0.1
Author: Ashik72
Author URI: https://www.upwork.com/freelancers/~01353e37a21e977904
License: GPLv2 or later
Text Domain: hotels_pro_fw
*/

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

if (file_exists(__DIR__ . '/vendor/autoload.php'))
  require __DIR__ . '/vendor/autoload.php';

  if (!function_exists('d')) {

  	function d($data) {

  		ob_start();
  		var_dump($data);
  		$output = ob_get_clean();
  		echo $output;
  	}
  }


	define( 'hotels_pro_fw_PLUGIN_DIR', dirname( __FILE__ ).DIRECTORY_SEPARATOR );
  define( 'hotels_pro_fw_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

  if ( ! defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

  if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

  	// Brace Yourself
  	require_once( plugin_dir_path( __FILE__ ) . 'plugin_loader.php' );

  	// Start the Engine
  	//add_action( 'plugins_loaded', array( 'MYPLUG', 'get_instance' ) );

  }
