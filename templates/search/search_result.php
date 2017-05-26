<?php
ob_start();
?>


<div class="hotel_search_result">
  <div class="container">
    <div class="row">

<div class="col-md-12">

  <?php

  _e(HotelSearchResult::getResult($_POST['hotel_name']))

   ?>


</div>

</div>
</div>
</div>

<?php
$output = ob_get_clean();
echo $output;

 ?>
