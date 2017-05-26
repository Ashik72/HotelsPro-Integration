<?php

function ShowProducts() {

$getProducts = HotelSearchResult::getProductsPrices($_POST);

if (empty($getProducts->results))
  return "Not available!";

  ob_start();
  d($getProducts);
  $output_getProducts = ob_get_clean();

  $searchCode = $getProducts->code;
  $result = $getProducts->results[0];
  $products = $result->products;
  $hotel_code = $result->hotel_code;

  $check_hotel_availability = HotelSearchResult::checkAvailability($searchCode, $hotel_code);
  d($check_hotel_availability);
  ob_start();

  // d($check_hotel_availability);
  // d($hotel_code);
  // d($searchCode);
  // d($result);
  // d($products);
  $html = "";
  $html .= "<div class='single_room_product'>";

  foreach ($products as $key => $single_product) {

    foreach ($single_product->rooms as $key => $room) {


      $html .= $room->room_category." - ".$room->room_description."<br>";

      foreach ($room->nightly_prices as $keynightly_prices => $roomnightly_prices) {
        $html .= $keynightly_prices ." : ".$roomnightly_prices." ".$single_product->currency."<br>";
        ob_start();
        d($room->pax);
        $output_roompax = ob_get_clean();
        $html_output_roompax .= $output_roompax;
      }




    }

$html .= '<form class="provision_form" name="provision_form" method="post">
<input type="hidden" name="product_code" value="'.$single_product->code.'">
<input type="hidden" name="provision_start" value="1">

<input type="submit" name="submit_provision" value="Provision">
</form>';

  }

  $html .= "</div>";
  $html .= $output_getProducts;
  $html .= $html_output_roompax;

  _e($html);

  $output = ob_get_clean();

  return $output;
}

_e(ShowProducts());

 ?>
