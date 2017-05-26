<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

use \JsonCollectionParser\Parser as Parser;
use Unirest\Request as Request;


/**
 * HotelBooking
 */
class HotelBooking
{

  private static $instance;

  public static function get_instance() {
  	if ( ! isset( self::$instance ) ) {
  		self::$instance = new self();
  	}

  	return self::$instance;
  }


  function __construct(){
    add_shortcode( 'hotelsPro_Search', [$this, 'hotelsPro_Search_func'] );


    add_action( 'wp_enqueue_scripts', array($this, 'load_custom_wp_frontend_style') );
    add_action( 'wp_ajax_destination_search', array($this, 'destination_search_func') );
    add_action( 'wp_ajax_nopriv_destination_search', array($this, 'destination_search_func') );

		add_action( 'wp_ajax_do_hotel_search', array($this, 'do_hotel_search_func') );
    add_action( 'wp_ajax_nopriv_do_hotel_search', array($this, 'do_hotel_search_func') );

		add_action( 'wp_ajax_get_hotel_summery', array($this, 'get_hotel_summery_func') );
    add_action( 'wp_ajax_nopriv_get_hotel_summery', array($this, 'get_hotel_summery_func') );

		add_action( 'wp_ajax_get_countries', array($this, 'get_countries_func') );
    add_action( 'wp_ajax_nopriv_get_countries', array($this, 'get_countries_func') );

		add_action( 'wp_ajax_get_destinations', array($this, 'get_destinations_func') );
    add_action( 'wp_ajax_nopriv_get_destinations', array($this, 'get_destinations_func') );

		add_shortcode( 'hotelsPro_Search_Local', [$this, 'hotelsPro_Search_Local_func'] );

  }


  public static function destination_search_func() {

    if (empty($_POST['q']))
      wp_die();

      $query_param = $_POST['q'];

      $titan = static::getTitan();
      $destination_table = $titan->getOption('load_destination_table');
      $db = DB::ConnectTitan($titan);
      $sql = "SELECT * FROM `{$destination_table}` WHERE `name` LIKE '%{$query_param}%'";
      $result = $db->query($sql);
      $result_array = [];

      if ($result->num_rows <= 0) {
        $db->close();
        echo json_encode([ "items" => [] ]);
        wp_die();
      }


        while($row = $result->fetch_assoc()) {

            $temp_row_array = [];

            foreach ($row as $row_key => $row_value) {

              if (strcmp($row_key, "code") === 0)
                $temp_row_array['id'] = $row_value;

              $temp_row_array[$row_key] = $row_value;
            }

            $result_array[] = $temp_row_array;
            $temp_row_array = null;
          }

      $db->close();

    echo json_encode([ "items" => $result_array ]);
    wp_die();
  }

  public static function getTitan() {
    $titan = TitanFramework::getInstance( 'hotels_pro_fw' );
    return $titan;
  }

  public static function hotelsPro_Search_func($atts) {

    $a = shortcode_atts( array(
    'foo' => 'something',
    'bar' => 'something else',
  ), $atts );

  $titan = static::getTitan();

  $destination_table = $titan->getOption('load_destination_table');
  $country_codes = static::get_country_nationality();
	$regions = static::get_regions();
  ob_start();
  include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."search.php";
	//include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."result.php";

  $output = ob_get_clean();

  return $output;


  }

	public static function get_regions() {

		$regions = [

			'af' => 'Africa',
			'an' => 'Antarctica',
			'as' => 'Asia',
			'eu' => 'Europe',
			'na' => 'North america',
			'oc' => 'Oceania',
			'sa' => 'South america'

		];

		$options = "";

		foreach ($regions as $key => $value) {
			$options .= '<option value="'.$key.'">'.$value.'</option>';
		}

		return $options;

	}

  public static function load_custom_wp_frontend_style() {


//BootStrap//
wp_enqueue_style( 'hotelsPro_Search-style-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );

wp_register_script( 'hotelsPro_Search-script-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'hotelsPro_Search-script-bootstrap' );

wp_register_script( 'hotelsPro_Search-script-bootstrap-hover-dropdown', hotels_pro_fw_PLUGIN_URL.'js/bootstrap-hover-dropdown.min.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'hotelsPro_Search-script-bootstrap-hover-dropdown' );

wp_register_script( 'hotelsPro_Search-script-bootstrap-jquery-ui-1.10.4.custom.min', hotels_pro_fw_PLUGIN_URL.'js/jquery-ui-1.10.4.custom.min.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'hotelsPro_Search-script-bootstrap-jquery-ui-1.10.4.custom.min' );

wp_enqueue_style( 'hotelsPro_Search-style-bootstrap-jquery-ui-1.10.4.custom.min', hotels_pro_fw_PLUGIN_URL.'css/jquery-ui-1.10.4.custom.min.css' );

wp_enqueue_style( 'hotelsPro_Search-style-select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
wp_register_script( 'hotelsPro_Search-style-select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'hotelsPro_Search-style-select2-js' );


//BootStrap//


wp_register_script( 'hotelsPro_Search-script-custom-booking', hotels_pro_fw_PLUGIN_URL.'js/custom.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'hotelsPro_Search-script-custom-booking' );


    wp_register_script( 'hotelsPro_Search-script', hotels_pro_fw_PLUGIN_URL.'js/hotel-search.js', array( 'jquery' ), '', true );

    wp_localize_script( 'hotelsPro_Search-script', 'hotelsPro_Search_Data', array( 'ajax_url' => admin_url('admin-ajax.php') ));

    wp_enqueue_script( 'hotelsPro_Search-script' );

    wp_enqueue_style( 'hotelsPro_Search-theme-sh', hotels_pro_fw_PLUGIN_URL.'css/theme-sh.css' );
    wp_enqueue_style( 'hotelsPro_Search-turquoise', hotels_pro_fw_PLUGIN_URL.'css/turquoise.css' );

    wp_enqueue_style( 'hotelsPro_Search-style', hotels_pro_fw_PLUGIN_URL.'css/hotel-search.css' );


  }

  public static function load_js_files($files_array = "") {

    if (empty($files_array)) return;

    if (!is_array($files_array)) return;

    foreach ($files_array as $key => $file_name) {
      # code...
    }

  }

  public static function get_country_nationality() {

    $titan = static::getTitan();

    $country_code_table = $titan->getOption('country_code_table');

    $db = DB::ConnectTitan($titan);
    $sql = "SELECT * FROM `{$country_code_table}`";
    $result = $db->query($sql);
    $result_array = [];

    if ($result->num_rows <= 0)
      return;

      $get_from_api = Unirest\Request::get('https://restcountries.eu/rest/v2/all', null, null, '', '');
      $get_from_api = $get_from_api->body;
      $count_countries = 0;
      $country_array = [];
      $total_countries = count($get_from_api);
      for ($i=0; $i < $total_countries; $i++) {
        $key_country = strtolower($get_from_api[$i]->alpha2Code);

        if (in_array($get_from_api[$i]->demonym, $country_array)) {
          $country_array[$key_country] = $get_from_api[$i]->demonym." (".$get_from_api[$i]->name.")";
          continue;
        }

        $country_array[$key_country] = $get_from_api[$i]->demonym;
      }

      while($row = $result->fetch_assoc())
        $result_array[$row['code']] = ( array_key_exists($row['code'], $country_array) ? (empty($country_array[$row['code']]) ? $row['code'] : $country_array[$row['code']]) : $row['code'] );

          $db->close();

    return $result_array;
  }


	public static function get_country_name($country_code = "") {

		if (empty($country_code))
			return;

			$titan = static::getTitan();

		$country_name = "";
		$get_from_api = Unirest\Request::get('https://restcountries.eu/rest/v2/alpha/'.$country_code, null, null, '', '');
		$get_from_api = $get_from_api->body;
		$country_name = $get_from_api;
		if (isset($country_name->status) && ( $country_name->status === 404 ))
			return $country_code;

		$country_name = $country_name->name;

		return $country_name;
	}

	public static function do_hotel_search_func() {

		if (empty($_POST['data']))
			wp_die();

			$titan = static::getTitan();
			$booking_api_url = $titan->getOption('booking_api_url');
			$search_endpoint = $titan->getOption('search_endpoint');
			$booking_api_url .= $search_endpoint;

		$data = $_POST['data'];

		$new_data_str = [];
		$new_data_str['pax'] = $data['adults'].",".$data['children'];
		$new_data_str['client_nationality'] = $data['nationality'];
		$new_data_str['checkin'] = $data['checkin'];
		$new_data_str['checkout'] = $data['checkout'];
		$new_data_str['destination_code'] = $data['destination'];
		$new_data_str['format'] = "json";

		$query_string = http_build_query($new_data_str);

		$booking_api_url .= "?".$query_string;

		$username = $titan->getOption('booking_api_user');
		$password = $titan->getOption('booking_api_password');

		//$booking_api_url = "https://api-test.hotelspro.com/api/v2/search/?pax=1%2C0&client_nationality=ae&checkin=2017-05-16&checkout=2017-05-17&destination_code=239c2&format=json";

		$response_initial = Unirest\Request::get($booking_api_url,
		 null, null, $username, $password);

		 $response_initial = $response_initial->body;


		 ob_start();
	   include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."result.php";
	   $output = ob_get_clean();

		_e(json_encode( ['html' => $output, 'hotel_data' => $response_initial] ));
		wp_die();

	}


		public static function get_hotel_summery_func() {

			if (empty($_POST['data']))
				wp_die();

			 $hotel_code = $_POST['data']['hotel_code'];
			 $hotel_info = static::get_hotel_info_from_api($hotel_code);

			 $code = $hotel_info->code;
			 $images = $hotel_info->images;
			 $name = $hotel_info->name;
			 $address = $hotel_info->address;
			 $products = $_POST['data']['products'];

			 ob_start();
	 	   include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."single-result.php";
			 d($hotel_info);
	 	   $output = ob_get_clean();

				echo json_encode(['html' => $output]);
				wp_die();


		}


		public static function get_hotel_info_from_api($hotel_code = "") {

			if (empty($hotel_code)) return;
			$titan = static::getTitan();

			$api_url = $titan->getOption( 'api_url');
			$api_user = $titan->getOption( 'api_user');
			$api_password = $titan->getOption( 'api_password');

			$api_url .= "hotels/".$hotel_code."/?format=json";

			$response = Unirest\Request::get($api_url, null, null, $api_user, $api_password);
			d($api_url);
			$response = $response->body;
			$titan = null;
			$response = $response[0];
			return $response;
		}

		public function get_countries_func() {

			if (empty($_POST['region']))
				wp_die();

			$region = $_POST['region'];

			$titan = static::getTitan();
			$db = DB::ConnectTitan($titan);
			$sql = "SELECT * FROM `countries` WHERE continents = '{$region}'";
			$result = $db->query($sql);
			$result_array = [];

			if ($result->num_rows <= 0) {
				$db->close();
				wp_die();
			}

			$countries_codes = [];

				while($row = $result->fetch_assoc())
					$countries_codes[] = [ 'code' => $row['code'], 'name' => static::get_country_name($row['code']) ] ;

				if (empty($countries_codes))
					wp_die();

				$country_options = "";

				foreach ($countries_codes as $country) {
					$country_options .= '<option value="'.$country['code'].'">'.$country['name'].'</option>';
				}

				echo json_encode($country_options);
				wp_die();

		}

		public function get_destinations_func() {

			if (empty($_POST['country']))
				wp_die();


				$country = $_POST['country'];

				$titan = static::getTitan();
				$db = DB::ConnectTitan($titan);
				$sql = "SELECT * FROM `destinations` WHERE country = '{$country}'";
				$result = $db->query($sql);
				$result_array = [];

				if ($result->num_rows <= 0) {
					$db->close();
					wp_die();
				}

				$destinations = [];

				while($row = $result->fetch_assoc())
					$destinations[] = [ 'code' => $row['code'], 'name' => $row['name'] ];

				$destination_options = "";

				foreach ($destinations as $destination)
					$destination_options .= '<option value="'.$destination['code'].'">'.$destination['name'].'</option>';



			echo json_encode($destination_options);
			wp_die();
		}

		public static function hotelsPro_Search_Local_func($atts) {

			$a = shortcode_atts( array(
			'foo' => 'something',
			'bar' => 'something else',
		), $atts );

		$titan = static::getTitan();

		$destination_table = $titan->getOption('load_destination_table');
		$country_codes = static::get_country_nationality();
		$regions = static::get_regions();
		ob_start();
		//include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."search.php";
		//include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."result.php";



		if (isset($_POST['submit_final_booking']) && isset($_POST['final_booking_start'])) {
			include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."final_booking.php";

			return;

		}

		if (isset($_POST['submit_provision']) && isset($_POST['provision_start'])) {
			include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."doProvision.php";

			return;

		}


		if (isset($_POST['submit']) && isset($_POST['start_booking'])) {
			include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."get_products.php";

			return;

		}


		if (!empty($_GET['hotel_code']) && !empty($_GET['check_hotel'])) {

			include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."single-hotel-check.php";

			return;
		}

		include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."search_simple.php";


		// $_POST['submit'] = 1;
		// $_POST['hotel_name'] = 'Hotel';

		if (isset($_POST['submit']))
			include_once hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."search_result.php";

		$output = ob_get_clean();

		return $output;


		}

}


?>
