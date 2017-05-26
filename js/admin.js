jQuery(document).ready(function($) {



var adminHP = {

 adjustFileUploadTag: function() {

   if ($("form").find("#json_data_file").length <= 0)
    return;

    $("form").attr("enctype", "multipart/form-data");



    if ($(".tmp_json_file_val").text().length > 0) {

      $("#hotels_pro_fw_uploaded_json_file_link").val($(".tmp_json_file_val").text());

      $(".tmp_json_file_val").css("display", "none");
    }


 },

 checkJSONFile: function() {

   $('input#hotels_pro_fw_uploaded_json_file_data_optslarge_file, input#hotels_pro_fw_uploaded_json_file_data_optsextra_tags').click(function() {

     var other_tag = ( $(this).val() == "large_file" ) ? "extra_tags" : "large_file";

     //if ($(this).is(":checked") && $('input[name="'+other_tag+'"]').is(":checked"))
     if ($(this).is(":checked") && $('input#hotels_pro_fw_uploaded_json_file_data_opts'+other_tag).is(":checked"))
       alert("You can not use JSON Streaming parser for file with extra tags, the structure must have to be a consistent array structure. Please use a JSON editor and edit the file.");


   })

   if( $("#hotels_pro_fw_uploaded_json_file_link").length <= 0 )
    return;

   if ($("#hotels_pro_fw_uploaded_json_file_link").val().length <= 0)
    return;



    var html = '<br><input type="checkbox" value="large_file" name="large_file"> This one is a large JSON file, use JSON Streaming parser.<br>';
    html += '<input type="checkbox" value="extra_tags" name="extra_tags"> This file has extra tags (count, next, previous etc)<br><br>';
    html += '<button class="verify_json_file">Verify And Get Fields</button>';
   //$("#hotels_pro_fw_uploaded_json_file_link").after(html);





   $(document).on("click", ".verify_json_file", function(event) {

     event.preventDefault();

     var file_link = $("#hotels_pro_fw_uploaded_json_file_link").val();
     var large_file = ( $('input[name="large_file"]').is(":checked") ? 1 : 0 );
     var extra_tags = ( $('input[name="extra_tags"]').is(":checked") ? 1 : 0 );

     if ( (extra_tags == 1) && (large_file == 1))
      return;

     console.log(file_link)
     console.log(large_file)
     console.log(extra_tags)

     var data = {
       'action': 'uploaded_file_verifier_tag_extract',
       'file_link' : file_link,
       'large_file' : large_file,
       'extra_tags' : extra_tags
     };

     jQuery.post(hotels_pro_fw_admin_plugin_data.ajax_url, data, function(response) {

       console.log(response);
       response = $.parseJSON(response);

     });


   })


 },

 jsonEditor: function() {

   if ($('#jsoneditor').length <= 0)
    return;


  var container = document.getElementById("jsoneditor");
  var options = {
    mode: 'view'
    };

  var json = {
    'array': [1, 2, 3],
    'boolean': true,
    'null': null,
    'number': 123,
    'object': {'a': 'b', 'c': 'd'},
    'string': 'Hello World'
  };
  var editor = new JSONEditor(container, options, json);

},


needed_fields: function() {

  if ($("#hotels_pro_fw_link_with_db_columns").length <= 0)
    return;

  if ($(".needed_records").length <= 0)
    return;

  $(document).on("keyup", "#hotels_pro_fw_link_with_db_columns", function(event) {

    if (event.which !== 13)
      return;

      var textarea_val = $("#hotels_pro_fw_link_with_db_columns").val();

      var textarea_val_str = textarea_val;

      textarea_val = textarea_val.split(/\n/);


      textarea_val = textarea_val.filter(Boolean)


      textarea_val_str = textarea_val_str.replace(/(?:(?:\r\n|\r|\n)\s*){2}/gm, "");
      $("#hotels_pro_fw_link_with_db_columns").val(textarea_val_str);

      // if (textarea_val[textarea_val.length-2].trim().length > 0)
      //   console.log(" > 0");
        //$(".needed_records").text( parseInt($(".needed_records").text()) - textarea_val.length );

  })


  $(document).on("focusout", "#hotels_pro_fw_link_with_db_columns", function(event) {

    var textarea_val = $("#hotels_pro_fw_link_with_db_columns").val();

    textarea_val = textarea_val.split(/\n/);

    textarea_val = textarea_val.filter(Boolean)

    $(".needed_records").text( parseInt($(".needed_records").data('needed_records')) - textarea_val.length );


  })


},

generate_sql: function() {

  $(document).on("click", ".generate_sql_from_column_values", function(event) {

    event.preventDefault();

    var textarea_val = $("#hotels_pro_fw_link_with_db_columns").val();

    if (textarea_val.length <= 0)
      return;

    var data = {
      'action': 'generate_sql',
      'textarea_val' : textarea_val
    };

    jQuery.post(hotels_pro_fw_admin_plugin_data.ajax_url, data, function(response) {

      response = $.parseJSON(response);

      console.log(response);

      if (response == null) {
        alert("Make sure you have same number of database columns on above box");
        return;
      }

      if (typeof response.err !== "undefined" && response.err !== null && response.err.length > 0) {
        alert(response.err);
        return;
      }



      $("#hotels_pro_fw_generate_sql_from_column_values").val(response);

    })


  })

},

import_2_db: function() {

  $(document).on("click", ".import_2_db", function(event) {
    event.preventDefault();

    $(this).text("Importing...");

    var data = {
      'action': 'import_2_db',
    };



    jQuery.post(hotels_pro_fw_admin_plugin_data.ajax_url, data, function(response) {

      response = $.parseJSON(response);

      if ( typeof response.error != "undefined" && response.error.length > 0 ) {
        alert(response.error);
        $(this).text("Import Failed!");

        return;
      }

      $(".import_2_db").text("Import Successful!");
      var notification_audio = new Audio(hotels_pro_fw_admin_plugin_data.audio_folder+'noti.mp3');
      notification_audio.play();
      $(".import_2_db").parent().find(".log_response").remove();

      var response_html = "";

      $.each(response, function(k, val) {

        response_html += "<span data-sql_index='"+k+"' class='sql_response_span'><a href='#'>";
        response_html += ( val.stat ? "true" : "false" );
        response_html += "</a></span>";
        if ( (k+1) != response.length )
          response_html += " | ";
      })

      $(".import_2_db").parent().append("<div class='log_response'>"+response_html+"</div>");


      $(document).on("click", ".sql_response_span a", function(event) {
        event.preventDefault();

        if ($(".sql_display_toggle").length <= 0)
          $(".import_2_db").parent().append("<a href='#' class='sql_display_toggle'>Show/Hide SQL</a>");

        $(".import_2_db").parent().append("<div class='sql_code' style='display: none'></div>");

        $(".sql_code").html("<code>"+response[$(this).parent().data("sql_index")].sql+"</code>");

      })

      $(document).on("click", ".sql_display_toggle", function(event) {
        event.preventDefault();
        if ($(".sql_code").css("display") == "none")
          $(".sql_code").css("display", "block");
        else
          $(".sql_code").css("display", "none");

      })

    })

  })

},

api_make_request: function() {

  if ($(".api_make_request").length <= 0)
    return;

  $(document).on("click", ".api_make_request", function(event) {
    event.preventDefault();

    $(".api_make_request").after("<span class='request_txt_api'><br><br>Requesting...</span>");

    var api_url_to_request = $("#api_url_to_request").val();
    var api_url_next_id = $("#api_url_next_id").val();
    var api_total_fetch = $("#api_total_fetch").val();
    var api_file_name = $("#api_file_name").val();

    var data = {
      'action': 'api_make_request',
      'api_url_to_request' : api_url_to_request,
      'api_url_next_id' : api_url_next_id,
      'api_total_fetch' : api_total_fetch,
      'api_file_name' : api_file_name
    };

    jQuery.post(hotels_pro_fw_admin_plugin_data.ajax_url, data, function(response) {
      console.log(response);

      response = $.parseJSON(response);

      html = "";

      $.each(response, function(i, val){

        html += i + " : " + val + "<br>";

      })
      $(".request_txt_api").remove();
      $(".api_make_request").after("<br><br>"+html);
      $("#api_url_next_id").val("");
      $("#api_file_name").val("");
      $("#hotels_pro_fw_uploaded_json_file_link").val(response.file);

    })


  })

}



}

adminHP.adjustFileUploadTag();
adminHP.checkJSONFile();
adminHP.jsonEditor();
adminHP.needed_fields();

adminHP.generate_sql();
adminHP.import_2_db();
adminHP.api_make_request();


  /////

  $(document).on("click", ".admin_clear_sold_seats button", function(event) {

      event.preventDefault();

      var postID = $(this).data("postid");

      var data = {
        'action': 'clear_sold_seats',
        'postID' : postID,
        'userID' : hsf_tb_admin_plugin_data.userID

      };

      jQuery.post(hsf_tb_admin_plugin_data.ajax_url, data, function(response) {

        response = $.parseJSON(response);

        if (response) {
          $(".admin_clear_sold_seats").parent().append("<div>Cleared Sold Seats Data</div>")
        }


      });

  })

})
