<div class="hotel_summery_search container" data-hotel_code="<?php _e($hotel_code); ?>">
    <div class="row">
        <div class="col-sm-3">

          <div class="hotel-images">
              <div id="myCarousel__<?php _e($code); ?>" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators" style="display: none">

                  <?php
                    foreach ($images as $key => $single_image) {

                        $class = ($key == 0 ) ? "active" : "";

                      _e('<li class="'.$class.'" data-target="#myCarousel_'.$code.'" data-slide-to="'.$key.'"></li>');
                    }

                   ?>

                  <!-- <li data-target="#myCarousel" data-slide-to="0" class="active"></li> -->
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner">

                  <?php

                    foreach ($images as $key => $single_image) {
                      $class = ($key == 0 ) ? "active" : "";

                      $image = ( !empty($single_image['thumbnail_images']['large']) ? $single_image['thumbnail_images']['large'] : "" );

                      if (empty($image))
                        continue;

                      ?>

                      <div class="item <?php _e($class); ?>">
                        <img src="<?php _e($image); ?>" style="width:100%;">
                      </div>


                      <?php
                    };
                   ?>

                </div>

                <!-- Left and right controls -->
                <a class="left carousel-control" href="#myCarousel_<?php _e($code); ?>" data-slide="prev">
                  <span class="glyphicon glyphicon-chevron-left"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#myCarousel_<?php _e($code); ?>" data-slide="next">
                  <span class="glyphicon glyphicon-chevron-right"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
          </div>

        </div>
        <div class="col-sm-6">

          <div class="hotel_detail">

            <div class="hotel_name">

                <?php _e($hotel_data['name']); ?>

            </div>

            <div class="hotel_address">

              <?php _e($hotel_data['address']); ?>

            </div>


          </div>

        </div>
        <div class="col-sm-3">

          <div class="hotel_booking">



            <?php _e('<a href="?hotel_code='.$hotel_code.'&check_hotel=1">Check Prices And Availability</a>'); ?>


          </div>


        </div>

    </div>


</div>
