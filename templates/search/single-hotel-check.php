<?php
ob_start();
?>
<div class="hotel_search_result">
  <div class="container">
    <div class="row">

<div class="col-md-12">

  <?php

function hotel_data_display() {

  $hotel_data = HotelSearchResult::getSingleResult($_GET['hotel_code']);


  if (empty($hotel_data))
    return;

  if (!empty($hotel_data) && isset($hotel_data['error']))
    return $hotel_data['desc'];

    $code = $hotel_data['code'];

    $images = unserialize($hotel_data['images']);
    $images = (empty($images) ? [] : $images);

    $html = "";
    ob_start();
    include hotels_pro_fw_PLUGIN_DIR."templates".DS."search".DS."single-hotel-images.php";
    $output = ob_get_clean();

    ob_start();
    d($hotel_data);
    $output_hotel_data = ob_get_clean();

    $html .= $output;

    $html .= "Name: ".$hotel_data['name']."<br>";
    $html .= "Address: ".$hotel_data['name']."<br>";
    $html .= "Hotel Information: ".$hotel_data['hotel_information']."<br>";
    $html .= $output_hotel_data;
    return $html;
}

_e(hotel_data_display());

   ?>

</div>
</div>

  <?php _e(HotelSearchResult::searchOnAPI($_GET['hotel_code'])); ?>


</div>
</div>



<?php
$output = ob_get_clean();
echo $output;

 ?>
