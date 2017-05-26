<?php


  function doBooking() {

    $code = $_POST['product_code'];

    $provisionData = HotelSearchResult::doProvision($code);

    $adult_quantity = (isset($provisionData->rooms[0]->pax->adult_quantity) ? $provisionData->rooms[0]->pax->adult_quantity : 0);

    ob_start();

    $html_adult_final = html_booking_form_adult($adult_quantity, $provisionData);

    $html .= $html_adult_final;

    _e($html);
    $output = ob_get_clean();

    ob_start();
    //d($provisionData);
    $outputprovisionData = ob_get_clean();

    $output .= $outputprovisionData;

    return $output;
  }


  function html_booking_form_adult($adult_count = 0, $provisionData = "") {

    ob_start();
    $html_adult = "";

    for ($i=0; $i < $adult_count; $i++) {
      $html_adult .= '<h3>Adult - '.( (int) ($i+1) ).'</h3>';
      $html_adult .= '<form class="booking_form" name="booking_form" method="post">
      <input type="hidden" name="provision_code" value="'.$provisionData->code.'">
      <input type="hidden" name="final_booking_start" value="1">
      First Name : <input type="text" name="fname[]"><br>
      Last Name: <input type="text" name="lname[]"><br>
      Total Room(s): <input type="text" name="room_number"><br>';

    }

    $html_adult .= '<input type="submit" name="submit_final_booking" value="Book Now">
    </form>';
    _e($html_adult);
    $output = ob_get_clean();
    return $output;
  }


_e(doBooking());

 ?>
