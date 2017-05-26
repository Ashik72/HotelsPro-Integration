<?php
ob_start();
?>


<div class="hotel_search_book">

  <section id="reservation-form">
      <div class="row">
        <div class="col-md-12">
          <form class="form-inline reservation-horizontal clearfix" role="form" method="post" name="bookFormStart" id="bookFormStart">
          <div id="message"></div><!-- Error message display -->



            <div class="row">



              <div class="col-sm-2">
                <div class="form-group">
                  <label for="nationality">Your Nationality</label>
                  <!-- <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."> <i class="fa fa-info-circle fa-lg"> </i> </div> -->
                  <select class="form-control" name="nationality" id="nationality">
                    <option selected="selected" disabled="disabled">Select your nationality</option>

                    <?php
                    ob_start();
                    $country_codes = HotelBooking::get_country_nationality();
                    foreach ($country_codes as $key => $single_country_codes) {
                      _e('<option value="'.$key.'">'.$single_country_codes.'</option>');
                    }
                    $output = ob_get_clean();
                    _e($output);
                     ?>
                  </select>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group">
                  <label for="checkin">Check-in</label>
                  <!-- <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Check-In is from 11:00"> <i class="fa fa-info-circle fa-lg"> </i> </div> -->
                  <i class="fa fa-calendar infield"></i>
                  <input name="checkin" type="text" id="checkin" value="" class="form-control" placeholder="Check-in"/>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group">
                  <label for="checkout">Check-out</label>
                  <!-- <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Check-out is from 12:00"> <i class="fa fa-info-circle fa-lg"> </i> </div> -->
                  <i class="fa fa-calendar infield"></i>
                  <input name="checkout" type="text" id="checkout" value="" class="form-control" placeholder="Check-out"/>
                </div>
              </div>
              <div class="col-sm-1">
                <div class="form-group">
                  <div class="guests-select">
                    <label>Guests</label>
                    <i class="fa fa-user infield"></i>
                    <div class="total form-control" id="test">1</div>
                    <div class="guests">
                      <div class="form-group adults">
                        <label for="adults">Adults</label>
                        <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="+18 years"> <i class="fa fa-info-circle fa-lg"> </i> </div>
                        <select name="adults" id="adults" class="form-control">
                          <option value="1">1 adult</option>
                          <option value="2">2 adults</option>
                          <option value="3">3 adults</option>
                          <option value="4">4 adult</option>
                          <option value="5">5 adults</option>
                          <option value="6">6 adults</option>

                        </select>
                      </div>
                      <div class="form-group children">
                        <label for="children">Children</label>
                        <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="0 till 18 years"> <i class="fa fa-info-circle fa-lg"> </i> </div>
                        <select name="children" id="children" class="form-control">
                          <option value="0">0 children</option>
                          <option value="1">1 child</option>
                          <option value="2">2 children</option>
                          <option value="3">3 children</option>
                          <option value="4">4 children</option>

                        </select>
                      </div>
                      <button type="button" class="btn btn-default button-save btn-block">Save</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <input type="hidden" name="hotel_code" value="<?php _e($hotel_code); ?>">
            <input type="hidden" name="start_booking" value="1">

        <div class="row">

          <div class="col-sm-2">
            <input type="submit" name="submit" class="btn btn-primary btn-block" value="Book">
          </div>



        </div>


          </form>
        </div>
      </div>
  </section>

</div>

<?php
$output = ob_get_clean();
echo $output;

 ?>
