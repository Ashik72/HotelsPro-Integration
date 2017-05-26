jQuery(document).ready(function($) {

  load_booking_functions = {

    destination_val : function() {

      $('#destination').change(function() {
            //console.log($(this).val());
          });

    },

    searchForm_submit: function() {


      $(document).on("submit", "#searchForm", function(event) {

        event.preventDefault();

        var formdata = {};

        formdata['destination'] = $('#destination').val();
        formdata['nationality'] = $('#nationality').val();
        formdata['checkin'] = $('#checkin').val();
        formdata['checkout'] = $('#checkout').val();
        formdata['adults'] = $('#adults').val();
        formdata['children'] = $('#children').val();
        console.log(formdata);
        var formdataStat = 1;
        $.each(formdata, function(k, dataVal) {

          if (dataVal == null) {
            formdataStat = 0;
            return;
          }

          if (dataVal.length <= 0) {
            formdataStat = 0;
            return;
          }

        })

        if (formdataStat <= 0) {
          //alert("Please select necessary fields!");
          //return;
        }


        var data = {
          'action': 'do_hotel_search',
          'data' : formdata
        };

        jQuery.post(hotelsPro_Search_Data.ajax_url, data, function(response) {
          if (response == null || response.length <= 0) {
            alert("Something went wrong, try again please");
            return;
          }

          response = $.parseJSON(response);


          $(".hotel_search_result").remove();
          $(".hotel_search").after(response.html);

          response = response.hotel_data;
          var search_code = response.code;

          $.each(response.results, function(i, el) {

            console.log(response.results[i]);

            var data = {
              'action': 'get_hotel_summery',
              'data' : response.results[i]
            };

            jQuery.post(hotelsPro_Search_Data.ajax_url, data, function(response) {
              if (response == null || response.length <= 0) {
                return;
              }
              response = $.parseJSON(response);

              $(".result-section-div").append(response.html);

            })


          })


          console.log(response);


        })

      })


    },

    image_carousel: function() {


      $(document).on("click", ".carousel-control", function(event) {

        event.preventDefault();

        var id = $(this).attr("href");
        var type = $(this).data("slide");
        console.log(type);
        console.log(id);

        if (type == "prev") {
          var current_point = $(id+" .carousel-inner .item.active");
          console.log(current_point);
          $(id+" .carousel-inner .item.active").addClass("tempItem");
          $(id+" .carousel-inner .item.active").next().addClass("active");
          $(id+" .carousel-inner .item.active").addClass("tempItem");
          return;
        }

        if (type == "next") {

          var current_point = $(id).find(".carousel-inner .item.active");

          console.log(current_point.html());

          current_point.removeClass('active');
          current_point.next().addClass('active');


          return;

        }


      })


    },

    enable_select2: function() {

        $("#region, #nationality, #country, #destination").select2();

    },

    on_region_change: function() {

      $('#region').change(function() {
            //console.log($(this).val());
            var region = $(this).val();

            if (region.length <= 0)
              return;

              var data = {
                'action': 'get_countries',
                'region' : region
              };

              $("#country").find("option").remove();
              $("#country").append('<option value="0" selected="selected">Loading Countries...</option>');

              jQuery.post(hotelsPro_Search_Data.ajax_url, data, function(response) {

                response = $.parseJSON(response);

                if (response == null || response.length <= 0) {
                  alert("No country found on this region!");
                  $("#country").find("option").remove();

                  return;
                }

                $("#country").find("option").remove();
                $("#country").append('<option value="0" selected="selected">Select Country</option>');
                $("#country").append(response);


          });
    });

  },

  on_country_change: function() {

    $('#country').change(function() {
          //console.log($(this).val());
          var country = $(this).val();

          if (country.length <= 0)
            return;

            var data = {
              'action': 'get_destinations',
              'country' : country
            };

            $("#destination").find("option").remove();
            $("#destination").append('<option value="0" selected="selected">Loading Destinations...</option>');

            jQuery.post(hotelsPro_Search_Data.ajax_url, data, function(response) {
              response = $.parseJSON(response);

              if (response == null || response.length <= 0) {
                alert("No destination found on this country!");
                $("#destination").find("option").remove();

                return;
              }

              $("#destination").find("option").remove();
              $("#destination").append('<option value="0" selected="selected">Select Destination</option>');
              $("#destination").append(response);


        });



  });


  }

}

  load_booking_functions.destination_val();
  //load_booking_functions.searchForm_submit();
  load_booking_functions.image_carousel();
  load_booking_functions.enable_select2();
  load_booking_functions.on_region_change();
  load_booking_functions.on_country_change();

});

jQuery(document).ready(function($) {

  $("#destination_old").select2({

  //$("#destination").select2({
  ajax: {
    method: "POST",
    url: hotelsPro_Search_Data.ajax_url,
    //url: "https://api.github.com/search/repositories",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        page: params.page,
        action: "destination_search"

      };
    },
    processResults: function (data, params) {
      // parse the results into the format expected by Select2
      // since we are using custom formatting functions we do not need to
      // alter the remote JSON data, except to indicate that infinite
      // scrolling can be used
      params.page = params.page || 1;


      return {
        results: data.items,
        pagination: {
          more: (params.page * 30) < data.total_count
        }
      };
    },
    cache: true
  },
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 2,
  templateResult: function(repo) {
    if (repo.loading) return repo.code;

    return repo.name;
  }, // omitted for brevity, see the source of this page
  templateSelection: function(repo) {
    return repo.name || repo.code;
  } // omitted for brevity, see the source of this page
});



});
