<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

use \JsonCollectionParser\Parser as Parser;
use Unirest\Request as Request;

/**
 * HotelSearchResult
 */
class HotelSearchResult extends HotelBooking
{

  function __construct()
  {
    # code...
  }

  public static function getResult($search_name = "") {

    if (empty($search_name))
      return;

      $titan = static::getTitan();
			$db = DB::ConnectTitan($titan);
      $hotels_table = $titan->getOption('hotels_table');
      $search_name = $db->real_escape_string($search_name);
			$sql = "SELECT * FROM `".$hotels_table."` WHERE name LIKE '%".$search_name."%'";
			$result = $db->query($sql);
			$result_array = [];

			if ($result->num_rows <= 0) {
				$db->close();
				//wp_die();
        return "<div class='no_hotel'>No hotel found!</div>";
			}

      while($row = $result->fetch_assoc())
        $result_array[] = $row;

      $count_i = count($result_array);

      $search_result = "";

      for ($i=0; $i < $count_i; $i++) {

        $hotel_data = $result_array[$i];

        //d($hotel_data);

        $hotel_code = $hotel_data['code'];
        $code =& $hotel_code;
        $products = [];

        $images = unserialize($hotel_data['images']);
        $images = (empty($images) ? [] : $images);
        //d($hotel_data);
        ob_start();

    		include hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."single-result.php";

    		$output = ob_get_clean();
        $search_result .= $output;

      }



      return $search_result;

  }

  public static function getSingleResult($hotel_code = "") {

    if (empty($hotel_code))
      return;


      $titan = static::getTitan();
			$db = DB::ConnectTitan($titan);
      $hotels_table = $titan->getOption('hotels_table');
      $hotel_code = $db->real_escape_string($hotel_code);
			$sql = "SELECT * FROM `".$hotels_table."` WHERE code = '".$hotel_code."' LIMIT 1";
			$result = $db->query($sql);
			$result_array = [];

			if ($result->num_rows <= 0) {
				$db->close();
				//wp_die();
        return ['error' => 1, 'desc' => "<div class='no_hotel no_hotel_data'>No data found!</div>"];
			}

      while($row = $result->fetch_assoc())
        $result_array[] = $row;

        $result_array = $result_array[0];




        return $result_array;
  }

  public static function searchOnAPI($hotel_code = "") {

  if (empty($hotel_code))
    return;


    $titan = static::getTitan();
    $db = DB::ConnectTitan($titan);
    ob_start();
    include hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."book-form.php";
    $output = ob_get_clean();


    return $output;
}


public static function getProductsPricesMultiple($data = "") {

  if (empty($data))
    return;

	if (empty($data['total_rooms']))
		return;

	$total_rooms = (int) $data['total_rooms'];



  $titan = static::getTitan();
  $booking_api_url = $titan->getOption('booking_api_url');
  $search_endpoint = $titan->getOption('search_endpoint');
  $booking_api_url .= $search_endpoint;

	if (count($data['adults']) !== $total_rooms)
		return;

	$adults = $data['adults'];

	if (!isset($data['children']))
		$data['children'] = [];

	if (count($data['children']) > $total_rooms)
		return;

	$children = $data['children'];

	$result_response_products = [];

	$username = $titan->getOption('booking_api_user');
	$password = $titan->getOption('booking_api_password');


	for ($i=0; $i < $total_rooms; $i++) {
		$new_data_str = [];

		if (isset($children[$i]))
			$children_str = implode(",", $children[$i]);

		if (isset($data['children'][$i]) && !empty($data['children'][$i]))
			$new_data_str['pax'] = $data['adults'][$i].",".$children_str;
		else
			$new_data_str['pax'] = $data['adults'][$i];

			$new_data_str['client_nationality'] = $data['nationality'];
			$new_data_str['checkin'] = $data['checkin'];
			$new_data_str['checkout'] = $data['checkout'];
			$new_data_str['destination_code'] = $data['destination'];
			$new_data_str['format'] = "json";
			$new_data_str['hotel_code'] = $data['hotel_code'];

			$query_string = http_build_query($new_data_str);
			$booking_api_url_tmp = $booking_api_url."?".$query_string;

			$response_initial = Unirest\Request::get($booking_api_url_tmp,
			 null, null, $username, $password);

			$response_initial = $response_initial->body;
			if (isset($response_initial->code) && !empty($response_initial->code))
				$result_response_products[$i]['search_code'] = $response_initial->code;


			$result_response_products[$i]['pax'] = $new_data_str['pax'];

			if (isset($response_initial->results) && !empty($response_initial->results))
				$response_initial = $response_initial->results;


			if (is_array($response_initial) && isset($response_initial[0]) ) {
				$response_initial = $response_initial[0];
				if (($response_initial->products) && !empty($response_initial->products)) {

					$check_availability = static::checkAvailability($result_response_products[$i]['search_code'], $data['hotel_code']);
					$result_response_products[$i]['products'] = $check_availability;

				}
			} else
				$result_response_products[$i]['products'] = [];
	}

	return $result_response_products;

}


public static function getProductsPrices($data = "") {

  if (empty($data))
    return;

	if (empty($data['total_rooms']))
		return;

	$total_rooms = (int) $data['total_rooms'];



  $titan = static::getTitan();
  $booking_api_url = $titan->getOption('booking_api_url');
  $search_endpoint = $titan->getOption('search_endpoint');
  $booking_api_url .= $search_endpoint;

	if (count($data['adults']) !== $total_rooms)
		return;

	$adults = $data['adults'];

	if (!isset($data['children']))
		$data['children'] = [];

	if (count($data['children']) > $total_rooms)
		return;

	$children = $data['children'];

	$result_response_products = [];

	$username = $titan->getOption('booking_api_user');
	$password = $titan->getOption('booking_api_password');
	$pax_query_string = "";
	for ($i=0; $i < $total_rooms; $i++) {
		$new_data_str = [];

		if (isset($children[$i]))
			$children_str = implode(",", $children[$i]);

		if (isset($data['children'][$i]) && !empty($data['children'][$i]))
			$pax_data = $data['adults'][$i].",".$children_str;
		else
			$pax_data = $data['adults'][$i];

			$pax_query_string .= "&pax=".$pax_data;
			$result_response_products['pax'][] = $pax_data;

}

$new_data_str['client_nationality'] = $data['nationality'];
$new_data_str['checkin'] = $data['checkin'];
$new_data_str['checkout'] = $data['checkout'];
$new_data_str['destination_code'] = $data['destination'];
$new_data_str['format'] = "json";
$new_data_str['hotel_code'] = $data['hotel_code'];

$query_string = http_build_query($new_data_str);

$query_string .= $pax_query_string;

$booking_api_url_tmp = $booking_api_url."?".$query_string;
d($booking_api_url_tmp);
$response_initial = Unirest\Request::get($booking_api_url_tmp,
 null, null, $username, $password);

$response_initial = $response_initial->body;

if (isset($response_initial->code) && !empty($response_initial->code))
	$result_response_products['search_code'] = $response_initial->code;


	if (isset($response_initial->results) && !empty($response_initial->results))
		$response_initial = $response_initial->results;

	if (is_array($response_initial) && isset($response_initial[0]) ) {
		$response_initial = $response_initial[0];
		if (($response_initial->products) && !empty($response_initial->products)) {

			$check_availability = static::checkAvailability($result_response_products['search_code'], $data['hotel_code']);
			$result_response_products['products'] = $check_availability;
		}
	} else
		$result_response_products['products'] = [];


	return $result_response_products;

}


public static function checkAvailability($searchCode = "", $hotel_code = "") {

  if (empty($searchCode))
    return;

  if (empty($hotel_code))
    return;

    $titan = static::getTitan();
    $booking_api_url = $titan->getOption('booking_api_url');
		$availability_endpoint = $titan->getOption('availability_endpoint');
		$booking_api_url .= $availability_endpoint;

    $username = $titan->getOption('booking_api_user');
    $password = $titan->getOption('booking_api_password');

    $new_data_str = [];
    $new_data_str['search_code'] = $searchCode;
    $new_data_str['hotel_code'] = $hotel_code;

    $query_string = http_build_query($new_data_str);

    $booking_api_url .= "?".$query_string;
    $response_initial = Unirest\Request::get($booking_api_url,
     null, null, $username, $password);

     $response_initial = $response_initial->body;
		 $product_results = [];
		 if ( isset($response_initial->results) && !empty($response_initial->results) )
		 	$product_results = $response_initial->results;

     return $product_results;
}

public static function doProvision($product_code = []) {

  if (empty($product_code) && !is_array($product_code))
    return;

    $titan = static::getTitan();
    $booking_api_url = $titan->getOption('booking_api_url');
    $username = $titan->getOption('booking_api_user');
    $password = $titan->getOption('booking_api_password');
		$provision_endpoint = $titan->getOption('provision_endpoint');
		$product_code = array_map('trim', $product_code);
		$provision_data = [];
		foreach ($product_code as $key => $single_product_code) {
			$booking_api_url_tmp = $booking_api_url.$provision_endpoint.$single_product_code;

			$headers = array('Accept' => 'application/json');
			$query = array();
			Unirest\Request::auth($username, $password);
			$response_initial = Unirest\Request::post($booking_api_url_tmp, $headers, $query);
		 	$response_initial = $response_initial->body;
			$provision_data[] = $response_initial;
		}

		return $provision_data;
}

public static function doProvisionAndBooking($product_codes = [], $name_data = []) {

	if (empty($product_codes) && !is_array($product_codes))
		return;

	$doProvision = static::doProvision($product_codes);

	// d($doProvision);
	// return;
	$book_data = [];

	foreach ($doProvision as $key => $single_provision) {

		if (!isset($single_provision->code) && empty($single_provision->code))
			continue;

			$single_code = $single_provision->code;
			$name_data_single = $name_data[$key];

			$book_data_tmp = static::doFinalBook($name_data_single, 1, $single_code);

			$book_data[] = $book_data_tmp;
	}

	return $book_data;

}

public static function cancelBooking($booking_code = "") {

	if (empty($booking_code))
		return;

		$titan = static::getTitan();
    $booking_api_url = $titan->getOption('booking_api_url');
    $username = $titan->getOption('booking_api_user');
    $password = $titan->getOption('booking_api_password');
		$cancel_endpoint = ( empty($titan->getOption('cancel_endpoint')) ? "/cancel/" : $titan->getOption('cancel_endpoint') );

    $booking_api_url .= $cancel_endpoint.$booking_code;
		$query = "";
    $headers = array('Accept' => 'application/json');

		Unirest\Request::auth($username, $password);
		$response_initial = Unirest\Request::post($booking_api_url, $headers, $query);

    $response_initial = $response_initial->body;

		return $response_initial;

}

//public static function doFinalBook($first_name = "", $last_name = "", $room = "", $provision_code = "") {
public static function doFinalBook($name_data = [], $room = 1, $provision_code = "") {

  if (empty($name_data) && !is_array($name_data))
    return;

  if (empty($room))
    return;

  if (empty($provision_code))
    return;

    $titan = static::getTitan();
    $booking_api_url = $titan->getOption('booking_api_url');
    $username = $titan->getOption('booking_api_user');
    $password = $titan->getOption('booking_api_password');
		$book_endpoint = $titan->getOption('book_endpoint');

    $booking_api_url .= $book_endpoint.$provision_code;
		$count_total = count($first_name);
		$query = [];

		//d($name_data);
		$name_data_str = "";
		$name_data_i = 0;
		foreach ($name_data as $key => $single_name_data) {
			if (count($single_name_data) > 2)
				$name_data_str .= "name=".$room.",".$single_name_data[0].",".$single_name_data[1].",child,".$single_name_data[2];
			else
				$name_data_str .= "name=".$room.",".$single_name_data[0].",".$single_name_data[1].",adult";

				if ( ($name_data_i+1) !== count($name_data))
					$name_data_str .= "&";

					$name_data_i++;
		}


		//$query = array("name" => "1,test,test,adult,1,testa,testa,adult");
		//$query = json_encode($query);
		//$query = array("name" => "1,test,test,adult&name=1,testa,testa,adult");
		//$query = "name=1,test,test,adult&name=1,testa,testa,adult";
		$query = $name_data_str;
    $headers = array('Accept' => 'application/json');
		//$query = array("name" => $name_data);
    //$query = http_build_query($query);

    Unirest\Request::auth($username, $password);
    $response_initial = Unirest\Request::post($booking_api_url, $headers, $query);

     $response_initial = $response_initial->body;




     return $response_initial;


}

}


?>
