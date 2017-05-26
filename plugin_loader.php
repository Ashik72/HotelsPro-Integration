<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));
  require_once( plugin_dir_path( __FILE__ ) . '/inc/class.db.php' );
  require_once( plugin_dir_path( __FILE__ ) . '/inc/admin/class.options.php' );
  require_once( plugin_dir_path( __FILE__ ) . '/inc/class.static_data.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/inc/admin/class.options_booking.php' );

  require_once( 'titan-framework-checker.php' );
  require_once( 'titan-framework-options.php' );

	require_once( plugin_dir_path( __FILE__ ) . '/inc/class.hotel_booking.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/inc/class.hotel_search_result.php' );

  add_action( 'plugins_loaded', function () {
  	HP_Static_Data::get_instance();

		HotelBooking::get_instance();

  } );

 ?>
