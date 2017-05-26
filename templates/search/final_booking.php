<?php
$fname = $_POST['fname'];
$lname = $_POST['fname'];
$rooms = (int) $_POST['room_number'];
$provision_code = $_POST['provision_code'];

  function doBookingFinal($fname, $lname, $rooms, $provision_code) {

    $final_Book = HotelSearchResult::doFinalBook($fname, $lname, $rooms, $provision_code);

    ob_start();

    d($final_Book);

    $output = ob_get_clean();

    return $output;
  }

_e(doBookingFinal($fname, $lname, $rooms, $provision_code));



 ?>
