<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

/**
 * BookingOptions
 */
class BookingOptions
{

  function __construct()
  {
    # code...
  }

  public static function doOptions($section = "") {

    if (empty($section)) return;

    $tab = $section->createTab( array(
          'name' => __( 'API Config', 'hotels_pro_fw' ),
      ) );

      $tab->createOption( array(
      'name' => 'API URL',
      'id' => 'booking_api_url',
      'type' => 'text',
      'desc' => 'Booking API URL',
      'default' => 'https://api-test.hotelspro.com/api/v2'
      ) );


      $tab->createOption( array(
      'name' => 'Enable Live Environment',
      'id' => 'is_booking_api_live',
      'type' => 'enable',
      'default' => false,
      'desc' => 'Enable or disable Live Environment',
      ) );


      $tab->createOption( array(
      'name' => 'API Username',
      'id' => 'booking_api_user',
      'type' => 'text',
      'desc' => 'Booking API Username'
      ) );

      $tab->createOption( array(
      'name' => 'API Password',
      'id' => 'booking_api_password',
      'type' => 'text',
      'desc' => 'Booking API Password',
      'is_password' => 1
      ) );


      $tab = $section->createTab( array(
            'name' => __( 'Data Load', 'hotels_pro_fw' ),
        ) );


        $tab->createOption( array(
        'name' => 'Destination Table',
        'id' => 'load_destination_table',
        'type' => 'text',
        'default' => 'destinations',
        'desc' => 'Desitination Table of Database'
        ) );

        $tab->createOption( array(
        'name' => 'Country Codes Table',
        'id' => 'country_code_table',
        'type' => 'text',
        'default' => 'countries',
        'desc' => 'Country Codes Table of Database'
        ) );


				$tab->createOption( array(
        'name' => 'Hotels Table',
        'id' => 'hotels_table',
        'type' => 'text',
        'default' => 'hotels',
        'desc' => 'Hotels Table of Database'
        ) );

				$tab = $section->createTab( array(
	            'name' => __( 'End Points', 'hotels_pro_fw' ),
	        ) );

					$tab->createOption( array(
	        'name' => 'Search Endpoint',
	        'id' => 'search_endpoint',
	        'type' => 'text',
	        'default' => "/search/",
	        'desc' => 'Search Endpoint [/search/]'
	        ) );


					$tab->createOption( array(
	        'name' => 'Availability Endpoint',
	        'id' => 'availability_endpoint',
	        'type' => 'text',
	        'default' => "/hotel-availability/",
	        'desc' => 'Availability Endpoint [/hotel-availability/]'
	        ) );


					$tab->createOption( array(
	        'name' => 'Provision Endpoint',
	        'id' => 'provision_endpoint',
	        'type' => 'text',
	        'default' => "/provision/",
	        'desc' => 'Provision Endpoint [/provision/]'
	        ) );

					$tab->createOption( array(
					'name' => 'Booking Endpoint',
					'id' => 'book_endpoint',
					'type' => 'text',
					'default' => "/book/",
					'desc' => 'Booking Endpoint [/book/]'
					) );

					$tab->createOption( array(
					'name' => 'Cancel Endpoint',
					'id' => 'cancel_endpoint',
					'type' => 'text',
					'default' => "/cancel/",
					'desc' => 'Cancel Endpoint [/cancel/]'
					) );

  }

}


 ?>
