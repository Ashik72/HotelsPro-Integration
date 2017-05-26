<?php
ob_start();
?>


<div class="hotel_search">

  <section id="reservation-form">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <form class="form-inline reservation-horizontal clearfix" role="form" method="post" name="searchForm" id="searchForm">
          <div id="message"></div><!-- Error message display -->


          <div class="row">

            <div class="col-sm-12">
              <div class="form-group">
                <label for="region" accesskey="R">Search By Hotel Name</label><br>
                <!-- <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."> <i class="fa fa-info-circle fa-lg"> </i> </div> -->
                <!-- <input data-dest_table="<?php _e($destination_table); ?>" name="destination" type="text" id="destination" value="" class="form-control" placeholder="Your Destination"/> -->
                <input name="hotel_name" type="text" id="hotel_name" value="" class="form-control" placeholder="Hotel Name"/>
              </div>
            </div>


          </div>




        <div class="row">

          <div class="col-sm-2">
              <input type="submit" name="submit" class="btn btn-primary btn-block" value="Search">
            <!-- <button type="submit" class="btn btn-primary btn-block">Search</button> -->
          </div>



        </div>


          </form>
        </div>
      </div>
    </div>
  </section>

</div>

<?php
$output = ob_get_clean();
echo $output;

 ?>
